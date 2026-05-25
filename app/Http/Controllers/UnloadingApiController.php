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
            'phone' => 'required|string|unique:users,phone',
            'pin'   => 'required|string|size:4',
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
            'phone' => 'required|string',
            'pin'   => 'required|string|size:4',
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

    public function getEntries()
    {
        $entries = UnloadingEntry::with(['unit', 'mediaLogs'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($e) {
                return [
                    'id'            => $e->id,
                    'unit_id'       => $e->unit_id,
                    'truck_no'      => $e->truck_no,
                    'purchase_type' => $e->purchase_type,
                    'sourced_from'  => $e->sourced_from,
                    'moisture'      => $e->moisture,
                    'fm'            => $e->fm,
                    'dm'            => $e->dm,
                    'status'        => $e->status,
                    'latitude'      => $e->latitude,
                    'longitude'     => $e->longitude,
                    'gps_accuracy'  => $e->gps_accuracy,
                    'created_at'    => $e->created_at,
                    'media_logs'    => $e->mediaLogs->map(fn($m) => [
                        'type'      => $m->type,
                        'file_path' => $m->file_path,
                        'caption'   => $m->caption,
                    ]),
                ];
            });

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

        // Resilient sync check — prevent duplicate submissions
        $id = $request->input('id');
        $existing = UnloadingEntry::with(['unit', 'mediaLogs'])->find($id);
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Entry already exists (already synced)',
                'entry'   => [
                    'id'            => $existing->id,
                    'unit_id'       => $existing->unit_id,
                    'truck_no'      => $existing->truck_no,
                    'purchase_type' => $existing->purchase_type,
                    'sourced_from'  => $existing->sourced_from,
                    'moisture'      => $existing->moisture,
                    'fm'            => $existing->fm,
                    'dm'            => $existing->dm,
                    'status'        => $existing->status,
                    'created_at'    => $existing->created_at,
                    'media_logs'    => $existing->mediaLogs,
                ],
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
        ]);

        // Decode and save media files if provided
        if ($request->has('media') && is_array($request->input('media'))) {
            foreach ($request->input('media') as $mediaItem) {
                $type       = $mediaItem['type'] ?? 'unknown';
                $base64Data = $mediaItem['data'] ?? '';

                if (empty($base64Data)) continue;

                $ext = 'jpg';
                if ($type === 'audio')  $ext = 'm4a';
                if ($type === 'video')  $ext = 'mp4';

                if (preg_match('/^data:[^;]+;base64,(.*)$/', $base64Data, $matches)) {
                    $raw = $matches[1];
                    if (preg_match('/^data:image\/(\w+);base64,/', $mediaItem['data'], $t)) $ext = $t[1];
                    elseif (preg_match('/^data:audio\/(\w+);base64,/', $mediaItem['data'], $t)) $ext = $t[1];
                    elseif (preg_match('/^data:video\/(\w+);base64,/', $mediaItem['data'], $t)) $ext = $t[1];
                } else {
                    $raw = $base64Data;
                }

                $decoded = base64_decode($raw);
                if ($decoded !== false) {
                    $filename = $type . '_' . uniqid() . '.' . $ext;
                    $path = 'media/' . $filename;
                    Storage::disk('public')->put($path, $decoded);

                    $entry->mediaLogs()->create([
                        'type'      => $type,
                        'file_path' => '/storage/' . $path,
                        'caption'   => $mediaItem['caption'] ?? null,
                    ]);
                }
            }
        }

        $savedEntry = UnloadingEntry::with(['unit', 'mediaLogs'])->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Entry synced successfully',
            'entry'   => [
                'id'            => $savedEntry->id,
                'unit_id'       => $savedEntry->unit_id,
                'truck_no'      => $savedEntry->truck_no,
                'purchase_type' => $savedEntry->purchase_type,
                'sourced_from'  => $savedEntry->sourced_from,
                'moisture'      => $savedEntry->moisture,
                'fm'            => $savedEntry->fm,
                'dm'            => $savedEntry->dm,
                'status'        => $savedEntry->status,
                'latitude'      => $savedEntry->latitude,
                'longitude'     => $savedEntry->longitude,
                'gps_accuracy'  => $savedEntry->gps_accuracy,
                'created_at'    => $savedEntry->created_at,
                'media_logs'    => $savedEntry->mediaLogs,
            ],
        ], 201);
    }
}
