<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lab Portal — Shiv Edibles</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/admin-dashboard.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        .status-pill { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 12px; font-weight: 600; padding: 0.3rem 0.65rem; border-radius: 9999px; border: 1px solid transparent; white-space: nowrap; }
        .status-pill__dot { width: 6px; height: 6px; border-radius: 9999px; }
        .status-pill--approved { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
        .status-pill--approved .status-pill__dot { background: #10b981; }
        .status-pill--pending { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
        .status-pill--pending .status-pill__dot { background: #3b82f6; }
        .status-pill--flagged { color: #b45309; background: #fffbeb; border-color: #fde68a; }
        .status-pill--flagged .status-pill__dot { background: #f59e0b; }
        .status-pill--rejected { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
        .status-pill--rejected .status-pill__dot { background: #ef4444; }
        #lab-test-modal.hidden { display: none !important; }
        #lab-test-modal:not(.hidden) { display: flex !important; }
        .lab-test-btn {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            white-space: nowrap;
            background-color: #0d2818;
            color: #fff;
            border: 1px solid #0d2818;
            font-weight: 600;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }
        .lab-test-btn:hover { background-color: #163a23; border-color: #163a23; }
        .lab-test-btn--sm { font-size: 12px; padding: 0.4rem 0.75rem; }
        .lab-test-btn svg { flex-shrink: 0; width: 14px; height: 14px; stroke: currentColor; }
    </style>
</head>
<body class="bg-[#f5f8f5] text-zinc-900 min-h-screen">
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <header class="h-14 bg-white border-b border-[#dee4de] px-6 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-700 to-violet-900 flex items-center justify-center text-white">
                <i data-lucide="flask-conical" class="w-4 h-4"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-zinc-900">Lab Portal</h1>
                <p class="text-[11px] text-zinc-500">Record and update quality lab tests</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-zinc-600 hidden sm:inline">{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition">Sign out</button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-8">
        <div class="mb-6">
            <h2 class="text-base font-bold text-zinc-900">Procurement entries</h2>
            <p class="text-[11px] text-zinc-500 mt-0.5">Select an entry to add or update lab test results.</p>
        </div>

        <div class="bg-white border border-[#dee4de] rounded-xl p-4 mb-5 shadow-sm">
            <form method="GET" action="{{ route('lab.dashboard') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-400 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search entry ID, truck, supplier…"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:outline-none focus:ring-2 focus:ring-[#0d2818]/10">
                </div>
                <select name="lab_filter" class="px-3 py-2.5 text-sm border border-zinc-200 rounded-lg bg-white focus:border-[#0d2818] focus:outline-none min-w-[10rem]">
                    <option value="">All entries</option>
                    <option value="pending" {{ request('lab_filter') === 'pending' ? 'selected' : '' }}>Awaiting lab test</option>
                    <option value="completed" {{ request('lab_filter') === 'completed' ? 'selected' : '' }}>Lab test recorded</option>
                </select>
                <button type="submit" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2.5 px-5 rounded-lg transition">Search</button>
            </form>
        </div>

        <div class="bg-white border border-[#dee4de] rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 border-b border-zinc-100">
                        <tr>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Entry</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Vehicle</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Center</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400 text-center">Field quality</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400 text-center">Lab status</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-zinc-400 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-zinc-50/80 transition">
                                <td class="px-4 py-3 font-mono text-xs font-semibold text-[#0d2818]">#{{ $entry->id }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-zinc-700">{{ $entry->truck_no }}</td>
                                <td class="px-4 py-3 text-zinc-600 max-w-[160px] truncate" title="{{ $entry->unit->name ?? '' }}">{{ $entry->unit->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-xs text-zinc-500">
                                    M {{ number_format($entry->moisture, 1) }}% · FM {{ number_format($entry->fm, 1) }}% · DM {{ number_format($entry->dm, 1) }}%
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($entry->lab_test_status)
                                        @php
                                            $labClass = match($entry->lab_test_status) {
                                                'pass' => 'approved',
                                                'fail' => 'rejected',
                                                'retest' => 'flagged',
                                                default => 'pending',
                                            };
                                        @endphp
                                        <span class="status-pill status-pill--{{ $labClass }}">
                                            <span class="status-pill__dot"></span>{{ ucfirst($entry->lab_test_status) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-zinc-400">Not tested</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                        data-entry-id="{{ $entry->id }}"
                                        onclick="event.stopPropagation(); openLabTestModalFromRow(this)"
                                        class="lab-test-row-btn lab-test-btn lab-test-btn--sm">
                                        <i data-lucide="flask-conical" class="w-3.5 h-3.5" aria-hidden="true"></i>
                                        <span>{{ $entry->lab_test_status ? 'Edit Lab Test' : 'Add Lab Test' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-400">No entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($entries->hasPages())
                <div class="px-4 py-3 border-t border-zinc-100 bg-zinc-50/60">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </main>

    @include('partials.lab-test-modal')

    <script>
        const labTestRouteTemplate = @json(route('entries.lab-test', ['id' => '__ENTRY_ID__']));
        let logsEntriesById = @json(
            $entries->getCollection()->mapWithKeys(fn ($e) => [$e->id => $e])->all()
        );

        @include('partials.lab-test-scripts')

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800';
            toast.className = `pointer-events-auto px-4 py-3 rounded-lg border shadow-lg text-sm font-medium ${bg} transition`;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            document.querySelectorAll('.lab-test-row-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openLabTestModalFromRow(btn);
                });
            });
        });
    </script>
</body>
</html>
