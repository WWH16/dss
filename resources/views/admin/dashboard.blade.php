@extends('layouts.dashboard')

@section('title', 'Admin Dashboard | Decision Support System: ISU Cauayan Canteen Client Evaluation System')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Admin Dashboard</h1>
            <p class="text-neutral-500 text-sm">Welcome back, <span class="font-semibold text-brand-700">{{ Auth::user()->name }}</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Summary Metrics ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-6 mb-8">
        @foreach([
            ['label' => 'Total Students', 'value' => $studentCount, 'icon' => 'group'],
            ['label' => 'Total Stalls', 'value' => $stallCount, 'icon' => 'storefront'],
            ['label' => 'Total Evaluations', 'value' => $evaluationCount, 'icon' => 'rate_review']
        ] as $stat)
            <div class="bg-white rounded-xl border border-neutral-200/60 p-5 flex items-center gap-4">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-lg text-xl leading-none">{{ $stat['icon'] }}</span>
                <div>
                    <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold">{{ $stat['label'] }}</span>
                    <span class="text-2xl font-extrabold text-neutral-900 tabular-nums leading-none mt-1 block">{{ $stat['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Stalls + Scores ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        {{-- Manage Stalls --}}
        <div class="lg:col-span-1 bg-white rounded-xl border border-neutral-200/60 p-6">
            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider mb-4 pb-2 border-b border-neutral-100">Manage Stalls</h2>

            <form action="{{ route('admin.stall.add') }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <input
                    type="text"
                    name="name"
                    class="flex-1 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-semibold focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/15"
                    placeholder="New Stall Name"
                    required>
                <button class="btn btn-primary text-xs py-2 px-3 font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm leading-none">add</span>
                    Add
                </button>
            </form>

            <div class="max-h-[240px] overflow-y-auto pr-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[9px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-1">
                            <th class="pb-1">Name</th>
                            <th class="pb-1 text-right" width="60">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse($stalls as $stall)
                            <tr class="text-xs hover:bg-neutral-50/50">
                                <td class="py-2 font-semibold text-neutral-800">{{ $stall->name }}</td>
                                <td class="py-2 text-right">
                                    {{-- Hidden real delete form --}}
                                    <form id="delete-form-{{ $stall->id }}" action="{{ route('admin.stall.delete', $stall->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    {{-- Trigger button --}}
                                    <button
                                        type="button"
                                        onclick="openDeleteModal({{ $stall->id }}, '{{ addslashes($stall->name) }}')"
                                        class="text-red-500 hover:text-red-600 font-bold text-[10px] tracking-wider uppercase inline-flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[14px] leading-none">delete</span>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-xs text-neutral-400 font-medium">
                                    No stalls available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Average Stall Scores --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200/60 p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Average Stall Scores</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Out of 5.0 max</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[450px]">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                            <th class="pb-2">Food Stall</th>
                            <th class="pb-2 text-center">Cleanliness</th>
                            <th class="pb-2 text-center">Service</th>
                            <th class="pb-2 text-center">Taste</th>
                            <th class="pb-2 text-center">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse($results as $result)
                            <tr class="text-xs text-neutral-800 hover:bg-neutral-50/50">
                                <td class="py-2.5 font-bold text-neutral-950">{{ $result->name }}</td>
                                <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->cleanliness, 2) }}</td>
                                <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->service, 2) }}</td>
                                <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->taste, 2) }}</td>
                                <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-neutral-400 font-medium">
                                    No evaluation results yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Charts Row ───────────────────────────────────────────────────── --}}
    @if($results->isNotEmpty())
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

        {{-- Bar Chart: Avg Scores per Stall --}}
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Stall Performance</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Avg score / 5.0</span>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="stallScoresChart"></canvas>
            </div>
        </div>

        {{-- Line Chart: Evaluations Over Time --}}
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation Activity</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Last 30 days</span>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="evalTrendChart"></canvas>
            </div>
        </div>

    </div>
    @endif

    {{-- ── Detailed Evaluations ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-neutral-100">
            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Detailed Evaluations</h2>
            <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                {{ $evaluations->count() }} total
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                        <th class="pb-2">Student</th>
                        <th class="pb-2">Stall</th>
                        <th class="pb-2 text-center">Clean</th>
                        <th class="pb-2 text-center">Serv</th>
                        <th class="pb-2 text-center">Taste</th>
                        <th class="pb-2 text-center">Price</th>
                        <th class="pb-2">Comment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-50">
                    @forelse($evaluations as $eval)
                        <tr class="text-xs text-neutral-800 hover:bg-neutral-50/30">
                            <td class="py-2.5 font-semibold text-neutral-900 leading-none">{{ $eval->student_name }}</td>
                            <td class="py-2.5 text-neutral-500 font-medium leading-none">{{ $eval->stall_name }}</td>
                            <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->cleanliness }}</td>
                            <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->service }}</td>
                            <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->taste }}</td>
                            <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->price }}</td>
                            <td class="py-2.5 text-neutral-600 text-xs max-w-[200px] truncate" title="{{ $eval->comment }}">{{ $eval->comment }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-xs text-neutral-400 font-medium">
                                No evaluations yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Delete Confirmation Modal ────────────────────────────────────────── --}}
<dialog id="delete-confirm-modal" class="confirm-modal">
    <div class="flex items-start gap-4 mb-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600">
            <span class="material-symbols-outlined" style="font-size: 1.4rem;">delete_forever</span>
        </div>
        <div>
            <h3 class="text-base font-bold text-neutral-900 leading-tight mb-1" style="font-family: var(--font-display);">Delete Stall?</h3>
            <p class="text-neutral-500 text-xs leading-relaxed">
                You are about to permanently delete <strong id="delete-stall-name" class="text-neutral-800"></strong>.
                This will also remove all associated evaluations. This action cannot be undone.
            </p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
        <button type="button" class="btn btn-ghost btn-sm js-close-delete-modal">Cancel</button>
        <button type="button" id="confirm-delete-btn"
            class="btn btn-sm font-bold text-white"
            style="background-color: oklch(0.58 0.23 28); border-color: oklch(0.58 0.23 28);">
            <span class="material-symbols-outlined text-sm leading-none">delete</span>
            Yes, Delete
        </button>
    </div>
</dialog>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ── Delete Modal ────────────────────────────────────────────────────
    var deleteModal = document.getElementById('delete-confirm-modal');
    var stallNameEl = document.getElementById('delete-stall-name');
    var confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    var pendingFormId = null;

    function openDeleteModal(stallId, stallName) {
        pendingFormId = 'delete-form-' + stallId;
        stallNameEl.textContent = stallName;
        deleteModal.showModal();
    }

    document.querySelectorAll('.js-close-delete-modal').forEach(function(btn) {
        btn.addEventListener('click', function() { deleteModal.close(); });
    });

    deleteModal.addEventListener('click', function(e) {
        var rect = deleteModal.getBoundingClientRect();
        var inside = rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
                     rect.left <= e.clientX && e.clientX <= rect.left + rect.width;
        if (!inside) deleteModal.close();
    });

    confirmDeleteBtn.addEventListener('click', function() {
        if (!pendingFormId) return;
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML = '<span class="material-symbols-outlined text-sm leading-none">hourglass_empty</span> Deleting...';
        document.getElementById(pendingFormId).submit();
    });

    // ── Shared chart defaults ───────────────────────────────────────────
    Chart.defaults.font.family = 'Plus Jakarta Sans';

    @if($results->isNotEmpty())
    // ── Bar Chart: Stall Performance ────────────────────────────────────
    (function() {
        var ctx = document.getElementById('stallScoresChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($results->pluck('name')),
                datasets: [
                    {
                        label: 'Cleanliness',
                        data: @json($results->pluck('cleanliness')->map(fn($v) => round($v, 2))),
                        backgroundColor: 'oklch(0.48 0.15 155 / 0.85)',
                        borderColor: 'oklch(0.40 0.13 155)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Service',
                        data: @json($results->pluck('service')->map(fn($v) => round($v, 2))),
                        backgroundColor: 'oklch(0.60 0.14 155 / 0.80)',
                        borderColor: 'oklch(0.52 0.14 155)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Taste',
                        data: @json($results->pluck('taste')->map(fn($v) => round($v, 2))),
                        backgroundColor: 'oklch(0.74 0.14 155 / 0.75)',
                        borderColor: 'oklch(0.65 0.13 155)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Price',
                        data: @json($results->pluck('price')->map(fn($v) => round($v, 2))),
                        backgroundColor: 'oklch(0.87 0.08 155 / 0.80)',
                        borderColor: 'oklch(0.78 0.10 155)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: '600' },
                            color: '#52525b',
                            padding: 14,
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                        }
                    },
                    tooltip: {
                        backgroundColor: 'oklch(0.18 0.07 155)',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2) + ' / 5.00';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '600' }, color: '#374151' },
                        border: { display: false }
                    },
                    y: {
                        min: 0, max: 5,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1, font: { size: 10 }, color: '#94a3b8' },
                        border: { display: false }
                    }
                }
            }
        });
    })();

    // ── Line Chart: Evaluation Activity ─────────────────────────────────
    (function() {
        var ctx = document.getElementById('evalTrendChart');
        if (!ctx) return;

        var trendDates  = @json($trendDates);
        var trendCounts = @json($trendCounts);

        // Show fewer labels on mobile — thin out to every 5th date
        var isMobile = window.innerWidth < 640;
        var tickCallback = function(val, idx) {
            if (isMobile) return idx % 5 === 0 ? trendDates[idx] : '';
            return idx % 3 === 0 ? trendDates[idx] : '';
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendDates,
                datasets: [{
                    label: 'Evaluations',
                    data: trendCounts,
                    borderColor: 'oklch(0.48 0.15 155)',
                    backgroundColor: 'oklch(0.56 0.17 155 / 0.12)',
                    pointBackgroundColor: 'oklch(0.48 0.15 155)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'oklch(0.18 0.07 155)',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) { return items[0].label; },
                            label: function(ctx) {
                                return ' ' + ctx.parsed.y + ' evaluation' + (ctx.parsed.y !== 1 ? 's' : '');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#94a3b8',
                            callback: tickCallback,
                            maxRotation: 0,
                        },
                        border: { display: false }
                    },
                    y: {
                        min: 0,
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            font: { size: 10 },
                            color: '#94a3b8'
                        },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    @endif
</script>
@endsection

@endsection