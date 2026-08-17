@extends('layouts.dashboard')

@section('title', 'Staff Dashboard | Decision Support System')

@section('head')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

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
                <a href="{{ route('staff.profile') }}" class="btn btn-secondary text-xs px-4 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs border border-neutral-200">
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
                    @if($stallRank)
                        @if($stallRank === 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-950 border border-amber-300 font-extrabold text-xs shadow-2xs">
                                <ion-icon name="trophy" class="text-amber-600 text-xs"></ion-icon>
                                #1 on Campus
                            </span>
                        @elseif($stallRank === 2)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon>
                                #2 on Campus
                            </span>
                        @elseif($stallRank === 3)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-600/30 font-bold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon>
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

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <a href="{{ route('staff.standings') }}" class="btn btn-secondary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 border border-neutral-200 shadow-2xs hover:bg-neutral-50">
                    <ion-icon name="podium-outline" class="text-sm text-neutral-600"></ion-icon>
                    Campus Standings
                </a>
            </div>
        </div>

        {{-- ── 3. Stall Metric Cards ───────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Overall Average --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Overall Score</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200/70 flex items-center justify-center text-amber-700">
                        <ion-icon name="star" class="text-base text-amber-500"></ion-icon>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                        {{ number_format($averages ? (float)$averages->overall : 0, 2) }}★
                    </span>
                    <span class="text-xs text-neutral-400 font-semibold">/ 5.00</span>
                </div>
                @if($campusCriteria)
                    <div class="mt-2.5 pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                        Campus avg: <strong class="text-neutral-700 font-bold">{{ number_format($campusCriteria->overall, 2) }}★</strong>
                    </div>
                @endif
            </div>

            {{-- Total Evaluations --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Total Evaluations</span>
                    <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-100/70 flex items-center justify-center text-brand-700">
                        <ion-icon name="receipt-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $totalEvaluations }}
                </div>
                <div class="mt-2.5 pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    Student reviews logged
                </div>
            </div>

            {{-- Unique Student Evaluators --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Unique Evaluators</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200/70 flex items-center justify-center text-emerald-700">
                        <ion-icon name="people-outline" class="text-base"></ion-icon>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $uniqueStudents }}
                </div>
                <div class="mt-2.5 pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                    Individual students
                </div>
            </div>
        </div>

        {{-- ── 4. Primary Chart Card: Evaluation Activity Timeline (Full Width) ──── --}}
        @if($totalEvaluations > 0 && $averages)
            <div class="bg-white rounded-xl border border-neutral-200/80 p-5 sm:p-6 shadow-xs">
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
                    <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                        <div class="flex items-center gap-1.5 bg-neutral-50 border border-neutral-200/90 rounded-lg p-1">
                            <select name="activity_month" onchange="this.form.submit()" aria-label="Filter activity by month"
                                class="bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-xs font-semibold text-neutral-700 shadow-2xs focus:outline-none focus:border-brand-700">
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

                            <select name="activity_year" onchange="this.form.submit()" aria-label="Filter activity by year"
                                class="bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-xs font-semibold text-neutral-700 shadow-2xs focus:outline-none focus:border-brand-700">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="relative w-full" style="height: 220px;">
                    <canvas id="stallTrendChart" role="img" aria-label="Line chart showing evaluation submissions for your stall for {{ $activityPeriodLabel }}"></canvas>
                </div>
            </div>

            {{-- ── 5. Secondary Analytics Grid: Benchmark Comparison & Rating Breakdown ── --}}
            @php
                $cleanVal = (float)$averages->cleanliness;
                $servVal  = (float)$averages->service;
                $tstVal   = (float)$averages->taste;
                $prcVal   = (float)$averages->price;

                $critMap = [
                    'Cleanliness' => $cleanVal,
                    'Service'     => $servVal,
                    'Taste'       => $tstVal,
                    'Price'       => $prcVal,
                ];
                arsort($critMap);
                $topCritName = array_key_first($critMap);
                $lowestCritName = array_key_last($critMap);
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-stretch">

                {{-- LEFT: Criteria Benchmark Comparison (3 Cols) --}}
                <div class="lg:col-span-3 bg-white rounded-xl border border-neutral-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
                            <div>
                                <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                                    <ion-icon name="bar-chart-outline" class="text-brand-700 text-base"></ion-icon>
                                    Benchmark Comparison
                                </h2>
                                <p class="text-xs text-neutral-500 mt-0.5">Your ratings vs. campus average per criterion</p>
                            </div>
                            @if($campusCriteria)
                                <span class="text-[11px] font-semibold text-neutral-500 bg-neutral-100 px-2 py-0.5 rounded-md tabular-nums">
                                    Campus Avg: {{ number_format($campusCriteria->overall, 2) }}★
                                </span>
                            @endif
                        </div>

                        {{-- Criteria Summary Chips with Direct Strength / Focus Badging --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-3.5">
                            @foreach([
                                ['name' => 'Cleanliness', 'score' => (float)$averages->cleanliness, 'camp' => $campusCriteria ? (float)$campusCriteria->cleanliness : 0],
                                ['name' => 'Service',     'score' => (float)$averages->service,     'camp' => $campusCriteria ? (float)$campusCriteria->service : 0],
                                ['name' => 'Taste',       'score' => (float)$averages->taste,       'camp' => $campusCriteria ? (float)$campusCriteria->taste : 0],
                                ['name' => 'Price',       'score' => (float)$averages->price,       'camp' => $campusCriteria ? (float)$campusCriteria->price : 0],
                            ] as $item)
                                @php
                                    $delta = $item['score'] - $item['camp'];
                                    $isTop = ($item['name'] === $topCritName);
                                    $isLow = ($item['name'] === $lowestCritName && $topCritName !== $lowestCritName);
                                @endphp
                                <div class="p-2.5 bg-neutral-50/80 border {{ $isTop ? 'border-emerald-200/90 ring-1 ring-emerald-200/60' : ($isLow ? 'border-amber-200/90 ring-1 ring-amber-200/60' : 'border-neutral-200/80') }} rounded-lg flex flex-col justify-between hover:border-neutral-300 transition-colors">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="text-[11px] font-semibold text-neutral-600 truncate">{{ $item['name'] }}</span>
                                        @if($isTop)
                                            <span class="text-[9px] font-bold text-emerald-800 bg-emerald-100/80 px-1.5 py-0.5 rounded shrink-0">Strength</span>
                                        @elseif($isLow)
                                            <span class="text-[9px] font-bold text-amber-800 bg-amber-100/80 px-1.5 py-0.5 rounded shrink-0">Focus</span>
                                        @endif
                                    </div>
                                    <div class="flex items-baseline justify-between mt-0.5">
                                        <span class="text-sm font-black text-neutral-900 tabular-nums">{{ number_format($item['score'], 2) }}★</span>
                                        <span class="text-[10px] font-bold tabular-nums {{ $delta >= 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                                            {{ $delta >= 0 ? '+' : '' }}{{ number_format($delta, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grouped Bar Chart --}}
                    <div class="relative w-full pt-2" style="height: 220px;">
                        <canvas id="criteriaBenchmarkChart" role="img" aria-label="Bar chart comparing your stall ratings against the campus average for each criterion"></canvas>
                    </div>
                </div>

                {{-- RIGHT: Rating Distribution (2 Cols) --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200/80 p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-neutral-100">
                            <div>
                                <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Rating Breakdown</h2>
                                <p class="text-xs text-neutral-500 mt-0.5">Score distribution from 5★ to 1★</p>
                            </div>
                            <span class="text-xs font-black text-neutral-800 tabular-nums bg-neutral-100 px-2 py-0.5 rounded">{{ number_format($averages ? (float)$averages->overall : 0, 2) }}★</span>
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
                                            <span class="font-mono text-[11px] text-neutral-400 tabular-nums">{{ $starRow['count'] }} ({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-neutral-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full {{ $starRow['color'] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-neutral-100 text-[11px] text-neutral-500 flex items-center justify-between">
                        <span>Total student reviews:</span>
                        <strong class="text-neutral-800 tabular-nums font-semibold">{{ $totalEvaluations }}</strong>
                    </div>
                </div>

            </div>
        @endif

        {{-- ── 7. Detailed Evaluations List (Strict Student Privacy) ────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
            {{-- Header with Search & Filter Controls --}}
            <div class="p-5 sm:p-6 pb-4 border-b border-neutral-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                            <span>Student Evaluations</span>
                            <span class="text-[11px] font-semibold text-neutral-500 bg-neutral-100 px-2 py-0.5 rounded-md">Anonymous Submissions</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Feedback logs submitted by students for your stall</p>
                    </div>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-2">
                        <div class="relative w-full sm:w-56">
                            <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm pointer-events-none"></ion-icon>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search in comments…" aria-label="Search student evaluation comments"
                                class="w-full pl-8 pr-3 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
                        </div>
                        <select name="sort" onchange="this.form.submit()" aria-label="Sort evaluations" class="px-2.5 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="rating_high" {{ request('sort') === 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                            <option value="rating_low" {{ request('sort') === 'rating_low' ? 'selected' : '' }}>Lowest Rating</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                        @if(request('q') || request('sort'))
                            <a href="{{ route('staff.dashboard') }}" class="text-xs text-neutral-500 hover:text-neutral-900 font-semibold shrink-0">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-lg bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400 mx-auto">
                        <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                    </div>
                    <p class="text-sm font-bold text-neutral-900 mb-0.5">No evaluations found</p>
                    <p class="text-xs text-neutral-500 max-w-xs mx-auto">
                        {{ request('q') ? 'No student feedback matched your search criteria.' : 'Your stall has not received any student evaluations yet.' }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[650px] hidden md:table">
                        <thead>
                            <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                                <th class="py-3.5 px-5 font-semibold">Date</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Average</th>
                                <th class="py-3.5 px-5 font-semibold">Student Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            @foreach($evaluations as $eval)
                                @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                                <tr class="hover:bg-neutral-50/60 transition-colors">
                                    <td class="py-3.5 px-5 font-medium text-neutral-600 text-xs tabular-nums whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                        <span class="block text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->cleanliness >= 4 ? 'text-emerald-700' : ($eval->cleanliness <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->cleanliness }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->service >= 4 ? 'text-emerald-700' : ($eval->service <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->service }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->taste >= 4 ? 'text-emerald-700' : ($eval->taste <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->taste }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->price >= 4 ? 'text-emerald-700' : ($eval->price <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->price }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800' }}">
                                            {{ number_format($avg, 1) }}★
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @if($eval->comment)
                                            <p class="text-xs text-neutral-700 line-clamp-2 max-w-xs font-normal" title="{{ $eval->comment }}">
                                                "{{ $eval->comment }}"
                                            </p>
                                        @else
                                            <span class="text-neutral-400 text-xs italic">No comment provided</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Mobile View -->
                    <div class="md:hidden divide-y divide-neutral-100">
                        @foreach($evaluations as $eval)
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <div class="p-4 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-neutral-600 tabular-nums">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </div>
                                <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->cleanliness }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Serv</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->service }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->taste }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->price }}★</span>
                                    </div>
                                </div>
                                @if($eval->comment)
                                    <p class="text-xs text-neutral-700 bg-neutral-50 p-2.5 rounded-md border border-neutral-200/60 mt-1 italic">
                                        "{{ $eval->comment }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-4 border-t border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-neutral-500">
                    <div>
                        Showing <strong class="text-neutral-900">{{ $evaluations->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900">{{ $evaluations->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900">{{ $evaluations->total() }}</strong> submissions
                    </div>
                    <div>
                        {{ $evaluations->links() }}
                    </div>
                </div>
            @endif
        </div>

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
                            maxTicksLimit: 8
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

    // ── 2. Criteria Benchmark Comparison Grouped Bar Chart ────────────────────
    var benchmarkCtx = document.getElementById('criteriaBenchmarkChart');
    if (benchmarkCtx) {
        var myScores = [
            {{ number_format($averages ? (float)$averages->cleanliness : 0, 2) }},
            {{ number_format($averages ? (float)$averages->service : 0, 2) }},
            {{ number_format($averages ? (float)$averages->taste : 0, 2) }},
            {{ number_format($averages ? (float)$averages->price : 0, 2) }}
        ];

        var campusScores = [
            {{ number_format($campusCriteria ? (float)$campusCriteria->cleanliness : 0, 2) }},
            {{ number_format($campusCriteria ? (float)$campusCriteria->service : 0, 2) }},
            {{ number_format($campusCriteria ? (float)$campusCriteria->taste : 0, 2) }},
            {{ number_format($campusCriteria ? (float)$campusCriteria->price : 0, 2) }}
        ];

        new Chart(benchmarkCtx, {
            type: 'bar',
            data: {
                labels: ['Cleanliness', 'Service Quality', 'Food Taste', 'Price Value'],
                datasets: [
                    {
                        label: 'Your Stall',
                        data: myScores,
                        backgroundColor: 'rgba(22, 101, 52, 0.88)',
                        borderColor: 'rgb(20, 83, 45)',
                        borderWidth: 1,
                        borderRadius: 5,
                        maxBarThickness: 36,
                        categoryPercentage: 0.72,
                        barPercentage: 0.88
                    },
                    {
                        label: 'Campus Avg',
                        data: campusScores,
                        backgroundColor: 'rgba(148, 163, 184, 0.80)',
                        borderColor: 'rgb(100, 116, 139)',
                        borderWidth: 1,
                        borderRadius: 5,
                        maxBarThickness: 36,
                        categoryPercentage: 0.72,
                        barPercentage: 0.88
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: '600' },
                            color: '#4b5563',
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2) + '★ / 5.00';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#374151'
                        },
                        border: { display: false }
                    },
                    y: {
                        min: 0,
                        max: 5,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#6b7280',
                            stepSize: 1
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