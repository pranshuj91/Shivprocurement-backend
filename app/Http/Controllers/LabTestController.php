<?php

namespace App\Http\Controllers;

use App\Models\UnloadingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabTestController extends Controller
{
    public function upsert(Request $request, string $id)
    {
        $data = $request->validate([
            'lab_name' => ['required', 'string', 'max:255'],
            'lab_test_status' => ['required', 'in:pending,pass,fail,retest'],
            'lab_moisture' => ['required', 'numeric', 'min:0', 'max:100'],
            'lab_fm' => ['required', 'numeric', 'min:0', 'max:100'],
            'lab_dm' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $entry = UnloadingEntry::findOrFail($id);
        $entry->fill($data);
        $entry->lab_recorded_at = now();
        $entry->lab_recorded_by = Auth::id();
        $entry->save();

        $entry->load(['unit', 'mediaLogs', 'labRecordedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Lab test saved successfully.',
            'entry' => $entry->load(['unit', 'mediaLogs', 'labRecordedBy']),
        ]);
    }
}
