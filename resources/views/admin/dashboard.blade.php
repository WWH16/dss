@extends('layouts.dashboard')
@section('title', 'Overview | Admin — DSS')
@section('head')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<style>
    @media print {
        .site-sidebar, .site-nav, .mobile-toggle, .no-print, header { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        .print-break-inside-avoid { break-inside: avoid; }
        body { background: #fff !important; color: #000 !important; font-size: 12pt; }
        .shadow-sm, .shadow-md, .shadow-2xs { box-shadow: none !important; }
        .border { border-color: #cbd5e1 !important; }
    }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Page Header with Quick Actions ────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                <span>Overview</span>
            </h1>
            <p class="text-neutral-500 text-xs sm:text-sm mt-0.5">
                Decision Support System • Campus Dining Performance & Evaluation Analytics
            </p>
        </div>

        {{-- Action Button Group --}}
        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 no-print">
            <button type="button" onclick="window.print()" class="btn btn-secondary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 border border-neutral-200 shadow-2xs hover:bg-neutral-50 cursor-pointer">
                <ion-icon name="print-outline" class="text-sm text-neutral-600"></ion-icon>
                Print Report
            </button>
            <a href="{{ route('admin.stalls') }}" class="btn btn-primary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs">
                <ion-icon name="add-circle-outline" class="text-sm"></ion-icon>
                Manage Stalls
            </a>
        </div>
    </div>

    {{-- ── 2. Diagnostic Warning (Only if any stall is below 3.0★) ─────── --}}
    @if(isset($attentionStalls) && $attentionStalls->isNotEmpty())
        <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-xl text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center shrink-0">
                    <ion-icon name="warning-outline" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-amber-950">Attention Required on {{ $attentionStalls->count() }} {{ Str::plural('Stall', $attentionStalls->count()) }}</h4>
                    <p class="text-[11px] text-amber-800 mt-0.5">
                        The following stall(s) have scored below the satisfactory 3.0★ threshold:
                        <strong>{{ $attentionStalls->pluck('name')->join(', ') }}</strong>.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.evaluations') }}" class="btn text-[11px] font-bold px-3 py-1.5 bg-amber-200/80 hover:bg-amber-300/80 text-amber-900 border border-amber-300 rounded-md self-start sm:self-auto shrink-0">
                Review Evaluations
            </a>
        </div>
    @endif

    @if($results->isNotEmpty())
        @php
            $podiumFirst  = $results->get(0);
            $podiumSecond = $results->get(1);
            $podiumThird  = $results->get(2);
            $firstScore   = $podiumFirst  ? (float)$podiumFirst->overall_score  : 0;
            $secondScore  = $podiumSecond ? (float)$podiumSecond->overall_score : 0;
            $thirdScore   = $podiumThird  ? (float)$podiumThird->overall_score  : 0;
        @endphp

        {{-- ── 3. Podium + Campus Health ──────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Stepped Podium Card --}}
            <div class="lg:col-span-1 bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 pt-4 pb-1">
                    <h2 class="text-sm font-bold text-neutral-900">Top Vendors</h2>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Ranked by DSS composite</p>
                </div>

                <div class="flex-1 flex flex-col justify-end px-3 pb-0 pt-4">
                    {{-- Names + scores above bars --}}
                    <div class="flex items-end justify-center gap-1">
                        {{-- #2 label --}}
                        @if($podiumSecond)
                            <div class="flex-1 text-center pb-1.5">
                                <ion-icon name="medal" class="text-slate-400 text-base"></ion-icon>
                                <p class="text-[11px] font-bold text-neutral-900 truncate mt-0.5 px-1" title="{{ $podiumSecond->name }}">{{ $podiumSecond->name }}</p>
                                <p class="text-sm font-black text-neutral-800 tabular-nums leading-tight">{{ number_format($secondScore, 2) }}</p>
                                <p class="text-[9px] text-neutral-400 font-mono">{{ $podiumSecond->eval_count }} {{ Str::plural('eval', $podiumSecond->eval_count) }}</p>
                            </div>
                        @endif

                        {{-- #1 label --}}
                        @if($podiumFirst)
                            <div class="flex-1 text-center pb-1.5">
                                <ion-icon name="trophy" class="text-amber-500 text-lg"></ion-icon>
                                <p class="text-xs font-black text-neutral-900 truncate mt-0.5 px-1" title="{{ $podiumFirst->name }}">{{ $podiumFirst->name }}</p>
                                <p class="text-base font-black text-neutral-900 tabular-nums leading-tight">{{ number_format($firstScore, 2) }}</p>
                                <p class="text-[9px] text-neutral-400 font-mono">{{ $podiumFirst->eval_count }} {{ Str::plural('eval', $podiumFirst->eval_count) }}</p>
                            </div>
                        @endif

                        {{-- #3 label --}}
                        @if($podiumThird)
                            <div class="flex-1 text-center pb-1.5">
                                <ion-icon name="medal" class="text-amber-700/60 text-sm"></ion-icon>
                                <p class="text-[11px] font-bold text-neutral-900 truncate mt-0.5 px-1" title="{{ $podiumThird->name }}">{{ $podiumThird->name }}</p>
                                <p class="text-sm font-black text-neutral-800 tabular-nums leading-tight">{{ number_format($thirdScore, 2) }}</p>
                                <p class="text-[9px] text-neutral-400 font-mono">{{ $podiumThird->eval_count }} {{ Str::plural('eval', $podiumThird->eval_count) }}</p>
                            </div>
                        @else
                            <div class="flex-1"></div>
                        @endif
                    </div>

                    {{-- Podium bars --}}
                    <div class="flex items-end justify-center gap-1">
                        @if($podiumSecond)
                            <div class="flex-1 h-16 bg-slate-100 border border-slate-200 border-b-0 rounded-t-lg flex items-center justify-center">
                                <span class="text-xl font-black text-slate-300">2</span>
                            </div>
                        @endif

                        @if($podiumFirst)
                            <div class="flex-1 h-24 bg-amber-50 border border-amber-200/80 border-b-0 rounded-t-lg flex items-center justify-center">
                                <span class="text-2xl font-black text-amber-300">1</span>
                            </div>
                        @endif

                        @if($podiumThird)
                            <div class="flex-1 h-11 bg-orange-50/60 border border-orange-200/60 border-b-0 rounded-t-lg flex items-center justify-center">
                                <span class="text-lg font-black text-orange-200">3</span>
                            </div>
                        @else
                            <div class="flex-1"></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Campus-Wide Criteria Health Barometer --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-4 pb-3 border-b border-neutral-100">
                        <div>
                            <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                                <ion-icon name="pulse-outline" class="text-brand-700 text-lg"></ion-icon>
                                Campus Dining Health Index
                            </h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Aggregate performance rating across all campus food vendors</p>
                        </div>
                        @if($campusHealth)
                            <div class="inline-flex items-center gap-1 bg-brand-50 border border-brand-200 text-brand-900 font-extrabold text-xs px-2.5 py-1 rounded-md self-start sm:self-auto tabular-nums">
                                Campus Avg: {{ number_format($campusHealth->avg_overall, 2) }}★
                            </div>
                        @endif
                    </div>

                    @if($campusHealth)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            {{-- Cleanliness --}}
                            <div class="p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Cleanliness</span>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-bold tabular-nums {{ $campusHealth->avg_cleanliness >= 4 ? 'text-emerald-700' : ($campusHealth->avg_cleanliness >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                        {{ number_format($campusHealth->avg_cleanliness, 2) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-neutral-400">/ 5.0</span>
                                </div>
                                <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full {{ $campusHealth->avg_cleanliness >= 4 ? 'bg-emerald-500' : ($campusHealth->avg_cleanliness >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($campusHealth->avg_cleanliness / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            {{-- Service --}}
                            <div class="p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Service</span>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-bold tabular-nums {{ $campusHealth->avg_service >= 4 ? 'text-emerald-700' : ($campusHealth->avg_service >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                        {{ number_format($campusHealth->avg_service, 2) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-neutral-400">/ 5.0</span>
                                </div>
                                <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full {{ $campusHealth->avg_service >= 4 ? 'bg-emerald-500' : ($campusHealth->avg_service >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($campusHealth->avg_service / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            {{-- Taste --}}
                            <div class="p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Food Taste</span>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-bold tabular-nums {{ $campusHealth->avg_taste >= 4 ? 'text-emerald-700' : ($campusHealth->avg_taste >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                        {{ number_format($campusHealth->avg_taste, 2) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-neutral-400">/ 5.0</span>
                                </div>
                                <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full {{ $campusHealth->avg_taste >= 4 ? 'bg-emerald-500' : ($campusHealth->avg_taste >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($campusHealth->avg_taste / 5) * 100 }}%"></div>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Affordability</span>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-lg font-bold tabular-nums {{ $campusHealth->avg_price >= 4 ? 'text-emerald-700' : ($campusHealth->avg_price >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                        {{ number_format($campusHealth->avg_price, 2) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-neutral-400">/ 5.0</span>
                                </div>
                                <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full {{ $campusHealth->avg_price >= 4 ? 'bg-emerald-500' : ($campusHealth->avg_price >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($campusHealth->avg_price / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── 4. Minimalist Stat Cards ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        @foreach([
            ['label' => 'Total Students',   'value' => $studentCount,    'icon' => 'people-outline',     'desc' => 'Registered evaluators', 'route' => route('admin.students')],
            ['label' => 'Canteen Stalls',   'value' => $stallCount,      'icon' => 'storefront-outline', 'desc' => 'Active vendors',       'route' => route('admin.stalls')],
            ['label' => 'Evaluations',      'value' => $evaluationCount, 'icon' => 'create-outline',     'desc' => 'Submissions logged',    'route' => route('admin.evaluations')],
        ] as $stat)
            <a href="{{ $stat['route'] }}" class="group bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm hover:border-brand-300 hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider group-hover:text-brand-800 transition-colors">{{ $stat['label'] }}</span>
                        <div class="w-9 h-9 rounded-lg bg-brand-50 border border-brand-100/70 flex items-center justify-center text-brand-700 group-hover:bg-brand-600 group-hover:text-white transition-all">
                            <ion-icon name="{{ $stat['icon'] }}" class="text-lg"></ion-icon>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight leading-none">
                        {{ $stat['value'] }}
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3 pt-2 border-t border-neutral-100/70 text-xs">
                    <span class="text-neutral-400 font-medium">{{ $stat['desc'] }}</span>
                    <span class="text-brand-700 font-bold opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5">
                        Manage <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    @if($results->isNotEmpty())
        {{-- ── 5. Primary Chart Card: Evaluation Activity ──────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm print-break-inside-avoid">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-5 pb-3 border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Activity Timeline</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">30-day evaluation volume submitted by students</p>
                </div>
            </div>
            <div class="relative w-full" style="height: 240px;">
                <canvas id="evalTrendChart" role="img" aria-label="Line chart showing the number of evaluations over the last 30 days">
                    <p>Line chart showing the number of evaluations over the last 30 days.</p>
                </canvas>
            </div>
        </div>

        {{-- ── 6. Secondary Chart Cards: Top Stalls & Share ─────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 print-break-inside-avoid">
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
            <div class="bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-neutral-100">
                        <div>
                            <h2 id="barChartTitle" class="text-base font-bold text-neutral-900 tracking-tight">Stall Performance Breakdown</h2>
                            <p id="barChartSubtitle" class="text-xs text-neutral-500 mt-0.5">Top {{ min(5, $stallCountTotal) }} stalls across 4 evaluation criteria</p>
                        </div>

                        {{-- Interactive Filter Switcher for N-stalls --}}
                        @if($stallCountTotal > 5)
                            <div class="inline-flex items-center rounded-lg border border-neutral-200 bg-neutral-100/80 p-0.5 text-xs font-semibold self-start sm:self-auto shrink-0 no-print" role="tablist" aria-label="Stall chart filter">
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all bg-white text-neutral-900 shadow-2xs font-bold cursor-pointer" data-range="top5">Top 5</button>
                                @if($stallCountTotal > 5)
                                    <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium cursor-pointer" data-range="top10">Top {{ min(10, $stallCountTotal) }}</button>
                                @endif
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium cursor-pointer" data-range="lowest5">Lowest 5</button>
                                <button type="button" class="stall-filter-btn px-2.5 py-1 rounded-md transition-all text-neutral-500 hover:text-neutral-900 font-medium cursor-pointer" data-range="all">All ({{ $stallCountTotal }})</button>
                            </div>
                        @endif
                    </div>

                    <div id="barChartWrapper" class="relative w-full overflow-x-auto" style="height: 240px;">
                        <canvas id="stallScoresChart" role="img" aria-label="Bar chart showing average scores per stall for Cleanliness, Service, Taste, and Price">
                            <p>Bar chart showing the average scores per stall for Cleanliness, Service, Taste, and Price.</p>
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Evaluation Share Donut Card --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-5 pb-3 border-b border-neutral-100">
                        <div>
                            <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Share</h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Distribution of student submissions per vendor</p>
                        </div>
                        <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-800 border border-brand-200/70 text-[11px] font-bold px-2.5 py-1 rounded-md tabular-nums shrink-0">
                            {{ $evaluationCount }} {{ Str::plural('Submission', $evaluationCount) }}
                        </span>
                    </div>

                    <div class="relative w-full flex items-center justify-center" style="height: 240px;">
                        <canvas id="evalPieChart" role="img" aria-label="Donut chart showing the distribution of evaluations per stall">
                            <p>Donut chart showing the distribution of evaluations per stall.</p>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 7. Ranked Stall Leaderboard Table ───────────────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden print-break-inside-avoid">
            <div class="p-5 sm:p-6 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                        <span>Ranked Stall Leaderboard</span>
                        <span class="text-xs font-semibold text-brand-700 bg-brand-50 border border-brand-200 px-2 py-0.5 rounded-md">DSS Rankings</span>
                    </h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Stalls sorted by overall composite score across all criteria</p>
                </div>
                <a href="{{ route('admin.stalls') }}" class="text-xs text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors no-print">
                    Manage All Stalls <ion-icon name="arrow-forward-outline" class="text-xs"></ion-icon>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold text-center w-16">Rank</th>
                            <th class="py-3.5 px-5 font-semibold">Food Stall</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-5 text-center font-semibold">Overall Composite</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($results as $index => $result)
                            @php
                                $rank = $index + 1;
                                $composite = (float)$result->overall_score;
                            @endphp
                            <tr class="hover:bg-neutral-50/60 transition-colors {{ $rank === 1 ? 'bg-brand-50/20 font-medium' : '' }}">
                                {{-- Rank Chip (Stepped Hierarchy) --}}
                                <td class="py-3.5 px-5 text-center">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-100 to-amber-50 text-amber-950 border border-amber-300 font-black text-sm shadow-xs">
                                            <ion-icon name="trophy" class="text-amber-600 text-base"></ion-icon>
                                            <span>#1</span>
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon>
                                            <span>#2</span>
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon>
                                            <span>#3</span>
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-semibold text-neutral-400 tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Stall Name & Submissions --}}
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $rank === 1 ? 'font-black text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $result->name }}</span>
                                        <span class="text-[10px] font-semibold text-neutral-400 font-mono">({{ $result->eval_count }} {{ Str::plural('eval', $result->eval_count) }})</span>
                                    </div>
                                </td>

                                {{-- Criteria Scores --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $result->cleanliness >= 4 ? 'text-emerald-700 bg-emerald-50' : ($result->cleanliness <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ number_format($result->cleanliness, 2) }}★
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $result->service >= 4 ? 'text-emerald-700 bg-emerald-50' : ($result->service <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ number_format($result->service, 2) }}★
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $result->taste >= 4 ? 'text-emerald-700 bg-emerald-50' : ($result->taste <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ number_format($result->taste, 2) }}★
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $result->price >= 4 ? 'text-emerald-700 bg-emerald-50' : ($result->price <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ number_format($result->price, 2) }}★
                                    </span>
                                </td>

                                {{-- Overall Composite Score --}}
                                <td class="py-3.5 px-5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-black {{ $composite >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200/90' : ($composite >= 3 ? 'bg-neutral-100 text-neutral-800' : 'bg-rose-50 text-rose-700 border border-rose-200') }} tabular-nums">
                                        {{ number_format($composite, 2) }} <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($results as $index => $result)
                        @php
                            $rank = $index + 1;
                            $composite = (float)$result->overall_score;
                        @endphp
                        <div class="p-4 flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gradient-to-r from-amber-100 to-amber-50 text-amber-950 border border-amber-300 font-black text-xs shadow-2xs">
                                            <ion-icon name="trophy" class="text-amber-600 text-sm"></ion-icon> #1
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-[11px]">
                                            <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon> #2
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-[11px]">
                                            <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon> #3
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-neutral-100 text-neutral-500 font-semibold text-[10px] tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                    <h3 class="{{ $rank === 1 ? 'font-black text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $result->name }}</h3>
                                </div>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-black text-brand-900 border border-brand-200">
                                    {{ number_format($composite, 2) }}★
                                </span>
                            </div>

                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->cleanliness, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Serv</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->service, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->taste, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->price, 1) }}★</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── 8. Recent Evaluations Card ──────────────────────────────────── --}}
        @if($recentEvaluations->isNotEmpty())
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden print-break-inside-avoid">
            <div class="p-5 sm:p-6 pb-4 flex items-center justify-between border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Recent Submissions</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Latest evaluations logged across campus</p>
                </div>
                <a href="{{ route('admin.evaluations') }}" class="text-xs text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors no-print">
                    View Ledger <ion-icon name="arrow-forward-outline" class="text-xs"></ion-icon>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[640px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold">Student</th>
                            <th class="py-3.5 px-4 font-semibold">Stall</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Clean</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Serv</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-4 text-center font-semibold">Average</th>
                            <th class="py-3.5 px-5 text-right font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($recentEvaluations as $eval)
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <tr class="hover:bg-neutral-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-neutral-900 truncate max-w-[150px]">{{ $eval->student_name }}</td>
                                <td class="py-3.5 px-4 text-neutral-700 font-semibold truncate max-w-[130px]">{{ $eval->stall_name }}</td>
                                <td class="py-3.5 px-3 text-center text-neutral-600 tabular-nums text-xs">{{ $eval->cleanliness }}★</td>
                                <td class="py-3.5 px-3 text-center text-neutral-600 tabular-nums text-xs">{{ $eval->service }}★</td>
                                <td class="py-3.5 px-3 text-center text-neutral-600 tabular-nums text-xs">{{ $eval->taste }}★</td>
                                <td class="py-3.5 px-3 text-center text-neutral-600 tabular-nums text-xs">{{ $eval->price }}★</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800' }}">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right text-neutral-400 text-xs tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans(null, true, true) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($recentEvaluations as $eval)
                        @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                        <div class="p-4 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $eval->student_name }}</h3>
                                <p class="text-xs font-medium text-brand-700 mt-0.5 truncate">{{ $eval->stall_name }}</p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="shrink-0 inline-flex items-center gap-1 bg-brand-50 px-2 py-1 rounded-md text-xs font-bold text-brand-900 border border-brand-200">
                                {{ number_format($avg, 1) }}★
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

@if($results->isNotEmpty())
    // ── 1. Scalable Bar Chart with Dynamic N-Stall Range Filtering ─────────
    var rawStallData = @json($allStallResults);
    var stallScoresChart = null;
    var stallCtx = document.getElementById('stallScoresChart');
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
                            cornerRadius: 6,
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
                        cornerRadius: 6,
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
                        cornerRadius: 6,
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