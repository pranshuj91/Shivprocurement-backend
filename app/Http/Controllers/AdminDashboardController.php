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
        $entriesLast7Days = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->get();

        $totalCounts = [];
        $pendingCounts = [];
        $outCounts = [];
        $approvedCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();
            
            $dayEntries = $entriesLast7Days->filter(function ($e) use ($dayStart, $dayEnd) {
                return $e->created_at >= $dayStart && $e->created_at <= $dayEnd;
            });
            
            $totalCounts[] = $dayEntries->count();
            $pendingCounts[] = $dayEntries->where('status', 'pending')->count();
            $outCounts[] = $dayEntries->filter(function ($e) {
                return $e->moisture > 10.0 || $e->fm > 2.0 || $e->dm > 2.0;
            })->count();
            $approvedCounts[] = $dayEntries->where('status', 'approved')->count();
        }

        $totalCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $totalPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $pendingCurrent = UnloadingEntry::where('status', 'pending')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $pendingPrevious = UnloadingEntry::where('status', 'pending')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $outCurrent = UnloadingEntry::where(function ($q) {
            $q->where('moisture', '>', 10.0)->orWhere('fm', '>', 2.0)->orWhere('dm', '>', 2.0);
        })->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $outPrevious = UnloadingEntry::where(function ($q) {
            $q->where('moisture', '>', 10.0)->orWhere('fm', '>', 2.0)->orWhere('dm', '>', 2.0);
        })->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $approvedCurrent = UnloadingEntry::where('status', 'approved')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $approvedPrevious = UnloadingEntry::where('status', 'approved')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

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
            'total_trend' => $this->calculateTrendPercentage($totalCurrent, $totalPrevious),
            'pending_trend' => $this->calculateTrendPercentage($pendingCurrent, $pendingPrevious),
            'out_of_spec_trend' => $this->calculateTrendPercentage($outCurrent, $outPrevious),
            'approved_trend' => $this->calculateTrendPercentage($approvedCurrent, $approvedPrevious),
            'total_sparkline' => $this->generateSparklinePath($totalCounts),
            'pending_sparkline' => $this->generateSparklinePath($pendingCounts),
            'out_of_spec_sparkline' => $this->generateSparklinePath($outCounts),
            'approved_sparkline' => $this->generateSparklinePath($approvedCounts),
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

    public function getStatsJson()
    {
        $entriesLast7Days = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->get();

        $totalCounts = [];
        $pendingCounts = [];
        $outCounts = [];
        $approvedCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();
            
            $dayEntries = $entriesLast7Days->filter(function ($e) use ($dayStart, $dayEnd) {
                return $e->created_at >= $dayStart && $e->created_at <= $dayEnd;
            });
            
            $totalCounts[] = $dayEntries->count();
            $pendingCounts[] = $dayEntries->where('status', 'pending')->count();
            $outCounts[] = $dayEntries->filter(function ($e) {
                return $e->moisture > 10.0 || $e->fm > 2.0 || $e->dm > 2.0;
            })->count();
            $approvedCounts[] = $dayEntries->where('status', 'approved')->count();
        }

        $totalCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $totalPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $pendingCurrent = UnloadingEntry::where('status', 'pending')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $pendingPrevious = UnloadingEntry::where('status', 'pending')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $outCurrent = UnloadingEntry::where(function ($q) {
            $q->where('moisture', '>', 10.0)->orWhere('fm', '>', 2.0)->orWhere('dm', '>', 2.0);
        })->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $outPrevious = UnloadingEntry::where(function ($q) {
            $q->where('moisture', '>', 10.0)->orWhere('fm', '>', 2.0)->orWhere('dm', '>', 2.0);
        })->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $approvedCurrent = UnloadingEntry::where('status', 'approved')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $approvedPrevious = UnloadingEntry::where('status', 'approved')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'total' => UnloadingEntry::count(),
                'pending' => UnloadingEntry::where('status', 'pending')->count(),
                'approved' => UnloadingEntry::where('status', 'approved')->count(),
                'flagged' => UnloadingEntry::where('status', 'flagged')->count(),
                'out_of_spec' => UnloadingEntry::where(function ($query) {
                    $query->where('moisture', '>', 10.0)
                          ->orWhere('fm', '>', 2.0)
                          ->orWhere('dm', '>', 2.0);
                })->count(),
                'total_trend' => $this->calculateTrendPercentage($totalCurrent, $totalPrevious),
                'pending_trend' => $this->calculateTrendPercentage($pendingCurrent, $pendingPrevious),
                'out_of_spec_trend' => $this->calculateTrendPercentage($outCurrent, $outPrevious),
                'approved_trend' => $this->calculateTrendPercentage($approvedCurrent, $approvedPrevious),
                'total_sparkline' => $this->generateSparklinePath($totalCounts),
                'pending_sparkline' => $this->generateSparklinePath($pendingCounts),
                'out_of_spec_sparkline' => $this->generateSparklinePath($outCounts),
                'approved_sparkline' => $this->generateSparklinePath($approvedCounts),
            ]
        ]);
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

    public function storeUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $unit = Unit::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Procurement center added successfully.',
            'unit' => $unit
        ]);
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|digits:10|unique:users,phone',
            'pin' => 'required|string|digits:4',
        ]);

        $supervisor = \App\Models\User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'pin' => \Illuminate\Support\Facades\Hash::make($request->input('pin')),
            'role' => 'supervisor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supervisor added successfully.',
            'supervisor' => $supervisor
        ]);
    }

    private function calculateTrendPercentage($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $diff = (($current - $previous) / $previous) * 100;
        $sign = $diff >= 0 ? '+' : '';
        return $sign . round($diff) . '%';
    }

    private function generateSparklinePath(array $counts): string
    {
        $min = min($counts);
        $max = max($counts);
        $range = $max - $min;
        
        $points = [];
        foreach ($counts as $i => $c) {
            $x = $i * (120 / 6); // width 120
            if ($range == 0) {
                $y = 15; // middle
            } else {
                $y = 30 - (($c - $min) / $range) * 20 - 5; // height 30, with 5px padding
            }
            $points[] = "$x,$y";
        }
        return "M " . implode(" L ", $points);
    }
}
