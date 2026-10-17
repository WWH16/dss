@extends('layouts.dashboard')

{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Campus Standings | Staff - DSS')
{{-- CHANGED (whole file): gold/silver/bronze colours follow the admin overview. #1 uses brand green (every amber-* became brand-*); #2 and #3 use neutral grey (every slate-*, the bronze amber variants and the orange podium bar became neutral-*). The near-invisible podium digits (slate-300, amber-300, orange-200) became neutral-600 / brand-700. Every 9px and 10px text became 11px, font-black and font-extrabold became font-bold, rounded-lg and plain rounded became rounded-md, py-0.2 (not a Tailwind v4 step) became py-0.5, grey eval counts and labels went from neutral-400 to neutral-500, the mobile "Service" label became "Serv" to fit four columns at 11px, and the em-dash placeholders for missing scores became "N/A". --}}

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
        
        // CHANGED: $topLeader, $topScore and $gapToLeader removed with the gap text below.
        $myScore = $myStallData ? (float)$myStallData->overall_score : 0;

        $podiumFirst  = $standings->count() > 0 ? $standings[0] : null;
        $podiumSecond = $standings->count() > 1 ? $standings[1] : null;
        $podiumThird  = $standings->count() > 2 ? $standings[2] : null;
        $podiumFourth = $standings->count() > 3 ? $standings[3] : null;

        $firstScore   = $podiumFirst  ? (float)$podiumFirst->overall_score  : 0;
        $secondScore  = $podiumSecond ? (float)$podiumSecond->overall_score : 0;
        $thirdScore   = $podiumThird  ? (float)$podiumThird->overall_score  : 0;
        $fourthScore  = $podiumFourth ? (float)$podiumFourth->overall_score : 0;

        // CHANGED: $gapToPodium removed with the gap text below.
        $leadOverSecond = max(0, $myScore - $secondScore);
        $leadOverFourth = max(0, $myScore - $fourthScore);

        // Calculate stall's top performing criterion
        $topCriteriaName = 'Taste';
        $topCriteriaScore = 0;
        if ($myStallData) {
            $criteriaMap = [
                'Taste'       => (float)($myStallData->taste ?? 0),
                'Cleanliness' => (float)($myStallData->cleanliness ?? 0),
                'Service'     => (float)($myStallData->service ?? 0),
                'Price'       => (float)($myStallData->price ?? 0),
            ];
            arsort($criteriaMap);
            $topCriteriaName = array_key_first($criteriaMap);
            $topCriteriaScore = reset($criteriaMap);
        }
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
                {{-- CHANGED: subtitle no longer promises criteria averages, since other stalls' scores are hidden. --}}
                Official composite performance rankings for campus food vendors.
            </p>
        </div>
    </div>

    {{-- ── 2. Staff Standing Delight Recognition Card (Pristine Layout) ── --}}
    @if($myStall && $myStallData && $myRank)
        <div class="bg-white rounded-xl border {{ $myRank === 1 ? 'border-brand-200/90' : ($myRank === 2 ? 'border-neutral-200/90' : ($myRank === 3 ? 'border-neutral-200' : 'border-neutral-200/80')) }} p-4 sm:p-5 shadow-2xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Left: Rank icon, stall name & concise accolade --}}
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl {{ $myRank === 1 ? 'bg-brand-50 border border-brand-200 text-neutral-500' : ($myRank === 2 ? 'bg-neutral-100 border border-neutral-300 text-neutral-800' : ($myRank === 3 ? 'bg-neutral-100 border border-neutral-200 text-brand-900' : 'bg-neutral-50 border border-neutral-200 text-neutral-700')) }} flex flex-col items-center justify-center shrink-0 mt-0.5 sm:mt-0 shadow-2xs">
                        @if($myRank === 1)
                            <ion-icon name="trophy-outline" class="text-lg text-brand-700 leading-none mb-0.5"></ion-icon>
                        @elseif($myRank === 2)
                            <ion-icon name="ribbon-outline" class="text-lg text-neutral-600 leading-none mb-0.5"></ion-icon>
                        @elseif($myRank === 3)
                            <ion-icon name="medal-outline" class="text-lg text-neutral-500 leading-none mb-0.5"></ion-icon>
                        @else
                            <ion-icon name="storefront-outline" class="text-lg text-neutral-600 leading-none mb-0.5"></ion-icon>
                        @endif
                        <span class="font-bold text-[11px] tabular-nums leading-none">#{{ $myRank }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-neutral-900 tracking-tight">{{ $myStall->name }}</h2>
                            @if($myRank === 1)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-900 border border-brand-200/90">
                                    <ion-icon name="star" class="text-[11px] text-brand-600"></ion-icon>
                                    Campus Leader
                                </span>
                            @elseif($myRank === 2)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-neutral-100 text-neutral-800 border border-neutral-200">
                                    <ion-icon name="ribbon-outline" class="text-[11px] text-neutral-600"></ion-icon>
                                    Silver Distinction
                                </span>
                            @elseif($myRank === 3)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-neutral-100 text-brand-900 border border-neutral-200">
                                    <ion-icon name="medal-outline" class="text-[11px] text-neutral-500"></ion-icon>
                                    Top 3 Podium
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-neutral-100 text-neutral-700 border border-neutral-200">
                                    Your Stall
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            @if($myRank === 1)
                                Highest rated food vendor on campus with a <strong class="text-neutral-900 font-bold">{{ number_format($myScore, 2) }}★</strong> composite score across <span class="font-semibold text-neutral-800">{{ $myStallData->eval_count }} {{ Str::plural('evaluation', $myStallData->eval_count) }}</span>.
                            {{-- CHANGED (rank 2 and rank 4+ lines): the points gap to #1 and to Top 3 is removed. It let staff work out another stall's score. --}}
                            @elseif($myRank === 2)
                                Ranked #2 on campus with a <strong class="text-neutral-900 font-bold">{{ number_format($myScore, 2) }}★</strong> composite score.
                            @elseif($myRank === 3)
                                Holding a Top 3 podium position with a <strong class="text-neutral-900 font-bold">{{ number_format($myScore, 2) }}★</strong> composite score.
                            @else
                                Currently ranked #{{ $myRank }} out of {{ $totalStalls }} vendors.
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Right: Score & Celebrate Button --}}
                <div class="flex items-center gap-2.5 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-neutral-100 justify-between md:justify-end">
                    <div class="h-11 px-4 rounded-md bg-neutral-50 border border-neutral-200/80 flex flex-col justify-center items-center sm:items-end min-w-[95px]">
                        <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider leading-none mb-1">Your Score</span>
                        <span class="text-sm font-bold text-neutral-900 tabular-nums leading-none">{{ $myScore > 0 ? number_format($myScore, 2) . '★' : 'N/A' }}</span>
                    </div>

                    @if($myRank <= 3)
                        <button type="button" onclick="fireRankConfetti({{ $myRank }})"
                            class="h-11 px-4 rounded-md text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-all active:scale-[0.98] cursor-pointer shadow-2xs border {{ $myRank === 1 ? 'bg-brand-50 hover:bg-brand-100 text-brand-900 border-brand-300' : ($myRank === 2 ? 'bg-neutral-100 hover:bg-neutral-200 text-neutral-800 border-neutral-300' : 'bg-brand-50 hover:bg-brand-100 text-brand-900 border-brand-300') }}">
                            <ion-icon name="sparkles-outline" class="text-base {{ $myRank === 1 ? 'text-brand-700' : ($myRank === 2 ? 'text-neutral-600' : 'text-neutral-500') }}"></ion-icon>
                            <span>Celebrate</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    {{-- ADDED: a stall with no evaluations is left out of $standings, so it gets this note instead of a rank card. --}}
    @elseif($myStall)
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-2xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-neutral-50 border border-neutral-200 text-neutral-600 flex items-center justify-center shrink-0 shadow-2xs">
                <ion-icon name="storefront-outline" class="text-lg"></ion-icon>
            </div>
            <div class="space-y-1">
                <h2 class="text-base font-bold text-neutral-900 tracking-tight">{{ $myStall->name }}</h2>
                <p class="text-xs text-neutral-600">Your stall isn't ranked yet.</p>
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
                            <ion-icon name="ribbon-outline" class="text-neutral-500 text-lg"></ion-icon>
                            <p class="text-xs font-bold text-neutral-900 truncate mt-1" title="{{ $podiumSecond->name }}">{{ $podiumSecond->name }}</p>
                            {{-- CHANGED (all three podium labels): score and eval count show only on the staff member's own stall. --}}
                            @if($isMeSecond)
                                <p class="text-lg font-bold text-neutral-800 tabular-nums leading-tight mt-0.5">{{ number_format($secondScore, 2) }}</p>
                                <p class="text-[11px] text-neutral-500 font-mono">{{ $podiumSecond->eval_count }} {{ Str::plural('eval', $podiumSecond->eval_count) }}</p>
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded-md text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
                            @endif
                        </div>
                    @endif

                    {{-- #1 label --}}
                    @if($podiumFirst)
                        <div class="flex-1 max-w-[200px] text-center pb-2">
                            <ion-icon name="trophy-outline" class="text-brand-600 text-xl"></ion-icon>
                            <p class="text-sm font-bold text-neutral-900 truncate mt-1" title="{{ $podiumFirst->name }}">{{ $podiumFirst->name }}</p>
                            @if($isMeFirst)
                                <p class="text-xl font-bold text-neutral-900 tabular-nums leading-tight mt-0.5">{{ number_format($firstScore, 2) }}</p>
                                <p class="text-[11px] text-neutral-500 font-mono">{{ $podiumFirst->eval_count }} {{ Str::plural('eval', $podiumFirst->eval_count) }}</p>
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded-md text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
                            @endif
                        </div>
                    @endif

                    {{-- #3 label --}}
                    @if($podiumThird)
                        <div class="flex-1 max-w-[200px] text-center pb-2">
                            <ion-icon name="medal-outline" class="text-neutral-500 text-base"></ion-icon>
                            <p class="text-xs font-bold text-neutral-900 truncate mt-1" title="{{ $podiumThird->name }}">{{ $podiumThird->name }}</p>
                            @if($isMeThird)
                                <p class="text-lg font-bold text-neutral-800 tabular-nums leading-tight mt-0.5">{{ number_format($thirdScore, 2) }}</p>
                                <p class="text-[11px] text-neutral-500 font-mono">{{ $podiumThird->eval_count }} {{ Str::plural('eval', $podiumThird->eval_count) }}</p>
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded-md text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200">You</span>
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
                        <div class="flex-1 max-w-[200px] h-20 sm:h-24 bg-neutral-100 border border-neutral-200 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-2xl font-bold text-neutral-600">2</span>
                        </div>
                    @endif

                    {{-- #1 bar (tallest — gold) --}}
                    @if($podiumFirst)
                        <div class="flex-1 max-w-[200px] h-28 sm:h-36 bg-brand-50 border border-brand-200/80 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-3xl font-bold text-brand-700">1</span>
                        </div>
                    @endif

                    {{-- #3 bar (shortest — bronze) --}}
                    @if($podiumThird)
                        <div class="flex-1 max-w-[200px] h-14 sm:h-16 bg-neutral-50 border border-neutral-200 border-b-0 rounded-t-lg flex items-center justify-center">
                            <span class="text-xl font-bold text-neutral-600">3</span>
                        </div>
                    @else
                        <div class="flex-1 max-w-[200px]"></div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- CHANGED: the all-stalls leaderboard table and its search box are replaced by this stall's own evaluations list (same partial as the staff dashboard). --}}
    @include('partials.stall-evaluations')

</div>

{{-- ── Canvas Confetti Library ─────────────────────────────────────────── --}}
{{-- CHANGED: loaded only for a top-3 stall, the only one that fires it, and deferred so it no longer blocks the page while it downloads. --}}
@if(($myRank ?? 999) <= 3)
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js" defer></script>
@endif

@section('scripts')
<script>
// ── Confetti Celebration Function ────────────────────────────────────────
function fireRankConfetti(rank) {
    if (typeof confetti !== 'function') return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    if (rank === 1) {
        // Gold + Campus Green Champions Shower
        confetti({
            particleCount: 80,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#f59e0b', '#fbbf24', '#15803d', '#22c55e', '#ffd700']
        });
        setTimeout(function() {
            confetti({
                particleCount: 45,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#f59e0b', '#15803d', '#ffd700']
            });
            confetti({
                particleCount: 45,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#f59e0b', '#15803d', '#ffd700']
            });
        }, 220);
    } else if (rank === 2) {
        // Silver + Sky Blue Starburst
        confetti({
            particleCount: 65,
            spread: 60,
            origin: { y: 0.6 },
            colors: ['#94a3b8', '#cbd5e1', '#38bdf8', '#15803d', '#ffffff']
        });
    } else if (rank === 3) {
        // Bronze + Warm Amber Burst
        confetti({
            particleCount: 55,
            spread: 50,
            origin: { y: 0.6 },
            colors: ['#d97706', '#f97316', '#b45309', '#15803d', '#ffffff']
        });
    }
}

// Auto-fire celebratory confetti on page entrance if staff stall is in Top 3
document.addEventListener('DOMContentLoaded', function() {
    var staffRank = {{ $myRank ?? 999 }};
    // CHANGED: skipped when the URL has a query string, so searching, sorting or paging the evaluations list does not replay the confetti.
    if (staffRank >= 1 && staffRank <= 3 && !window.location.search) {
        setTimeout(function() {
            fireRankConfetti(staffRank);
        }, 400);
    }
});
// CHANGED: the live stall-name filter is removed with the leaderboard table it filtered.
</script>
@endsection
@endsection
