@extends('layouts.dashboard')
@section('title', 'Overview | Admin — DSS')
@section('head')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-7">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Overview</h1>
            <p class="text-neutral-500 text-sm mt-0.5">Welcome back, <span class="font-semibold text-brand-700">{{ Auth::user()->name }}</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2.5 shadow-sm">
            <ion-icon name="checkmark-circle" class="text-lg text-emerald-600"></ion-icon>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── 2. Minimalist Stat Cards ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
        @foreach([
            ['label' => 'Total Students',   'value' => $studentCount,    'icon' => 'people-outline',    'desc' => 'Enrolled evaluators'],
            ['label' => 'Canteen Stalls',   'value' => $stallCount,      'icon' => 'storefront-outline', 'desc' => 'Active food vendors'],
            ['label' => 'Evaluations',      'value' => $evaluationCount, 'icon' => 'create-outline',    'desc' => 'Feedback submitted'],
        ] as $stat)
            <div class="bg-white rounded-xl border border-neutral-200/70 p-6 shadow-sm hover:border-brand-300/80 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">{{ $stat['label'] }}</span>
                    <div class="w-10 h-10 rounded-lg bg-brand-50 border border-brand-100/70 flex items-center justify-center text-brand-700">
                        <ion-icon name="{{ $stat['icon'] }}" class="text-xl text-brand-700"></ion-icon>
                    </div>
                </div>
                <div class="text-3xl font-bold text-neutral-900 tabular-nums tracking-tight leading-none">
                    {{ $stat['value'] }}
                </div>
                <p class="text-xs text-neutral-400 mt-2 font-medium">{{ $stat['desc'] }}</p>
            </div>
        @endforeach
    </div>

    @if($results->isNotEmpty())
        {{-- ── 3. Primary Chart Card: Evaluation Activity ──────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-6 pb-4 border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Activity</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">30-day evaluation timeline across canteen stalls</p>
                </div>
            </div>
            <div class="relative w-full" style="height: 250px;">
                <canvas id="evalTrendChart" role="img" aria-label="Line chart showing the number of evaluations over the last 30 days">
                    <p>Line chart showing the number of evaluations over the last 30 days. Please use the data tables below for accessible data.</p>
                </canvas>
            </div>
        </div>

        {{-- ── 4. Secondary Chart Cards: Top Stalls & Share ─────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-7" style="content-visibility: auto; contain-intrinsic-size: auto 320px;">
            @php
                $allStallResults = collect($results)->map(function($stall) {
                    $obj = clone $stall;
                    $obj->cleanliness = round((float)$obj->cleanliness, 2);
                    $obj->service = round((float)$obj->service, 2);
                    $obj->taste = round((float)$obj->taste, 2);
                    $obj->price = round((float)$obj->price, 2);
                    $obj->avg = round(($obj->cleanliness + $obj->service + $obj->taste + $obj->price) / 4, 2);
                    return $obj;
                })->sortByDesc('avg')->values();
                $stallCountTotal = $allStallResults->count();
            @endphp

            {{-- Stall Performance Breakdown Card --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-neutral-100">
                        <div>
                            <h2 id="barChartTitle" class="text-base font-bold text-neutral-900 tracking-tight">Stall Performance Breakdown</h2>
                            <p id="barChartSubtitle" class="text-xs text-neutral-500 mt-0.5">Top {{ min(5, $stallCountTotal) }} stalls across 4 evaluation criteria</p>
                        </div>

                        {{-- Interactive Filter Switcher for N-stalls --}}
                        @if($stallCountTotal > 5)
                            <div class="inline-flex items-center rounded-lg border border-neutral-200 bg-neutral-100/80 p-0.5 text-xs font-semibold self-start sm:self-auto shrink-0" role="tablist" aria-label="Stall chart filter">
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all bg-white text-neutral-900 shadow-2xs font-bold" data-range="top5">Top 5</button>
                                @if($stallCountTotal > 5)
                                    <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium" data-range="top10">Top {{ min(10, $stallCountTotal) }}</button>
                                @endif
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium" data-range="lowest5">Lowest 5</button>
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium" data-range="all">All ({{ $stallCountTotal }})</button>
                            </div>
                        @endif
                    </div>

                    <div id="barChartWrapper" class="relative w-full overflow-x-auto" style="height: 250px;">
                        <canvas id="stallScoresChart" role="img" aria-label="Bar chart showing average scores per stall for Cleanliness, Service, Taste, and Price">
                            <p>Bar chart showing the average scores per stall for Cleanliness, Service, Taste, and Price.</p>
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Evaluation Share Donut Card --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b border-neutral-100">
                        <div>
                            <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Share</h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Distribution of student submissions per stall</p>
                        </div>
                        <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-800 border border-brand-200/70 text-[11px] font-bold px-2.5 py-1 rounded-full tabular-nums shrink-0">
                            {{ $evaluationCount }} {{ Str::plural('Submission', $evaluationCount) }}
                        </span>
                    </div>

                    <div class="relative w-full flex items-center justify-center" style="height: 250px;">
                        <canvas id="evalPieChart" role="img" aria-label="Donut chart showing the distribution of evaluations per stall">
                            <p>Donut chart showing the distribution of evaluations per stall. Please use the data tables below for accessible data.</p>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Average Stall Scores Card ────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden" style="content-visibility: auto; contain-intrinsic-size: auto 320px;">
            <div class="p-6 pb-4">
                <h2 class="text-base font-bold text-neutral-900 tracking-tight">Average Stall Scores</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Aggregate performance ratings per category</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[500px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/70 border-y border-neutral-100">
                            <th class="py-3 px-6 font-semibold">Food Stall</th>
                            <th class="py-3 px-4 text-center font-semibold">Cleanliness</th>
                            <th class="py-3 px-4 text-center font-semibold">Service</th>
                            <th class="py-3 px-4 text-center font-semibold">Taste</th>
                            <th class="py-3 px-4 text-center font-semibold">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($results as $result)
                            <tr class="text-sm hover:bg-neutral-50/60 transition-colors">
                                <td class="py-3.5 px-6 font-semibold text-neutral-900">{{ $result->name }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold tabular-nums {{ $result->cleanliness >= 4 ? 'text-brand-700' : ($result->cleanliness >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($result->cleanliness, 2) }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold tabular-nums {{ $result->service >= 4 ? 'text-brand-700' : ($result->service >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($result->service, 2) }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold tabular-nums {{ $result->taste >= 4 ? 'text-brand-700' : ($result->taste >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($result->taste, 2) }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold tabular-nums {{ $result->price >= 4 ? 'text-brand-700' : ($result->price >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($result->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100 border-t border-neutral-100">
                    @foreach($results as $result)
                        <div class="p-5 flex flex-col gap-2.5">
                            <h3 class="font-semibold text-neutral-900 text-sm">{{ $result->name }}</h3>
                            <div class="grid grid-cols-4 gap-2 text-center">
                                @foreach([
                                    ['label' => 'Clean', 'val' => $result->cleanliness],
                                    ['label' => 'Serv',  'val' => $result->service],
                                    ['label' => 'Taste', 'val' => $result->taste],
                                    ['label' => 'Price', 'val' => $result->price],
                                ] as $m)
                                <div class="bg-neutral-50 rounded-lg p-2 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase tracking-widest mb-0.5">{{ $m['label'] }}</span>
                                    <span class="block text-xs font-bold {{ $m['val'] >= 4 ? 'text-brand-700' : ($m['val'] >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($m['val'], 1) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── 6. Recent Evaluations Card ──────────────────────────────────── --}}
        @if($recentEvaluations->isNotEmpty())
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden" style="content-visibility: auto; contain-intrinsic-size: auto 340px;">
            <div class="p-6 pb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Recent Evaluations</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Latest submitted feedback records from students</p>
                </div>
                <a href="{{ route('admin.evaluations') }}" class="text-xs text-brand-700 hover:text-brand-800 font-semibold inline-flex items-center gap-1 transition-colors">
                    View All <ion-icon name="arrow-forward-outline" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[640px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/70 border-y border-neutral-100">
                            <th class="py-3 px-6 font-semibold">Student</th>
                            <th class="py-3 px-4 font-semibold">Stall</th>
                            <th class="py-3 px-3 text-center font-semibold">Clean</th>
                            <th class="py-3 px-3 text-center font-semibold">Serv</th>
                            <th class="py-3 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3 px-3 text-center font-semibold">Price</th>
                            <th class="py-3 px-3 text-center font-semibold">Avg</th>
                            <th class="py-3 px-6 text-right font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($recentEvaluations as $eval)
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <tr class="text-sm hover:bg-neutral-50/60 transition-colors">
                                <td class="py-3.5 px-6 font-semibold text-neutral-900 truncate max-w-[150px]" title="{{ $eval->student_name }}">{{ $eval->student_name }}</td>
                                <td class="py-3.5 px-4 text-neutral-600 font-medium truncate max-w-[130px]" title="{{ $eval->stall_name }}">{{ $eval->stall_name }}</td>
                                <td class="py-3.5 px-3 text-center text-neutral-500 tabular-nums text-xs">{{ number_format($eval->cleanliness, 1) }}</td>
                                <td class="py-3.5 px-3 text-center text-neutral-500 tabular-nums text-xs">{{ number_format($eval->service, 1) }}</td>
                                <td class="py-3.5 px-3 text-center text-neutral-500 tabular-nums text-xs">{{ number_format($eval->taste, 1) }}</td>
                                <td class="py-3.5 px-3 text-center text-neutral-500 tabular-nums text-xs">{{ number_format($eval->price, 1) }}</td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="font-bold tabular-nums text-xs {{ $avg >= 4 ? 'text-brand-700' : ($avg >= 3 ? 'text-amber-700' : 'text-red-600') }}">{{ number_format($avg, 1) }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-right text-neutral-400 text-xs tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans(null, true, true) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100 border-t border-neutral-100">
                    @foreach($recentEvaluations as $eval)
                        @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                        <div class="p-5 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-neutral-900 leading-tight truncate">{{ $eval->student_name }}</h3>
                                <p class="text-xs font-medium text-neutral-500 mt-0.5 truncate">{{ $eval->stall_name }}</p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="shrink-0 inline-flex items-center gap-1 bg-neutral-100/80 px-2.5 py-1 rounded-md text-xs font-bold text-neutral-900 border border-neutral-200/60">
                                {{ number_format($avg, 1) }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    @else
        {{-- Empty State Card --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 p-12 text-center shadow-sm">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-4 bg-brand-50 text-brand-700 border border-brand-100">
                <ion-icon name="bar-chart-outline" class="text-3xl text-brand-700"></ion-icon>
            </div>
            <p class="font-bold text-neutral-900 mb-1 text-base tracking-tight">No evaluation data yet</p>
            <p class="text-sm text-neutral-500 max-w-xs mx-auto leading-relaxed">
                Charts and statistics will appear here automatically once students begin submitting stall evaluations.
            </p>
        </div>
    @endif

</div>

<style>
    :root {
        --chart-color-1: oklch(0.48 0.15 155);
        --chart-color-1-alpha: oklch(0.48 0.15 155 / 0.85);
        --chart-color-1-border: oklch(0.40 0.13 155);
        
        --chart-color-2: oklch(0.60 0.14 155);
        --chart-color-2-alpha: oklch(0.60 0.14 155 / 0.80);
        --chart-color-2-border: oklch(0.52 0.14 155);
        
        --chart-color-3: oklch(0.74 0.14 155);
        --chart-color-3-alpha: oklch(0.74 0.14 155 / 0.75);
        --chart-color-3-border: oklch(0.65 0.13 155);
        
        --chart-color-4: oklch(0.87 0.08 155);
        --chart-color-4-alpha: oklch(0.87 0.08 155 / 0.80);
        --chart-color-4-border: oklch(0.78 0.10 155);

        --chart-line-bg: oklch(0.56 0.17 155 / 0.12);
        
        --pie-color-1: var(--chart-color-1);
        --pie-color-2: var(--chart-color-2);
        --pie-color-3: var(--chart-color-3);
        --pie-color-4: var(--chart-color-4);
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = 'Plus Jakarta Sans';

    // Cache computed styles once
    var rootStyles = getComputedStyle(document.documentElement);
    var c1 = rootStyles.getPropertyValue('--chart-color-1').trim();
    var c1a = rootStyles.getPropertyValue('--chart-color-1-alpha').trim();
    var c1b = rootStyles.getPropertyValue('--chart-color-1-border').trim();
    var c2 = rootStyles.getPropertyValue('--chart-color-2').trim();
    var c2a = rootStyles.getPropertyValue('--chart-color-2-alpha').trim();
    var c2b = rootStyles.getPropertyValue('--chart-color-2-border').trim();
    var c3 = rootStyles.getPropertyValue('--chart-color-3').trim();
    var c3a = rootStyles.getPropertyValue('--chart-color-3-alpha').trim();
    var c3b = rootStyles.getPropertyValue('--chart-color-3-border').trim();
    var c4 = rootStyles.getPropertyValue('--chart-color-4').trim();
    var c4a = rootStyles.getPropertyValue('--chart-color-4-alpha').trim();
    var c4b = rootStyles.getPropertyValue('--chart-color-4-border').trim();
    var cLineBg = rootStyles.getPropertyValue('--chart-line-bg').trim();

    var pieColors = [
        rootStyles.getPropertyValue('--pie-color-1').trim() || c1,
        rootStyles.getPropertyValue('--pie-color-2').trim() || c2,
        rootStyles.getPropertyValue('--pie-color-3').trim() || c3,
        rootStyles.getPropertyValue('--pie-color-4').trim() || c4
    ];

@if($results->isNotEmpty())
    // ── 1. Scalable Bar Chart with Dynamic N-Stall Range Filtering ─────────
    var rawStallData = @json($allStallResults);
    var stallScoresChart = null;
    var stallCtx = document.getElementById('stallScoresChart');
    var barWrapper = document.getElementById('barChartWrapper');
    var barTitle = document.getElementById('barChartTitle');
    var barSubtitle = document.getElementById('barChartSubtitle');

    function getSliceForRange(range) {
        var sorted = [...rawStallData];
        if (range === 'top5') {
            return {
                title: 'Top ' + Math.min(5, sorted.length) + ' Performing Stalls',
                subtitle: 'Top ' + Math.min(5, sorted.length) + ' vendors by composite rating across 4 criteria',
                items: sorted.sort((a, b) => b.avg - a.avg).slice(0, 5)
            };
        } else if (range === 'top10') {
            return {
                title: 'Top ' + Math.min(10, sorted.length) + ' Performing Stalls',
                subtitle: 'Top ' + Math.min(10, sorted.length) + ' vendors by composite rating across 4 criteria',
                items: sorted.sort((a, b) => b.avg - a.avg).slice(0, 10)
            };
        } else if (range === 'lowest5') {
            return {
                title: 'Lowest 5 Performing Stalls',
                subtitle: 'Vendors with lowest composite scores needing attention',
                items: sorted.sort((a, b) => a.avg - b.avg).slice(0, 5)
            };
        } else {
            return {
                title: 'All ' + sorted.length + ' Canteen Stalls',
                subtitle: 'Complete vendor performance comparison across 4 criteria',
                items: sorted.sort((a, b) => b.avg - a.avg)
            };
        }
    }

    function renderBarChart(range) {
        var configData = getSliceForRange(range);
        var items = configData.items;

        if (barTitle) barTitle.textContent = configData.title;
        if (barSubtitle) barSubtitle.textContent = configData.subtitle;

        var labels = items.map(function(s) { return s.name; });
        var cleanData = items.map(function(s) { return s.cleanliness; });
        var servData  = items.map(function(s) { return s.service; });
        var tasteData = items.map(function(s) { return s.taste; });
        var priceData = items.map(function(s) { return s.price; });

        if (!stallScoresChart && stallCtx) {
            stallScoresChart = new Chart(stallCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Cleanliness', data: cleanData, backgroundColor: c1a, borderColor: c1b, borderWidth: 1, borderRadius: 3, borderSkipped: false, maxBarThickness: 16 },
                        { label: 'Service',     data: servData,  backgroundColor: c2a, borderColor: c2b, borderWidth: 1, borderRadius: 3, borderSkipped: false, maxBarThickness: 16 },
                        { label: 'Taste',       data: tasteData, backgroundColor: c3a, borderColor: c3b, borderWidth: 1, borderRadius: 3, borderSkipped: false, maxBarThickness: 16 },
                        { label: 'Price',       data: priceData, backgroundColor: c4a, borderColor: c4b, borderWidth: 1, borderRadius: 3, borderSkipped: false, maxBarThickness: 16 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    datasets: {
                        bar: {
                            categoryPercentage: 0.82,
                            barPercentage: 0.90
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 11, weight: '600' },
                                color: '#52525b',
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'oklch(0.18 0.07 155)',
                            titleFont: { size: 12, weight: '700' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    return ' ' + c.dataset.label + ': ' + c.parsed.y.toFixed(2) + ' / 5.00';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11, weight: '600' },
                                color: '#374151',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 12,
                                callback: function(val) {
                                    var text = this.getLabelForValue(val) || '';
                                    return text.length > 14 ? text.substring(0, 12) + '…' : text;
                                }
                            },
                            border: { display: false }
                        },
                        y: {
                            min: 0,
                            max: 5,
                            grid: { color: '#f1f5f9' },
                            ticks: { stepSize: 1, font: { size: 10 }, color: '#94a3b8' },
                            border: { display: false }
                        }
                    }
                }
            });
        } else if (stallScoresChart) {
            stallScoresChart.data.labels = labels;
            stallScoresChart.data.datasets[0].data = cleanData;
            stallScoresChart.data.datasets[1].data = servData;
            stallScoresChart.data.datasets[2].data = tasteData;
            stallScoresChart.data.datasets[3].data = priceData;
            stallScoresChart.update();
        }
    }

    if (stallCtx) {
        renderBarChart('top5');

        // Wire range filter buttons
        document.querySelectorAll('.stall-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.stall-filter-btn').forEach(function(b) {
                    b.classList.remove('bg-white', 'text-neutral-900', 'shadow-2xs', 'font-bold');
                    b.classList.add('text-neutral-500', 'hover:text-neutral-900', 'font-medium');
                });
                btn.classList.add('bg-white', 'text-neutral-900', 'shadow-2xs', 'font-bold');
                btn.classList.remove('text-neutral-500', 'hover:text-neutral-900', 'font-medium');

                var range = btn.getAttribute('data-range');
                renderBarChart(range);
            });
        });
    }

    // ── 2. Line Chart: 30-Day Evaluation Activity ──────────────────────────
    var trendCtx = document.getElementById('evalTrendChart');
    if (trendCtx) {
        var trendDates  = @json($trendDates);
        var trendCounts = @json($trendCounts);
        var isMobile = window.innerWidth < 640;
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendDates,
                datasets: [{
                    label: 'Evaluations',
                    data: trendCounts,
                    borderColor: c1,
                    backgroundColor: cLineBg,
                    pointBackgroundColor: c1,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true
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
                            title: function(i) { return i[0].label; },
                            label: function(c) {
                                return ' ' + c.parsed.y + ' evaluation' + (c.parsed.y !== 1 ? 's' : '');
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
                            maxRotation: 0,
                            callback: function(v, i) {
                                return (isMobile ? i % 5 : i % 3) === 0 ? trendDates[i] : '';
                            }
                        },
                        border: { display: false }
                    },
                    y: {
                        min: 0,
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1, precision: 0, font: { size: 10 }, color: '#94a3b8' },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── 3. Evaluation Share Donut Chart with "Top 4 + Others" Cluster ────────
    var pieCtx = document.getElementById('evalPieChart');
    if (pieCtx) {
        var rawPie = @json($pieChartData);
        var sortedPie = [...rawPie].sort(function(a, b) { return b.count - a.count; });
        var totalSubmissions = sortedPie.reduce(function(sum, item) { return sum + item.count; }, 0);

        var finalPieLabels = [];
        var finalPieData   = [];

        if (sortedPie.length <= 5) {
            sortedPie.forEach(function(item) {
                finalPieLabels.push(item.name);
                finalPieData.push(item.count);
            });
        } else {
            // Keep top 4 distinct
            for (var k = 0; k < 4; k++) {
                finalPieLabels.push(sortedPie[k].name);
                finalPieData.push(sortedPie[k].count);
            }
            // Cluster the remainder into "Other Stalls"
            var otherCount = 0;
            for (var m = 4; m < sortedPie.length; m++) {
                otherCount += sortedPie[m].count;
            }
            finalPieLabels.push('Other Stalls (' + (sortedPie.length - 4) + ')');
            finalPieData.push(otherCount);
        }

        var donutColors = [
            'oklch(0.48 0.15 155)',
            'oklch(0.58 0.14 195)',
            'oklch(0.72 0.16 75)',
            'oklch(0.58 0.18 280)',
            'oklch(0.70 0.03 240)'
        ];

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: finalPieLabels,
                datasets: [{
                    data: finalPieData,
                    backgroundColor: donutColors.slice(0, finalPieLabels.length),
                    borderWidth: 2.5,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: '600' },
                            color: '#52525b',
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'oklch(0.18 0.07 155)',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(c) {
                                var val = c.parsed;
                                var pct = totalSubmissions > 0 ? ((val / totalSubmissions) * 100).toFixed(1) : 0;
                                return ' ' + c.label + ': ' + val + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
@endif
});
</script>
@endsection

@endsection