<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnloadingEntry;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Calculate stats (independent of listing filters)
        $stats = [
            'total' => UnloadingEntry::count(),
            'pending' => UnloadingEntry::where('status', 'pending')->count(),
            'approved' => UnloadingEntry::where('status', 'approved')->count(),
            'flagged' => UnloadingEntry::where('status', 'flagged')->count(),
            'out_of_spec' => UnloadingEntry::where(function ($query) {
                $query->where('moisture', '>', 10.0)
                      ->orWhere('fm', '>', 2.0)
                      ->orWhere('dm', '>', 2.0);
            })->count(),
        ];

        // 2. Build entries query with filters
        $query = UnloadingEntry::with(['unit', 'mediaLogs'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('truck_no', 'like', "%{$search}%")
                  ->orWhere('sourced_from', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->input('unit_id'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'out_of_spec') {
                $query->where(function ($q) {
                    $q->where('moisture', '>', 10.0)
                      ->orWhere('fm', '>', 2.0)
                      ->orWhere('dm', '>', 2.0);
                });
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('date_filter')) {
            $dateFilter = $request->input('date_filter');
            if ($dateFilter === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($dateFilter === 'week') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($dateFilter === 'month') {
                $query->where('created_at', '>=', now()->subDays(30));
            }
        }

        $moistureTrend = UnloadingEntry::orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->reverse()
            ->pluck('moisture')
            ->toArray();

        $mandiLeaderboard = UnloadingEntry::select('sourced_from')
            ->selectRaw('AVG(moisture) as avg_moisture')
            ->selectRaw('COUNT(*) as total_logs')
            ->selectRaw('SUM(CASE WHEN status = "flagged" OR status = "rejected" THEN 1 ELSE 0 END) as issue_logs')
            ->groupBy('sourced_from')
            ->orderBy('avg_moisture', 'asc')
            ->take(5)
            ->get();

        $entries = $query->paginate(15)->withQueryString();
        $units = Unit::all();
        $supervisors = \App\Models\User::orderBy('role', 'asc')->get();

        return view('admin.dashboard', compact('entries', 'stats', 'units', 'moistureTrend', 'mandiLeaderboard', 'supervisors'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,flagged,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        $entry = UnloadingEntry::findOrFail($id);
        $entry->status = $request->input('status');
        if ($request->has('remarks')) {
            $entry->remarks = $request->input('remarks');
        }
        $entry->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status' => $entry->status,
            'remarks' => $entry->remarks,
        ]);
    }
}
