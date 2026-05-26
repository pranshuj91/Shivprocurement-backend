<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shiv Edibles Ltd. — Procurement Admin</title>
    
    <!-- Inter — clean UI font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 0.9375rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
        }

        /* Premium hover states & smooth card transitions */
        .stats-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.1);
            border-color: #d4d4d8;
        }

        .premium-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .premium-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 24px -12px rgba(0, 0, 0, 0.12);
            border-color: #d4d4d8;
        }

        /* Premium procurement logs table */
        .logs-table-card {
            box-shadow: 0 1px 2px rgba(13, 40, 24, 0.04), 0 8px 24px -12px rgba(13, 40, 24, 0.12);
        }
        .logs-table-scroll {
            scrollbar-gutter: stable;
        }
        .logs-table thead th {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #a1a1aa;
            background: linear-gradient(180deg, #fafafa 0%, #f4f4f5 100%);
            border-bottom: 1px solid #e4e4e7;
            padding: 0.875rem 1.25rem;
            white-space: nowrap;
        }
        .logs-table thead th:first-child {
            padding-left: 1.5rem;
        }
        .logs-table thead th:last-child {
            padding-right: 1.5rem;
        }
        .logs-table tbody tr.select-row {
            transition: background-color 0.18s ease, box-shadow 0.18s ease;
            border-bottom: 1px solid #f4f4f5;
        }
        .logs-table tbody tr.select-row:hover {
            background: linear-gradient(90deg, rgba(13, 40, 24, 0.045) 0%, rgba(250, 250, 250, 0.9) 42%);
        }
        .logs-table tbody tr.select-row:hover .logs-row-chevron {
            opacity: 1;
            transform: translateX(0);
        }
        .logs-table tbody tr.select-row.selected {
            background: linear-gradient(90deg, rgba(13, 40, 24, 0.07) 0%, #f4f4f5 55%) !important;
            box-shadow: inset 3px 0 0 #0d2818;
        }
        .logs-table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }
        .logs-table tbody td:first-child {
            padding-left: 1.5rem;
        }
        .logs-table tbody td:last-child {
            padding-right: 1.5rem;
        }
        .logs-id-badge {
            display: inline-flex;
            align-items: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
            font-weight: 600;
            color: #0d2818;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 0.5rem;
            padding: 0.2rem 0.55rem;
            letter-spacing: -0.02em;
        }
        .metric-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            min-width: 3.25rem;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            padding: 0.35rem 0.55rem;
            border-radius: 0.5rem;
            border: 1px solid transparent;
        }
        .metric-pill--ok {
            color: #047857;
            background: #ecfdf5;
            border-color: #a7f3d0;
        }
        .metric-pill--warn {
            color: #b45309;
            background: #fffbeb;
            border-color: #fde68a;
        }
        .metric-pill__dot {
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background: currentColor;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.3rem 0.65rem;
            border-radius: 9999px;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .status-pill__dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            flex-shrink: 0;
        }
        .status-pill--pending {
            color: #1d4ed8;
            background: #eff6ff;
            border-color: #bfdbfe;
        }
        .status-pill--pending .status-pill__dot { background: #3b82f6; }
        .status-pill--approved {
            color: #047857;
            background: #ecfdf5;
            border-color: #a7f3d0;
        }
        .status-pill--approved .status-pill__dot { background: #10b981; }
        .status-pill--flagged {
            color: #b45309;
            background: #fffbeb;
            border-color: #fde68a;
        }
        .status-pill--flagged .status-pill__dot { background: #f59e0b; }
        .status-pill--rejected {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fecaca;
        }
        .status-pill--rejected .status-pill__dot { background: #ef4444; }
        .logs-row-chevron {
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity 0.18s ease, transform 0.18s ease;
            color: #a1a1aa;
        }
        .logs-table-footer nav[role="navigation"] {
            width: 100%;
        }
        .logs-table-footer nav[role="navigation"] > div {
            gap: 0.5rem;
        }
        .logs-table-footer a,
        .logs-table-footer span {
            font-size: 14px !important;
            border-radius: 0.5rem !important;
        }
        .logs-table-footer span[aria-current="page"] span {
            background: #0d2818 !important;
            border-color: #0d2818 !important;
            color: #fff !important;
        }

        /* Compact data tables (supervisors, etc.) */
        .logs-table.data-table {
            table-layout: fixed;
        }
        .logs-table.data-table tbody tr {
            border-bottom: 1px solid #f4f4f5;
            transition: background-color 0.15s ease;
        }
        .logs-table.data-table tbody tr:hover {
            background: #fafafa;
        }
        .logs-table--supervisors .col-supervisor { width: 28%; }
        .logs-table--supervisors .col-phone { width: 22%; }
        .logs-table--supervisors .col-role { width: 14%; }
        .logs-table--supervisors .col-status { width: 14%; }
        .logs-table--supervisors .col-registered { width: 22%; }

        /* Profile settings — horizontal row layout */
        .profile-hero {
            background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
        }
        .profile-settings-block {
            padding: 0 1.75rem 1.25rem;
            max-width: 40rem;
        }
        .profile-settings-block__title {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #a1a1aa;
            padding: 1.25rem 0 0.5rem;
            margin: 0;
        }
        .profile-settings-block__title:not(:first-child) {
            margin-top: 0.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f4f4f5;
        }
        .profile-row {
            display: grid;
            grid-template-columns: 8.5rem 1fr;
            gap: 1rem 1.5rem;
            align-items: center;
            padding: 0.625rem 0;
        }
        .profile-row label {
            font-size: 0.9375rem;
            font-weight: 500;
            color: #52525b;
            line-height: 1.4;
        }
        .profile-row input {
            width: 100%;
            max-width: 22rem;
            font-size: 0.9375rem;
        }
        .profile-row--readonly .profile-readonly {
            display: inline-flex;
            align-items: center;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #0d2818;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 0.5rem;
            padding: 0.35rem 0.65rem;
        }
        @media (max-width: 640px) {
            .profile-row {
                grid-template-columns: 1fr;
                gap: 0.35rem;
            }
            .profile-row input {
                max-width: none;
            }
        }

        /* Analytics */
        .analytics-metric {
            background: #fff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
        }
        .analytics-metric__value {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0d2818;
            line-height: 1.2;
        }
        .activity-bars {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            height: 8rem;
            padding-top: 0.5rem;
        }
        .activity-bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            min-width: 0;
        }
        .activity-bar-track {
            width: 100%;
            max-width: 2.5rem;
            height: 6rem;
            background: #f4f4f5;
            border-radius: 0.375rem;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
        }
        .activity-bar-fill {
            width: 100%;
            background: linear-gradient(180deg, #1b4d3e 0%, #0d2818 100%);
            border-radius: 0.375rem 0.375rem 0 0;
            min-height: 4px;
            transition: height 0.3s ease;
        }
        .status-meter-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
        }
        .status-meter-row + .status-meter-row {
            border-top: 1px solid #f4f4f5;
        }
        .status-meter-bar {
            flex: 1;
            height: 0.375rem;
            background: #f4f4f5;
            border-radius: 9999px;
            overflow: hidden;
        }
        .status-meter-fill {
            height: 100%;
            border-radius: 9999px;
        }
        .status-meter-fill--approved { background: #10b981; }
        .status-meter-fill--pending { background: #3b82f6; }
        .status-meter-fill--flagged { background: #f59e0b; }
        .status-meter-fill--rejected { background: #ef4444; }

        /* Procurement center cards */
        .center-card {
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 1.25rem;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .center-card:hover {
            border-color: #bbf7d0;
            box-shadow: 0 4px 16px -8px rgba(13, 40, 24, 0.15);
        }
        .center-stat {
            text-align: left;
        }
        .center-stat__label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .center-stat__value {
            font-size: 1.0625rem;
            font-weight: 600;
            color: #18181b;
            margin-top: 0.15rem;
        }

        /* Premium select row highlight */
        .select-row {
            transition: all 0.15s ease;
            cursor: pointer;
        }

        /* Custom scrollbar for premium aesthetic */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e4e4e7;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d4d4d8;
        }

        /* Keep drawer off-page when Tailwind has not loaded yet */
        #drawer.hidden {
            display: none !important;
        }
    </style>
</head>
<body class="bg-[#f5f8f5] text-zinc-900 h-screen w-screen overflow-hidden flex">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-gradient-to-b from-[#f4f7f4] via-[#eff2ef] to-[#ebefeb] border-r border-[#dee4de] flex flex-col shrink-0">
        <!-- Logo Header -->
        <div class="h-16 px-6 flex items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0d2818] to-[#1b4d3e] flex items-center justify-center text-white shadow-sm shadow-[#0d2818]/20">
                    <i data-lucide="sprout" class="w-5 h-5 text-emerald-200"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold uppercase tracking-wider text-[#0d2818] leading-tight">SHIV EDIBLES</h1>
                    <span class="text-[11px] text-[#2e5a44] font-semibold tracking-wide uppercase opacity-75">Procurement Portal</span>
                </div>
            </div>
        </div>
        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-[#0d2818] to-[#143d24] text-white border border-[#0d2818] transition duration-200 cursor-pointer text-left shadow-md shadow-[#0d2818]/15 translate-x-0.5">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-200"></i>
                Dashboard
            </button>

            <button onclick="switchTab('logs')" id="nav-logs" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-zinc-400"></i>
                Procurement Logs
            </button>
            
            <button onclick="switchTab('units')" id="nav-units" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left">
                <i data-lucide="git-branch" class="w-4 h-4 text-zinc-400"></i>
                Procurement Centers
            </button>

            <button onclick="switchTab('supervisors')" id="nav-supervisors" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left">
                <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                Supervisors List
            </button>

            <button onclick="switchTab('analytics')" id="nav-analytics" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-zinc-400"></i>
                Analytics & Reports
            </button>

            <button onclick="switchTab('settings')" id="nav-settings" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left">
                <i data-lucide="settings" class="w-4 h-4 text-zinc-400"></i>
                Settings
            </button>
        </nav>

        <!-- Sidebar footer branding/version -->
        <div class="p-6 border-t border-[#dee4de] flex flex-col gap-1 text-center bg-[#ebefeb]/40">
            <span class="text-[10px] text-[#2e5a44] font-bold tracking-wider uppercase">Shiv Procurement</span>
            <span class="text-[9px] text-zinc-400 font-medium">v1.1 • Enterprise Admin</span>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden bg-[#f5f8f5]">
        @php
            $hour = date('H');
            $greeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
        @endphp
        
        <!-- Header / Stats Summary -->
        <header class="h-16 bg-[#f5f8f5] border-b border-[#dee4de] px-8 flex items-center justify-between shrink-0">
            <div>
                <h2 class="text-base font-bold tracking-tight text-zinc-900 font-sans">Good {{ $greeting }}, {{ auth()->user()->name ?? 'Manager' }}</h2>
                <p class="text-[11px] text-zinc-500 mt-0.5 font-medium">Here's what's happening at Shiv Edibles today.</p>
            </div>
            <div class="flex items-center gap-4">
                <!-- User Profile & Log Out -->
                <div class="flex items-center gap-3 pl-1">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-800 font-semibold text-sm tracking-wider">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-sm font-semibold text-zinc-800 leading-none">{{ auth()->user()->name ?? 'Manager' }}</span>
                            <span class="text-[9px] text-zinc-400 font-medium mt-0.5">HQ Manager</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline flex items-center">
                        @csrf
                        <button type="submit" class="p-1.5 hover:bg-red-50 hover:text-red-600 rounded-lg text-zinc-400 transition cursor-pointer flex items-center justify-center border border-transparent hover:border-red-100" title="Log Out">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>

                <!-- Time & Refresh -->
                <button onclick="window.location.reload()" class="p-2 hover:bg-zinc-100/70 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

            <!-- Scrollable Workspace Container -->
            <div class="flex-1 overflow-y-auto flex flex-col bg-[#f5f8f5]">
                     <!-- Tab 1: Dashboard Feed -->
            <div id="view-dashboard" class="flex flex-col">
                <!-- Stats Grid Block -->
                @php
                    // Card 1: Total Logs
                    $totalTrendVal = $stats['total_trend'];
                    $isTotalNegative = str_starts_with($totalTrendVal, '-');
                    $totalBadgeColor = $isTotalNegative ? 'rose' : 'emerald';
                    $totalTrendIcon = $isTotalNegative ? 'trending-down' : 'trending-up';

                    // Card 2: Pending Verification
                    $pendingTrendVal = $stats['pending_trend'];
                    $isPendingNegative = str_starts_with($pendingTrendVal, '-');
                    $pendingBadgeColor = $isPendingNegative ? 'emerald' : 'rose';
                    $pendingTrendIcon = $isPendingNegative ? 'trending-down' : 'trending-up';

                    // Card 3: Quality Outliers
                    $outTrendVal = $stats['out_of_spec_trend'];
                    $isOutNegative = str_starts_with($outTrendVal, '-');
                    $outBadgeColor = $isOutNegative ? 'emerald' : 'rose';
                    $outTrendIcon = $isOutNegative ? 'trending-down' : 'trending-up';

                    // Card 4: Approved Entries
                    $approvedTrendVal = $stats['approved_trend'];
                    $isApprovedNegative = str_starts_with($approvedTrendVal, '-');
                    $approvedBadgeColor = $isApprovedNegative ? 'rose' : 'emerald';
                    $approvedTrendIcon = $isApprovedNegative ? 'trending-down' : 'trending-up';
                @endphp
                <section class="p-6 pb-2 shrink-0 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Card 1: Total Logs -->
                    <div class="premium-card bg-white border border-[#dee4de] rounded-xl p-3.5 flex flex-col justify-between shadow-xs transition duration-200 min-h-[84px] relative overflow-hidden text-left">
                        <div class="flex items-center gap-1.5">
                            <div class="p-1 bg-zinc-50 border border-zinc-100 rounded-md text-zinc-500">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Total Logs</span>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight" id="stat-total">{{ number_format($stats['total']) }}</h3>
                            <div class="flex items-center gap-2">
                                <svg id="svg-total-sparkline" class="w-12 h-6 text-{{ $totalBadgeColor }}-500" viewBox="0 0 120 30" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path id="path-total-sparkline" d="{{ $stats['total_sparkline'] }}"></path>
                                </svg>
                                <span id="badge-total-trend" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-{{ $totalBadgeColor }}-50 text-{{ $totalBadgeColor }}-700 border border-{{ $totalBadgeColor }}-100">
                                    <i data-lucide="{{ $totalTrendIcon }}" class="w-2 h-2"></i>
                                    {{ $stats['total_trend'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Pending Verification -->
                    <div class="premium-card bg-white border border-[#dee4de] rounded-xl p-3.5 flex flex-col justify-between shadow-xs transition duration-200 min-h-[84px] relative overflow-hidden text-left">
                        <div class="flex items-center gap-1.5">
                            <div class="p-1 bg-zinc-50 border border-zinc-100 rounded-md text-zinc-500">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Pending Verification</span>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight" id="stat-pending">{{ number_format($stats['pending']) }}</h3>
                            <div class="flex items-center gap-2">
                                <svg id="svg-pending-sparkline" class="w-12 h-6 text-{{ $pendingBadgeColor }}-500" viewBox="0 0 120 30" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path id="path-pending-sparkline" d="{{ $stats['pending_sparkline'] }}"></path>
                                </svg>
                                <span id="badge-pending-trend" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-{{ $pendingBadgeColor }}-50 text-{{ $pendingBadgeColor }}-700 border border-{{ $pendingBadgeColor }}-100">
                                    <i data-lucide="{{ $pendingTrendIcon }}" class="w-2 h-2"></i>
                                    {{ $stats['pending_trend'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Quality Outliers -->
                    <div class="premium-card bg-white border border-[#dee4de] rounded-xl p-3.5 flex flex-col justify-between shadow-xs transition duration-200 min-h-[84px] relative overflow-hidden text-left">
                        <div class="flex items-center gap-1.5">
                            <div class="p-1 bg-zinc-50 border border-zinc-100 rounded-md text-zinc-500">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Quality Outliers</span>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight" id="stat-out-of-spec">{{ number_format($stats['out_of_spec']) }}</h3>
                            <div class="flex items-center gap-2">
                                <svg id="svg-out-of-spec-sparkline" class="w-12 h-6 text-{{ $outBadgeColor }}-500" viewBox="0 0 120 30" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path id="path-out-of-spec-sparkline" d="{{ $stats['out_of_spec_sparkline'] }}"></path>
                                </svg>
                                <span id="badge-out-of-spec-trend" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-{{ $outBadgeColor }}-50 text-{{ $outBadgeColor }}-700 border border-{{ $outBadgeColor }}-100">
                                    <i data-lucide="{{ $outTrendIcon }}" class="w-2 h-2"></i>
                                    {{ $stats['out_of_spec_trend'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Approved Entries -->
                    <div class="premium-card bg-white border border-[#dee4de] rounded-xl p-3.5 flex flex-col justify-between shadow-xs transition duration-200 min-h-[84px] relative overflow-hidden text-left">
                        <div class="flex items-center gap-1.5">
                            <div class="p-1 bg-zinc-50 border border-zinc-100 rounded-md text-zinc-500">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Approved Entries</span>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight" id="stat-approved">{{ number_format($stats['approved']) }}</h3>
                            <div class="flex items-center gap-2">
                                <svg id="svg-approved-sparkline" class="w-12 h-6 text-{{ $approvedBadgeColor }}-500" viewBox="0 0 120 30" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path id="path-approved-sparkline" d="{{ $stats['approved_sparkline'] }}"></path>
                                </svg>
                                <span id="badge-approved-trend" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-{{ $approvedBadgeColor }}-50 text-{{ $approvedBadgeColor }}-700 border border-{{ $approvedBadgeColor }}-100">
                                    <i data-lucide="{{ $approvedTrendIcon }}" class="w-2 h-2"></i>
                                    {{ $stats['approved_trend'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 2: Procurement Logs (filters + entries table) -->
            <div id="view-logs" class="hidden flex flex-col flex-1 min-h-0">
                <div class="px-8 pt-6 pb-0 shrink-0">
                    <h3 class="text-base font-bold text-zinc-900">Procurement Logs</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Search, filter, and review all unloading entries.</p>
                </div>

                <!-- Search & Filters (Standalone Row) -->
                <section class="p-8 pb-0 shrink-0">
                    <div class="bg-white border border-[#dee4de] rounded-xl p-4 shadow-sm">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <input type="hidden" name="tab" value="logs">
                            <!-- Left: Search and Dropdowns -->
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                <!-- Search -->
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                    </span>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                        placeholder="Search Truck ID, Supplier..." 
                                        class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:outline-none transition">
                                </div>

                                <!-- Procurement Center -->
                                <div>
                                    <select name="unit_id" id="unit_id" 
                                        class="w-full px-3 py-2 text-sm bg-white border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:outline-none transition">
                                        <option value="">All Units</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <select name="status" id="status" 
                                        class="w-full px-3 py-2 text-sm bg-white border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:outline-none transition">
                                        <option value="">All Quality Statuses</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Verify</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (In-Spec)</option>
                                        <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged (Out-of-Spec)</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="out_of_spec" {{ request('status') === 'out_of_spec' ? 'selected' : '' }}>Quality Outliers</option>
                                    </select>
                                </div>

                                <!-- Time Window -->
                                <div>
                                    <select name="date_filter" id="date_filter" 
                                        class="w-full px-3 py-2 text-sm bg-white border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:outline-none transition">
                                        <option value="">All Time</option>
                                        <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                                        <option value="week" {{ request('date_filter') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                                        <option value="month" {{ request('date_filter') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right: Action Buttons -->
                            <div class="flex items-center gap-2 shrink-0 self-end lg:self-auto">
                                <button type="submit" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Apply Filters
                                </button>
                                @if(request()->anyFilled(['search', 'unit_id', 'status', 'date_filter']))
                                    <a href="{{ route('admin.dashboard', ['tab' => 'logs']) }}" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-sm font-medium rounded-lg transition duration-150 flex items-center justify-center cursor-pointer">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Premium Logs Table -->
                <section class="flex-1 px-8 pb-8 pt-5 flex flex-col min-h-0">
                    <div class="logs-table-card flex flex-col flex-1 min-h-0 rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                        <!-- Table toolbar -->
                        <div class="shrink-0 px-5 py-4 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gradient-to-r from-white via-white to-emerald-50/30">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0d2818]/5 border border-[#0d2818]/10 flex items-center justify-center text-[#0d2818]">
                                    <i data-lucide="layers" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-800 leading-tight">
                                        {{ number_format($entries->total()) }} {{ Str::plural('entry', $entries->total()) }}
                                    </p>
                                    @if($entries->total() > 0)
                                        <p class="text-[11px] text-zinc-400 mt-0.5">
                                            Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ number_format($entries->total()) }}
                                        </p>
                                    @else
                                        <p class="text-[11px] text-zinc-400 mt-0.5">No records to display</p>
                                    @endif
                                </div>
                            </div>
                            <p class="text-[11px] text-zinc-400 flex items-center gap-1.5 sm:justify-end">
                                <i data-lucide="mouse-pointer-click" class="w-3.5 h-3.5"></i>
                                Select a row to open details
                            </p>
                        </div>

                        <div class="flex-1 overflow-auto logs-table-scroll min-h-0">
                            <table class="logs-table w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10">
                                    <tr>
                                        <th>Entry ID</th>
                                        <th>Vehicle</th>
                                        <th>Center</th>
                                        <th>Source</th>
                                        <th class="text-center">Quality</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Received</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-zinc-700">
                                    @forelse($entries as $entry)
                                        @php
                                            $sourceLabel = $entry->sourced_from ?? 'Spot Buyer';
                                            $sourceInitial = strtoupper(substr(preg_replace('/\s+/', '', $sourceLabel), 0, 2));
                                        @endphp
                                        <tr class="select-row group"
                                            data-id="{{ $entry->id }}"
                                            data-json="{{ json_encode($entry) }}">
                                            <td class="whitespace-nowrap">
                                                <span class="logs-id-badge">#{{ $entry->id }}</span>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200/70 flex items-center justify-center text-zinc-500 shrink-0">
                                                        <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                                                    </span>
                                                    <span class="font-mono text-[11px] font-semibold tracking-tight text-zinc-800">{{ $entry->truck_no }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-700/70 shrink-0"></i>
                                                    <span class="text-zinc-600 font-medium truncate max-w-[140px]" title="{{ $entry->unit->name ?? 'N/A' }}">{{ $entry->unit->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0d2818] to-[#1b4d3e] text-[10px] font-bold text-white flex items-center justify-center shrink-0 shadow-sm">
                                                        {{ $sourceInitial }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        <p class="font-semibold text-zinc-800 truncate max-w-[160px]" title="{{ $sourceLabel }}">{{ $sourceLabel }}</p>
                                                        <p class="text-[10px] text-zinc-400 mt-0.5">{{ $entry->purchase_type ?? 'Direct' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <span class="metric-pill {{ $entry->moisture > $settings->moisture_threshold ? 'metric-pill--warn' : 'metric-pill--ok' }}" title="Moisture">
                                                        <span class="metric-pill__dot"></span>
                                                        {{ number_format($entry->moisture, 1) }}%
                                                    </span>
                                                    <span class="metric-pill {{ $entry->fm > $settings->fm_threshold ? 'metric-pill--warn' : 'metric-pill--ok' }}" title="Foreign Matter">
                                                        <span class="metric-pill__dot"></span>
                                                        {{ number_format($entry->fm, 1) }}%
                                                    </span>
                                                    <span class="metric-pill {{ $entry->dm > $settings->dm_threshold ? 'metric-pill--warn' : 'metric-pill--ok' }}" title="Damaged">
                                                        <span class="metric-pill__dot"></span>
                                                        {{ number_format($entry->dm, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center row-status-cell whitespace-nowrap">
                                                @if($entry->status === 'approved')
                                                    <span class="status-pill status-pill--approved"><span class="status-pill__dot"></span>Approved</span>
                                                @elseif($entry->status === 'flagged')
                                                    <span class="status-pill status-pill--flagged"><span class="status-pill__dot"></span>Flagged</span>
                                                @elseif($entry->status === 'rejected')
                                                    <span class="status-pill status-pill--rejected"><span class="status-pill__dot"></span>Rejected</span>
                                                @else
                                                    <span class="status-pill status-pill--pending"><span class="status-pill__dot"></span>Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <div class="text-right">
                                                        <p class="text-[11px] font-medium text-zinc-700">{{ $entry->created_at ? $entry->created_at->format('d M Y') : 'N/A' }}</p>
                                                        <p class="text-[10px] text-zinc-400 font-mono mt-0.5">{{ $entry->created_at ? $entry->created_at->format('H:i') : '' }}</p>
                                                    </div>
                                                    <i data-lucide="chevron-right" class="logs-row-chevron w-4 h-4 shrink-0"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <div class="py-16 px-6 text-center">
                                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-400 mb-4">
                                                        <i data-lucide="inbox" class="w-7 h-7"></i>
                                                    </div>
                                                    <p class="text-sm font-semibold text-zinc-700">No records found</p>
                                                    <p class="text-sm text-zinc-400 mt-1 max-w-sm mx-auto">Try adjusting your search or filters to find unloading entries.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($entries->hasPages() || $entries->total() > 0)
                            <div class="logs-table-footer shrink-0 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <p class="text-[11px] text-zinc-400 order-2 sm:order-1">
                                    Page {{ $entries->currentPage() }} of {{ $entries->lastPage() ?: 1 }}
                                </p>
                                @if($entries->hasPages())
                                    <div class="order-1 sm:order-2">
                                        {{ $entries->links() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Tab 2: Procurement Centers -->
            <div id="view-units" class="hidden flex flex-col">
                <div class="px-8 pt-6 pb-0 shrink-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-left">
                        <h3 class="text-base font-bold text-zinc-900">Procurement Centers</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Manage and monitor active crushing units and receiving depots.</p>
                    </div>
                    <button onclick="openAddCenterModal()" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10 shrink-0">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Center
                    </button>
                </div>

                <section class="px-8 pb-8 pt-5">
                    <div class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-zinc-100 flex items-center gap-3 bg-gradient-to-r from-white via-white to-emerald-50/30">
                            <div class="w-9 h-9 rounded-xl bg-[#0d2818]/5 border border-[#0d2818]/10 flex items-center justify-center text-[#0d2818]">
                                <i data-lucide="warehouse" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-zinc-800 leading-tight">{{ $units->count() }} active {{ Str::plural('center', $units->count()) }}</p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">Receiving depots across Rajasthan</p>
                            </div>
                        </div>

                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($unitAnalytics as $ua)
                                @php
                                    $unit = $ua->unit;
                                    $location = str_contains($unit->name, 'Baran') ? 'Baran, RJ' : (str_contains($unit->name, 'Kota') ? 'Kota, RJ' : 'Moondla, RJ');
                                    $capacity = $unit->id == 1 ? '300 MT' : ($unit->id == 2 ? '250 MT' : '150 MT');
                                @endphp
                                <article class="center-card text-left">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl bg-[#0d2818]/5 border border-[#0d2818]/10 flex items-center justify-center text-[#0d2818] shrink-0">
                                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-semibold text-zinc-900 truncate">{{ $unit->name }}</h4>
                                                <p class="text-sm text-zinc-500 mt-0.5">{{ $unit->code ?? 'UNIT-'.$unit->id }} · {{ $location }}</p>
                                            </div>
                                        </div>
                                        <span class="status-pill status-pill--approved shrink-0"><span class="status-pill__dot"></span>Active</span>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-zinc-100">
                                        <div class="center-stat">
                                            <span class="center-stat__label">Logs</span>
                                            <p class="center-stat__value">{{ number_format($ua->total) }}</p>
                                        </div>
                                        <div class="center-stat">
                                            <span class="center-stat__label">Approved</span>
                                            <p class="center-stat__value text-emerald-700">{{ $ua->approval_rate }}%</p>
                                        </div>
                                        <div class="center-stat">
                                            <span class="center-stat__label">Avg moisture</span>
                                            <p class="center-stat__value {{ $ua->avg_moisture > $settings->moisture_threshold ? 'text-amber-600' : '' }}">{{ $ua->avg_moisture }}%</p>
                                        </div>
                                        <div class="center-stat">
                                            <span class="center-stat__label">Capacity</span>
                                            <p class="center-stat__value">{{ $capacity }}</p>
                                        </div>
                                    </div>

                                    @if($ua->pending > 0)
                                        <p class="text-xs text-blue-600 mt-3 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $ua->pending }} pending verification
                                        </p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 3: Supervisors List -->
            <div id="view-supervisors" class="hidden flex flex-col">
                <div class="px-8 pt-6 pb-0 shrink-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-left">
                        <h3 class="text-base font-bold text-zinc-900">Unloading Supervisors</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Manage mobile supervisor accounts and access authorizations.</p>
                    </div>
                    <button onclick="openAddSupervisorModal()" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10 shrink-0 self-start sm:self-auto">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Supervisor
                    </button>
                </div>

                <section class="px-8 pb-8 pt-5">
                    <div class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-zinc-100 flex items-center justify-between gap-3 bg-gradient-to-r from-white via-white to-emerald-50/30">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0d2818]/5 border border-[#0d2818]/10 flex items-center justify-center text-[#0d2818]">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-800 leading-tight">{{ $supervisors->count() }} {{ Str::plural('supervisor', $supervisors->count()) }}</p>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">Field staff with mobile app access</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto logs-table-scroll">
                            <table class="logs-table logs-table--supervisors data-table w-full text-left border-collapse">
                                <colgroup>
                                    <col class="col-supervisor">
                                    <col class="col-phone">
                                    <col class="col-role">
                                    <col class="col-status">
                                    <col class="col-registered">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Supervisor</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Registered</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-zinc-700">
                                    @forelse($supervisors as $supervisor)
                                        @php
                                            $initials = strtoupper(substr(preg_replace('/\s+/', '', $supervisor->name), 0, 2));
                                            $isManager = $supervisor->role === 'manager';
                                        @endphp
                                        <tr class="group hover:bg-zinc-50/80 transition">
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0d2818] to-[#1b4d3e] text-[10px] font-bold text-white flex items-center justify-center shrink-0 shadow-sm">
                                                        {{ $initials }}
                                                    </span>
                                                    <span class="font-semibold text-zinc-800 truncate min-w-0">{{ $supervisor->name }}</span>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-zinc-400 shrink-0"></i>
                                                    <span class="font-mono text-[11px] font-medium text-zinc-700">{{ $supervisor->phone ?? 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center whitespace-nowrap">
                                                @if($isManager)
                                                    <span class="status-pill status-pill--approved"><span class="status-pill__dot"></span>Manager</span>
                                                @else
                                                    <span class="status-pill status-pill--pending"><span class="status-pill__dot"></span>Supervisor</span>
                                                @endif
                                            </td>
                                            <td class="text-center whitespace-nowrap">
                                                <span class="status-pill status-pill--approved"><span class="status-pill__dot"></span>Active</span>
                                            </td>
                                            <td class="text-right whitespace-nowrap">
                                                <p class="text-[11px] font-medium text-zinc-700">{{ $supervisor->created_at ? $supervisor->created_at->format('d M Y') : 'N/A' }}</p>
                                                <p class="text-[10px] text-zinc-400 font-mono mt-0.5">{{ $supervisor->created_at ? $supervisor->created_at->format('H:i') : '' }}</p>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="py-16 px-6 text-center">
                                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-400 mb-4">
                                                        <i data-lucide="user-x" class="w-7 h-7"></i>
                                                    </div>
                                                    <p class="text-sm font-semibold text-zinc-700">No supervisors yet</p>
                                                    <p class="text-sm text-zinc-400 mt-1">Add a supervisor to grant mobile app access.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 4: Analytics & Reports -->
            <div id="view-analytics" class="hidden flex flex-col">
                <div class="px-8 pt-6 pb-0 shrink-0">
                    <h3 class="text-base font-bold text-zinc-900">Analytics & Reports</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Quality compliance, intake trends, and supplier performance at a glance.</p>
                </div>

                <section class="px-8 pb-8 pt-5 space-y-5">
                    <!-- Key metrics -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="analytics-metric text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                                <span class="text-sm font-medium text-zinc-500">Pass rate</span>
                            </div>
                            <p class="analytics-metric__value text-emerald-700">{{ $analytics['pass_rate'] }}%</p>
                            <p class="text-sm text-zinc-400 mt-1">{{ number_format($stats['approved']) }} of {{ number_format($stats['total']) }} logs</p>
                        </div>
                        <div class="analytics-metric text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="droplets" class="w-4 h-4 text-[#0d2818]"></i>
                                <span class="text-sm font-medium text-zinc-500">Avg moisture</span>
                            </div>
                            <p class="analytics-metric__value {{ $analytics['avg_moisture'] > $settings->moisture_threshold ? 'text-amber-600' : '' }}">{{ $analytics['avg_moisture'] }}%</p>
                            <p class="text-sm text-zinc-400 mt-1">Threshold {{ number_format($settings->moisture_threshold, 1) }}%</p>
                        </div>
                        <div class="analytics-metric text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="layers" class="w-4 h-4 text-zinc-500"></i>
                                <span class="text-sm font-medium text-zinc-500">Avg F.M. / D.M.</span>
                            </div>
                            <p class="analytics-metric__value">{{ $analytics['avg_fm'] }}% <span class="text-zinc-300 font-normal">/</span> {{ $analytics['avg_dm'] }}%</p>
                            <p class="text-sm text-zinc-400 mt-1">Foreign matter & damaged</p>
                        </div>
                        <div class="analytics-metric text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                                <span class="text-sm font-medium text-zinc-500">Out of spec</span>
                            </div>
                            <p class="analytics-metric__value text-amber-600">{{ number_format($stats['out_of_spec']) }}</p>
                            <p class="text-sm text-zinc-400 mt-1">Above quality limits</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <!-- Weekly intake -->
                        <div class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-zinc-100">
                                <p class="text-sm font-semibold text-zinc-800">7-day intake</p>
                                <p class="text-sm text-zinc-400 mt-0.5">Unloading entries received per day</p>
                            </div>
                            <div class="p-5">
                                <div class="activity-bars">
                                    @foreach($weeklyActivity as $day)
                                        @php $barPct = $weeklyMax > 0 ? max(4, ($day['count'] / $weeklyMax) * 100) : 4; @endphp
                                        <div class="activity-bar-col" title="{{ $day['date'] }}: {{ $day['count'] }} entries">
                                            <div class="activity-bar-track">
                                                <div class="activity-bar-fill" style="height: {{ $barPct }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-medium text-zinc-500">{{ $day['label'] }}</span>
                                            <span class="text-sm font-semibold text-zinc-800">{{ $day['count'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Verification status -->
                        <div class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                            <div class="px-5 py-4 border-b border-zinc-100">
                                <p class="text-sm font-semibold text-zinc-800">Verification status</p>
                                <p class="text-sm text-zinc-400 mt-0.5">How logs are distributed across workflow states</p>
                            </div>
                            <div class="p-5">
                                @foreach($analytics['status_rows'] as $row)
                                    @php
                                        $pct = $stats['total'] > 0 ? round(($row['count'] / $stats['total']) * 100) : 0;
                                    @endphp
                                    <div class="status-meter-row">
                                        <span class="status-pill status-pill--{{ $row['class'] }} w-24 justify-center shrink-0"><span class="status-pill__dot"></span>{{ $row['label'] }}</span>
                                        <div class="status-meter-bar">
                                            <div class="status-meter-fill status-meter-fill--{{ $row['class'] }}" style="width: {{ max($pct, $row['count'] > 0 ? 2 : 0) }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-zinc-700 w-16 text-right shrink-0">{{ number_format($row['count']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Top suppliers -->
                    <div class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                        <div class="px-5 py-4 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-gradient-to-r from-white via-white to-emerald-50/30">
                            <div>
                                <p class="text-sm font-semibold text-zinc-800">Top suppliers by volume</p>
                                <p class="text-sm text-zinc-400 mt-0.5">Highest-traffic mandis and sourcing points</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto logs-table-scroll">
                            <table class="logs-table data-table w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="w-12">#</th>
                                        <th>Supplier / Mandi</th>
                                        <th class="text-center">Logs</th>
                                        <th class="text-center">Avg moisture</th>
                                        <th class="text-center">Issues</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-zinc-700">
                                    @forelse($topSuppliers as $index => $supplier)
                                        <tr>
                                            <td class="text-zinc-400 font-medium">{{ $index + 1 }}</td>
                                            <td>
                                                <span class="font-semibold text-zinc-800">{{ $supplier->sourced_from ?: 'Unknown' }}</span>
                                            </td>
                                            <td class="text-center font-semibold text-zinc-800">{{ number_format($supplier->total_logs) }}</td>
                                            <td class="text-center">
                                                <span class="metric-pill {{ $supplier->avg_moisture > $settings->moisture_threshold ? 'metric-pill--warn' : 'metric-pill--ok' }}">
                                                    <span class="metric-pill__dot"></span>
                                                    {{ number_format($supplier->avg_moisture, 1) }}%
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($supplier->issue_logs > 0)
                                                    <span class="status-pill status-pill--flagged"><span class="status-pill__dot"></span>{{ $supplier->issue_logs }}</span>
                                                @else
                                                    <span class="status-pill status-pill--approved"><span class="status-pill__dot"></span>None</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-sm text-zinc-400">No supplier data yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 5: Settings (Profile) -->
            <div id="view-settings" class="hidden flex flex-col">
                <div class="px-8 pt-6 pb-0 shrink-0">
                    <h3 class="text-base font-bold text-zinc-900">Profile Settings</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Update your account details and password for the HQ portal.</p>
                </div>

                <section class="px-8 pb-8 pt-5">
                    <form id="profile-settings-form" onsubmit="submitProfileSettings(event)" class="logs-table-card rounded-2xl border border-[#dee4de] bg-white overflow-hidden">
                        <div class="profile-hero px-6 py-5 border-b border-zinc-100 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-full bg-[#0d2818] text-sm font-semibold text-white flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-zinc-900 leading-tight">{{ auth()->user()->name ?? 'Manager' }}</p>
                                    <p class="text-sm text-zinc-500 mt-0.5">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                            </div>
                            <span class="status-pill status-pill--approved"><span class="status-pill__dot"></span>{{ ucfirst(auth()->user()->role ?? 'manager') }}</span>
                        </div>

                        <div id="profile-settings-errors" class="hidden mx-6 mt-5 text-sm text-red-700 bg-red-50 border border-red-100 rounded-lg px-3 py-2 whitespace-pre-line"></div>

                        <div class="profile-settings-block">
                            <p class="profile-settings-block__title">Account</p>

                            <div class="profile-row">
                                <label for="profile_name">Full name</label>
                                <input type="text" name="name" id="profile_name" required
                                    value="{{ auth()->user()->name ?? '' }}"
                                    class="px-3 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-900 focus:border-[#0d2818] focus:outline-none focus:ring-2 focus:ring-[#0d2818]/10 transition">
                            </div>

                            <div class="profile-row">
                                <label for="profile_email">Email</label>
                                <input type="email" name="email" id="profile_email" required
                                    value="{{ auth()->user()->email ?? '' }}"
                                    class="px-3 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-900 focus:border-[#0d2818] focus:outline-none focus:ring-2 focus:ring-[#0d2818]/10 transition">
                            </div>

                            <div class="profile-row profile-row--readonly">
                                <label>Access level</label>
                                <span class="profile-readonly">{{ ucfirst(auth()->user()->role ?? 'manager') }} · HQ Portal</span>
                            </div>

                            <p class="profile-settings-block__title">Security</p>

                            <div class="profile-row">
                                <label for="profile_password">New password</label>
                                <input type="password" name="password" id="profile_password" autocomplete="new-password"
                                    class="px-3 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-900 focus:border-[#0d2818] focus:outline-none focus:ring-2 focus:ring-[#0d2818]/10 transition"
                                    placeholder="Leave blank to keep current">
                            </div>

                            <div class="profile-row">
                                <label for="profile_password_confirmation">Confirm password</label>
                                <input type="password" name="password_confirmation" id="profile_password_confirmation" autocomplete="new-password"
                                    class="px-3 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-900 focus:border-[#0d2818] focus:outline-none focus:ring-2 focus:ring-[#0d2818]/10 transition"
                                    placeholder="Repeat new password">
                            </div>
                        </div>

                        <div class="border-t border-zinc-100 bg-zinc-50/80 px-6 py-4 flex justify-end gap-3">
                            <button type="submit" id="profile-settings-submit" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2.5 px-5 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                                <i data-lucide="save" class="w-3.5 h-3.5"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <!-- Slide-over Drawer Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0 pointer-events-none" onclick="closeDrawer()"></div>

    <!-- Details Overlay Drawer -->
    <div id="drawer" class="hidden fixed top-0 right-0 h-full w-full sm:w-[500px] md:w-[600px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-out border-l border-zinc-200 flex flex-col">
        <!-- Drawer Header -->
        <div class="h-16 border-b border-zinc-200 px-6 flex items-center justify-between shrink-0 bg-zinc-50">
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold tracking-tight text-zinc-800" id="drawer-entry-id">UL-000000</h3>
                    <span class="font-mono text-[10px] font-medium bg-zinc-200 px-2 py-0.5 rounded text-zinc-600" id="drawer-plate">RJ-20-XX-0000</span>
                </div>
                <span class="text-[10px] text-zinc-400 font-medium" id="drawer-date">23 May 2026, 12:00</span>
            </div>
            <button onclick="closeDrawer()" class="p-1.5 hover:bg-zinc-200/50 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Drawer Content Body -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Center & Supplier Profile -->
            <div class="bg-zinc-50 rounded-lg p-4 border border-zinc-200/50 flex flex-col gap-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Receiving Center</span>
                        <p class="text-sm font-semibold text-zinc-700 mt-0.5" id="drawer-center">Shiv Agrevo Ltd., Baran</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Purchase Mode</span>
                        <p class="text-sm font-semibold text-zinc-700 mt-0.5" id="drawer-purchase-type">Depo</p>
                    </div>
                </div>
                <div class="border-t border-zinc-200/60 my-1"></div>
                <div>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Supplier / Source Mandi</span>
                    <p class="text-sm font-semibold text-[#0d2818] mt-0.5" id="drawer-supplier">Kota Mandi / Rajasthan</p>
                </div>
            </div>

            <!-- Quality Spec Badges -->
            <div class="space-y-3">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Quality Specifications</h4>
                <div class="grid grid-cols-3 gap-3">
                    <!-- Moisture -->
                    <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg p-3 text-center">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Moisture</span>
                        <p class="text-base font-bold text-zinc-800 mt-1" id="drawer-moisture">0.0%</p>
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium" id="drawer-moisture-spec">In-Spec</span>
                    </div>
                    <!-- Foreign Matter -->
                    <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg p-3 text-center">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">F.M. (Dirt/Straw)</span>
                        <p class="text-base font-bold text-zinc-800 mt-1" id="drawer-fm">0.0%</p>
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium" id="drawer-fm-spec">In-Spec</span>
                    </div>
                    <!-- Damaged Seeds -->
                    <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg p-3 text-center">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">D.M. (Damaged)</span>
                        <p class="text-base font-bold text-zinc-800 mt-1" id="drawer-dm">0.0%</p>
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium" id="drawer-dm-spec">In-Spec</span>
                    </div>
                </div>
            </div>

            <!-- Weighbridge & Payout Calculator -->
            <div class="space-y-3" id="drawer-weighbridge-container">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Weighbridge Ticket & Deductions</h4>
                <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg overflow-hidden">
                    <div class="bg-zinc-100/75 px-4 py-2 border-b border-zinc-200/60 flex items-center justify-between text-[11px] font-semibold text-zinc-600">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="scale" class="w-3.5 h-3.5 text-zinc-400"></i> Weighment Ticket
                        </span>
                        <span class="font-mono text-zinc-400 text-[10px]" id="drawer-operator">Operator: N/A</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-4 border-b border-zinc-200/40 text-center bg-white">
                        <div>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Gross Weight</span>
                            <p class="text-sm font-bold text-zinc-700 font-mono mt-0.5" id="drawer-gross-weight">0.000 MT</p>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Tare Weight</span>
                            <p class="text-sm font-bold text-zinc-700 font-mono mt-0.5" id="drawer-tare-weight">0.000 MT</p>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Net Weight</span>
                            <p class="text-sm font-bold text-zinc-800 font-mono mt-0.5" id="drawer-net-weight">0.000 MT</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-2 bg-zinc-50/50">
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-500 font-medium">Standard Moisture Limit</span>
                            <span class="text-zinc-700 font-medium" id="drawer-moisture-limit">{{ number_format($settings->moisture_threshold, 1) }}% max</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-500 font-medium">Moisture Penalty Deduction</span>
                            <span class="font-semibold text-amber-600 font-mono" id="drawer-moisture-deduction">-0.000 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-500 font-medium">Foreign Matter Penalty Deduction</span>
                            <span class="font-semibold text-amber-600 font-mono" id="drawer-fm-deduction">-0.000 MT</span>
                        </div>
                        <div class="border-t border-dashed border-zinc-200 my-2"></div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-700 font-bold">Net Payout Weight</span>
                            <span class="text-sm font-bold text-emerald-700 font-mono" id="drawer-payout-weight">0.000 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-[9px] text-zinc-400 mt-1">
                            <span>*Deducts 1.5% weight per 1% excess moisture & 1% per 1% excess FM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Records (Photos/Video) -->
            <div class="space-y-3">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Field Captures (Photos & Videos)</h4>
                <div class="grid grid-cols-2 gap-4" id="drawer-media-gallery">
                    <!-- Media placeholders filled dynamically -->
                </div>
            </div>

            <!-- Audio Recording Note -->
            <div class="space-y-3" id="drawer-audio-container">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Voice quality recording</h4>
                <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-4 flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 text-emerald-800 border border-emerald-100 rounded-full shrink-0">
                            <i data-lucide="mic" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Field Note Note</span>
                            <p class="text-sm font-semibold text-zinc-700">Supervisor Audio Verification</p>
                        </div>
                    </div>
                    <audio id="drawer-audio-player" controls class="w-full h-9 rounded-md outline-none"></audio>
                </div>
            </div>            <!-- GPS Location -->
            <div class="space-y-3">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">GPS Audit Coordinates</h4>
                <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-zinc-700 font-mono" id="drawer-gps-coords">25.0744, 75.8372</p>
                            <span class="text-[10px] text-zinc-400" id="drawer-gps-accuracy">Accuracy: ±5.0m (Unloaded at Center)</span>
                        </div>
                    </div>
                    <a href="#" id="drawer-maps-link" target="_blank" class="px-3 py-1.5 bg-white hover:bg-zinc-100 text-zinc-700 text-sm font-semibold border border-zinc-200 rounded-md transition duration-150 flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="navigation" class="w-3.5 h-3.5"></i> Open Maps
                    </a>
                </div>
            </div>

            <!-- Verification Audit Remarks -->
            <div class="space-y-3" id="drawer-remarks-timeline">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Compliance Verification Comments</h4>
                <div class="bg-zinc-50 border border-zinc-200/60 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-zinc-200/60 text-zinc-600 rounded-full shrink-0 mt-0.5">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Manager Remark</span>
                            <p class="text-sm text-zinc-600 italic leading-relaxed" id="drawer-remarks-text">"No comments added yet."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks Confirmation Panel (Slide-up inside drawer) -->
        <div id="remarks-panel" class="border-t border-zinc-200 bg-white p-6 space-y-4 hidden shrink-0 shadow-inner">
            <div class="flex justify-between items-center">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400" id="remarks-panel-title">Confirm Action</h4>
                <button onclick="hideRemarksPanel()" class="text-zinc-400 hover:text-zinc-600 text-sm font-semibold cursor-pointer">Cancel</button>
            </div>
            <textarea id="action-remarks-input" rows="2" placeholder="Add verification remarks or justification notes..." class="w-full p-2.5 text-sm bg-zinc-50 border border-zinc-200 rounded-md focus:border-zinc-400 focus:bg-white focus:outline-none transition"></textarea>
            <button id="remarks-submit-btn" onclick="submitStatusWithRemarks()" class="w-full bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                <i data-lucide="check" class="w-3.5 h-3.5"></i> Confirm Submit
            </button>
        </div>
 
        <!-- Drawer Footer Controls -->
        <div id="drawer-actions-footer" class="p-6 border-t border-zinc-200 bg-zinc-50/50 flex gap-3 shrink-0">
            <!-- Action buttons -->
            <button onclick="promptRemarks('approved')" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-emerald-700/10">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Approve Log
            </button>
            <button onclick="promptRemarks('flagged')" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-amber-500/10">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> Flag Quality
            </button>
            <button onclick="promptRemarks('rejected')" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-red-600/10">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Reject
            </button>
        </div>
    </div>

    <!-- Add Procurement Center Modal -->
    <div id="add-center-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col gap-4 text-left">
            <div class="flex justify-between items-center border-b border-zinc-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-emerald-50 text-[#0d2818] border border-emerald-100/50 rounded-lg">
                        <i data-lucide="git-branch" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Add Procurement Center</h3>
                        <p class="text-[10px] text-zinc-400 font-medium">Create a new crushing unit or receiving depot.</p>
                    </div>
                </div>
                <button onclick="closeAddCenterModal()" class="p-1.5 hover:bg-zinc-100 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form id="add-center-form" onsubmit="submitAddCenter(event)" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Center Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Shiv Agrevo Depot, Baran" 
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Center Code *</label>
                    <input type="text" name="code" required placeholder="e.g. DEPO-BARAN" 
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="e.g. 25.0744" 
                            class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="e.g. 75.8372" 
                            class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    </div>
                </div>

                <!-- Error message container -->
                <div id="add-center-errors" class="hidden text-red-600 bg-red-50 border border-red-100 rounded-lg p-3 text-sm whitespace-pre-line"></div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAddCenterModal()" class="flex-1 px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-sm font-semibold rounded-lg transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                        Save Center
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Supervisor Modal -->
    <div id="add-supervisor-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col gap-4 text-left">
            <div class="flex justify-between items-center border-b border-zinc-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-blue-50 text-blue-800 border border-blue-100 rounded-lg">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Add Supervisor</h3>
                        <p class="text-[10px] text-zinc-400 font-medium">Create a supervisor account for mobile app login.</p>
                    </div>
                </div>
                <button onclick="closeAddSupervisorModal()" class="p-1.5 hover:bg-zinc-100 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form id="add-supervisor-form" onsubmit="submitAddSupervisor(event)" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Supervisor Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Ramesh Kumar" 
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Phone Number *</label>
                    <input type="tel" name="phone" required placeholder="e.g. 9876543210"
                        maxlength="10" inputmode="numeric"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    <span class="text-[9px] text-zinc-400">Must be exactly 10 digits (used for logging into mobile app).</span>
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">4-Digit Security PIN *</label>
                    <input type="password" name="pin" required placeholder="e.g. 1234"
                        maxlength="4" inputmode="numeric"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)"
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    <span class="text-[9px] text-zinc-400">Must be exactly 4 digits.</span>
                </div>

                <!-- Error message container -->
                <div id="add-supervisor-errors" class="hidden text-red-600 bg-red-50 border border-red-100 rounded-lg p-3 text-sm whitespace-pre-line"></div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAddSupervisorModal()" class="flex-1 px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-sm font-semibold rounded-lg transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                        Save Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Drawer Controller Script -->
    <script>
        let currentEntryId = null;
        let procurementSettings = @json($settings->toThresholdArray());

        function getThresholds() {
            return procurementSettings;
        }

        function applySpecBadge(value, threshold, valueEl, specEl) {
            valueEl.innerText = value.toFixed(1) + '%';
            if (value > threshold) {
                specEl.innerText = 'Out of Spec (>' + threshold.toFixed(1) + '%)';
                specEl.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-amber-50 text-amber-700 border border-amber-200';
            } else {
                specEl.innerText = 'In-Spec (≤' + threshold.toFixed(1) + '%)';
                specEl.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
            }
        }

        function parseValidationErrors(resData) {
            if (resData.errors) {
                return Object.values(resData.errors).flat().join('\n');
            }
            return resData.message || 'An error occurred.';
        }

        // Auto initialize lucide icons
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();

            @if(request('tab') === 'logs' || request()->anyFilled(['search', 'unit_id', 'status', 'date_filter']))
                switchTab('logs');
            @elseif(request('tab') === 'settings')
                switchTab('settings');
            @endif

            // Row click listener
            document.querySelectorAll('.select-row').forEach(row => {
                row.addEventListener('click', function() {
                    const data = JSON.parse(this.dataset.json);
                    openDrawer(data, this);
                });
            });

            // Start polling stats every 5 seconds to keep cards dynamic
            setInterval(updateStatsWidgets, 5000);
        });

        function statusPillHtml(status) {
            const labels = { approved: 'Approved', flagged: 'Flagged', rejected: 'Rejected', pending: 'Pending' };
            const modifier = ['approved', 'flagged', 'rejected'].includes(status) ? status : 'pending';
            const label = labels[modifier] || 'Pending';
            return `<span class="status-pill status-pill--${modifier}"><span class="status-pill__dot"></span>${label}</span>`;
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-2.5 px-4 py-3 text-sm font-medium rounded-lg shadow-lg border pointer-events-auto transition duration-300 transform translate-y-2 opacity-0 bg-white ${
                type === 'success' ? 'text-emerald-800 border-emerald-100 bg-emerald-50/50' : 
                type === 'error' ? 'text-red-800 border-red-100 bg-red-50/50' : 'text-zinc-700 border-zinc-200'
            }`;

            const iconName = type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info';
            toast.innerHTML = `<i data-lucide="${iconName}" class="w-4 h-4 shrink-0"></i> <span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();

            // Trigger reflow & slide-in
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            // Auto dismiss
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function openDrawer(entry, rowElement) {
            currentEntryId = entry.id;

            // Reset remarks slide-up panel on open
            hideRemarksPanel();

            // Highlight selected row using the premium CSS class
            document.querySelectorAll('.select-row').forEach(r => r.classList.remove('selected'));
            rowElement.classList.add('selected');

            // Text contents
            document.getElementById('drawer-entry-id').innerText = entry.id;
            document.getElementById('drawer-plate').innerText = entry.truck_no;
            
            const dateObj = new Date(entry.created_at);
            document.getElementById('drawer-date').innerText = dateObj.toLocaleDateString('en-GB', {
                day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            document.getElementById('drawer-center').innerText = entry.unit ? entry.unit.name : 'N/A';
            document.getElementById('drawer-purchase-type').innerText = entry.purchase_type || 'Direct';
            document.getElementById('drawer-supplier').innerText = entry.sourced_from || 'Spot Buyer';

            const thresholds = getThresholds();
            const moisture = parseFloat(entry.moisture);
            const fm = parseFloat(entry.fm);
            const dm = parseFloat(entry.dm);

            applySpecBadge(moisture, thresholds.moisture, document.getElementById('drawer-moisture'), document.getElementById('drawer-moisture-spec'));
            applySpecBadge(fm, thresholds.fm, document.getElementById('drawer-fm'), document.getElementById('drawer-fm-spec'));
            applySpecBadge(dm, thresholds.dm, document.getElementById('drawer-dm'), document.getElementById('drawer-dm-spec'));

            const moistureLimitEl = document.getElementById('drawer-moisture-limit');
            if (moistureLimitEl) {
                moistureLimitEl.innerText = thresholds.moisture.toFixed(1) + '% max';
            }

            // Weighbridge & deductions calculator
            const gross = parseFloat(entry.gross_weight) || 0;
            const tare = parseFloat(entry.tare_weight) || 0;
            const net = parseFloat(entry.net_weight) || (gross - tare);
            
            document.getElementById('drawer-operator').innerText = 'Operator: ' + (entry.operator_name || 'N/A');
            document.getElementById('drawer-gross-weight').innerText = gross.toFixed(3) + ' MT';
            document.getElementById('drawer-tare-weight').innerText = tare.toFixed(3) + ' MT';
            document.getElementById('drawer-net-weight').innerText = net.toFixed(3) + ' MT';

            let mDeduct = 0;
            if (moisture > thresholds.moisture) {
                mDeduct = (moisture - thresholds.moisture) * 0.015 * net;
            }
            document.getElementById('drawer-moisture-deduction').innerText = '-' + mDeduct.toFixed(3) + ' MT';

            let fmDeduct = 0;
            if (fm > thresholds.fm) {
                fmDeduct = (fm - thresholds.fm) * 0.01 * net;
            }
            document.getElementById('drawer-fm-deduction').innerText = '-' + fmDeduct.toFixed(3) + ' MT';

            const netPayoutWeight = net - mDeduct - fmDeduct;
            document.getElementById('drawer-payout-weight').innerText = netPayoutWeight.toFixed(3) + ' MT';

            // Review comments timeline
            const remarkText = entry.remarks ? `"${entry.remarks}"` : '"No comments added yet."';
            document.getElementById('drawer-remarks-text').innerText = remarkText;

            // GPS
            if (entry.latitude && entry.longitude) {
                document.getElementById('drawer-gps-coords').innerText = `${entry.latitude}, ${entry.longitude}`;
                document.getElementById('drawer-gps-accuracy').innerText = `Accuracy: ±${entry.gps_accuracy || 5}m (Unloaded at Center)`;
                document.getElementById('drawer-maps-link').href = `https://www.google.com/maps/search/?api=1&query=${entry.latitude},${entry.longitude}`;
                document.getElementById('drawer-maps-link').style.display = 'flex';
            } else {
                document.getElementById('drawer-gps-coords').innerText = 'No GPS Captured';
                document.getElementById('drawer-gps-accuracy').innerText = 'Location disabled on device';
                document.getElementById('drawer-maps-link').style.display = 'none';
            }

            // Media Gallery and Audio Player
            const gallery = document.getElementById('drawer-media-gallery');
            gallery.innerHTML = '';
            
            let audioLog = null;
            
            if (entry.media_logs && entry.media_logs.length > 0) {
                entry.media_logs.forEach(media => {
                    if (media.type === 'audio') {
                        audioLog = media;
                    } else {
                        const caption = media.caption || (media.type === 'truck' ? 'Weighbridge capture' : 'Material quality capture');
                        const cardHtml = `
                            <div class="flex flex-col gap-1.5 bg-zinc-50 border border-zinc-200/50 rounded-lg p-2.5">
                                <div class="aspect-video w-full rounded-md overflow-hidden bg-zinc-100 border border-zinc-200/30">
                                    <img src="${media.file_path}" alt="${media.type}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                </div>
                                <span class="text-[9px] text-zinc-400 font-medium">${caption}</span>
                            </div>
                        `;
                        gallery.innerHTML += cardHtml;
                    }
                });
            }

            // Audio Player configuration
            const audioContainer = document.getElementById('drawer-audio-container');
            const audioPlayer = document.getElementById('drawer-audio-player');
            if (audioLog) {
                audioContainer.style.display = 'block';
                audioPlayer.src = audioLog.file_path;
            } else {
                audioContainer.style.display = 'none';
                audioPlayer.src = '';
            }

            // Show Drawer and Backdrop
            const drawer = document.getElementById('drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            drawer.classList.remove('hidden');
            backdrop.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                drawer.classList.remove('translate-x-full');
            }, 50);
        }

        function closeDrawer() {
            const drawer = document.getElementById('drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            drawer.classList.add('translate-x-full');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            
            // Remove highlighted row using the premium CSS class
            document.querySelectorAll('.select-row').forEach(r => r.classList.remove('selected'));
            
            // Stop audio if playing
            document.getElementById('drawer-audio-player').pause();

            setTimeout(() => {
                backdrop.classList.add('hidden');
                drawer.classList.add('hidden');
            }, 300);
        }

        let pendingStatusTarget = null;

        function promptRemarks(status) {
            pendingStatusTarget = status;
            
            const submitBtn = document.getElementById('remarks-submit-btn');
            const panelTitle = document.getElementById('remarks-panel-title');
            
            if (status === 'approved') {
                panelTitle.innerText = "Confirm Approve Log";
                submitBtn.className = "w-full bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
            } else if (status === 'flagged') {
                panelTitle.innerText = "Confirm Flag Quality";
                submitBtn.className = "w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
            } else if (status === 'rejected') {
                panelTitle.innerText = "Confirm Reject Log";
                submitBtn.className = "w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
            }

            document.getElementById('drawer-actions-footer').classList.add('hidden');
            document.getElementById('remarks-panel').classList.remove('hidden');
            
            document.getElementById('action-remarks-input').value = "";
            document.getElementById('action-remarks-input').focus();
        }

        function hideRemarksPanel() {
            document.getElementById('remarks-panel').classList.add('hidden');
            document.getElementById('drawer-actions-footer').classList.remove('hidden');
            pendingStatusTarget = null;
        }

        function submitStatusWithRemarks() {
            if (!pendingStatusTarget) return;
            const remarks = document.getElementById('action-remarks-input').value;
            updateEntryStatus(pendingStatusTarget, remarks);
            hideRemarksPanel();
        }

        function updateEntryStatus(status, remarks = '') {
            if (!currentEntryId) return;

            const url = `/admin/entries/${currentEntryId}/status`;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Apply loading state on body
            document.body.classList.add('pointer-events-none');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ status: status, remarks: remarks })
            })
            .then(res => res.json())
            .then(data => {
                document.body.classList.remove('pointer-events-none');
                
                if (data.success) {
                    showToast(`Entry status updated to ${status}.`, 'success');
                    
                    // Update table status cell dynamically
                    const row = document.querySelector(`tr[data-id="${currentEntryId}"]`);
                    if (row) {
                        const statusCell = row.querySelector('.row-status-cell');
                        statusCell.innerHTML = statusPillHtml(status);

                        // Update local dataset JSON
                        const datasetJson = JSON.parse(row.dataset.json);
                        datasetJson.status = status;
                        datasetJson.remarks = remarks; // Update remarks locally
                        row.dataset.json = JSON.stringify(datasetJson);
                    }

                    // Request update for stats counters
                    updateStatsWidgets();
                    
                    // Close the drawer
                    closeDrawer();
                } else {
                    showToast('Failed to update status.', 'error');
                }
            })
            .catch(err => {
                document.body.classList.remove('pointer-events-none');
                showToast('Network error while updating status.', 'error');
                console.error(err);
            });
        }

        function updateTrendBadge(badgeId, svgId, trendStr, isNegativeGood = false) {
            const badge = document.getElementById(badgeId);
            const svg = document.getElementById(svgId);
            if (!badge) return;

            const isNegative = trendStr.startsWith('-');
            const isZero = trendStr === '0%';
            
            let isGood = false;
            if (isZero) {
                isGood = true;
            } else if (isNegativeGood) {
                isGood = isNegative;
            } else {
                isGood = !isNegative;
            }

            const iconName = isNegative ? 'trending-down' : 'trending-up';

            if (isGood) {
                badge.className = "inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100";
                if (svg) {
                    svg.setAttribute('class', 'w-12 h-6 text-emerald-500');
                }
            } else {
                badge.className = "inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100";
                if (svg) {
                    svg.setAttribute('class', 'w-12 h-6 text-rose-500');
                }
            }

            badge.innerHTML = `<i data-lucide="${iconName}" class="w-2.5 h-2.5"></i> ${trendStr}`;
        }

        function updateStatsWidgets() {
            fetch('/admin/stats')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.stats;

                        // Update counts
                        if (document.getElementById('stat-total')) {
                            document.getElementById('stat-total').innerText = Number(stats.total).toLocaleString();
                        }
                        if (document.getElementById('stat-pending')) {
                            document.getElementById('stat-pending').innerText = Number(stats.pending).toLocaleString();
                        }
                        if (document.getElementById('stat-out-of-spec')) {
                            document.getElementById('stat-out-of-spec').innerText = Number(stats.out_of_spec).toLocaleString();
                        }
                        if (document.getElementById('stat-approved')) {
                            document.getElementById('stat-approved').innerText = Number(stats.approved).toLocaleString();
                        }

                        // Update sparkline paths
                        if (document.getElementById('path-total-sparkline')) {
                            document.getElementById('path-total-sparkline').setAttribute('d', stats.total_sparkline);
                        }
                        if (document.getElementById('path-pending-sparkline')) {
                            document.getElementById('path-pending-sparkline').setAttribute('d', stats.pending_sparkline);
                        }
                        if (document.getElementById('path-out-of-spec-sparkline')) {
                            document.getElementById('path-out-of-spec-sparkline').setAttribute('d', stats.out_of_spec_sparkline);
                        }
                        if (document.getElementById('path-approved-sparkline')) {
                            document.getElementById('path-approved-sparkline').setAttribute('d', stats.approved_sparkline);
                        }

                        // Update trend badges
                        updateTrendBadge('badge-total-trend', 'svg-total-sparkline', stats.total_trend, false);
                        updateTrendBadge('badge-pending-trend', 'svg-pending-sparkline', stats.pending_trend, true);
                        updateTrendBadge('badge-out-of-spec-trend', 'svg-out-of-spec-sparkline', stats.out_of_spec_trend, true);
                        updateTrendBadge('badge-approved-trend', 'svg-approved-sparkline', stats.approved_trend, false);

                        // Reinitialize lucide icons for newly injected markup
                        lucide.createIcons();
                    }
                })
                .catch(err => console.error('Error fetching stats:', err));
        }

        function submitProfileSettings(event) {
            event.preventDefault();
            const form = document.getElementById('profile-settings-form');
            const errorsDiv = document.getElementById('profile-settings-errors');
            const submitBtn = document.getElementById('profile-settings-submit');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const data = {
                name: form.name.value.trim(),
                email: form.email.value.trim(),
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
            };

            if (!data.password) {
                delete data.password;
                delete data.password_confirmation;
            }

            errorsDiv.classList.add('hidden');
            errorsDiv.innerText = '';
            submitBtn.disabled = true;

            fetch('{{ route('admin.settings.profile') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(async res => {
                const resData = await res.json();
                if (!res.ok) {
                    throw new Error(parseValidationErrors(resData));
                }
                return resData;
            })
            .then(resData => {
                if (resData.success) {
                    showToast('Profile updated successfully.', 'success');
                    form.password.value = '';
                    form.password_confirmation.value = '';
                    setTimeout(() => {
                        window.location.href = '{{ route('admin.dashboard') }}?tab=settings';
                    }, 1200);
                }
            })
            .catch(err => {
                errorsDiv.innerText = err.message || 'Network error. Please try again.';
                errorsDiv.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.disabled = false;
            });
        }

        function switchTab(tabId) {
            // Hide all tabs
            document.getElementById('view-dashboard').classList.add('hidden');
            document.getElementById('view-logs').classList.add('hidden');
            document.getElementById('view-units').classList.add('hidden');
            document.getElementById('view-supervisors').classList.add('hidden');
            document.getElementById('view-analytics').classList.add('hidden');
            document.getElementById('view-settings').classList.add('hidden');

            // Show target tab
            document.getElementById('view-' + tabId).classList.remove('hidden');

            // Toggle active classes on nav buttons
            const navs = ['dashboard', 'logs', 'units', 'supervisors', 'analytics', 'settings'];
            navs.forEach(id => {
                const btn = document.getElementById('nav-' + id);
                if (!btn) return;
                const icon = btn.querySelector('[data-lucide]');
                if (id === tabId) {
                    btn.className = "w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-[#0d2818] to-[#143d24] text-white border border-[#0d2818] transition duration-200 cursor-pointer text-left shadow-md shadow-[#0d2818]/15 translate-x-0.5";
                    if (icon) {
                        icon.classList.remove('text-zinc-400');
                        icon.classList.add('text-emerald-200');
                    }
                } else {
                    btn.className = "w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-white hover:border-zinc-200/60 hover:shadow-xs hover:translate-x-0.5 transition duration-200 border border-transparent cursor-pointer text-left";
                    if (icon) {
                        icon.classList.remove('text-emerald-200');
                        icon.classList.add('text-zinc-400');
                    }
                }
            });

            const url = new URL(window.location);
            if (tabId === 'dashboard') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tabId);
            }
            window.history.replaceState({}, '', url);
        }

        // Add Center Modal helpers
        function openAddCenterModal() {
            const modal = document.getElementById('add-center-modal');
            modal.classList.remove('hidden');
            document.getElementById('add-center-form').reset();
            document.getElementById('add-center-errors').classList.add('hidden');
            document.getElementById('add-center-errors').innerText = '';
            
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
        }

        function closeAddCenterModal() {
            const modal = document.getElementById('add-center-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function submitAddCenter(event) {
            event.preventDefault();
            const form = document.getElementById('add-center-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const errorsDiv = document.getElementById('add-center-errors');
            
            errorsDiv.classList.add('hidden');
            errorsDiv.innerText = '';
            
            fetch('/admin/units', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            })
            .then(async res => {
                const resData = await res.json();
                if (res.status === 422) {
                    let errorMsg = '';
                    if (resData.errors) {
                        errorMsg = Object.values(resData.errors).flat().join('\n');
                    } else {
                        errorMsg = resData.message;
                    }
                    throw new Error(errorMsg);
                }
                if (!res.ok) {
                    throw new Error(resData.message || 'An error occurred.');
                }
                return resData;
            })
            .then(resData => {
                if (resData.success) {
                    showToast('Procurement center added successfully!', 'success');
                    closeAddCenterModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    errorsDiv.innerText = resData.message || 'An error occurred.';
                    errorsDiv.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                errorsDiv.innerText = err.message || 'Network error. Please try again.';
                errorsDiv.classList.remove('hidden');
            });
        }

        // Add Supervisor Modal helpers
        function openAddSupervisorModal() {
            const modal = document.getElementById('add-supervisor-modal');
            modal.classList.remove('hidden');
            document.getElementById('add-supervisor-form').reset();
            document.getElementById('add-supervisor-errors').classList.add('hidden');
            document.getElementById('add-supervisor-errors').innerText = '';
            
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
            }, 10);
        }

        function closeAddSupervisorModal() {
            const modal = document.getElementById('add-supervisor-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function submitAddSupervisor(event) {
            event.preventDefault();
            const form = document.getElementById('add-supervisor-form');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const errorsDiv = document.getElementById('add-supervisor-errors');
            
            errorsDiv.classList.add('hidden');
            errorsDiv.innerText = '';

            // Client-side validation
            if (!/^\d{10}$/.test(data.phone)) {
                errorsDiv.innerText = 'Phone number must be exactly 10 digits (numbers only).';
                errorsDiv.classList.remove('hidden');
                return;
            }
            if (!/^\d{4}$/.test(data.pin)) {
                errorsDiv.innerText = 'PIN must be exactly 4 digits (numbers only).';
                errorsDiv.classList.remove('hidden');
                return;
            }
            
            fetch('/admin/supervisors', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            })
            .then(async res => {
                const resData = await res.json();
                if (res.status === 422) {
                    let errorMsg = '';
                    if (resData.errors) {
                        errorMsg = Object.values(resData.errors).flat().join('\n');
                    } else {
                        errorMsg = resData.message;
                    }
                    throw new Error(errorMsg);
                }
                if (!res.ok) {
                    throw new Error(resData.message || 'An error occurred.');
                }
                return resData;
            })
            .then(resData => {
                if (resData.success) {
                    showToast('Supervisor added successfully!', 'success');
                    closeAddSupervisorModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    errorsDiv.innerText = resData.message || 'An error occurred.';
                    errorsDiv.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                errorsDiv.innerText = err.message || 'Network error. Please try again.';
                errorsDiv.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>
