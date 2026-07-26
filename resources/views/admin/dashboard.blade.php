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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach([
            ['label' => 'Total Students',   'value' => $studentCount,    'icon' => 'group'],
            ['label' => 'Total Stalls',      'value' => $stallCount,      'icon' => 'storefront'],
            ['label' => 'Total Evaluations', 'value' => $evaluationCount, 'icon' => 'rate_review'],
        ] as $stat)
            <div class="bg-white rounded-xl border border-neutral-200/60 p-5 flex items-center gap-4 shadow-sm">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-lg text-xl leading-none">{{ $stat['icon'] }}</span>
                <div>
                    <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold">{{ $stat['label'] }}</span>
                    <span class="text-2xl font-extrabold text-neutral-900 tabular-nums leading-none mt-1 block">{{ $stat['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    @if($results->isNotEmpty())
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Stall Performance</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Avg score / 5.0</span>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="stallScoresChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation Activity</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Last 30 days</span>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="evalTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation Share</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Per Stall</span>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="evalPieChart"></canvas>
            </div>
        </div>
    </div>
    @else
        <div class="bg-white rounded-xl border border-neutral-200/60 p-12 text-center shadow-sm">
            <div class="w-14 h-14 rounded-full bg-brand-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-2xl text-brand-500">bar_chart</span>
            </div>
            <p class="font-semibold text-neutral-700 mb-1">No chart data yet</p>
            <p class="text-sm text-neutral-400">Charts will appear once students submit evaluations.</p>
        </div>
    @endif

    {{-- Data Tables --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        
        {{-- Average Scores Table --}}
        @if($results->isNotEmpty())
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Average Stall Scores</h2>
                <span class="text-[10px] text-neutral-400 font-bold uppercase">Out of 5.0 max</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[400px]">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100">
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
        @endif

        {{-- Recent Evaluations Table --}}
        @if($recentEvaluations->isNotEmpty())
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-5 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Recent Evaluations</h2>
                <a href="{{ route('admin.evaluations') }}" class="text-[10px] text-brand-600 hover:text-brand-700 font-bold uppercase inline-flex items-center gap-0.5 transition-colors">
                    View All <span class="material-symbols-outlined text-[12px] leading-none">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse min-w-[400px]">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="pb-2">Student</th>
                            <th class="pb-2">Stall</th>
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
                                <td class="py-3 text-center font-bold tabular-nums {{ $avg >= 4 ? 'text-brand-600' : ($avg >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ number_format($avg, 1) }}
                                </td>
                                <td class="py-3 text-right text-neutral-400 text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans(null, true, true) }} ago
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = 'Plus Jakarta Sans';

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
                { label: 'Cleanliness', data: @json($results->pluck('cleanliness')->map(fn($v) => round($v,2))), backgroundColor: 'oklch(0.48 0.15 155 / 0.85)', borderColor: 'oklch(0.40 0.13 155)', borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Service',     data: @json($results->pluck('service')->map(fn($v) => round($v,2))),     backgroundColor: 'oklch(0.60 0.14 155 / 0.80)', borderColor: 'oklch(0.52 0.14 155)', borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Taste',       data: @json($results->pluck('taste')->map(fn($v) => round($v,2))),       backgroundColor: 'oklch(0.74 0.14 155 / 0.75)', borderColor: 'oklch(0.65 0.13 155)', borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
                { label: 'Price',       data: @json($results->pluck('price')->map(fn($v) => round($v,2))),       backgroundColor: 'oklch(0.87 0.08 155 / 0.80)', borderColor: 'oklch(0.78 0.10 155)', borderWidth: 1.5, borderRadius: 4, borderSkipped: false },
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
            datasets: [{ label: 'Evaluations', data: trendCounts, borderColor: 'oklch(0.48 0.15 155)', backgroundColor: 'oklch(0.56 0.17 155 / 0.12)', pointBackgroundColor: 'oklch(0.48 0.15 155)', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, borderWidth: 2.5, tension: 0.4, fill: true }]
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
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: pieData.map(function(item) { return item.name; }),
            datasets: [{
                data: pieData.map(function(item) { return item.count; }),
                backgroundColor: [
                    'oklch(0.48 0.15 155)',
                    'oklch(0.60 0.14 155)',
                    'oklch(0.74 0.14 155)',
                    'oklch(0.87 0.08 155)'
                ],
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