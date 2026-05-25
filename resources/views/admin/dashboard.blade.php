<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shiv Edibles Ltd. — Procurement Admin</title>
    
    <!-- Instrument Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
    </style>
</head>
<body class="bg-zinc-50/50 text-zinc-900 h-screen w-screen overflow-hidden flex">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-white border-r border-zinc-200 flex flex-col shrink-0">
        <!-- Logo Header -->
        <div class="h-20 border-b border-zinc-200 px-6 flex items-center">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-6 bg-[#0d2818] rounded-full"></span>
                <div>
                    <h1 class="text-xs font-bold uppercase tracking-widest text-[#0d2818] leading-none">SHIV EDIBLES</h1>
                    <span class="text-[10px] text-zinc-400 font-medium">Procurement Portal</span>
                </div>
            </div>
        </div>
        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold bg-emerald-50/60 text-[#0d2818] border border-emerald-100/50 transition cursor-pointer text-left">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#0d2818]"></i>
                Dashboard
            </button>
            
            <button onclick="switchTab('units')" id="nav-units" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 transition border border-transparent cursor-pointer text-left">
                <i data-lucide="git-branch" class="w-4 h-4 text-zinc-400"></i>
                Procurement Centers
            </button>

            <button onclick="switchTab('supervisors')" id="nav-supervisors" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 transition border border-transparent cursor-pointer text-left">
                <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                Supervisors List
            </button>

            <button onclick="switchTab('analytics')" id="nav-analytics" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 transition border border-transparent cursor-pointer text-left">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-zinc-400"></i>
                Analytics & Reports
            </button>

            <button onclick="switchTab('settings')" id="nav-settings" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 transition border border-transparent cursor-pointer text-left">
                <i data-lucide="settings" class="w-4 h-4 text-zinc-400"></i>
                Settings
            </button>
        </nav>

        <!-- Sidebar footer branding/version -->
        <div class="p-6 border-t border-zinc-100 text-center">
            <span class="text-[10px] text-zinc-400 font-medium">Shiv Procurement v1.1</span>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden">
        @php
            $hour = date('H');
            $greeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
        @endphp
        
        <!-- Header / Stats Summary -->
        <header class="h-20 bg-white border-b border-zinc-200 px-8 flex items-center justify-between shrink-0">
            <div>
                <h2 class="text-sm font-bold tracking-tight text-zinc-900 font-sans">Good {{ $greeting }}, {{ auth()->user()->name ?? 'Manager' }}</h2>
                <p class="text-[11px] text-zinc-500 mt-0.5 font-medium">Here's what's happening at Shiv Edibles today.</p>
            </div>
            <div class="flex items-center gap-4">
                <!-- Mini Stats Widget -->
                <div class="hidden xl:flex items-center gap-6 text-xs text-zinc-400 pr-4 border-r border-zinc-150">
                    <div>
                        <span class="font-semibold text-zinc-700" id="stat-total-badge">{{ $stats['total'] }}</span> logs
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="font-semibold text-zinc-700" id="stat-pending-badge">{{ $stats['pending'] }}</span> pending
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-semibold text-zinc-700" id="stat-approved-badge">{{ $stats['approved'] }}</span> approved
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="font-semibold text-zinc-700" id="stat-flagged-badge">{{ $stats['flagged'] }}</span> flagged
                    </div>
                </div>

                <!-- Date display -->
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-zinc-500 bg-zinc-50 border border-zinc-200/60 px-3 py-1.5 rounded-lg">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-zinc-400"></i>
                    <span>Today: <span class="font-semibold text-zinc-700">{{ now()->format('d M, Y') }}</span></span>
                </div>

                <!-- User Profile & Log Out -->
                <div class="flex items-center gap-3 pl-1">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-800 font-semibold text-xs tracking-wider">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-semibold text-zinc-800 leading-none">{{ auth()->user()->name ?? 'Manager' }}</span>
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
                <button onclick="window.location.reload()" class="p-2 hover:bg-zinc-50 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </header>

        <!-- Scrollable Workspace Container -->
        <div class="flex-1 overflow-y-auto flex flex-col bg-zinc-50/50">
            
            <!-- Tab 1: Dashboard Feed -->
            <div id="view-dashboard" class="flex flex-col">
                <!-- Stats Grid Block -->
                <section class="p-8 pb-0 grid grid-cols-1 md:grid-cols-4 gap-4 shrink-0">
                    <!-- Stat 1: Total Logs -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 flex items-center justify-between shadow-sm relative overflow-hidden">
                        <div class="relative z-10 text-left">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">Total Entries</span>
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight mt-1" id="stat-total">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="p-3 bg-zinc-50 border border-zinc-100 rounded-lg text-zinc-500 relative z-10">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <!-- Sophisticated Dotted Grid Pattern -->
                        <div class="absolute inset-0 pointer-events-none opacity-[0.25]">
                            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="dotGridTotal" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <circle cx="1.5" cy="1.5" r="0.75" fill="#71717a" />
                                    </pattern>
                                    <linearGradient id="fadeTotal" x1="0" y1="1" x2="0.8" y2="0.2">
                                        <stop offset="0%" stop-color="white" stop-opacity="1" />
                                        <stop offset="50%" stop-color="white" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="white" stop-opacity="0" />
                                    </linearGradient>
                                    <mask id="maskTotal">
                                        <rect width="100%" height="100%" fill="url(#fadeTotal)" />
                                    </mask>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#dotGridTotal)" mask="url(#maskTotal)" />
                            </svg>
                        </div>
                    </div>
                    <!-- Stat 2: Pending Approval -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 flex items-center justify-between shadow-sm relative overflow-hidden">
                        <div class="relative z-10 text-left">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">Pending Verify</span>
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight mt-1 text-blue-600" id="stat-pending">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 border border-blue-100/55 rounded-lg text-blue-600 relative z-10">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <!-- Sophisticated Dotted Grid Pattern -->
                        <div class="absolute inset-0 pointer-events-none opacity-[0.25]">
                            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="dotGridPending" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <circle cx="1.5" cy="1.5" r="0.75" fill="#3b82f6" />
                                    </pattern>
                                    <linearGradient id="fadePending" x1="0" y1="1" x2="0.8" y2="0.2">
                                        <stop offset="0%" stop-color="white" stop-opacity="1" />
                                        <stop offset="50%" stop-color="white" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="white" stop-opacity="0" />
                                    </linearGradient>
                                    <mask id="maskPending">
                                        <rect width="100%" height="100%" fill="url(#fadePending)" />
                                    </mask>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#dotGridPending)" mask="url(#maskPending)" />
                            </svg>
                        </div>
                    </div>
                    <!-- Stat 3: Quality Outliers -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 flex items-center justify-between shadow-sm relative overflow-hidden">
                        <div class="relative z-10 text-left">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">Quality Outliers</span>
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight mt-1 text-amber-600" id="stat-out-of-spec">{{ $stats['out_of_spec'] }}</h3>
                        </div>
                        <div class="p-3 bg-amber-50 border border-amber-100/55 rounded-lg text-amber-600 relative z-10">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <!-- Sophisticated Dotted Grid Pattern -->
                        <div class="absolute inset-0 pointer-events-none opacity-[0.25]">
                            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="dotGridOutliers" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <circle cx="1.5" cy="1.5" r="0.75" fill="#f59e0b" />
                                    </pattern>
                                    <linearGradient id="fadeOutliers" x1="0" y1="1" x2="0.8" y2="0.2">
                                        <stop offset="0%" stop-color="white" stop-opacity="1" />
                                        <stop offset="50%" stop-color="white" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="white" stop-opacity="0" />
                                    </linearGradient>
                                    <mask id="maskOutliers">
                                        <rect width="100%" height="100%" fill="url(#fadeOutliers)" />
                                    </mask>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#dotGridOutliers)" mask="url(#maskOutliers)" />
                            </svg>
                        </div>
                    </div>
                    <!-- Stat 4: Approved Logs -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 flex items-center justify-between shadow-sm relative overflow-hidden">
                        <div class="relative z-10 text-left">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-zinc-400">Approved Logs</span>
                            <h3 class="text-2xl font-bold text-zinc-800 tracking-tight mt-1 text-emerald-600" id="stat-approved">{{ $stats['approved'] }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 border border-emerald-100/55 rounded-lg text-emerald-600 relative z-10">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <!-- Sophisticated Dotted Grid Pattern -->
                        <div class="absolute inset-0 pointer-events-none opacity-[0.25]">
                            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="dotGridApproved" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <circle cx="1.5" cy="1.5" r="0.75" fill="#10b981" />
                                    </pattern>
                                    <linearGradient id="fadeApproved" x1="0" y1="1" x2="0.8" y2="0.2">
                                        <stop offset="0%" stop-color="white" stop-opacity="1" />
                                        <stop offset="50%" stop-color="white" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="white" stop-opacity="0" />
                                    </linearGradient>
                                    <mask id="maskApproved">
                                        <rect width="100%" height="100%" fill="url(#fadeApproved)" />
                                    </mask>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#dotGridApproved)" mask="url(#maskApproved)" />
                            </svg>
                        </div>
                    </div>
                </section>

                <!-- Horizontal Filter Bar (In-page) -->
                <section class="px-8 pt-6 pb-0 shrink-0">
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Left: Search and Dropdowns -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- Search -->
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                </span>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                    placeholder="Search Truck ID, Supplier..." 
                                    class="w-full pl-9 pr-4 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                            </div>

                            <!-- Procurement Center -->
                            <div>
                                <select name="unit_id" id="unit_id" 
                                    class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
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
                                    class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
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
                                    class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ request('date_filter') === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                                    <option value="month" {{ request('date_filter') === 'month' ? 'selected' : '' }}>Last 30 Days</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right: Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0 self-end lg:self-auto">
                            <button type="submit" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-xs font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                                <i data-lucide="filter" class="w-3.5 h-3.5"></i> Apply Filters
                            </button>
                            @if(request()->anyFilled(['search', 'unit_id', 'status', 'date_filter']))
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-medium rounded-lg transition duration-150 flex items-center justify-center cursor-pointer">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </section>

                <!-- Table & Leaderboard Layout -->
                <section class="flex-1 p-8 grid grid-cols-1 lg:grid-cols-4 gap-6 min-h-0">
                    <!-- Left: Log Table (Col Span 3) -->
                    <div class="lg:col-span-3 flex flex-col h-full min-h-0">
                        <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                            <!-- Table Wrapper with overflow-y-auto -->
                            <div class="flex-1 overflow-y-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold uppercase tracking-wider text-zinc-400 sticky top-0 z-10">
                                            <th class="py-3 px-6">Entry ID</th>
                                            <th class="py-3 px-6">Vehicle Plate</th>
                                            <th class="py-3 px-6">Procurement Center</th>
                                            <th class="py-3 px-6">Sourced From / Type</th>
                                            <th class="py-3 px-6 text-center">Moisture</th>
                                            <th class="py-3 px-6 text-center">F.M.</th>
                                            <th class="py-3 px-6 text-center">D.M.</th>
                                            <th class="py-3 px-6 text-center">Status</th>
                                            <th class="py-3 px-6 text-right">Received At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 text-xs">
                                        @forelse($entries as $entry)
                                            <tr class="hover:bg-zinc-50/70 transition cursor-pointer select-row border-l-2 border-transparent" 
                                                data-id="{{ $entry->id }}"
                                                data-json="{{ json_encode($entry) }}">
                                                <!-- ID -->
                                                <td class="py-3.5 px-6 font-semibold text-[#0d2818]">
                                                    {{ $entry->id }}
                                                </td>
                                                <!-- Plate -->
                                                <td class="py-3.5 px-6">
                                                    <div class="flex items-center gap-1.5">
                                                        <i data-lucide="truck" class="w-3.5 h-3.5 text-zinc-400"></i>
                                                        <span class="font-mono tracking-tight font-medium bg-zinc-100 px-2 py-0.5 rounded text-zinc-700 text-[11px] border border-zinc-200/50">{{ $entry->truck_no }}</span>
                                                    </div>
                                                </td>
                                                <!-- Center -->
                                                <td class="py-3.5 px-6 text-zinc-650">
                                                    {{ $entry->unit->name ?? 'N/A' }}
                                                </td>
                                                <!-- Source -->
                                                <td class="py-3.5 px-6">
                                                    <div class="flex flex-col text-left">
                                                        <span class="font-semibold text-zinc-750">{{ $entry->sourced_from ?? 'Spot Buyer' }}</span>
                                                        <span class="text-[10px] text-zinc-400 mt-0.5">{{ $entry->purchase_type ?? 'Direct' }}</span>
                                                    </div>
                                                </td>
                                                <!-- Moisture -->
                                                <td class="py-3.5 px-6 text-center">
                                                    <span class="inline-flex items-center gap-1 font-semibold {{ $entry->moisture > 10.0 ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-emerald-700 bg-emerald-50 border border-emerald-100' }} px-2 py-0.5 rounded text-[11px]">
                                                        <span class="w-1 h-1 rounded-full {{ $entry->moisture > 10.0 ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                                                        {{ number_format($entry->moisture, 1) }}%
                                                    </span>
                                                </td>
                                                <!-- FM -->
                                                <td class="py-3.5 px-6 text-center">
                                                    <span class="inline-flex items-center gap-1 font-semibold {{ $entry->fm > 2.0 ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-emerald-700 bg-emerald-50 border border-emerald-100' }} px-2 py-0.5 rounded text-[11px]">
                                                        <span class="w-1 h-1 rounded-full {{ $entry->fm > 2.0 ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                                                        {{ number_format($entry->fm, 1) }}%
                                                    </span>
                                                </td>
                                                <!-- DM -->
                                                <td class="py-3.5 px-6 text-center">
                                                    <span class="inline-flex items-center gap-1 font-semibold {{ $entry->dm > 2.0 ? 'text-amber-700 bg-amber-50 border border-amber-200' : 'text-emerald-700 bg-emerald-50 border border-emerald-100' }} px-2 py-0.5 rounded text-[11px]">
                                                        <span class="w-1 h-1 rounded-full {{ $entry->dm > 2.0 ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                                                        {{ number_format($entry->dm, 1) }}%
                                                    </span>
                                                </td>
                                                <!-- Status -->
                                                <td class="py-3.5 px-6 text-center row-status-cell">
                                                    @if($entry->status === 'approved')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            Approved
                                                        </span>
                                                    @elseif($entry->status === 'flagged')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                            Flagged
                                                        </span>
                                                    @elseif($entry->status === 'rejected')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                            Rejected
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <!-- Received At -->
                                                <td class="py-3.5 px-6 text-right text-zinc-500 font-mono text-[11px]">
                                                    {{ $entry->created_at ? $entry->created_at->format('d M, H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="py-12 text-center text-zinc-400">
                                                    <div class="flex flex-col items-center justify-center gap-2">
                                                        <i data-lucide="inbox" class="w-8 h-8 text-zinc-300"></i>
                                                        <span>No unloading records found matching your filters.</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination footer -->
                            @if($entries->hasPages())
                                <div class="border-t border-zinc-100 bg-zinc-50/50 p-4">
                                    {{ $entries->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Mandi Leaderboard (Col Span 1) -->
                    <div class="lg:col-span-1 flex flex-col h-full min-h-0">
                        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm flex flex-col h-full overflow-hidden">
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 shrink-0">
                                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 font-sans">Mandi Leaderboard</h4>
                                <span class="text-[9px] text-zinc-500 font-bold bg-zinc-100 px-2 py-0.5 rounded">Moisture Asc</span>
                            </div>
                            <div class="flex-1 overflow-y-auto pt-3 space-y-3.5">
                                @foreach($mandiLeaderboard as $mandi)
                                    @php
                                        $avgM = (float)$mandi->avg_moisture;
                                    @endphp
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $avgM > 10.0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                            <span class="text-zinc-650 font-semibold truncate text-left" title="{{ $mandi->sourced_from }}">{{ $mandi->sourced_from }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0 pl-2">
                                            <span class="font-mono font-bold {{ $avgM > 10.0 ? 'text-amber-600' : 'text-emerald-700' }}">{{ number_format($avgM, 1) }}%</span>
                                            <span class="text-[9px] text-zinc-400 bg-zinc-50 border border-zinc-200/40 px-1 py-0.5 rounded font-mono">{{ $mandi->total_logs }} logs</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tab 2: Procurement Centers -->
            <div id="view-units" class="hidden flex-1 p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <h3 class="text-sm font-bold text-zinc-900">Procurement Centers</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Manage and monitor active crushing units and receiving depots.</p>
                    </div>
                    <button onclick="alert('Adding center is disabled in demo mode.')" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-xs font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Center
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($units as $unit)
                        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4 relative overflow-hidden">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1 text-left">
                                    <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400 bg-zinc-50 border border-zinc-200/40 px-2 py-0.5 rounded">Unit ID: {{ $unit->id }}</span>
                                    <h4 class="text-xs font-bold text-[#0d2818] pt-1.5">{{ $unit->name }}</h4>
                                    <p class="text-[11px] text-zinc-500 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-[#0d2818]"></i>
                                        {{ str_contains($unit->name, 'Baran') ? 'Baran, RJ' : (str_contains($unit->name, 'Kota') ? 'Kota, RJ' : 'Moondla, RJ') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-250">Active</span>
                            </div>
                            
                            <div class="border-t border-zinc-100 pt-4 grid grid-cols-2 gap-4 text-left">
                                <div>
                                    <span class="text-[9px] uppercase font-semibold text-zinc-400">Total Shipments</span>
                                    <p class="text-sm font-bold text-zinc-800 mt-0.5">{{ $entries->where('unit_id', $unit->id)->count() }} logs</p>
                                </div>
                                <div>
                                    <span class="text-[9px] uppercase font-semibold text-zinc-400">Crush Capacity</span>
                                    <p class="text-sm font-bold text-zinc-800 mt-0.5">{{ $unit->id == 1 ? '300 MT' : ($unit->id == 2 ? '250 MT' : '150 MT') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab 3: Supervisors List -->
            <div id="view-supervisors" class="hidden flex-1 p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <h3 class="text-sm font-bold text-zinc-900">Unloading Supervisors</h3>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Manage mobile supervisor accounts and access authorizations.</p>
                    </div>
                    <button onclick="alert('Adding supervisor is disabled in demo mode.')" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-xs font-semibold py-2 px-4 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Supervisor
                    </button>
                </div>
                
                <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold uppercase tracking-wider text-zinc-400">
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Phone Number</th>
                                <th class="py-3 px-6">Role</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-right">Registered At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-xs text-zinc-700">
                            @foreach($supervisors as $supervisor)
                                <tr class="hover:bg-zinc-50/50 transition">
                                    <td class="py-3.5 px-6 font-semibold text-zinc-800 text-left">
                                        {{ $supervisor->name }}
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-[11px] text-left">
                                        {{ $supervisor->phone ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-left">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $supervisor->role === 'manager' ? 'bg-[#0d2818]/10 text-[#0d2818] border border-[#0d2818]/20' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                            {{ ucfirst($supervisor->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-center">
                                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[10px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-right text-zinc-500 font-mono text-[11px]">
                                        {{ $supervisor->created_at ? $supervisor->created_at->format('d M Y, H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 4: Analytics & Reports -->
            <div id="view-analytics" class="hidden flex-1 p-8 space-y-6">
                <div class="text-left">
                    <h3 class="text-sm font-bold text-zinc-900">Analytics & Quality Reports</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Real-time seed quality analytics, moisture trends, and compliance metrics.</p>
                </div>

                <!-- Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card 1: Pass Rate -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-3 relative overflow-hidden">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 text-left">Quality Compliance</h4>
                        <div class="flex items-baseline gap-2 relative z-10 text-left">
                            <span class="text-2xl font-bold text-[#0d2818]">{{ number_format(($stats['approved'] / max($stats['total'], 1)) * 100, 0) }}%</span>
                            <span class="text-[10px] text-zinc-400 font-medium">Pass Rate</span>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-1.5 relative z-10">
                            <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ ($stats['approved'] / max($stats['total'], 1)) * 100 }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-medium text-left">Total approved vs flagged/rejected shipments.</p>
                    </div>

                    <!-- Card 2: Average Moisture -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-3 relative overflow-hidden">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 text-left">Average Moisture</h4>
                        <div class="flex items-baseline gap-2 relative z-10 text-left">
                            <span class="text-2xl font-bold text-[#0d2818]">8.8%</span>
                            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">Optimal</span>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-1.5 relative z-10">
                            <div class="bg-[#0d2818] h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-medium text-left">Average moisture level across received soybean logs.</p>
                    </div>

                    <!-- Card 3: Quality Outliers -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-3 relative overflow-hidden">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 text-left">Out-of-Spec Shipments</h4>
                        <div class="flex items-baseline gap-2 relative z-10 text-left">
                            <span class="text-2xl font-bold text-amber-650">{{ $stats['out_of_spec'] }}</span>
                            <span class="text-[10px] text-zinc-400 font-medium">flagged logs</span>
                        </div>
                        <div class="w-full bg-zinc-100 rounded-full h-1.5 relative z-10">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ ($stats['out_of_spec'] / max($stats['total'], 1)) * 100 }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-medium text-left">Shipments exceeding standard moisture or FM levels.</p>
                    </div>
                </div>

                <!-- Sourcing Breakdown -->
                <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#0d2818] border-b border-zinc-100 pb-3 text-left font-sans">Location Quality Breakdown</h4>
                    <div class="space-y-4 text-left">
                        @foreach($mandiLeaderboard as $mandi)
                            @php $pct = 100 - (($mandi->avg_moisture / 15.0) * 100); @endphp
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-zinc-700">{{ $mandi->sourced_from }}</span>
                                    <span class="font-mono text-zinc-500">Avg Moisture: <span class="font-bold text-[#0d2818]">{{ number_format($mandi->avg_moisture, 2) }}%</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $mandi->avg_moisture > 10.0 ? 'bg-amber-500' : 'bg-emerald-600' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tab 5: Settings -->
            <div id="view-settings" class="hidden flex-1 p-8 space-y-6">
                <div class="text-left">
                    <h3 class="text-sm font-bold text-zinc-900">System Settings</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Configure quality limits, weighbridge tolerances, and manager preferences.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quality Baselines Card -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#0d2818] border-b border-zinc-100 pb-3 flex items-center gap-2 text-left font-sans">
                            <i data-lucide="sliders" class="w-4 h-4"></i> Standard Quality Limits
                        </h4>
                        
                        <div class="space-y-4 text-left">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Moisture Threshold (%)</label>
                                <input type="number" step="0.1" value="10.0" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                                <span class="text-[9px] text-zinc-400">Values above this trigger dynamic price deductions.</span>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Foreign Matter Threshold (%)</label>
                                <input type="number" step="0.1" value="2.0" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Damaged seeds Threshold (%)</label>
                                <input type="number" step="0.1" value="2.0" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Manager Profile Preferences -->
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#0d2818] border-b border-zinc-100 pb-3 flex items-center gap-2 text-left font-sans">
                            <i data-lucide="user" class="w-4 h-4"></i> Profile Details
                        </h4>
                        
                        <div class="space-y-4 text-left">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Full Name</label>
                                <input type="text" value="{{ auth()->user()->name ?? 'HQ Manager' }}" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Email Address</label>
                                <input type="email" value="{{ auth()->user()->email ?? 'admin@shivedibles.com' }}" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Access Level</label>
                                <input type="text" value="System Administrator" disabled class="w-full px-3 py-2 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Slide-over Drawer Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0 pointer-events-none" onclick="closeDrawer()"></div>

    <!-- Details Overlay Drawer -->
    <div id="drawer" class="fixed top-0 right-0 h-full w-full sm:w-[500px] md:w-[600px] bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-out border-l border-zinc-200 flex flex-col">
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
                        <p class="text-xs font-semibold text-zinc-700 mt-0.5" id="drawer-center">Shiv Agrevo Ltd., Baran</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Purchase Mode</span>
                        <p class="text-xs font-semibold text-zinc-700 mt-0.5" id="drawer-purchase-type">Depo</p>
                    </div>
                </div>
                <div class="border-t border-zinc-200/60 my-1"></div>
                <div>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Supplier / Source Mandi</span>
                    <p class="text-xs font-semibold text-[#0d2818] mt-0.5" id="drawer-supplier">Kota Mandi / Rajasthan</p>
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
                            <p class="text-xs font-bold text-zinc-700 font-mono mt-0.5" id="drawer-gross-weight">0.000 MT</p>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Tare Weight</span>
                            <p class="text-xs font-bold text-zinc-700 font-mono mt-0.5" id="drawer-tare-weight">0.000 MT</p>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold tracking-wider text-zinc-400">Net Weight</span>
                            <p class="text-xs font-bold text-zinc-800 font-mono mt-0.5" id="drawer-net-weight">0.000 MT</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-2 bg-zinc-50/50">
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-500 font-medium">Standard Moisture Limit</span>
                            <span class="text-zinc-700 font-medium">10.0% max</span>
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
                            <span class="text-xs font-bold text-emerald-700 font-mono" id="drawer-payout-weight">0.000 MT</span>
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
                            <p class="text-xs font-semibold text-zinc-700">Supervisor Audio Verification</p>
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
                            <p class="text-xs font-semibold text-zinc-700 font-mono" id="drawer-gps-coords">25.0744, 75.8372</p>
                            <span class="text-[10px] text-zinc-400" id="drawer-gps-accuracy">Accuracy: ±5.0m (Unloaded at Center)</span>
                        </div>
                    </div>
                    <a href="#" id="drawer-maps-link" target="_blank" class="px-3 py-1.5 bg-white hover:bg-zinc-100 text-zinc-700 text-xs font-semibold border border-zinc-200 rounded-md transition duration-150 flex items-center gap-1.5 cursor-pointer">
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
                            <p class="text-xs text-zinc-600 italic leading-relaxed" id="drawer-remarks-text">"No comments added yet."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks Confirmation Panel (Slide-up inside drawer) -->
        <div id="remarks-panel" class="border-t border-zinc-200 bg-white p-6 space-y-4 hidden shrink-0 shadow-inner">
            <div class="flex justify-between items-center">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-400" id="remarks-panel-title">Confirm Action</h4>
                <button onclick="hideRemarksPanel()" class="text-zinc-400 hover:text-zinc-600 text-xs font-semibold cursor-pointer">Cancel</button>
            </div>
            <textarea id="action-remarks-input" rows="2" placeholder="Add verification remarks or justification notes..." class="w-full p-2.5 text-xs bg-zinc-50 border border-zinc-200 rounded-md focus:border-zinc-400 focus:bg-white focus:outline-none transition"></textarea>
            <button id="remarks-submit-btn" onclick="submitStatusWithRemarks()" class="w-full bg-[#0d2818] hover:bg-[#163a23] text-white text-xs font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                <i data-lucide="check" class="w-3.5 h-3.5"></i> Confirm Submit
            </button>
        </div>
 
        <!-- Drawer Footer Controls -->
        <div id="drawer-actions-footer" class="p-6 border-t border-zinc-200 bg-zinc-50/50 flex gap-3 shrink-0">
            <!-- Action buttons -->
            <button onclick="promptRemarks('approved')" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-emerald-700/10">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Approve Log
            </button>
            <button onclick="promptRemarks('flagged')" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-amber-500/10">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> Flag Quality
            </button>
            <button onclick="promptRemarks('rejected')" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-2.5 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm shadow-red-600/10">
                <i data-lucide="x-circle" class="w-4 h-4"></i> Reject
            </button>
        </div>iv>
    </div>

    <!-- Drawer Controller Script -->
    <script>
        let currentEntryId = null;

        // Auto initialize lucide icons
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();

            // Row click listener
            document.querySelectorAll('.select-row').forEach(row => {
                row.addEventListener('click', function() {
                    const data = JSON.parse(this.dataset.json);
                    openDrawer(data, this);
                });
            });
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-2.5 px-4 py-3 text-xs font-medium rounded-lg shadow-lg border pointer-events-auto transition duration-300 transform translate-y-2 opacity-0 bg-white ${
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

            // Highlight selected row
            document.querySelectorAll('.select-row').forEach(r => r.classList.remove('bg-zinc-100/50', 'border-l-zinc-800'));
            rowElement.classList.add('bg-zinc-100/50', 'border-l-zinc-800');

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

            // Moisture spec
            const moisture = parseFloat(entry.moisture);
            const mElem = document.getElementById('drawer-moisture');
            const mSpec = document.getElementById('drawer-moisture-spec');
            mElem.innerText = moisture.toFixed(1) + '%';
            if (moisture > 10.0) {
                mSpec.innerText = 'Out of Spec (>10.0%)';
                mSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-amber-50 text-amber-700 border border-amber-200';
            } else {
                mSpec.innerText = 'In-Spec (≤10.0%)';
                mSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
            }

            // FM spec
            const fm = parseFloat(entry.fm);
            const fmElem = document.getElementById('drawer-fm');
            const fmSpec = document.getElementById('drawer-fm-spec');
            fmElem.innerText = fm.toFixed(1) + '%';
            if (fm > 2.0) {
                fmSpec.innerText = 'Out of Spec (>2.0%)';
                fmSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-amber-50 text-amber-700 border border-amber-200';
            } else {
                fmSpec.innerText = 'In-Spec (≤2.0%)';
                fmSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
            }

            // DM spec
            const dm = parseFloat(entry.dm);
            const dmElem = document.getElementById('drawer-dm');
            const dmSpec = document.getElementById('drawer-dm-spec');
            dmElem.innerText = dm.toFixed(1) + '%';
            if (dm > 2.0) {
                dmSpec.innerText = 'Out of Spec (>2.0%)';
                dmSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-amber-50 text-amber-700 border border-amber-200';
            } else {
                dmSpec.innerText = 'In-Spec (≤2.0%)';
                dmSpec.className = 'inline-block text-[9px] px-1.5 py-0.5 rounded mt-2 font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
            }

            // Weighbridge & deductions calculator
            const gross = parseFloat(entry.gross_weight) || 0;
            const tare = parseFloat(entry.tare_weight) || 0;
            const net = parseFloat(entry.net_weight) || (gross - tare);
            
            document.getElementById('drawer-operator').innerText = 'Operator: ' + (entry.operator_name || 'N/A');
            document.getElementById('drawer-gross-weight').innerText = gross.toFixed(3) + ' MT';
            document.getElementById('drawer-tare-weight').innerText = tare.toFixed(3) + ' MT';
            document.getElementById('drawer-net-weight').innerText = net.toFixed(3) + ' MT';

            // Calculate Moisture Penalty: 1.5% deduction per 1% excess moisture over 10.0%
            let mDeduct = 0;
            if (moisture > 10.0) {
                mDeduct = (moisture - 10.0) * 0.015 * net;
            }
            document.getElementById('drawer-moisture-deduction').innerText = '-' + mDeduct.toFixed(3) + ' MT';

            // Calculate FM Penalty: 1.0% deduction per 1% excess FM over 2.0%
            let fmDeduct = 0;
            if (fm > 2.0) {
                fmDeduct = (fm - 2.0) * 0.01 * net;
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
            document.getElementById('drawer-backdrop').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('drawer-backdrop').classList.remove('opacity-0');
                document.getElementById('drawer-backdrop').classList.add('opacity-100');
                document.getElementById('drawer').classList.remove('translate-x-full');
            }, 50);
        }

        function closeDrawer() {
            document.getElementById('drawer').classList.add('translate-x-full');
            document.getElementById('drawer-backdrop').classList.remove('opacity-100');
            document.getElementById('drawer-backdrop').classList.add('opacity-0');
            
            // Remove highlighted row
            document.querySelectorAll('.select-row').forEach(r => r.classList.remove('bg-zinc-100/50', 'border-l-zinc-800'));
            
            // Stop audio if playing
            document.getElementById('drawer-audio-player').pause();

            setTimeout(() => {
                document.getElementById('drawer-backdrop').classList.add('hidden');
            }, 300);
        }

        let pendingStatusTarget = null;

        function promptRemarks(status) {
            pendingStatusTarget = status;
            
            const submitBtn = document.getElementById('remarks-submit-btn');
            const panelTitle = document.getElementById('remarks-panel-title');
            
            if (status === 'approved') {
                panelTitle.innerText = "Confirm Approve Log";
                submitBtn.className = "w-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
            } else if (status === 'flagged') {
                panelTitle.innerText = "Confirm Flag Quality";
                submitBtn.className = "w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
            } else if (status === 'rejected') {
                panelTitle.innerText = "Confirm Reject Log";
                submitBtn.className = "w-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-2 px-4 rounded-md transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-sm";
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
                        // Update status cell content
                        const statusCell = row.querySelector('.row-status-cell');
                        let badgeHtml = '';
                        if (status === 'approved') {
                            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>`;
                        } else if (status === 'flagged') {
                            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">Flagged</span>`;
                        } else if (status === 'rejected') {
                            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Rejected</span>`;
                        } else {
                            badgeHtml = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Pending</span>`;
                        }
                        statusCell.innerHTML = badgeHtml;

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

        function updateStatsWidgets() {
            // Recalculate stats dynamically from visible rows
            const rows = document.querySelectorAll('.select-row');
            let total = 0, pending = 0, approved = 0, flagged = 0;
            
            rows.forEach(row => {
                const data = JSON.parse(row.dataset.json);
                total++;
                if (data.status === 'pending') pending++;
                else if (data.status === 'approved') approved++;
                else if (data.status === 'flagged') flagged++;
            });

            // Update DOM
            const statTotal = document.getElementById('stat-total');
            const statTotalBadge = document.getElementById('stat-total-badge');
            const statPending = document.getElementById('stat-pending');
            const statPendingBadge = document.getElementById('stat-pending-badge');
            const statApproved = document.getElementById('stat-approved');
            const statApprovedBadge = document.getElementById('stat-approved-badge');
            const statFlagged = document.getElementById('stat-flagged');
            const statFlaggedBadge = document.getElementById('stat-flagged-badge');

            if (statTotal) statTotal.innerText = total;
            if (statTotalBadge) statTotalBadge.innerText = total;
            if (statPending) statPending.innerText = pending;
            if (statPendingBadge) statPendingBadge.innerText = pending;
            if (statApproved) statApproved.innerText = approved;
            if (statApprovedBadge) statApprovedBadge.innerText = approved;
            if (statFlagged) statFlagged.innerText = flagged;
            if (statFlaggedBadge) statFlaggedBadge.innerText = flagged;
        }

        function switchTab(tabId) {
            // Hide all tabs
            document.getElementById('view-dashboard').classList.add('hidden');
            document.getElementById('view-units').classList.add('hidden');
            document.getElementById('view-supervisors').classList.add('hidden');
            document.getElementById('view-analytics').classList.add('hidden');
            document.getElementById('view-settings').classList.add('hidden');

            // Show target tab
            document.getElementById('view-' + tabId).classList.remove('hidden');

            // Toggle active classes on nav buttons
            const navs = ['dashboard', 'units', 'supervisors', 'analytics', 'settings'];
            navs.forEach(id => {
                const btn = document.getElementById('nav-' + id);
                if (!btn) return;
                const icon = btn.querySelector('[data-lucide]');
                if (id === tabId) {
                    btn.className = "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold bg-emerald-50/60 text-[#0d2818] border border-emerald-100/50 transition cursor-pointer text-left";
                    if (icon) {
                        icon.classList.remove('text-zinc-400');
                        icon.classList.add('text-[#0d2818]');
                    }
                } else {
                    btn.className = "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 transition border border-transparent cursor-pointer text-left";
                    if (icon) {
                        icon.classList.remove('text-[#0d2818]');
                        icon.classList.add('text-zinc-400');
                    }
                }
            });
        }
    </script>
</body>
</html>
