@extends('layouts.dashboard')

{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Campus Standings | Staff - DSS')
{{-- CHANGED (whole file): gold/silver/bronze colours follow the admin overview. #1 uses brand green (every amber-* became brand-*); #2 and #3 use neutral grey (every slate-*, the bronze amber variants and the orange podium bar became neutral-*). The near-invisible podium digits (slate-300, amber-300, orange-200) became neutral-600 / brand-700. Every 9px and 10px text became 11px, font-black and font-extrabold became font-bold, rounded-lg and plain rounded became rounded-md, py-0.2 (not a Tailwind v4 step) became py-0.5, grey eval counts and labels went from neutral-400 to neutral-500, the mobile "Service" label became "Serv" to fit four columns at 11px, and the em-dash placeholders for missing scores became "N/A". --}}

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @php
        $totalStalls = $standings->count();
        // CHANGED: the unused campus average, top-criterion, fourth-place and lead-over values are removed, and the rank lookup uses Collection::search.
        $myStallData = $standings->firstWhere('id', $myStall ? $myStall->id : null);
        $myIndex = $myStall ? $standings->search(fn ($s) => $s->id == $myStall->id) : false;
        $myRank = $myIndex === false ? null : $myIndex + 1;

        // CHANGED: $topLeader, $topScore and $gapToLeader removed with the gap text below.
        $myScore = $myStallData ? (float)$myStallData->overall_score : 0;

        $podiumFirst  = $standings->count() > 0 ? $standings[0] : null;
        $podiumSecond = $standings->count() > 1 ? $standings[1] : null;
        $podiumThird  = $standings->count() > 2 ? $standings[2] : null;
    @endphp

    {{-- CHANGED (colour pass): the page now follows theme.css's "committed green" strategy. Greys use the green-tinted ink-* tokens instead of Tailwind's neutral-*, your-stall card is a dark brand-green panel, and the podium bars are pale greens that fade from #1 (brand-100) to #3. --}}

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="pb-3 border-b border-ink-100">
        {{-- CHANGED: the title and pill wrap onto two lines on phones instead of squeezing the pill text onto two lines. --}}
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 mb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-ink-900 tracking-tight">Campus Stall Standings</h1>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200 whitespace-nowrap">
                <ion-icon name="podium-outline" class="text-xs text-brand-600" aria-hidden="true"></ion-icon>
                DSS Campus Benchmark
            </span>
        </div>
        <p class="text-xs sm:text-sm text-ink-500">
            {{-- CHANGED: subtitle no longer promises criteria averages, since other stalls' scores are hidden. --}}
            Official composite performance rankings for campus food vendors.
        </p>
    </div>

    {{-- ── 2. Staff Standing Delight Recognition Card (Pristine Layout) ── --}}
    @if($myStall && $myStallData && $myRank)
        <div class="bg-brand-900 text-white rounded-xl p-4 sm:p-5 shadow-brand">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Left: Rank icon, stall name & concise accolade --}}
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-brand-800 border border-brand-700 text-brand-50 flex flex-col items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                        @php $rankIcon = ['trophy-outline', 'ribbon-outline', 'medal-outline'][$myRank - 1] ?? 'storefront-outline'; @endphp
                        <ion-icon name="{{ $rankIcon }}" class="text-lg {{ $myRank === 1 ? 'text-brand-300' : 'text-brand-200' }} leading-none mb-0.5" aria-hidden="true"></ion-icon>
                        <span class="font-bold text-[11px] tabular-nums leading-none">#{{ $myRank }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-white tracking-tight">{{ $myStall->name }}</h2>
                            @if($myRank === 1)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-300 text-brand-950">
                                    <ion-icon name="star" class="text-[11px] text-brand-800" aria-hidden="true"></ion-icon>
                                    Campus Leader
                                </span>
                            @elseif($myRank === 2)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-800 text-brand-100 border border-brand-700">
                                    <ion-icon name="ribbon-outline" class="text-[11px] text-brand-300" aria-hidden="true"></ion-icon>
                                    Silver Distinction
                                </span>
                            @elseif($myRank === 3)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-800 text-brand-100 border border-brand-700">
                                    <ion-icon name="medal-outline" class="text-[11px] text-brand-300" aria-hidden="true"></ion-icon>
                                    Top 3 Podium
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-brand-800 text-brand-100 border border-brand-700">
                                    Your Stall
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-brand-100 leading-relaxed">
                            @if($myRank === 1)
                                Highest rated food vendor on campus with a <strong class="text-white font-bold">{{ number_format($myScore, 2) }}★</strong> composite score across <span class="font-semibold text-white">{{ $myStallData->eval_count }} {{ Str::plural('evaluation', $myStallData->eval_count) }}</span>.
                            {{-- CHANGED (rank 2 and rank 4+ lines): the points gap to #1 and to Top 3 is removed. It let staff work out another stall's score. --}}
                            @elseif($myRank === 2)
                                Ranked #2 on campus with a <strong class="text-white font-bold">{{ number_format($myScore, 2) }}★</strong> composite score.
                            @elseif($myRank === 3)
                                Holding a Top 3 podium position with a <strong class="text-white font-bold">{{ number_format($myScore, 2) }}★</strong> composite score.
                            @else
                                Currently ranked #{{ $myRank }} out of {{ $totalStalls }} vendors.
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Right: Score & Celebrate Button --}}
                <div class="flex items-center gap-2.5 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-brand-800 justify-between md:justify-end">
                    <div class="h-11 px-4 rounded-md bg-brand-800 border border-brand-700 flex flex-col justify-center items-center md:items-end min-w-[95px]">
                        <span class="text-[11px] font-bold text-brand-200 uppercase tracking-wider leading-none mb-1">Your Score</span>
                        <span class="text-sm font-bold text-white tabular-nums leading-none">{{ $myScore > 0 ? number_format($myScore, 2) . '★' : 'N/A' }}</span>
                    </div>

                    @if($myRank <= 3)
                        <button type="button" onclick="fireRankConfetti({{ $myRank }})"
                            class="h-11 px-4 rounded-md text-xs font-bold inline-flex items-center justify-center gap-1.5 transition-colors active:scale-[0.98] cursor-pointer bg-brand-50 hover:bg-white text-brand-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                            <ion-icon name="sparkles-outline" class="text-base text-brand-600" aria-hidden="true"></ion-icon>
                            <span>Celebrate</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    {{-- ADDED: a stall with no evaluations is left out of $standings, so it gets this note instead of a rank card. --}}
    @elseif($myStall)
        <div class="bg-white rounded-xl border border-ink-200/80 p-4 sm:p-5 shadow-2xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center shrink-0">
                <ion-icon name="storefront-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="space-y-1">
                <h2 class="text-base font-bold text-ink-900 tracking-tight">{{ $myStall->name }}</h2>
                <p class="text-xs text-ink-600">Your stall isn't ranked yet.</p>
            </div>
        </div>
    @endif

    {{-- ── 3. Stepped Podium ──────────────────────────────────────────────── --}}
    @if($standings->count() >= 2)
        @php
            // CHANGED: the three hand-copied podium columns became one loop over these slots, in display order (#2, #1, #3).
            $podiumSlots = [
                ['rank' => 2, 'stall' => $podiumSecond, 'icon' => 'ribbon-outline', 'bar' => 'h-28 sm:h-32 bg-brand-50 border-brand-200', 'num' => 'text-2xl text-brand-700'],
                ['rank' => 1, 'stall' => $podiumFirst,  'icon' => 'trophy-outline', 'bar' => 'h-36 sm:h-44 bg-brand-100 border-brand-200', 'num' => 'text-3xl text-brand-800'],
                ['rank' => 3, 'stall' => $podiumThird,  'icon' => 'medal-outline',  'bar' => 'h-20 sm:h-24 bg-brand-50/50 border-brand-100', 'num' => 'text-xl text-brand-700'],
            ];
        @endphp

        <div class="bg-white rounded-xl border border-ink-200/80 shadow-2xs overflow-hidden">
            <div class="px-4 sm:px-5 pt-4 pb-2">
                <h2 class="text-sm font-bold text-ink-900">Campus Top Vendors</h2>
                <p class="text-[11px] text-ink-500 mt-0.5">Ranked by overall DSS composite score</p>
            </div>

            {{-- ── Podium visual ── --}}
            {{-- CHANGED: every label has the same fixed parts (icon row, one-line name), so the gap from each name to its bar is identical. Your own score and eval count moved inside your bar. --}}
            <div class="px-3 sm:px-6 pt-6 flex items-end justify-center gap-1.5 sm:gap-3">
                @foreach($podiumSlots as $slot)
                    @if($slot['stall'])
                        @php $isMe = $myStall && $myStall->id == $slot['stall']->id; @endphp
                        <div class="flex-1 min-w-0 max-w-[200px] text-center">
                            <div class="h-6 flex items-center justify-center gap-1">
                                <ion-icon name="{{ $slot['icon'] }}" class="text-lg {{ $slot['rank'] === 1 ? 'text-brand-600' : 'text-brand-700' }}" aria-hidden="true"></ion-icon>
                                @if($isMe)
                                    <span class="px-1.5 py-0.5 rounded-md text-[11px] font-bold leading-none bg-brand-700 text-white">You</span>
                                @endif
                            </div>
                            {{-- Two-line slot, bottom-aligned: long names wrap instead of truncating on phones, and every name still ends the same distance above its bar. --}}
                            <div class="h-[2lh] mt-1 flex items-end justify-center text-xs sm:text-sm leading-tight">
                                <p class="font-bold text-ink-900 line-clamp-2 px-0.5 break-words" title="{{ $slot['stall']->name }}">{{ $slot['stall']->name }}</p>
                            </div>
                            <div class="mt-2 {{ $slot['bar'] }} border border-b-0 rounded-t-lg flex flex-col items-center justify-center gap-1.5">
                                <span class="{{ $slot['num'] }} font-bold leading-none tabular-nums">{{ $slot['rank'] }}</span>
                                {{-- CHANGED: score and eval count show only on the staff member's own stall. --}}
                                @if($isMe)
                                    <span class="text-xs font-bold tabular-nums leading-none text-brand-800">{{ number_format($slot['stall']->overall_score, 2) }}★</span>
                                    <span class="text-[11px] font-mono leading-none text-brand-800">{{ $slot['stall']->eval_count }} {{ Str::plural('eval', $slot['stall']->eval_count) }}</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex-1 max-w-[200px]"></div>
                    @endif
                @endforeach
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
