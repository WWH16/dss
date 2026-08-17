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

    {{-- ── 3. Stepped Podium ──────────────────────────────────────────────── --}}
    @if($standings->count() >= 2)
        @php
            $isMeFirst  = $podiumFirst  && $myStall && $myStall->id == $podiumFirst->id;
            $isMeSecond = $podiumSecond && $myStall && $myStall->id == $podiumSecond->id;
            $isMeThird  = $podiumThird  && $myStall && $myStall->id == $podiumThird->id;
            $firstScore  = $podiumFirst  ? (float)$podiumFirst->overall_score  : 0;
            $secondScore = $podiumSecond ? (float)$podiumSecond->overall_score : 0;
            $thirdScore  = $podiumThird  ? (float)$podiumThird->overall_score  : 0;
        @endphp

        <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
            <div class="px-5 pt-4 pb-2">
                <h2 class="text-sm font-bold text-neutral-900">Campus Top Vendors</h2>
                <p class="text-[11px] text-neutral-500 mt-0.5">Ranked by overall DSS composite score</p>
            </div>

            {{-- ── Podium visual ── --}}
            <div class="px-4 sm:px-6 pt-6 pb-0">

                {{-- Names + scores floating above the bars --}}
                <div class="flex items-end justify-center gap-1 sm:gap-2">

                    {{-- #2 label --}}
                    @if($podiumSecond)
                        <div class="flex-1 max-w-[200px] text-center pb-2">
                            <ion-icon name="medal" class="text-slate-400 text-lg"></ion-icon>
                            <p class="text-xs font-bold text-neutral-900 truncate mt-1" title="{{ $podiumSecond->name }}">{{ $podiumSecond->name }}</p>
                            <p class="text-lg font-black text-neutral-800 tabular-nums leading-tight mt-0.5">{{ number_format($secondScore, 2) }}</p>
                            <p class="text-[10px] text-neutral-400 font-mono">{{ $podiumSecond->eval_count }} {{ Str::plural('eval', $podiumSecond->eval_count) }}</p>
                            @if($isMeSecond)
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
                            @endif
                        </div>
                    @endif

                    {{-- #1 label --}}
                    @if($podiumFirst)
                        <div class="flex-1 max-w-[200px] text-center pb-2">
                            <ion-icon name="trophy" class="text-amber-500 text-xl"></ion-icon>
                            <p class="text-sm font-black text-neutral-900 truncate mt-1" title="{{ $podiumFirst->name }}">{{ $podiumFirst->name }}</p>
                            <p class="text-xl font-black text-neutral-900 tabular-nums leading-tight mt-0.5">{{ number_format($firstScore, 2) }}</p>
                            <p class="text-[10px] text-neutral-400 font-mono">{{ $podiumFirst->eval_count }} {{ Str::plural('eval', $podiumFirst->eval_count) }}</p>
                            @if($isMeFirst)
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
                            @endif
                        </div>
                    @endif

                    {{-- #3 label --}}
                    @if($podiumThird)
                        <div class="flex-1 max-w-[200px] text-center pb-2">
                            <ion-icon name="medal" class="text-amber-700/60 text-base"></ion-icon>
                            <p class="text-xs font-bold text-neutral-900 truncate mt-1" title="{{ $podiumThird->name }}">{{ $podiumThird->name }}</p>
                            <p class="text-lg font-black text-neutral-800 tabular-nums leading-tight mt-0.5">{{ number_format($thirdScore, 2) }}</p>
                            <p class="text-[10px] text-neutral-400 font-mono">{{ $podiumThird->eval_count }} {{ Str::plural('eval', $podiumThird->eval_count) }}</p>
                            @if($isMeThird)
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
                            @endif
                        </div>
                    @else
                        <div class="flex-1 max-w-[200px]"></div>
                    @endif
                </div>

                {{-- The actual podium bars --}}
                <div class="flex items-end justify-center gap-1 sm:gap-2">
                    {{-- #2 bar (medium — silver) --}}
                    @if($podiumSecond)
                        <div class="flex-1 max-w-[200px] h-20 sm:h-24 bg-slate-100 border border-slate-200 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-2xl font-black text-slate-300">2</span>
                        </div>
                    @endif

                    {{-- #1 bar (tallest — gold) --}}
                    @if($podiumFirst)
                        <div class="flex-1 max-w-[200px] h-28 sm:h-36 bg-amber-50 border border-amber-200/80 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-3xl font-black text-amber-300">1</span>
                        </div>
                    @endif

                    {{-- #3 bar (shortest — bronze) --}}
                    @if($podiumThird)
                        <div class="flex-1 max-w-[200px] h-14 sm:h-16 bg-orange-50/60 border border-orange-200/60 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-xl font-black text-orange-200">3</span>
                        </div>
                    @else
                        <div class="flex-1 max-w-[200px]"></div>
                    @endif
                </div>
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
