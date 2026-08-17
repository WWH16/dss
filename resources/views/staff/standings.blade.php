@extends('layouts.dashboard')

@section('title', 'Campus Standings | Staff — DSS')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @php
        $totalStalls = $standings->count();
        $evaluatedStalls = $standings->where('overall_score', '>', 0);
        $campusAvgScore = $evaluatedStalls->isNotEmpty() ? $evaluatedStalls->avg('overall_score') : 0;
        
        $myStallData = $standings->firstWhere('id', $myStall ? $myStall->id : null);
        $myRank = null;
        if ($myStall) {
            foreach ($standings as $idx => $s) {
                if ($s->id == $myStall->id) {
                    $myRank = $idx + 1;
                    break;
                }
            }
        }
        
        $topLeader = $standings->first();
        $topScore = $topLeader ? (float)$topLeader->overall_score : 0;
        $myScore = $myStallData ? (float)$myStallData->overall_score : 0;
        $gapToLeader = max(0, $topScore - $myScore);

        $podiumFirst  = $standings->count() > 0 ? $standings[0] : null;
        $podiumSecond = $standings->count() > 1 ? $standings[1] : null;
        $podiumThird  = $standings->count() > 2 ? $standings[2] : null;
    @endphp

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-neutral-100">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Campus Stall Standings</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-neutral-100 text-neutral-800 border border-neutral-200">
                    <ion-icon name="podium-outline" class="text-xs"></ion-icon>
                    DSS Campus Benchmark
                </span>
            </div>
            <p class="text-xs sm:text-sm text-neutral-500">
                Official composite performance rankings and criteria averages for campus food vendors.
            </p>
        </div>

        @if($myStall)
            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs border border-neutral-200">
                    <ion-icon name="storefront-outline" class="text-sm"></ion-icon>
                    My Stall Dashboard
                </a>
            </div>
        @endif
    </div>

    {{-- ── 2. Staff Standing Notice Card ────────────────────────────────────── --}}
    @if($myStall && $myStallData)
        <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                {{-- Left: Stall Rank & Identity --}}
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-13 h-13 rounded-xl {{ $myRank === 1 ? 'bg-amber-50 border border-amber-300 text-amber-900' : ($myRank <= 3 ? 'bg-slate-100 border border-slate-300 text-slate-900' : 'bg-neutral-100 border border-neutral-200 text-neutral-800') }} flex flex-col items-center justify-center shrink-0 shadow-2xs">
                        @if($myRank === 1)
                            <ion-icon name="trophy" class="text-amber-600 text-base leading-none mb-0.5"></ion-icon>
                        @elseif($myRank === 2 || $myRank === 3)
                            <ion-icon name="medal" class="text-slate-600 text-base leading-none mb-0.5"></ion-icon>
                        @else
                            <ion-icon name="storefront" class="text-neutral-700 text-base leading-none mb-0.5"></ion-icon>
                        @endif
                        <span class="font-black text-sm tabular-nums leading-none">#{{ $myRank ?? '-' }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-neutral-900">{{ $myStall->name }}</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-900 border border-brand-200">
                                Your Assigned Stall
                            </span>
                        </div>
                        <p class="text-xs text-neutral-500">
                            Currently ranked <strong class="text-neutral-900 font-bold">#{{ $myRank }}</strong> out of <span class="font-semibold">{{ $totalStalls }} {{ Str::plural('vendor', $totalStalls) }}</span> on campus with <span class="font-semibold text-neutral-800">{{ $myStallData->eval_count }} {{ Str::plural('evaluation', $myStallData->eval_count) }}</span>.
                        </p>
                    </div>
                </div>

                {{-- Right: 2 Key Metrics (Your Score + Campus Avg) --}}
                <div class="grid grid-cols-2 gap-3 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-neutral-100">
                    {{-- Your Score --}}
                    <div class="bg-neutral-50 p-2.5 rounded-lg border border-neutral-200/80 text-center min-w-[105px]">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Your Score</span>
                        <div class="flex items-center justify-center gap-1 mt-0.5">
                            <span class="text-base font-black text-neutral-900 tabular-nums">
                                {{ $myScore > 0 ? number_format($myScore, 2) : '—' }}
                            </span>
                            @if($myScore > 0)
                                <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                            @endif
                        </div>
                    </div>

                    {{-- Campus Average --}}
                    <div class="bg-neutral-50 p-2.5 rounded-lg border border-neutral-200/80 text-center min-w-[105px]">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Campus Avg</span>
                        <div class="flex items-center justify-center gap-1 mt-0.5">
                            <span class="text-base font-black text-neutral-700 tabular-nums">
                                {{ $campusAvgScore > 0 ? number_format($campusAvgScore, 2) : '—' }}
                            </span>
                            @if($campusAvgScore > 0)
                                <ion-icon name="star" class="text-neutral-400 text-xs"></ion-icon>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── 3. Top Tier Podium Showcase (Top 3 Stalls) ─────────────────────── --}}
    @if($standings->isNotEmpty())
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="trophy-outline" class="text-amber-600 text-sm"></ion-icon>
                    <span>Top Performing Vendors</span>
                </h2>
                <span class="text-[11px] text-neutral-400 font-medium">Ranked by overall DSS composite</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
                {{-- #1 First Place Card (Hero) --}}
                @if($podiumFirst)
                    @php
                        $isMeFirst = $myStall && $myStall->id == $podiumFirst->id;
                        $firstScore = (float)$podiumFirst->overall_score;
                    @endphp
                    <div class="bg-white rounded-xl border border-amber-200/80 shadow-2xs p-5 flex flex-col justify-between group hover:border-amber-300 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 text-amber-950 border border-amber-300 font-black text-xs shadow-2xs">
                                    <ion-icon name="trophy" class="text-amber-600 text-sm"></ion-icon>
                                    1st Place
                                </span>
                                @if($isMeFirst)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-900 border border-brand-200">Your Stall</span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-lg font-black text-neutral-900 tracking-tight">{{ $podiumFirst->name }}</h3>
                                <p class="text-[11px] text-neutral-400 font-mono mt-0.5">{{ $podiumFirst->eval_count }} {{ Str::plural('evaluation', $podiumFirst->eval_count) }}</p>
                            </div>

                            {{-- Composite Score Pill --}}
                            <div class="p-3 bg-amber-50/60 rounded-lg border border-amber-200/70 flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-950">Overall Composite</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-lg font-black text-amber-950 tabular-nums">{{ number_format($firstScore, 2) }}</span>
                                    <ion-icon name="star" class="text-amber-500 text-sm"></ion-icon>
                                </div>
                            </div>

                            {{-- Criteria Breakdown Mini-Meter --}}
                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Cleanliness</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumFirst->cleanliness ? number_format($podiumFirst->cleanliness, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Service</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumFirst->service ? number_format($podiumFirst->service, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Taste</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumFirst->taste ? number_format($podiumFirst->taste, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Price</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumFirst->price ? number_format($podiumFirst->price, 1) : '-' }}★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- #2 Second Place Card --}}
                @if($podiumSecond)
                    @php
                        $isMeSecond = $myStall && $myStall->id == $podiumSecond->id;
                        $secondScore = (float)$podiumSecond->overall_score;
                    @endphp
                    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs p-5 flex flex-col justify-between group hover:border-neutral-300 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                    <ion-icon name="medal" class="text-slate-600 text-sm"></ion-icon>
                                    2nd Place
                                </span>
                                @if($isMeSecond)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-900 border border-brand-200">Your Stall</span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-neutral-900 tracking-tight">{{ $podiumSecond->name }}</h3>
                                <p class="text-[11px] text-neutral-400 font-mono mt-0.5">{{ $podiumSecond->eval_count }} {{ Str::plural('evaluation', $podiumSecond->eval_count) }}</p>
                            </div>

                            <div class="p-2.5 bg-slate-50/80 rounded-lg border border-slate-200/70 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-800">Overall Composite</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-base font-black text-slate-900 tabular-nums">{{ number_format($secondScore, 2) }}</span>
                                    <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Clean</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumSecond->cleanliness ? number_format($podiumSecond->cleanliness, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Service</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumSecond->service ? number_format($podiumSecond->service, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Taste</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumSecond->taste ? number_format($podiumSecond->taste, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Price</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumSecond->price ? number_format($podiumSecond->price, 1) : '-' }}★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- #3 Third Place Card --}}
                @if($podiumThird)
                    @php
                        $isMeThird = $myStall && $myStall->id == $podiumThird->id;
                        $thirdScore = (float)$podiumThird->overall_score;
                    @endphp
                    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs p-5 flex flex-col justify-between group hover:border-neutral-300 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50/70 text-amber-900 border border-amber-600/30 font-extrabold text-xs shadow-2xs">
                                    <ion-icon name="medal" class="text-amber-700 text-sm"></ion-icon>
                                    3rd Place
                                </span>
                                @if($isMeThird)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-900 border border-brand-200">Your Stall</span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-neutral-900 tracking-tight">{{ $podiumThird->name }}</h3>
                                <p class="text-[11px] text-neutral-400 font-mono mt-0.5">{{ $podiumThird->eval_count }} {{ Str::plural('evaluation', $podiumThird->eval_count) }}</p>
                            </div>

                            <div class="p-2.5 bg-amber-50/40 rounded-lg border border-amber-600/20 flex items-center justify-between">
                                <span class="text-xs font-semibold text-amber-950">Overall Composite</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-base font-black text-amber-950 tabular-nums">{{ number_format($thirdScore, 2) }}</span>
                                    <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Clean</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumThird->cleanliness ? number_format($podiumThird->cleanliness, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Service</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumThird->service ? number_format($podiumThird->service, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Taste</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumThird->taste ? number_format($podiumThird->taste, 1) : '-' }}★</span>
                                </div>
                                <div class="flex items-center justify-between px-2 py-1 bg-neutral-50 rounded border border-neutral-100">
                                    <span class="text-neutral-500 font-medium">Price</span>
                                    <span class="font-bold text-neutral-800">{{ $podiumThird->price ? number_format($podiumThird->price, 1) : '-' }}★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ── 4. Complete Campus Leaderboard Table ───────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
        {{-- Header & Search Bar --}}
        <div class="px-5 py-4 border-b border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-neutral-50/50">
            <div>
                <h2 class="text-sm font-bold text-neutral-900 tracking-tight">All Canteen Stalls Leaderboard</h2>
                <p class="text-[11px] text-neutral-500 mt-0.5">Aggregated performance and ranking of all food stalls</p>
            </div>

            {{-- Live Search Filter --}}
            <div class="relative w-full sm:w-60">
                <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm pointer-events-none"></ion-icon>
                <input type="text" id="standings-search-input"
                    placeholder="Search stalls…"
                    class="w-full pl-8 pr-3 py-1.5 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
            </div>
        </div>

        @if($standings->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center text-neutral-400 mx-auto mb-3">
                    <ion-icon name="storefront-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-800 mb-1">No Stalls Registered Yet</h3>
                <p class="text-xs text-neutral-500">Standings will appear once canteen vendors are added by an Administrator.</p>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold text-center w-20">Rank</th>
                            <th class="py-3.5 px-5 font-semibold">Food Vendor</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-5 text-center font-semibold">Composite Score</th>
                        </tr>
                    </thead>
                    <tbody id="standings-table-body" class="divide-y divide-neutral-100 text-xs">
                        @foreach($standings as $index => $stall)
                            @php
                                $rank = $index + 1;
                                $composite = (float)$stall->overall_score;
                                $isMyStall = $myStall && $myStall->id == $stall->id;
                            @endphp
                            <tr class="standings-row hover:bg-neutral-50/70 transition-colors {{ $isMyStall ? 'bg-neutral-50 font-semibold' : '' }}"
                                data-stall-name="{{ strtolower($stall->name) }}">
                                {{-- Rank Badge --}}
                                <td class="py-3 px-5 text-center">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-amber-50 text-amber-950 border border-amber-300 font-black text-xs shadow-2xs">
                                            <ion-icon name="trophy" class="text-amber-600 text-xs"></ion-icon>
                                            <span>#1</span>
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon>
                                            <span>#2</span>
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon>
                                            <span>#3</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-neutral-100 text-neutral-600 font-mono font-bold text-[11px] tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Stall Name & Tag --}}
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-neutral-900">{{ $stall->name }}</span>
                                        @if($isMyStall)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-brand-50 text-brand-900 text-[10px] font-extrabold border border-brand-200">
                                                Your Stall
                                            </span>
                                        @endif
                                        <span class="text-[10px] font-medium text-neutral-400 font-mono">({{ $stall->eval_count }} {{ Str::plural('eval', $stall->eval_count) }})</span>
                                    </div>
                                </td>

                                {{-- Criteria Scores with Color Accents --}}
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-bold {{ $stall->cleanliness >= 4.0 ? 'text-emerald-700 bg-emerald-50' : ($stall->cleanliness && $stall->cleanliness < 3.0 ? 'text-rose-700 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->cleanliness ? number_format($stall->cleanliness, 2) . '★' : '—' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-bold {{ $stall->service >= 4.0 ? 'text-emerald-700 bg-emerald-50' : ($stall->service && $stall->service < 3.0 ? 'text-rose-700 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->service ? number_format($stall->service, 2) . '★' : '—' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-bold {{ $stall->taste >= 4.0 ? 'text-emerald-700 bg-emerald-50' : ($stall->taste && $stall->taste < 3.0 ? 'text-rose-700 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->taste ? number_format($stall->taste, 2) . '★' : '—' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-bold {{ $stall->price >= 4.0 ? 'text-emerald-700 bg-emerald-50' : ($stall->price && $stall->price < 3.0 ? 'text-rose-700 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->price ? number_format($stall->price, 2) . '★' : '—' }}
                                    </span>
                                </td>

                                {{-- Overall Composite Score --}}
                                <td class="py-3 px-5 text-center">
                                    @if($composite > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-black {{ $composite >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : ($composite >= 3 ? 'bg-neutral-100 text-neutral-800' : 'bg-rose-50 text-rose-800 border border-rose-200') }} tabular-nums shadow-2xs">
                                            {{ number_format($composite, 2) }}
                                            <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                        </span>
                                    @else
                                        <span class="text-xs text-neutral-400 font-medium italic">No evaluations</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div id="standings-mobile-list" class="md:hidden divide-y divide-neutral-100">
                @foreach($standings as $index => $stall)
                    @php
                        $rank = $index + 1;
                        $composite = (float)$stall->overall_score;
                        $isMyStall = $myStall && $myStall->id == $stall->id;
                    @endphp
                    <div class="standings-mobile-item p-4 space-y-3 {{ $isMyStall ? 'bg-neutral-50' : '' }}"
                        data-stall-name="{{ strtolower($stall->name) }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($rank === 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-50 text-amber-950 border border-amber-300 font-black text-xs">
                                        <ion-icon name="trophy" class="text-amber-600 text-xs"></ion-icon> #1
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs">
                                        <ion-icon name="medal" class="text-slate-600 text-xs"></ion-icon> #2
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-xs">
                                        <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon> #3
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-neutral-100 text-neutral-500 font-bold text-[10px] font-mono">
                                        #{{ $rank }}
                                    </span>
                                @endif
                                <h3 class="text-xs font-bold text-neutral-900">{{ $stall->name }}</h3>
                                @if($isMyStall)
                                    <span class="px-1.5 py-0.2 rounded bg-brand-50 text-brand-900 text-[9px] font-extrabold border border-brand-200">You</span>
                                @endif
                            </div>
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-black bg-brand-50 text-brand-900 border border-brand-200">
                                {{ $composite > 0 ? number_format($composite, 2) . '★' : '—' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                            <div class="bg-neutral-50 rounded p-1.5 border border-neutral-100">
                                <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                <span class="font-bold text-neutral-800 text-[11px]">{{ $stall->cleanliness ? number_format($stall->cleanliness, 1) : '-' }}★</span>
                            </div>
                            <div class="bg-neutral-50 rounded p-1.5 border border-neutral-100">
                                <span class="block text-[9px] font-bold text-neutral-400 uppercase">Service</span>
                                <span class="font-bold text-neutral-800 text-[11px]">{{ $stall->service ? number_format($stall->service, 1) : '-' }}★</span>
                            </div>
                            <div class="bg-neutral-50 rounded p-1.5 border border-neutral-100">
                                <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                <span class="font-bold text-neutral-800 text-[11px]">{{ $stall->taste ? number_format($stall->taste, 1) : '-' }}★</span>
                            </div>
                            <div class="bg-neutral-50 rounded p-1.5 border border-neutral-100">
                                <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                <span class="font-bold text-neutral-800 text-[11px]">{{ $stall->price ? number_format($stall->price, 1) : '-' }}★</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No results message --}}
            <div id="no-standings-results" class="hidden py-8 px-6 text-center">
                <p class="text-xs font-semibold text-neutral-500">No matching stalls found on the leaderboard.</p>
            </div>
        @endif
    </div>

</div>

@section('scripts')
<script>
// ── Live Filter for Standings Table ──────────────────────────────────────
var standingsSearch = document.getElementById('standings-search-input');
var tableRows       = document.querySelectorAll('.standings-row');
var mobileCards     = document.querySelectorAll('.standings-mobile-item');
var noResultsMsg    = document.getElementById('no-standings-results');

if (standingsSearch) {
    standingsSearch.addEventListener('input', function(e) {
        var query = e.target.value.toLowerCase().trim();
        var visibleCount = 0;

        tableRows.forEach(function(row) {
            var name = row.getAttribute('data-stall-name') || '';
            if (name.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        mobileCards.forEach(function(card) {
            var name = card.getAttribute('data-stall-name') || '';
            card.style.display = name.includes(query) ? 'block' : 'none';
        });

        if (noResultsMsg) {
            noResultsMsg.style.display = visibleCount === 0 && query.length > 0 ? 'block' : 'none';
        }
    });
}
</script>
@endsection
@endsection
