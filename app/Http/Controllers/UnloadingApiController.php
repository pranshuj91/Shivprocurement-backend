<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Supplier;
use App\Models\UnloadingEntry;
use App\Models\MediaLog;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UnloadingApiController extends Controller
{
    public function getUnits()
    {
        return response()->json(Unit::all());
    }

    public function getSuppliers()
    {
        return response()->json(Supplier::all());
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|digits:10|unique:users,phone',
            'pin'   => 'required|string|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'  => $request->input('name'),
            'phone' => $request->input('phone'),
            'pin'   => $request->input('pin'), // Auto-hashed by casts() in User model
            'role'  => 'supervisor',
        ]);

        $token = $user->createToken('SupervisorToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
            ],
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits:10',
            'pin'   => 'required|string|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('phone', $request->input('phone'))
                    ->where('role', 'supervisor')
                    ->first();

        if (!$user || !Hash::check($request->input('pin'), $user->pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or PIN.'
            ], 401);
        }

        $token = $user->createToken('SupervisorToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
            ],
            'token' => $token
        ]);
    }

    public function getEntries(Request $request)
    {
        $user = $request->user();

        $entries = UnloadingEntry::with(['unit', 'mediaLogs'])
            ->when($user, function ($query) use ($user) {
                // Supervisors only see their own entries; legacy rows (null created_by) stay visible
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhereNull('created_by');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($e) => $this->formatEntry($e));

        return response()->json($entries);
    }

    public function storeEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'            => 'required|string',
            'unit_id'       => 'required|exists:units,id',
            'truck_no'      => 'required|string',
            'purchase_type' => 'required|string',
            'sourced_from'  => 'nullable|string',
            'moisture'      => 'required|numeric|min:0',
            'fm'            => 'required|numeric|min:0',
            'dm'            => 'required|numeric|min:0',
            'media'         => 'nullable|array',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'gps_accuracy'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Resilient sync check — prevent duplicate submissions
        $id = $request->input('id');
        $existing = UnloadingEntry::with(['unit', 'mediaLogs'])->find($id);
        if ($existing) {
            // Only the owner (or legacy null-owner) can update
            if ($existing->created_by && $user && (int) $existing->created_by !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update this entry.',
                ], 403);
            }

            $existing->update([
                'unit_id'       => $request->input('unit_id'),
                'truck_no'      => $request->input('truck_no'),
                'purchase_type' => $request->input('purchase_type'),
                'sourced_from'  => $request->input('sourced_from'),
                'moisture'      => $request->input('moisture'),
                'fm'            => $request->input('fm'),
                'dm'            => $request->input('dm'),
                'latitude'      => $request->input('latitude'),
                'longitude'     => $request->input('longitude'),
                'gps_accuracy'  => $request->input('gps_accuracy'),
                'created_by'    => $existing->created_by ?: ($user?->id),
            ]);

            // Keep track of preserved media logs
            $keptPaths = [];
            $incomingMedia = $request->input('media') ?? [];

            foreach ($incomingMedia as $mediaItem) {
                $base64Data = $mediaItem['data'] ?? '';
                if (is_string($base64Data) && (str_starts_with($base64Data, '/storage/') || str_starts_with($base64Data, '/images/'))) {
                    $keptPaths[] = $base64Data;
                }
            }

            // Remove media logs that are no longer part of the entry
            $existing->mediaLogs()->whereNotIn('file_path', $keptPaths)->delete();

            // Process all media logs
            foreach ($incomingMedia as $mediaItem) {
                $this->storeMediaItem($existing, $mediaItem);
            }

            $savedEntry = UnloadingEntry::with(['unit', 'mediaLogs'])->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Entry updated and synced successfully',
                'entry'   => $this->formatEntry($savedEntry),
            ]);
        }

        // Create new entry
        $entry = UnloadingEntry::create([
            'id'            => $id,
            'unit_id'       => $request->input('unit_id'),
            'truck_no'      => $request->input('truck_no'),
            'purchase_type' => $request->input('purchase_type'),
            'sourced_from'  => $request->input('sourced_from'),
            'moisture'      => $request->input('moisture'),
            'fm'            => $request->input('fm'),
            'dm'            => $request->input('dm'),
            'status'        => 'pending',
            'latitude'      => $request->input('latitude'),
            'longitude'     => $request->input('longitude'),
            'gps_accuracy'  => $request->input('gps_accuracy'),
            'created_by'    => $user?->id,
        ]);

        // Decode and save media files if provided
        if ($request->has('media') && is_array($request->input('media'))) {
            foreach ($request->input('media') as $mediaItem) {
                $this->storeMediaItem($entry, $mediaItem);
            }
        }

        $savedEntry = UnloadingEntry::with(['unit', 'mediaLogs'])->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Entry synced successfully',
            'entry'   => $this->formatEntry($savedEntry),
        ], 201);
    }

    private function formatEntry(UnloadingEntry $e): array
    {
        return [
            'id'            => $e->id,
            'unit_id'       => $e->unit_id,
            'unit'          => $e->unit ? [
                'id'   => $e->unit->id,
                'name' => $e->unit->name,
                'code' => $e->unit->code,
            ] : null,
            'truck_no'      => $e->truck_no,
            'purchase_type' => $e->purchase_type,
            'sourced_from'  => $e->sourced_from,
            'moisture'      => $e->moisture,
            'fm'            => $e->fm,
            'dm'            => $e->dm,
            'status'        => $e->status,
            'remarks'       => $e->remarks,
            'latitude'      => $e->latitude,
            'longitude'     => $e->longitude,
            'gps_accuracy'  => $e->gps_accuracy,
            'created_by'    => $e->created_by,
            'created_at'    => $e->created_at,
            'media_logs'    => $e->mediaLogs->map(fn ($m) => [
                'type'      => $m->type,
                'file_path' => $m->file_path,
                'caption'   => $m->caption,
            ]),
        ];
    }

    private function storeMediaItem(UnloadingEntry $entry, array $mediaItem): void
    {
        $type       = $mediaItem['type'] ?? 'unknown';
        $base64Data = $mediaItem['data'] ?? '';

        if ($base64Data === '' || $base64Data === null) {
            return;
        }

        // Existing media path — preserve/update caption only
        if (is_string($base64Data) && (str_starts_with($base64Data, '/storage/') || str_starts_with($base64Data, '/images/'))) {
            $entry->mediaLogs()->where('file_path', $base64Data)->update([
                'caption' => $mediaItem['caption'] ?? null,
            ]);

            return;
        }

        $ext = 'jpg';
        if ($type === 'audio') {
            $ext = 'm4a';
        }
        if ($type === 'video') {
            $ext = 'mp4';
        }

        if (preg_match('/^data:[^;]+;base64,(.*)$/', $base64Data, $matches)) {
            $raw = $matches[1];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $t)) {
                $ext = $t[1];
            } elseif (preg_match('/^data:audio\/(\w+);base64,/', $base64Data, $t)) {
                $ext = $t[1];
            } elseif (preg_match('/^data:video\/(\w+);base64,/', $base64Data, $t)) {
                $ext = $t[1];
            }
        } else {
            $raw = $base64Data;
        }

        $decoded = base64_decode($raw, true);
        if ($decoded === false || $decoded === '') {
            return;
        }

        $filename = $type.'_'.uniqid().'.'.$ext;
        $path = 'media/'.$filename;
        Storage::disk('public')->put($path, $decoded);

        $entry->mediaLogs()->create([
            'type'      => $type,
            'file_path' => '/storage/'.$path,
            'caption'   => $mediaItem['caption'] ?? null,
        ]);
    }
}
