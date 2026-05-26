<?php

namespace App\Http\Controllers;

use App\Models\ProcurementSetting;
use App\Models\UnloadingEntry;
use Illuminate\Http\Request;

class LabDashboardController extends Controller
{
    public function index(Request $request)
    {
        $settings = ProcurementSetting::current();

        $query = UnloadingEntry::with(['unit', 'labRecordedBy'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('truck_no', 'like', "%{$search}%")
                    ->orWhere('sourced_from', 'like', "%{$search}%");
            });
        }

        if ($request->input('lab_filter') === 'pending') {
            $query->whereNull('lab_test_status');
        } elseif ($request->input('lab_filter') === 'completed') {
            $query->whereNotNull('lab_test_status');
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('lab.dashboard', compact('entries', 'settings'));
    }
}
