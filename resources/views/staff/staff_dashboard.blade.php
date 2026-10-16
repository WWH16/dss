@extends('layouts.dashboard')

@section('title', 'Staff Dashboard | Decision Support System')
{{-- CHANGED (whole file): rank chips follow the admin overview (#1 brand green, #2 and #3 neutral grey instead of amber, slate and bronze); the Overall Score and Unique Evaluators icon boxes went from amber and emerald to brand green; every 9px and 10px text became 11px; font-black and font-extrabold became font-bold; rounded-lg and plain rounded became rounded-md; grey helper text went from neutral-400 to neutral-500; the mobile criteria boxes lost their inner border; comments show in typographic quotes; the empty-state icon is brand green. --}}

@section('head')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
@endsection

@section('content')
{{-- CHANGED: tighter section spacing below 640px (space-y-4, was 6). --}}
<div class="max-w-6xl mx-auto space-y-4 sm:space-y-6">

    {{-- ── 1. Unassigned Stall State ───────────────────────────────────────── --}}
    @if(!$hasStall || !$stall)
        <div class="bg-white rounded-xl border border-neutral-200/80 p-8 sm:p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-xl bg-amber-50 border border-amber-200/80 flex items-center justify-center mb-4 text-amber-700 mx-auto">
                <ion-icon name="shield-outline" class="text-3xl"></ion-icon>
            </div>
            <h2 class="text-xl font-bold text-neutral-900 tracking-tight mb-2">Account Pending Stall Assignment</h2>
            <p class="text-xs sm:text-sm text-neutral-500 max-w-md mx-auto leading-relaxed mb-6">
                Your staff account is pending verification and assignment to a food stall by an Administrator. Evaluation records and campus performance standings are restricted until your stall assignment is active.
            </p>
            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="{{ route('staff.profile') }}" class="btn btn-secondary text-xs px-4 py-2 min-h-11 sm:min-h-0 rounded-md font-bold inline-flex items-center gap-1.5 shadow-2xs border border-neutral-200">
                    <ion-icon name="person-outline" class="text-sm"></ion-icon>
                    View Account Profile
                </a>
            </div>
        </div>
    @else

        {{-- ── 2. Page Header with Assigned Stall Scope ───────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-neutral-200/70">
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">{{ $stall->name }}</h1>
                    {{-- CHANGED: the rank chip needs at least 2 other rated stalls, the same rule as the benchmark; "#1 on Campus" out of 1 or 2 stalls said little. --}}
                    @if($stallRank && $campusCriteria)
                        @if($stallRank === 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-900 border border-brand-200 font-bold text-xs shadow-2xs">
                                <ion-icon name="trophy" class="text-brand-600 text-xs"></ion-icon>
                                #1 on Campus
                            </span>
                        @elseif($stallRank === 2)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-neutral-500 text-xs"></ion-icon>
                                #2 on Campus
                            </span>
                        @elseif($stallRank === 3)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-neutral-400 text-xs"></ion-icon>
                                #3 on Campus
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-700 font-semibold text-xs border border-neutral-200">
                                Rank #{{ $stallRank }} of {{ $totalStalls }}
                            </span>
                        @endif
                    @endif
                </div>
                <p class="text-xs text-neutral-500">
                    Canteen Staff Portal • Evaluation analytics for <strong class="text-neutral-800 font-semibold">{{ $stall->name }}</strong>
                </p>
            </div>

            {{-- CHANGED: buttons on this page are 44px tall below 640px, for touch. --}}
            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <a href="{{ route('staff.standings') }}" class="btn btn-secondary text-xs px-3.5 py-2 min-h-11 sm:min-h-0 rounded-md font-bold inline-flex items-center gap-1.5 border border-neutral-200 shadow-2xs hover:bg-neutral-50">
                    <ion-icon name="podium-outline" class="text-sm text-neutral-600"></ion-icon>
                    Campus Standings
                </a>
            </div>
        </div>

        {{-- ── 3. Stall Metric Cards ───────────────────────────────────────── --}}
        {{-- CHANGED: the decorative icon boxes hide below 640px to give the labels room. With the This Month card: four across from 1024px and two by two below, phones included, so the four numbers fit in one screen. The footer sits at the bottom (mt-auto, was justify-between) so the numbers line up when a label or footer wraps. --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Overall Average --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Overall Score</span>
                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-100/70 hidden sm:flex items-center justify-center text-brand-700">
                        <ion-icon name="star" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="flex flex-wrap items-baseline gap-x-1.5 mb-2.5">
                    <span class="text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight">
                        {{ number_format($averages ? (float)$averages->overall : 0, 2) }}★
                    </span>
                    <span class="text-xs text-neutral-500 font-semibold">/ 5.00</span>
                </div>
                {{-- CHANGED: compares with the other stalls only, and says so when there are too few of them. --}}
                <div class="mt-auto pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    @if($campusCriteria)
                        Avg of other stalls: <strong class="text-neutral-700 font-bold">{{ number_format($campusCriteria->overall, 2) }}★</strong>
                    @else
                        Not enough campus data yet
                    @endif
                </div>
            </div>

            {{-- ADDED: students who rated this stall this month. A student rates a stall once a month, so the number restarts each month. --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">This Month</span>
                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-100/70 hidden sm:flex items-center justify-center text-brand-700">
                        <ion-icon name="calendar-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="mb-2.5 text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight">
                    {{ $monthStudents }}
                </div>
                <div class="mt-auto pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    {{ Str::plural('Student', $monthStudents) }} rated you in {{ now('Asia/Manila')->format('F') }}
                </div>
            </div>

            {{-- Total Evaluations --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Total Evaluations</span>
                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-100/70 hidden sm:flex items-center justify-center text-brand-700">
                        <ion-icon name="receipt-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="mb-2.5 text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight">
                    {{ $totalEvaluations }}
                </div>
                <div class="mt-auto pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    Student reviews logged
                </div>
            </div>

            {{-- Unique Student Evaluators --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Unique Evaluators</span>
                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-100/70 hidden sm:flex items-center justify-center text-brand-700">
                        <ion-icon name="people-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="mb-2.5 text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight">
                    {{ $uniqueStudents }}
                </div>
                <div class="mt-auto pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    Individual students
                </div>
            </div>
        </div>

        {{-- ── 4. Primary Chart Card: Evaluation Activity Timeline (Full Width) ──── --}}
        @if($totalEvaluations > 0 && $averages)
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-neutral-100 gap-3">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="pulse-outline" class="text-brand-700 text-base"></ion-icon>
                            Evaluation Activity Timeline
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            {{ $activityPeriodLabel }} • <strong class="text-neutral-800 font-semibold tabular-nums">{{ $activityTotalCount }}</strong> {{ $activityTotalCount === 1 ? 'submission logged' : 'submissions logged' }}
                        </p>
                    </div>

                    {{-- Month / Year Filter Controls --}}
                    {{-- CHANGED: the year select shows only for a month or whole-year view; it never applied to the rolling 30 days. --}}
                    {{-- CHANGED: an Apply button submits the filter instead of each select's change, which reloaded the page on every arrow key and did nothing without JavaScript. Picking a period only shows or hides the year select. On the narrowest phones the button wraps to a full-width row of its own. --}}
                    {{-- CHANGED: below 640px the filters fill the row as 44px selects with 16px text (iOS zooms the page on focus below 16px). --}}
                    <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                        <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto bg-neutral-50 border border-neutral-200/90 rounded-md p-1">
                            <select name="activity_month" onchange="this.form.activity_year.hidden = this.value === '30_days'" aria-label="Filter activity by month"
                                class="flex-1 sm:flex-none min-h-11 sm:min-h-0 bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-base sm:text-xs font-semibold text-neutral-700 shadow-2xs focus:outline-none focus:border-brand-700">
                                <option value="30_days" {{ $selectedMonth === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="all" {{ $selectedMonth === 'all' ? 'selected' : '' }}>Whole Year</option>
                                <optgroup label="Month">
                                    @foreach([
                                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                    ] as $mNum => $mName)
                                        <option value="{{ $mNum }}" {{ (string)$selectedMonth === (string)$mNum ? 'selected' : '' }}>{{ $mName }}</option>
                                    @endforeach
                                </optgroup>
                            </select>

                            <select name="activity_year" aria-label="Filter activity by year" @if($selectedMonth === '30_days') hidden @endif
                                class="flex-1 sm:flex-none min-h-11 sm:min-h-0 bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-base sm:text-xs font-semibold text-neutral-700 shadow-2xs focus:outline-none focus:border-brand-700">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer min-h-11 sm:min-h-0 grow sm:grow-0">Apply</button>
                        </div>
                    </form>
                </div>

                <div class="relative w-full" style="height: 220px;">
                    <canvas id="stallTrendChart" role="img" aria-label="Line chart showing evaluation submissions for your stall for {{ $activityPeriodLabel }}"></canvas>
                </div>
            </div>

            {{-- ── 5. Secondary Analytics Grid: Benchmark Comparison & Rating Breakdown ── --}}
            {{-- CHANGED: one criteria list feeds the cards. The tags read Highest / Lowest, since they rank this stall's own criteria rather than the gap to campus, and show only when the scores differ. --}}
            @php
                $criteria = [
                    ['name' => 'Cleanliness', 'score' => (float)$averages->cleanliness, 'camp' => $campusCriteria?->cleanliness],
                    ['name' => 'Service',     'score' => (float)$averages->service,     'camp' => $campusCriteria?->service],
                    ['name' => 'Taste',       'score' => (float)$averages->taste,       'camp' => $campusCriteria?->taste],
                    ['name' => 'Price',       'score' => (float)$averages->price,       'camp' => $campusCriteria?->price],
                ];
                $topScore = max(array_column($criteria, 'score'));
                $lowScore = min(array_column($criteria, 'score'));
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-5 items-stretch">

                {{-- LEFT: Criteria Benchmark Comparison (3 Cols) --}}
                {{-- CHANGED: the grouped bar chart that repeated these four values is gone. Each card now carries a bar of its score out of 5, with a marker at the other stalls' average. --}}
                {{-- CHANGED: the criterion cards stay 2x2 on phones (were 1 per row), and the tag and the difference wrap under when space runs out. --}}
                <div class="lg:col-span-3 bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col">
                    <div class="pb-3 border-b border-neutral-100">
                        <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="bar-chart-outline" class="text-brand-700 text-base"></ion-icon>
                            Benchmark Comparison
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            @if($campusCriteria)
                                Your average per criterion out of 5. The dark marker is the average of the other {{ $campusCriteria->stalls }} rated stalls.
                            @else
                                Your average per criterion out of 5. Not enough campus data yet: comparisons need at least 2 other rated stalls.
                            @endif
                        </p>
                    </div>

                    <div class="grid grid-cols-2 auto-rows-fr gap-2.5 mt-3.5 flex-1">
                        @foreach($criteria as $item)
                            @php
                                $isTop = $topScore > $lowScore && $item['score'] === $topScore;
                                $isLow = $topScore > $lowScore && $item['score'] === $lowScore;
                                $delta = $item['camp'] !== null ? round($item['score'] - $item['camp'], 2) : null;
                            @endphp
                            <div class="p-3 bg-neutral-50/80 border {{ $isTop ? 'border-emerald-200/90 ring-1 ring-emerald-200/60' : ($isLow ? 'border-amber-200/90 ring-1 ring-amber-200/60' : 'border-neutral-200/80') }} rounded-md flex flex-col justify-between gap-3">
                                <div class="flex flex-wrap items-center justify-between gap-1">
                                    <span class="text-xs font-semibold text-neutral-600">{{ $item['name'] }}</span>
                                    @if($isTop)
                                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/80 px-1.5 py-0.5 rounded-md shrink-0">Highest</span>
                                    @elseif($isLow)
                                        <span class="text-[11px] font-bold text-amber-800 bg-amber-100/80 px-1.5 py-0.5 rounded-md shrink-0">Lowest</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-baseline justify-between gap-x-2 gap-y-0.5">
                                    <span class="text-base sm:text-lg font-bold text-neutral-900 tabular-nums">{{ number_format($item['score'], 2) }}★</span>
                                    @if($delta !== null)
                                        <span class="text-[11px] font-bold tabular-nums {{ $delta > 0 ? 'text-emerald-700' : ($delta < 0 ? 'text-amber-700' : 'text-neutral-500') }}">
                                            {{ $delta > 0 ? '+' : ($delta < 0 ? '-' : '') }}{{ number_format(abs($delta), 2) }} vs others
                                        </span>
                                    @endif
                                </div>
                                <div class="relative h-2 rounded-full bg-neutral-200/70" aria-hidden="true">
                                    <div class="h-full rounded-full bg-brand-600" style="width: {{ $item['score'] / 5 * 100 }}%"></div>
                                    @if($item['camp'] !== null)
                                        <div class="absolute -top-1 -bottom-1 w-0.5 rounded-full bg-neutral-900" style="left: calc({{ $item['camp'] / 5 * 100 }}% - 1px)"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: Rating Distribution (2 Cols) --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-neutral-100">
                            <div>
                                <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Rating Breakdown</h2>
                                {{-- CHANGED: states the bucketing rule the query uses (ROUND of each review's 4-criteria mean). --}}
                                <p class="text-xs text-neutral-500 mt-0.5">Each review's average, rounded to the nearest star</p>
                            </div>
                            <span class="text-xs font-bold text-neutral-800 tabular-nums bg-neutral-100 px-2 py-0.5 rounded-md">{{ number_format($averages ? (float)$averages->overall : 0, 2) }}★</span>
                        </div>

                        @php
                            $s5 = $ratingDistribution ? (int)$ratingDistribution->stars_5 : 0;
                            $s4 = $ratingDistribution ? (int)$ratingDistribution->stars_4 : 0;
                            $s3 = $ratingDistribution ? (int)$ratingDistribution->stars_3 : 0;
                            $s2 = $ratingDistribution ? (int)$ratingDistribution->stars_2 : 0;
                            $s1 = $ratingDistribution ? (int)$ratingDistribution->stars_1 : 0;
                        @endphp

                        <div class="space-y-3 pt-1">
                            @foreach([
                                ['label' => '5 Stars', 'stars' => '5★', 'count' => $s5, 'color' => 'bg-emerald-500'],
                                ['label' => '4 Stars', 'stars' => '4★', 'count' => $s4, 'color' => 'bg-emerald-400'],
                                ['label' => '3 Stars', 'stars' => '3★', 'count' => $s3, 'color' => 'bg-amber-400'],
                                ['label' => '2 Stars', 'stars' => '2★', 'count' => $s2, 'color' => 'bg-orange-400'],
                                ['label' => '1 Star',  'stars' => '1★', 'count' => $s1, 'color' => 'bg-rose-400'],
                            ] as $starRow)
                                @php
                                    $pct = $totalEvaluations > 0 ? round(($starRow['count'] / $totalEvaluations) * 100) : 0;
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-neutral-700 text-xs">{{ $starRow['label'] }}</span>
                                        <div class="flex items-center gap-1.5">
                                            {{-- CHANGED: body font and neutral-600 instead of light grey monospace. --}}
                                            <span class="text-[11px] font-semibold text-neutral-600 tabular-nums">{{ $starRow['count'] }} ({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-neutral-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full {{ $starRow['color'] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- CHANGED: reads "Based on N reviews" so a small sample is plain next to the bars. --}}
                    <div class="mt-4 pt-3 border-t border-neutral-100 text-[11px] text-neutral-500">
                        Based on <strong class="text-neutral-800 tabular-nums font-semibold">{{ $totalEvaluations }}</strong> {{ Str::plural('review', $totalEvaluations) }}
                    </div>
                </div>

            </div>
        @endif

        {{-- CHANGED: the evaluations list moved to partials/stall-evaluations.blade.php; the staff standings page includes the same partial. --}}
        @include('partials.stall-evaluations')

    @endif
</div>

@if($hasStall && $stall && $totalEvaluations > 0)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = 'Plus Jakarta Sans';

    // ── 1. 30-Day Evaluation Activity Timeline Chart ─────────────────────────
    var trendCtx = document.getElementById('stallTrendChart');
    if (trendCtx) {
        var trendDates  = @json($trendDates ?? []);
        var trendCounts = @json($trendCounts ?? []);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendDates,
                datasets: [{
                    label: 'Evaluations',
                    data: trendCounts,
                    borderColor: 'rgb(22, 101, 52)',
                    backgroundColor: 'rgba(22, 101, 52, 0.08)',
                    borderWidth: 2.2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2.5,
                    pointHoverRadius: 5.5,
                    pointBackgroundColor: 'rgb(22, 101, 52)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                // CHANGED: a tap anywhere above a day shows its tooltip; the points alone were too small to hit on touch.
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.parsed.y + ' ' + (ctx.parsed.y === 1 ? 'evaluation' : 'evaluations');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#6b7280',
                            // CHANGED: 5 date labels on phones (was 8 at every width), so they do not crowd.
                            maxTicksLimit: window.matchMedia('(min-width: 640px)').matches ? 8 : 5
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#6b7280',
                            stepSize: 1,
                            precision: 0
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endsection
@endif
@endsection