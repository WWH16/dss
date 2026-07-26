@extends('layouts.dashboard')
@section('title', 'Overview | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Overview</h1>
        <p class="text-neutral-500 text-sm">Welcome back, <span class="font-semibold text-brand-700">{{ Auth::user()->name }}</span></p>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Metric Cards --}}
    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm mb-6 flex flex-col sm:flex-row sm:divide-x divide-neutral-100 overflow-hidden">
        @foreach([
            ['label' => 'Total Students',   'value' => $studentCount,    'icon' => 'group'],
            ['label' => 'Total Stalls',      'value' => $stallCount,      'icon' => 'storefront'],
            ['label' => 'Total Evaluations', 'value' => $evaluationCount, 'icon' => 'rate_review'],
        ] as $stat)
            <div class="flex-1 p-5 lg:p-6 flex items-center gap-5 hover:bg-neutral-50/50 transition-colors">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-3 rounded-xl text-2xl leading-none">{{ $stat['icon'] }}</span>
                <div>
                    <span class="text-[11px] text-neutral-500 block uppercase tracking-wider font-semibold">{{ $stat['label'] }}</span>
                    <span class="text-3xl font-bold text-neutral-900 tabular-nums leading-none mt-1 block tracking-tight">{{ $stat['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Dashboard Data (Bento Grid) --}}
    @if($results->isNotEmpty())
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Top Row: Activity (8) + Share (4) --}}
        <div class="lg:col-span-8 bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-base tracking-tight">Evaluation Activity</h2>
            </div>
            <div class="relative w-full" style="height: 260px;">
                <canvas id="evalTrendChart" role="img" aria-label="Line chart showing the number of evaluations over the last 30 days">
                    <p>Line chart showing the number of evaluations over the last 30 days. Please use the data tables below for accessible data.</p>
                </canvas>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-base tracking-tight">Evaluation Share</h2>
            </div>
            <div class="relative w-full" style="height: 260px;">
                <canvas id="evalPieChart" role="img" aria-label="Pie chart showing the distribution of evaluations per stall">
                    <p>Pie chart showing the distribution of evaluations per stall. Please use the data tables below for accessible data.</p>
                </canvas>
            </div>
        </div>

        {{-- Middle Row: Average Scores (7) + Performance Bar (5) --}}
        <div class="lg:col-span-7 bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-base tracking-tight">Average Stall Scores</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[400px]">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="pb-2">Food Stall</th>
                            <th class="pb-2 text-center">Cleanliness</th>
                            <th class="pb-2 text-center">Service</th>
                            <th class="pb-2 text-center">Taste</th>
                            <th class="pb-2 text-center">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach($results as $result)
                            <tr class="text-sm hover:bg-neutral-50/50 transition-colors">
                                <td class="py-3 font-bold text-neutral-900">{{ $result->name }}</td>
                                <td class="py-3 text-center font-bold tabular-nums {{ $result->cleanliness >= 4 ? 'text-brand-600' : ($result->cleanliness >= 3 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($result->cleanliness, 2) }}</td>
                                <td class="py-3 text-center font-bold tabular-nums {{ $result->service >= 4 ? 'text-brand-600' : ($result->service >= 3 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($result->service, 2) }}</td>
                                <td class="py-3 text-center font-bold tabular-nums {{ $result->taste >= 4 ? 'text-brand-600' : ($result->taste >= 3 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($result->taste, 2) }}</td>
                                <td class="py-3 text-center font-bold tabular-nums {{ $result->price >= 4 ? 'text-brand-600' : ($result->price >= 3 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($result->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-base tracking-tight">Stall Performance</h2>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="stallScoresChart" role="img" aria-label="Bar chart showing the average scores per stall">
                    <p>Bar chart showing the average scores per stall for Cleanliness, Service, Taste, and Price.</p>
                </canvas>
            </div>
        </div>

        {{-- Bottom Row: Recent Evaluations (12) --}}
        @if($recentEvaluations->isNotEmpty())
        <div class="lg:col-span-12 bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-base tracking-tight">Recent Evaluations</h2>
                <a href="{{ route('admin.evaluations') }}" class="relative text-[11px] text-brand-600 hover:text-brand-700 font-bold uppercase inline-flex items-center gap-0.5 transition-colors before:absolute before:inset-[-12px]">
                    View All <span class="material-symbols-outlined text-[14px] leading-none" aria-hidden="true">arrow_forward</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="pb-2">Student</th>
                            <th class="pb-2">Stall</th>
                            <th class="pb-2 text-center">Cleanliness</th>
                            <th class="pb-2 text-center">Service</th>
                            <th class="pb-2 text-center">Taste</th>
                            <th class="pb-2 text-center">Price</th>
                            <th class="pb-2 text-center">Avg Score</th>
                            <th class="pb-2 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach($recentEvaluations as $eval)
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <tr class="text-sm hover:bg-neutral-50/50 transition-colors">
                                <td class="py-3 font-semibold text-neutral-900 truncate max-w-[120px]" title="{{ $eval->student_name }}">{{ $eval->student_name }}</td>
                                <td class="py-3 text-neutral-500 font-medium truncate max-w-[100px]" title="{{ $eval->stall_name }}">{{ $eval->stall_name }}</td>
                                <td class="py-3 text-center text-neutral-500 tabular-nums">{{ number_format($eval->cleanliness, 1) }}</td>
                                <td class="py-3 text-center text-neutral-500 tabular-nums">{{ number_format($eval->service, 1) }}</td>
                                <td class="py-3 text-center text-neutral-500 tabular-nums">{{ number_format($eval->taste, 1) }}</td>
                                <td class="py-3 text-center text-neutral-500 tabular-nums">{{ number_format($eval->price, 1) }}</td>
                                <td class="py-3 text-center font-bold tabular-nums {{ $avg >= 4 ? 'text-brand-600' : ($avg >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ number_format($avg, 1) }}
                                </td>
                                <td class="py-3 text-right text-neutral-400 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans(null, true, true) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
    @else
        <div class="p-6">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6 rounded-xl border border-dashed border-brand-200 bg-brand-50/30">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5 bg-white shadow-sm border border-brand-100">
                    <span class="material-symbols-outlined text-3xl text-brand-600">bar_chart</span>
                </div>
                <p class="font-bold text-neutral-900 mb-2 text-lg tracking-tight">No chart data yet</p>
                <p class="text-sm text-neutral-500 max-w-sm leading-relaxed">
                    Charts and insights will automatically appear here once students begin submitting their stall evaluations.
                </p>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = 'Plus Jakarta Sans';

// Get global styles
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

@if($results->isNotEmpty())
// Bar Chart
(function() {
    var ctx = document.getElementById('stallScoresChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($results->pluck('name')),
            datasets: [
                { label: 'Cleanliness', data: @json($results->pluck('cleanliness')->map(fn($v) => round($v,2))), backgroundColor: c1a, borderColor: c1b, borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Service',     data: @json($results->pluck('service')->map(fn($v) => round($v,2))),     backgroundColor: c2a, borderColor: c2b, borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Taste',       data: @json($results->pluck('taste')->map(fn($v) => round($v,2))),       backgroundColor: c3a, borderColor: c3b, borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Price',       data: @json($results->pluck('price')->map(fn($v) => round($v,2))),       backgroundColor: c4a, borderColor: c4b, borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11, weight: '600' }, color: '#52525b', padding: 14, usePointStyle: true, pointStyle: 'rectRounded' } },
                tooltip: { backgroundColor: 'oklch(0.18 0.07 155)', titleFont: { size: 12, weight: '700' }, bodyFont: { size: 11 }, padding: 10, cornerRadius: 8, callbacks: { label: function(c) { return ' ' + c.dataset.label + ': ' + c.parsed.y.toFixed(2) + ' / 5.00'; } } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#374151' }, border: { display: false } },
                y: { min: 0, max: 5, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1, font: { size: 10 }, color: '#94a3b8' }, border: { display: false } }
            }
        }
    });
})();

// Line Chart
(function() {
    var ctx = document.getElementById('evalTrendChart');
    if (!ctx) return;
    var trendDates  = @json($trendDates);
    var trendCounts = @json($trendCounts);
    var isMobile = window.innerWidth < 640;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendDates,
            datasets: [{ label: 'Evaluations', data: trendCounts, borderColor: c1, backgroundColor: cLineBg, pointBackgroundColor: c1, pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, borderWidth: 2.5, tension: 0.4, fill: true }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'oklch(0.18 0.07 155)', titleFont: { size: 12, weight: '700' }, bodyFont: { size: 11 }, padding: 10, cornerRadius: 8, callbacks: { title: function(i) { return i[0].label; }, label: function(c) { return ' ' + c.parsed.y + ' evaluation' + (c.parsed.y !== 1 ? 's' : ''); } } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 0, callback: function(v,i) { return (isMobile ? i%5 : i%3) === 0 ? trendDates[i] : ''; } }, border: { display: false } },
                y: { min: 0, beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1, precision: 0, font: { size: 10 }, color: '#94a3b8' }, border: { display: false } }
            }
        }
    });
})();

// Pie Chart
(function() {
    var ctx = document.getElementById('evalPieChart');
    if (!ctx) return;
    var pieData = @json($pieChartData);
    
    var rootStyles = getComputedStyle(document.documentElement);
    var pieColors = [
        rootStyles.getPropertyValue('--pie-color-1').trim(),
        rootStyles.getPropertyValue('--pie-color-2').trim(),
        rootStyles.getPropertyValue('--pie-color-3').trim(),
        rootStyles.getPropertyValue('--pie-color-4').trim()
    ];

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: pieData.map(function(item) { return item.name; }),
            datasets: [{
                data: pieData.map(function(item) { return item.count; }),
                backgroundColor: pieColors,
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11, weight: '600' }, color: '#52525b', padding: 14, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: {
                    backgroundColor: 'oklch(0.18 0.07 155)',
                    titleFont: { size: 12, weight: '700' }, bodyFont: { size: 11 },
                    padding: 10, cornerRadius: 8,
                    callbacks: {
                        label: function(c) {
                            return ' ' + c.label + ': ' + c.parsed + ' evaluation' + (c.parsed !== 1 ? 's' : '');
                        }
                    }
                }
            }
        }
    });
})();
@endif
</script>
@endsection

@endsection