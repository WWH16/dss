@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Overview | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Overview" to match the H1, instead of the layout default "Dashboard". --}}
@section('header_title', 'Overview')
@section('head')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<style>
    @media print {
        .site-sidebar, .site-nav, .no-print { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        .print-break-inside-avoid { break-inside: avoid; }
        body { background: #fff !important; color: #000 !important; font-size: 12pt; }
        .shadow-sm, .shadow-md, .shadow-2xs { box-shadow: none !important; }
        .border { border-color: #cbd5e1 !important; }
    }
</style>
@endsection

{{-- ── 1. Page Header with Quick Actions ────────────────────────────── --}}
{{-- CHANGED: the actions moved out of the content into the photo banner under the top bar (the layout's banner section). --}}
@section('banner')
    {{-- CHANGED: the visible "Overview" heading repeated the top bar title, and the description line was removed. The h1 stays for screen readers and heading navigation. --}}
    <h1 class="sr-only">Overview</h1>

    {{-- Action Button Group --}}
    <div class="flex flex-wrap items-center gap-2 shrink-0 sm:ml-auto no-print">
        {{-- CHANGED: was a button calling window.print() on the dashboard; now opens the dedicated report page with print options. --}}
        {{-- CHANGED: dropped the Tailwind size, border and shadow utilities, which the unlayered .btn rule overrode; .btn-secondary now supplies the white fill and border. Icon tinted brand green. --}}
        {{-- CHANGED: Print Report and Manage Stalls are admin-only; both hidden for clinic users. --}}
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.report') }}" class="btn btn-secondary font-bold">
            <ion-icon name="print-outline" class="text-base text-brand-700"></ion-icon>
            Print Report
        </a>
        {{-- CHANGED: dropped the same dead size utilities so both header buttons share one size from .btn. --}}
        {{-- CHANGED: btn-primary became btn-secondary; the solid green button drew the eye before the activity numbers. --}}
        <a href="{{ route('admin.stalls') }}" class="btn btn-secondary font-bold">
            <ion-icon name="add-circle-outline" class="text-base text-brand-700"></ion-icon>
            Manage Stalls
        </a>
        @endif
    </div>
@endsection

@section('content')
{{-- CHANGED: was max-w-7xl mx-auto, which centred a 1280px column and left dead space beside the sidebar on wide screens. Content now starts at the main padding and grows to 1600px. --}}
<div class="max-w-[1600px] space-y-6">

    {{-- ── 2. Diagnostic Warning (Only if any stall is below 3.0★) ─────── --}}
    @if(isset($attentionStalls) && $attentionStalls->isNotEmpty())
        {{-- CHANGED: amber block became a white card with a rose left edge and icon. Below 3.0 is red in the Health Index legend, and the amber fill clashed with the brand green. --}}
        <div class="p-4 bg-white border border-neutral-200/70 border-l-4 border-l-rose-500 rounded-xl text-neutral-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <ion-icon name="warning-outline" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-neutral-900">Attention Required on {{ $attentionStalls->count() }} {{ Str::plural('Stall', $attentionStalls->count()) }}</h4>
                    <p class="text-[11px] text-neutral-600 mt-0.5">
                        The following stall(s) have scored below the satisfactory 3.0★ threshold:
                        {{-- CHANGED: each stall is followed by its low criteria, lowest first. --}}
                        <strong class="text-neutral-900">{{ $attentionStalls->map(fn ($s) => $s->name . ' (' . collect(array_keys($s->flags))->map(fn ($c) => \App\Services\NeedsImprovement::LABELS[$c])->join(', ') . ')')->join(', ') }}</strong>.
                    </p>
                </div>
            </div>
            {{-- CHANGED: added btn-sm for a smaller size; button is an outlined brand green so it does not compete with the rose edge, border marked important because .btn resets it to transparent. --}}
            <a href="{{ route('admin.evaluations') }}" class="btn btn-sm font-bold bg-white hover:bg-brand-50 text-brand-700 border-brand-200! hover:border-brand-300! self-start sm:self-auto shrink-0">
                Review Evaluations
            </a>
        </div>
    @endif

    {{-- ── 3. Minimalist Stat Cards ───────────────────────────────────────── --}}
    {{-- CHANGED: stat cards moved from below the podium to the first row, ordered activity first (Evaluations, Students, Stalls, Campus average), and the grid went from 3 to 4 columns (2 on small screens). --}}
    {{-- CHANGED: "Canteen Stalls" became "Stalls Evaluated" (stalls with at least one evaluation, out of all stalls), and "Campus Average" moved here from the Health Index header badge. Both read from data the controller already loads. The total counts every stall, active or not, so the caption makes no "active" claim. --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        {{-- CHANGED: Students and Stalls cards link only for admins; clinic users cannot open those pages. --}}
        @php $campusAvg = $campusHealth->avg_overall; @endphp
        @foreach([
            ['label' => 'Evaluations',      'value' => $evaluationCount, 'suffix' => null, 'icon' => 'create-outline',     'desc' => 'Submissions logged',    'route' => route('admin.evaluations')],
            ['label' => 'Total Students',   'value' => $studentCount,    'suffix' => null, 'icon' => 'people-outline',     'desc' => 'Registered evaluators', 'route' => Auth::user()->role === 'admin' ? route('admin.students') : null],
            ['label' => 'Stalls Evaluated', 'value' => $results->count(), 'suffix' => '/ ' . $stallCount, 'icon' => 'storefront-outline', 'desc' => 'With at least one evaluation', 'route' => Auth::user()->role === 'admin' ? route('admin.stalls') : null],
            ['label' => 'Campus Average',   'value' => $campusAvg !== null ? number_format($campusAvg, 2) : 'N/A', 'suffix' => $campusAvg !== null ? '/ 5.00' : null, 'icon' => 'star-outline', 'desc' => 'Mean of all 4 criteria', 'route' => null],
        ] as $stat)
            <a @if($stat['route']) href="{{ $stat['route'] }}" @endif class="group bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm {{ $stat['route'] ? 'hover:border-brand-300 hover:shadow-md' : '' }} transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider group-hover:text-brand-800 transition-colors">{{ $stat['label'] }}</span>
                        {{-- CHANGED: icon hidden below 640px so the two-column stat labels fit on one line. --}}
                        <div class="w-9 h-9 rounded-lg bg-brand-50 border border-brand-100/70 hidden sm:flex items-center justify-center text-brand-700 {{ $stat['route'] ? 'group-hover:bg-brand-600 group-hover:text-white' : '' }} transition-all">
                            <ion-icon name="{{ $stat['icon'] }}" class="text-lg"></ion-icon>
                        </div>
                    </div>
                    {{-- CHANGED: value centered (experiment). --}}
                    <div class="text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight leading-none text-center">
                        {{ $stat['value'] }}@if($stat['suffix'])<span class="text-sm sm:text-base font-semibold text-neutral-500 ml-1">{{ $stat['suffix'] }}</span>@endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3 pt-2 border-t border-neutral-100/70 text-xs">
                    <span class="text-neutral-500 font-medium">{{ $stat['desc'] }}</span>
                    {{-- CHANGED: dropped the "Manage" hover label, which repeated the header's "Manage Stalls" action; the chevron alone signals the card is a link. --}}
                    @if($stat['route'])
                    <span class="text-brand-700 opacity-0 group-hover:opacity-100 transition-opacity flex items-center" aria-hidden="true">
                        <ion-icon name="chevron-forward-outline" class="text-sm"></ion-icon>
                    </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    @if($results->isNotEmpty())
        {{-- CHANGED: the timeline was a full-width card and Recent Submissions was the last card on the page. They now share one row (8 + 4 columns from 1280px; stacked below, where the 4-column card would be about 224px wide) so recent activity reads together. --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 sm:gap-6 print-break-inside-avoid">
        {{-- ── 4. Primary Chart Card: Evaluation Activity ──────────────────── --}}
        <div class="xl:col-span-8 bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Activity Timeline</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">
                        {{ $activityPeriodLabel }} • <strong class="text-neutral-800 font-semibold tabular-nums">{{ $activityTotalCount }}</strong> total {{ $activityTotalCount === 1 ? 'submission' : 'submissions' }}
                    </p>
                </div>

                {{-- Month / Year Filter Controls --}}
                {{-- CHANGED: an Apply button submits the filter instead of each select's change, which reloaded the page on every arrow key and did nothing without JavaScript. --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                    <div class="flex items-center gap-1.5 bg-neutral-50 border border-neutral-200/90 rounded-lg p-1">
                        <select name="activity_month" aria-label="Filter activity by month"
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

                        <select name="activity_year" aria-label="Filter activity by year"
                            class="bg-white border border-neutral-200 rounded-md px-2.5 py-1 text-xs font-semibold text-neutral-700 shadow-2xs focus:outline-none focus:border-brand-700">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Apply</button>
                    </div>
                </form>
            </div>
            {{-- CHANGED: fixed 240px height became a 200px minimum that grows to the row height, so the chart fills the card beside Recent Submissions. The absolute inner box keeps Chart.js from feeding its own canvas height back into the layout. --}}
            <div class="relative w-full flex-1 min-h-[200px]">
                <div class="absolute inset-0">
                    <canvas id="evalTrendChart" role="img" aria-label="Line chart showing evaluation submissions for {{ $activityPeriodLabel }}">
                        <p>Line chart showing evaluation submissions for {{ $activityPeriodLabel }}.</p>
                    </canvas>
                </div>
            </div>
        </div>

        {{-- ── 5. Recent Evaluations Card ──────────────────────────────────── --}}
        {{-- CHANGED: the 8-column table became one compact list at every width so it fits a 4-column card. The stall name leads each row; "Anonymous student" moved to the muted meta line because it carries no information. --}}
        <div class="xl:col-span-4 bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 sm:p-6 pb-4 flex items-center justify-between border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Recent Submissions</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Latest {{ $recentEvaluations->count() }} evaluations</p>
                </div>
                <a href="{{ route('admin.evaluations') }}" class="text-xs text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors no-print">
                    View Ledger <ion-icon name="arrow-forward-outline" class="text-xs"></ion-icon>
                </a>
            </div>

            <ul class="divide-y divide-neutral-100">
                @foreach($recentEvaluations as $eval)
                    @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                    <li class="px-5 sm:px-6 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $eval->stall_name }}</p>
                            {{-- CHANGED: criterion names written in full instead of "Clean" and "Serv". --}}
                            <p class="text-[11px] text-neutral-500 mt-1 tabular-nums">
                                Cleanliness {{ $eval->cleanliness }} · Service {{ $eval->service }} · Taste {{ $eval->taste }} · Price {{ $eval->price }}
                            </p>
                            <p class="text-[11px] text-neutral-500 mt-0.5">Anonymous student · {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</p>
                        </div>
                        {{-- CHANGED: averages below 3 now show red, matching the leaderboard's composite column. --}}
                        <span class="shrink-0 inline-flex items-center gap-0.5 px-2 py-1 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : ($avg >= 3 ? 'bg-neutral-100 text-neutral-800' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                            {{ number_format($avg, 1) }}★
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        </div>

        {{-- ── 6. Ranked Stall Leaderboard Table ───────────────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden print-break-inside-avoid">
            <div class="p-5 sm:p-6 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-neutral-100">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                        <span>Ranked Stall Leaderboard</span>
                        <span class="text-xs font-semibold text-brand-700 bg-brand-50 border border-brand-200 px-2 py-0.5 rounded-md">DSS Rankings</span>
                    </h2>
                    {{-- CHANGED: subtitle states how many of the registered stalls have evaluations, so a short leaderboard next to the stall total is not confusing. --}}
                    <p class="text-xs text-neutral-500 mt-0.5">{{ $results->count() }} of {{ $stallCount }} {{ Str::plural('stall', $stallCount) }} evaluated, ranked by SAW score across all criteria</p>
                    @if (!empty($ahpConsistency))
                        <p class="text-[11px] text-neutral-500 mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="font-semibold text-neutral-600">AHP weights</span>
                            @foreach ($ahpConsistency['weights'] as $criterion => $weight)
                                <span class="font-mono tabular-nums">{{ str_replace('_', ' ', $criterion) }} {{ number_format($weight, 2) }}</span>
                            @endforeach
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded font-semibold {{ $ahpConsistency['consistent'] ? 'text-emerald-700 bg-emerald-50 border border-emerald-200/60' : 'text-rose-700 bg-rose-50 border border-rose-200/60' }}">
                                CR {{ number_format($ahpConsistency['CR'], 4) }}
                                {{ $ahpConsistency['consistent'] ? '(consistent)' : '(inconsistent)' }}
                            </span>
                        </p>
                    @endif
                </div>
                {{-- CHANGED: removed the "Manage All Stalls" link; the header's "Manage Stalls" button is the one entry point to that page. --}}
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold text-center w-16">Rank</th>
                            <th class="py-3.5 px-5 font-semibold">Food Stall</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-5 text-center font-semibold">Overall Composite</th>
                            <th class="py-3.5 px-3 text-center font-semibold">SAW</th>
                            <th class="py-3.5 px-3 text-center font-semibold">AHP</th>
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
                                {{-- CHANGED: the amber gradient (#1) and slate/amber (#2, #3) chips became one chip shape: brand green for #1, neutral grey for #2 and #3, same size, font-bold. Ranks 4+ darkened from neutral-400 to neutral-500 for contrast. --}}
                                <td class="py-3.5 px-5 text-center">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-brand-50 text-brand-900 border border-brand-200 font-bold text-xs">
                                            <ion-icon name="trophy" class="text-brand-600 text-xs"></ion-icon>
                                            <span>#1</span>
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-xs">
                                            <ion-icon name="medal" class="text-neutral-500 text-xs"></ion-icon>
                                            <span>#2</span>
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-xs">
                                            <ion-icon name="medal" class="text-neutral-400 text-xs"></ion-icon>
                                            <span>#3</span>
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-semibold text-neutral-500 tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Stall Name & Submissions --}}
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2">
                                        {{-- CHANGED: #1 name font-black dropped to font-bold; eval count raised from 10px neutral-400 to 11px neutral-500. --}}
                                        <span class="{{ $rank === 1 ? 'font-bold text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $result->name }}</span>
                                        <span class="text-[11px] font-semibold text-neutral-500 font-mono">({{ $result->eval_count }} {{ Str::plural('eval', $result->eval_count) }})</span>
                                    </div>
                                </td>

                                {{-- Criteria Scores --}}
                                {{-- CHANGED: the four cells use the Health Index rule (4 and up green, 3 to 3.99 amber, below 3 red). Before, 3 to 3.99 was plain grey and the red cutoff was 2.9, so a 2.95 showed red in one card and grey in the other. The four copy-pasted cells are now one @foreach. --}}
                                @foreach(['cleanliness', 'service', 'taste', 'price'] as $criterion)
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $result->$criterion >= 4 ? 'text-emerald-700 bg-emerald-50' : ($result->$criterion >= 3 ? 'text-amber-700 bg-amber-50' : 'text-rose-600 bg-rose-50') }}">
                                        {{ number_format($result->$criterion, 2) }}★
                                    </span>
                                </td>
                                @endforeach

                                {{-- Overall Composite Score --}}
                                <td class="py-3.5 px-5 text-center">
                                    {{-- CHANGED: font-black dropped to font-bold, and the amber star ion-icon replaced with the ★ glyph used everywhere else on the page. --}}
                                    <span class="inline-flex items-center gap-0.5 px-2.5 py-1 rounded-md text-xs font-bold {{ $composite >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200/90' : ($composite >= 3 ? 'bg-neutral-100 text-neutral-800' : 'bg-rose-50 text-rose-700 border border-rose-200') }} tabular-nums">
                                        {{ number_format($composite, 2) }}★
                                    </span>
                                </td>

                                {{-- DSS Scores (0 to 1) --}}
                                <td class="py-3.5 px-3 text-center text-xs font-semibold font-mono text-neutral-700 tabular-nums">{{ number_format($result->saw_score, 4) }}</td>
                                <td class="py-3.5 px-3 text-center text-xs font-semibold font-mono text-neutral-700 tabular-nums">{{ number_format($result->ahp_score, 4) }}</td>
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
                                {{-- CHANGED (mobile rank chips, name, composite): same colour and weight rule as the desktop table: brand green #1, neutral #2/#3, font-bold instead of font-black/extrabold, 11px minimum text. --}}
                                <div class="flex items-center gap-2">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-50 text-brand-900 border border-brand-200 font-bold text-[11px]">
                                            <ion-icon name="trophy" class="text-brand-600 text-xs"></ion-icon> #1
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-[11px]">
                                            <ion-icon name="medal" class="text-neutral-500 text-xs"></ion-icon> #2
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-900 border border-neutral-200 font-bold text-[11px]">
                                            <ion-icon name="medal" class="text-neutral-400 text-xs"></ion-icon> #3
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center min-w-6 h-5 px-1 rounded-md bg-neutral-100 text-neutral-600 font-semibold text-[11px] tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                    <h3 class="{{ $rank === 1 ? 'font-bold text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $result->name }}</h3>
                                </div>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200">
                                    {{ number_format($composite, 2) }}★
                                </span>
                            </div>

                            {{-- CHANGED: criterion boxes lost their inner border; labels raised from 9px neutral-400 to 11px neutral-500. --}}
                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->cleanliness, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Serv</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->service, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->taste, 1) }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900">{{ number_format($result->price, 1) }}★</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CHANGED: the Health Index sat beside the podium in the first row and the Evaluation Share donut sat beside the "Top Performing Stalls" bar chart. The podium and bar chart are removed (the leaderboard ranks the same stalls with the same scores); Health Index and Share now share the last row (8 + 4 columns from 1280px; stacked below). --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 sm:gap-6 print-break-inside-avoid">

            {{-- Campus-Wide Criteria Health Barometer --}}
            <div class="xl:col-span-8 bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-neutral-100">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="pulse-outline" class="text-brand-700 text-lg"></ion-icon>
                            Campus Dining Health Index
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Aggregate performance rating across all campus food vendors</p>
                    </div>
                    {{-- CHANGED: the "Campus Avg" badge moved to the stat cards. In its place, a legend for the green, amber, and red score colours, which had no key. --}}
                    <ul class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] font-semibold text-neutral-600 self-start sm:self-auto" aria-label="Score colour legend">
                        <li class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>4.00 and up</li>
                        <li class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500" aria-hidden="true"></span>3.00 to 3.99</li>
                        <li class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500" aria-hidden="true"></span>Below 3.00</li>
                    </ul>
                </div>

                {{-- CHANGED: the four copy-pasted criterion tiles are now one @foreach over the same four averages. Tiles lost their inner border (fewer nested boxes), and the 10px neutral-400 labels and "/ 5.0" suffix became 11px neutral-500 for WCAG AA contrast. --}}
                {{-- CHANGED: tiles are a 2 x 2 grid that fills the card height instead of one row of four that left the lower half empty. Dropped the @if($campusHealth) guard: the aggregate query always returns a row. --}}
                <div class="flex-1 grid grid-cols-2 gap-3">
                    @foreach(['avg_cleanliness' => 'Cleanliness', 'avg_service' => 'Service', 'avg_taste' => 'Food Taste', 'avg_price' => 'Affordability'] as $healthKey => $healthLabel)
                        @php $healthValue = (float) $campusHealth->$healthKey; @endphp
                        <div class="p-4 bg-neutral-50 rounded-lg flex flex-col justify-between gap-2">
                            <span class="text-[11px] font-semibold text-neutral-500 uppercase tracking-wider block">{{ $healthLabel }}</span>
                            <div class="flex items-baseline justify-between">
                                <span class="text-2xl font-bold tabular-nums {{ $healthValue >= 4 ? 'text-emerald-700' : ($healthValue >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                    {{ number_format($healthValue, 2) }}
                                </span>
                                <span class="text-[11px] font-semibold text-neutral-500">/ 5.0</span>
                            </div>
                            <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full {{ $healthValue >= 4 ? 'bg-emerald-500' : ($healthValue >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($healthValue / 5) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Evaluation Share Donut Card --}}
            <div class="xl:col-span-4 bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between gap-3 mb-5 pb-3 border-b border-neutral-100">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight">Evaluation Share</h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Distribution of student submissions per vendor</p>
                    </div>
                    <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-800 border border-brand-200/70 text-[11px] font-bold px-2.5 py-1 rounded-md tabular-nums shrink-0">
                        {{ $evaluationCount }} {{ Str::plural('Submission', $evaluationCount) }}
                    </span>
                </div>

                {{-- CHANGED: fixed 240px height became a 240px minimum that grows with the Health Index card beside it. --}}
                <div class="relative w-full flex-1 min-h-[240px]">
                    <div class="absolute inset-0">
                        <canvas id="evalPieChart" role="img" aria-label="Donut chart showing the distribution of evaluations per stall">
                            <p>Donut chart showing the distribution of evaluations per stall.</p>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>


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
        /* CHANGED: removed the alpha, border, colour 2-4, and pie variables; only the removed bar chart read them. */
        --chart-line-bg: oklch(0.56 0.17 155 / 0.12);
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
    // CHANGED: dropped the alpha, border, and colour 2-4 reads; only the bar chart used them.
    var cLineBg = rootStyles.getPropertyValue('--chart-line-bg').trim();

@if($results->isNotEmpty())
    // CHANGED: removed the "Top Performing Stalls" bar chart and its Top 5 / Top 10 / Lowest 5 / All filter; the leaderboard shows the same per-criterion scores in the same SAW order.
    // ── 1. Line Chart: 30-Day Evaluation Activity ──────────────────────────
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

    // ── 2. Evaluation Share Donut Chart with "Top 4 + Others" Cluster ────────
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

        // CHANGED: the teal, amber and purple slices became brand-green steps (dark to light) so the donut uses the page's one accent; "Other Stalls" stays neutral grey.
        var donutColors = [
            'oklch(0.32 0.11 155)',
            'oklch(0.48 0.15 155)',
            'oklch(0.64 0.16 155)',
            'oklch(0.84 0.10 155)',
            'oklch(0.80 0.01 155)'
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