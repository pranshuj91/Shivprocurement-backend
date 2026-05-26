<?php

namespace App\Http\Controllers;

use App\Models\ProcurementSetting;
use App\Models\Unit;
use App\Models\UnloadingEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $settings = ProcurementSetting::current();

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
            $outCounts[] = $dayEntries->filter(fn ($e) => $settings->entryIsOutOfSpec($e))->count();
            $approvedCounts[] = $dayEntries->where('status', 'approved')->count();
        }

        $totalCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $totalPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $pendingCurrent = UnloadingEntry::where('status', 'pending')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $pendingPrevious = UnloadingEntry::where('status', 'pending')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $outCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->where(function ($q) use ($settings) {
                $settings->applyOutOfSpecScope($q);
            })
            ->count();
        $outPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])
            ->where(function ($q) use ($settings) {
                $settings->applyOutOfSpecScope($q);
            })
            ->count();

        $approvedCurrent = UnloadingEntry::where('status', 'approved')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $approvedPrevious = UnloadingEntry::where('status', 'approved')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $outOfSpecCount = UnloadingEntry::where(function ($q) use ($settings) {
            $settings->applyOutOfSpecScope($q);
        })->count();

        $stats = [
            'total' => UnloadingEntry::count(),
            'pending' => UnloadingEntry::where('status', 'pending')->count(),
            'approved' => UnloadingEntry::where('status', 'approved')->count(),
            'flagged' => UnloadingEntry::where('status', 'flagged')->count(),
            'out_of_spec' => $outOfSpecCount,
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
        $query = UnloadingEntry::with(['unit', 'mediaLogs', 'labRecordedBy'])
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
                $settings->applyOutOfSpecScope($query);
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

        $entries = $query->paginate(15)->withQueryString();

        $logsEntriesById = $entries->getCollection()->mapWithKeys(
            fn (UnloadingEntry $entry) => [$entry->id => $entry]
        );

        $units = Unit::withCount([
            'unloadingEntries',
            'unloadingEntries as approved_count' => fn ($q) => $q->where('status', 'approved'),
            'unloadingEntries as pending_count' => fn ($q) => $q->where('status', 'pending'),
        ])->get();

        $unitAnalytics = $units->map(function ($unit) use ($settings) {
            $avgMoisture = UnloadingEntry::where('unit_id', $unit->id)->avg('moisture') ?? 0;
            $outOfSpec = UnloadingEntry::where('unit_id', $unit->id)
                ->where(function ($q) use ($settings) {
                    $settings->applyOutOfSpecScope($q);
                })
                ->count();
            $total = $unit->unloading_entries_count;
            $approvalRate = $total > 0 ? round(($unit->approved_count / $total) * 100) : 0;

            return (object) [
                'unit' => $unit,
                'total' => $total,
                'approved' => $unit->approved_count,
                'pending' => $unit->pending_count,
                'avg_moisture' => round((float) $avgMoisture, 1),
                'out_of_spec' => $outOfSpec,
                'approval_rate' => $approvalRate,
            ];
        });

        $supervisors = User::orderBy('role', 'asc')->get();

        $analytics = [
            'avg_moisture' => round((float) (UnloadingEntry::avg('moisture') ?? 0), 1),
            'avg_fm' => round((float) (UnloadingEntry::avg('fm') ?? 0), 1),
            'avg_dm' => round((float) (UnloadingEntry::avg('dm') ?? 0), 1),
            'pass_rate' => $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0,
            'status_rows' => [
                ['key' => 'approved', 'label' => 'Approved', 'count' => $stats['approved'], 'class' => 'approved'],
                ['key' => 'pending', 'label' => 'Pending', 'count' => $stats['pending'], 'class' => 'pending'],
                ['key' => 'flagged', 'label' => 'Flagged', 'count' => $stats['flagged'], 'class' => 'flagged'],
                ['key' => 'rejected', 'label' => 'Rejected', 'count' => UnloadingEntry::where('status', 'rejected')->count(), 'class' => 'rejected'],
            ],
        ];

        $weeklyActivity = [];
        $weeklyMax = 1;
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $count = UnloadingEntry::whereDate('created_at', $day)->count();
            $weeklyMax = max($weeklyMax, $count);
            $weeklyActivity[] = [
                'label' => $day->format('D'),
                'date' => $day->format('d M'),
                'count' => $count,
            ];
        }

        $topSuppliers = UnloadingEntry::select('sourced_from')
            ->selectRaw('COUNT(*) as total_logs')
            ->selectRaw('AVG(moisture) as avg_moisture')
            ->selectRaw('SUM(CASE WHEN status IN ("flagged", "rejected") THEN 1 ELSE 0 END) as issue_logs')
            ->groupBy('sourced_from')
            ->orderByDesc('total_logs')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'entries',
            'logsEntriesById',
            'stats',
            'units',
            'unitAnalytics',
            'supervisors',
            'settings',
            'analytics',
            'weeklyActivity',
            'weeklyMax',
            'topSuppliers',
        ));
    }

    public function getStatsJson()
    {
        $settings = ProcurementSetting::current();
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
            $outCounts[] = $dayEntries->filter(fn ($e) => $settings->entryIsOutOfSpec($e))->count();
            $approvedCounts[] = $dayEntries->where('status', 'approved')->count();
        }

        $totalCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $totalPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $pendingCurrent = UnloadingEntry::where('status', 'pending')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $pendingPrevious = UnloadingEntry::where('status', 'pending')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        $outCurrent = UnloadingEntry::where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->where(function ($q) use ($settings) {
                $settings->applyOutOfSpecScope($q);
            })
            ->count();
        $outPrevious = UnloadingEntry::whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])
            ->where(function ($q) use ($settings) {
                $settings->applyOutOfSpecScope($q);
            })
            ->count();

        $approvedCurrent = UnloadingEntry::where('status', 'approved')->where('created_at', '>=', now()->subDays(7)->startOfDay())->count();
        $approvedPrevious = UnloadingEntry::where('status', 'approved')->whereBetween('created_at', [now()->subDays(14)->startOfDay(), now()->subDays(7)->startOfDay()])->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'total' => UnloadingEntry::count(),
                'pending' => UnloadingEntry::where('status', 'pending')->count(),
                'approved' => UnloadingEntry::where('status', 'approved')->count(),
                'flagged' => UnloadingEntry::where('status', 'flagged')->count(),
                'out_of_spec' => UnloadingEntry::where(function ($q) use ($settings) {
                    $settings->applyOutOfSpecScope($q);
                })->count(),
                'total_trend' => $this->calculateTrendPercentage($totalCurrent, $totalPrevious),
                'pending_trend' => $this->calculateTrendPercentage($pendingCurrent, $pendingPrevious),
                'out_of_spec_trend' => $this->calculateTrendPercentage($outCurrent, $outPrevious),
                'approved_trend' => $this->calculateTrendPercentage($approvedCurrent, $approvedPrevious),
                'total_sparkline' => $this->generateSparklinePath($totalCounts),
                'pending_sparkline' => $this->generateSparklinePath($pendingCounts),
                'out_of_spec_sparkline' => $this->generateSparklinePath($outCounts),
                'approved_sparkline' => $this->generateSparklinePath($approvedCounts),
            ],
        ]);
    }

    public function updateQualitySettings(Request $request)
    {
        $data = $request->validate([
            'moisture_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'fm_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'dm_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $settings = ProcurementSetting::current();
        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Quality limits updated successfully.',
            'settings' => $settings->fresh()->toThresholdArray(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
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
            'unit' => $unit,
        ]);
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|digits:10|unique:users,phone',
            'pin' => 'required|string|digits:4',
        ]);

        $supervisor = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'pin' => Hash::make($request->input('pin')),
            'role' => 'supervisor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supervisor added successfully.',
            'supervisor' => $supervisor,
        ]);
    }

    private function calculateTrendPercentage($current, $previous): string
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }
        $diff = (($current - $previous) / $previous) * 100;
        $sign = $diff >= 0 ? '+' : '';

        return $sign.round($diff).'%';
    }

    private function generateSparklinePath(array $counts): string
    {
        $min = min($counts);
        $max = max($counts);
        $range = $max - $min;

        $points = [];
        foreach ($counts as $i => $c) {
            $x = $i * (120 / 6);
            if ($range == 0) {
                $y = 15;
            } else {
                $y = 30 - (($c - $min) / $range) * 20 - 5;
            }
            $points[] = "$x,$y";
        }

        return 'M '.implode(' L ', $points);
    }
}
