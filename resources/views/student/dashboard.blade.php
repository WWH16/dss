@extends('layouts.dashboard')

@section('title', 'Student Dashboard | DSS')
@section('header_title', 'Dashboard')
{{-- CHANGED (whole file): every rounded-lg and plain rounded became rounded-md (cards stay rounded-xl); every 9px and 10px text became 11px; font-black and font-extrabold became font-bold; grey helper text went from neutral-400 to neutral-500 for contrast. --}}

@section('content')
{{-- CHANGED: bottom padding keeps the floating scan button clear of the last card. --}}
{{-- CHANGED: pb-24 became pb-32 so the last rating badge sits well above the floating button at the end of the page. --}}
<div class="space-y-6 pb-32">

    {{-- ── 1. Page Header & Greeting Bar ───────────────────────────────── --}}
    {{-- CHANGED: the "Evaluate a Stall" button is gone, since evaluations now open only from a stall's QR code. The greeting card carries a short how-to strip instead. --}}
    <div class="bg-white rounded-xl border border-ink-100/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 flex items-center gap-3.5">
            {{-- CHANGED: hidden on phones, where the header bar above already shows the same initial. --}}
            <div class="hidden sm:flex w-12 h-12 rounded-xl bg-brand-50 border border-brand-200/80 text-brand-700 items-center justify-center font-bold text-lg shrink-0 shadow-2xs">
                {{ strtoupper(substr($profile->name ?? ($profile->student_number ?? 'S'), 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-bold text-ink-900 truncate tracking-tight leading-tight">
                        Hello, {{ $profile->name ?? 'Student' }}
                    </h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-semibold bg-ink-50 text-ink-600 border border-ink-100">
                        {{ $profile->student_number ?? 'Student' }}
                    </span>
                </div>
                {{-- CHANGED: states the one-stall-per-month rule instead of asking students to rate every stall they eat at. --}}
                <p class="text-xs text-ink-500 mt-0.5">
                    You can evaluate one stall each month. Your ratings help keep campus dining clean, fair and good.
                </p>
            </div>
        </div>

        {{-- CHANGED: the viewfinder was inline here and took the whole strip. The scanner now lives in the
             #qr-scan-modal dialog at the bottom of this page, opened by the floating Scan QR button, so the
             strip is back to the three steps. --}}
        {{-- CHANGED: the strip now shows this month's evaluation status. While it is open, the heading names the
             month and the three steps stay. Once it is used, the steps give way to the stall rated and the date
             the next evaluation opens. --}}
        {{-- CHANGED: $nextOpens and $star are set once here for the strip, the cards and the scanner dialog. --}}
        @php
            $monthName = $monthStart->format('F');
            $nextOpens = $monthStart->copy()->addMonth()->format('F j');
            $star = '<span aria-hidden="true">★</span><span class="sr-only"> out of 5</span>';
        @endphp
        <div class="px-5 sm:px-6 py-4 bg-brand-50/60 border-t border-brand-100 flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 rounded-md bg-brand-700 text-white flex items-center justify-center shrink-0 shadow-2xs">
                    <ion-icon name="{{ $thisMonthEval ? 'checkmark-done-outline' : 'qr-code-outline' }}" class="text-xl" aria-hidden="true"></ion-icon>
                </div>
                <h2 class="text-sm font-bold text-brand-900 leading-tight">
                    {{ $thisMonthEval ? "$monthName evaluation done" : "Your $monthName evaluation is open" }}
                </h2>
            </div>
            @if($thisMonthEval)
                <p class="md:flex-1 md:pl-5 md:border-l md:border-brand-200 text-xs text-brand-900 leading-relaxed">
                    You rated <span class="font-bold">{{ $thisMonthEval->stall_name }}</span>
                    {{-- CHANGED: the ★ glyph is hidden from screen readers, which read it as "black star"; they hear "out of 5" instead (same on every rating below). --}}
                    <span class="tabular-nums">{{ number_format($evaluatedStallsMap[$thisMonthEval->stall_id]['latest_avg'], 1) }}{!! $star !!}</span>.
                    Your next evaluation opens on <span class="font-bold">{{ $nextOpens }}</span>.
                </p>
            @else
                <ol class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-4 md:flex-1 md:pl-5 md:border-l md:border-brand-200">
                    {{-- CHANGED: step 3 says the submission uses this month's evaluation. --}}
                    @foreach(["Find the QR code posted at the stall's counter.", 'Open the scanner and point it at the code.', "Answer the survey and submit. That uses your $monthName evaluation."] as $step)
                        <li class="flex items-start gap-2 text-xs text-brand-900">
                            <span class="w-5 h-5 rounded-full bg-white border border-brand-200 text-brand-800 text-[11px] font-bold flex items-center justify-center shrink-0 tabular-nums">{{ $loop->iteration }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- ── 2. Summary Metric Cards (3 Balanced Columns) ───────────────────── --}}
    {{-- CHANGED: the three metrics are equal-weight cards in a 3-column grid again. Coverage no longer leads
         with a progress bar and a "not rated yet" count; with one evaluation a month, rating every stall is not
         the goal, so it is now a plain count beside the other two. --}}
    {{-- CHANGED: on phones the three cards sit in one row (was one per row) with smaller padding; the icons and the captions under the numbers show from sm up. --}}
    {{-- CHANGED: the hover border is gone from these cards and the stall cards; nothing on them is clickable. --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        {{-- Metric 1: Stalls Rated --}}
        <div class="bg-white rounded-xl border border-ink-100/80 p-3 sm:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider leading-tight">Stalls Rated</span>
                <ion-icon name="storefront-outline" class="hidden sm:block text-base text-brand-700 shrink-0" aria-hidden="true"></ion-icon>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-1 mt-3">
                <span class="text-xl font-bold text-ink-900 tabular-nums tracking-tight">{{ $uniqueEvaluatedCount }}</span>
                <span class="text-[11px] text-ink-500 font-semibold tabular-nums">/ {{ $totalStallsCount }}</span>
            </div>
            <div class="hidden sm:block text-[11px] text-ink-500 font-medium mt-0.5">
                Different open stalls you have rated
            </div>
        </div>

        {{-- Metric 2: Total Reviews --}}
        <div class="bg-white rounded-xl border border-ink-100/80 p-3 sm:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider leading-tight">Reviews</span>
                <ion-icon name="receipt-outline" class="hidden sm:block text-base text-brand-700 shrink-0" aria-hidden="true"></ion-icon>
            </div>
            <div class="text-xl font-bold text-ink-900 tabular-nums tracking-tight mt-3">
                {{ $totalEvalsCount }}
            </div>
            <div class="hidden sm:block text-[11px] text-ink-500 font-medium mt-0.5">
                {{ $totalEvalsCount === 1 ? '1 evaluation logged' : $totalEvalsCount . ' evaluations logged' }}
            </div>
        </div>

        {{-- Metric 3: Avg Rating Given --}}
        <div class="bg-white rounded-xl border border-ink-100/80 p-3 sm:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider leading-tight">Avg Given</span>
                {{-- CHANGED: icon box recoloured from amber to brand green (one accent). --}}
                <ion-icon name="star" class="hidden sm:block text-base text-brand-700 shrink-0" aria-hidden="true"></ion-icon>
            </div>
            <div class="flex flex-wrap items-baseline gap-x-1 mt-3">
                {{-- CHANGED: the no-ratings placeholder is "N/A" instead of an em-dash. --}}
                <span class="text-xl font-bold text-ink-900 tabular-nums tracking-tight">
                    {!! $totalEvalsCount > 0 ? number_format($overallAvgGiven, 2) . $star : 'N/A' !!}
                </span>
                @if($totalEvalsCount > 0)
                    <span class="hidden sm:inline text-[11px] text-ink-500 font-semibold" aria-hidden="true">/ 5.00</span>
                @endif
            </div>
            <div class="hidden sm:block text-[11px] text-ink-500 font-medium mt-0.5">
                {{ $totalEvalsCount > 0 ? 'Your evaluation baseline' : 'No ratings yet' }}
            </div>
        </div>
    </div>

    {{-- ── 2. Main Analytics & Actions (2-Column Asymmetric Grid) ──────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        {{-- LEFT: Food Stalls Directory (2 Cols) --}}
        {{-- CHANGED: the stall list is collapsed until the student opens it (native <details>), instead of every stall showing on load. The summary row carries the title and the rated count; search and filters live inside. --}}
        <details id="stallsDirectory" class="lg:col-span-2 group">
            <summary class="list-none [&::-webkit-details-marker]:hidden cursor-pointer select-none bg-white rounded-xl border border-ink-100/80 px-5 py-4 shadow-xs hover:border-brand-300 transition-colors flex items-center justify-between gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600/40">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                        <ion-icon name="restaurant-outline" class="text-base" aria-hidden="true"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base font-bold text-ink-900 tracking-tight">Campus Food Stalls</h2>
                        <p class="text-xs text-ink-500 mt-0.5">
                            You've rated <span class="font-bold text-ink-700 tabular-nums">{{ $uniqueEvaluatedCount }}</span> of <span class="font-bold text-ink-700 tabular-nums">{{ $totalStallsCount }}</span> open {{ Str::plural('stall', $totalStallsCount) }}
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 shrink-0">
                    <span class="group-open:hidden">Show</span>
                    <span class="hidden group-open:inline">Hide</span>
                    <ion-icon name="chevron-down-outline" class="text-sm transition-transform duration-200 group-open:rotate-180" aria-hidden="true"></ion-icon>
                </span>
            </summary>

            <div class="mt-4 space-y-4">
            {{-- Stalls Directory Search & Filters --}}
            <div class="bg-white rounded-xl border border-ink-100/80 p-5 shadow-xs">
                {{-- Search & Filter Bar --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        {{-- CHANGED: icon, placeholder and clear button were ink-300 (~2.5:1), below the 4.5:1 text and 3:1 control minimums; all three are ink-500 now. --}}
                        <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-500 text-sm" aria-hidden="true"></ion-icon>
                        {{-- CHANGED: py-1.5 became py-2.5 (44px tall) for thumbs, and focus:outline-none now has a ring to replace the outline it removes. --}}
                        {{-- CHANGED: text-base on phones; iOS Safari zooms the page into any input under 16px. --}}
                        <input type="text" id="stallSearchInput" placeholder="Search stall by name..."
                            aria-label="Search stalls by name"
                            class="w-full bg-surface border border-ink-100 rounded-md pl-9 pr-11 py-2.5 text-base sm:text-xs font-medium text-ink-800 placeholder:text-ink-500 focus:outline-none focus:border-brand-700 focus:ring-2 focus:ring-brand-600/30 focus:bg-white transition-colors">
                        {{-- CHANGED: was a ~20px tap target; the padding gives it 40px, pulled back with -m-2 so the icon stays put. --}}
                        {{-- CHANGED: p-2 only reached 28px; it is now a 44px square (w-11 h-11) at the field's right edge, and the field's right padding grew from pr-10 to pr-11 to match. --}}
                        <button type="button" id="clearSearchBtn" aria-label="Clear the search" class="hidden absolute right-0 top-1/2 -translate-y-1/2 w-11 h-11 text-ink-500 hover:text-ink-700 text-xs">
                            <ion-icon name="close-circle" aria-hidden="true"></ion-icon>
                        </button>
                    </div>

                    {{-- Filter Chips --}}
                    {{-- CHANGED: flex-wrap so the three pills wrap instead of overflowing on 320px phones; active pill brand-700 instead of ink-900, matching the admin pages (the script below applies the same class). --}}
                    {{-- CHANGED: role="tablist" promised tab semantics these plain buttons never had (no role="tab", no tabpanel, no arrow-key navigation), so it is now a group of toggle buttons. aria-pressed carries the selected state that was conveyed by background colour alone, and py-1 became py-2 for thumbs. --}}
                    {{-- CHANGED: pills are 44px tall (min-h-11, was py-2 at 32px), the same height as the search field and a full thumb target. --}}
                    <div class="flex flex-wrap items-center gap-1.5 shrink-0" role="group" aria-label="Filter stalls by rating status">
                        <button type="button" aria-pressed="true" class="filter-pill active px-3 min-h-11 rounded-md text-xs font-bold bg-brand-700 text-white transition-colors shadow-2xs" data-filter="all">
                            All ({{ $totalStallsCount }})
                        </button>
                        <button type="button" aria-pressed="false" class="filter-pill px-3 min-h-11 rounded-md text-xs font-bold bg-ink-50 text-ink-600 hover:bg-ink-100/70 transition-colors" data-filter="needs_rating">
                            {{-- CHANGED: "Needs Rating" became "Not Yet Rated"; nothing here can be rated directly any more. --}}
                            Not Yet Rated ({{ max(0, $totalStallsCount - $uniqueEvaluatedCount) }})
                        </button>
                        <button type="button" aria-pressed="false" class="filter-pill px-3 min-h-11 rounded-md text-xs font-bold bg-ink-50 text-ink-600 hover:bg-ink-100/70 transition-colors" data-filter="rated">
                            Rated ({{ $uniqueEvaluatedCount }})
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stalls Grid --}}
            @if($stalls->isEmpty())
                <div class="bg-white border border-ink-100/80 rounded-xl p-10 text-center shadow-xs">
                    {{-- CHANGED: empty-state icon brand-700 on brand-50 instead of neutral-400 on neutral-100, like the admin empty states. --}}
                    <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="storefront-outline" class="text-2xl" aria-hidden="true"></ion-icon>
                    </div>
                    <h3 class="text-sm font-bold text-ink-900">No Food Stalls Open for Evaluation</h3>
                    <p class="text-xs text-ink-500 mt-1 max-w-sm mx-auto">There are currently no active food stalls open for student evaluations.</p>
                </div>
            @else
                <div id="stallsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($stalls as $stall)
                        @php
                            $evalInfo = $evaluatedStallsMap->get($stall->id);
                            $isRated = !is_null($evalInfo);
                        @endphp
                        <div class="stall-card bg-white rounded-xl border border-ink-100/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between"
                            data-name="{{ strtolower($stall->name) }}"
                            data-status="{{ $isRated ? 'rated' : 'needs_rating' }}">
                            
                            <div>
                                {{-- Card Header: Icon & Status Badge --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-md bg-surface border border-ink-100/70 text-ink-700 flex items-center justify-center shrink-0">
                                        <ion-icon name="storefront-outline" class="text-lg text-brand-700" aria-hidden="true"></ion-icon>
                                    </div>

                                    @if($isRated)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shrink-0">
                                            <ion-icon name="checkmark-circle" class="text-xs text-emerald-600" aria-hidden="true"></ion-icon>
                                            Rated ({{ number_format($evalInfo['latest_avg'], 1) }}{!! $star !!})
                                        </span>
                                    @else
                                        {{-- CHANGED: neutral "Not yet rated" chip instead of an amber warning; an unrated stall is not a problem to fix. --}}
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-surface text-ink-600 border border-ink-100 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-ink-300"></span>
                                            Not yet rated
                                        </span>
                                    @endif
                                </div>

                                {{-- Stall Name & Description --}}
                                <h3 class="font-bold text-ink-900 text-sm leading-snug line-clamp-1">
                                    {{ $stall->name }}
                                </h3>
                                {{-- CHANGED: no invented fallback description; the line is left out when the stall has none. --}}
                                @if($stall->description)
                                    <p class="text-xs text-ink-500 mt-1 line-clamp-2">{{ $stall->description }}</p>
                                @endif

                                @if($isRated)
                                    <p class="text-[11px] text-ink-500 font-medium mt-2">
                                        Last evaluated {{ \Carbon\Carbon::parse($evalInfo['latest_date'])->diffForHumans() }} ({{ $evalInfo['eval_count'] }} {{ Str::plural('time', $evalInfo['eval_count']) }})
                                    </p>
                                @endif
                            </div>

                            {{-- CHANGED: removed the Rate Stall / Rate Again button; the how-to strip at the top explains scanning the stall's QR code. --}}

                        </div>
                    @endforeach
                </div>

                {{-- ADDED: searching and filtering hid stall cards with no announcement. The script writes the visible count here. --}}
                <p id="stallsResultCount" class="sr-only" role="status" aria-live="polite"></p>

                {{-- Empty Search Results Notice --}}
                {{-- CHANGED: the magnifier was ink-300 (~2.5:1 against white), below the 3:1 minimum for a meaningful graphic. --}}
                <div id="noSearchResults" class="hidden bg-white border border-ink-100/80 rounded-xl p-8 text-center shadow-xs">
                    <ion-icon name="search-outline" class="text-2xl text-ink-500 mx-auto mb-2" aria-hidden="true"></ion-icon>
                    <p class="text-xs font-bold text-ink-800">No matching food stalls found</p>
                    <p class="text-[11px] text-ink-500 mt-0.5">Try adjusting your search terms or filter selection.</p>
                </div>
            @endif
            </div>

        </details>

        {{-- RIGHT: Campus Spotlight & Review History (1 Col) --}}
        {{-- CHANGED: every decorative ion-icon on this page now has aria-hidden="true"; Ionicons otherwise labels each one with its name ("checkmark circle", "trophy"). --}}
        <div class="space-y-5">

            {{-- 1. Campus Top Stall Spotlight --}}
            @if($topCampusStall)
                <div class="bg-white rounded-xl border border-ink-100/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-ink-50">
                        {{-- CHANGED: "#1 Campus Favorite" chip recoloured from amber to brand green, as the #1 chip on the admin overview. --}}
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-900 border border-brand-200 font-bold text-[11px] shadow-2xs">
                            <ion-icon name="trophy" class="text-brand-600 text-xs" aria-hidden="true"></ion-icon>
                            #1 Campus Favorite
                        </span>
                        <span class="text-[11px] font-bold text-ink-500 uppercase tracking-wider">DSS Benchmark</span>
                    </div>

                    <div>
                        {{-- CHANGED: h3 became h2; the card has no h2 of its own, so the stall name sat under "Campus Food Stalls" in the heading outline. --}}
                        <h2 class="text-base font-bold text-ink-900 tracking-tight leading-tight">
                            {{ $topCampusStall->name }}
                        </h2>
                        {{-- CHANGED: no invented fallback description; the line is left out when the stall has none. --}}
                        @if($topCampusStall->description)
                            <p class="text-xs text-ink-500 mt-1 line-clamp-2">{{ $topCampusStall->description }}</p>
                        @endif

                        <div class="flex items-center justify-between bg-surface border border-ink-100/70 rounded-md p-2.5 mt-3">
                            <div>
                                {{-- CHANGED: "Overall Score" became "Average Rating". The #1 stall is picked by its SAW score; this number is the plain average of the four criteria, so it is labelled as one. --}}
                                <span class="text-[11px] font-semibold text-ink-500 block">Average Rating</span>
                                <span class="text-sm font-bold text-ink-900 tabular-nums">
                                    {{ number_format((float)$topCampusStall->overall_score, 2) }}{!! $star !!}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-semibold text-ink-500 block">Evaluations</span>
                                <span class="text-sm font-bold text-ink-700 tabular-nums">
                                    {{ $topCampusStall->eval_count }} {{ Str::plural('review', $topCampusStall->eval_count) }}
                                </span>
                            </div>
                        </div>

                        {{-- CHANGED: removed the Evaluate button; evaluations open only from the stall's QR code. --}}
                    </div>
                </div>
            @endif

            {{-- 2. Recent Evaluations Ledger --}}
            <div class="bg-white rounded-xl border border-ink-100/80 p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-ink-50">
                    <div>
                        <h2 class="text-sm font-bold text-ink-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="time-outline" class="text-ink-500 text-sm" aria-hidden="true"></ion-icon>
                            Your Recent Ratings
                        </h2>
                        <p class="text-[11px] text-ink-500 mt-0.5">Latest reviews submitted</p>
                    </div>
                    @if($totalEvalsCount > 0)
                        {{-- CHANGED: an 11px link is a ~14px tap target; the padding takes it past the 24px minimum without moving the text. --}}
                        <a href="{{ route('student.history') }}" class="text-[11px] text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-0.5 transition-colors p-2 -m-2">
                            History <ion-icon name="chevron-forward-outline" class="text-xs" aria-hidden="true"></ion-icon>
                        </a>
                    @endif
                </div>

                @if($myStudentEvals->isEmpty())
                    <div class="py-8 text-center flex flex-col items-center justify-center">
                        {{-- CHANGED: empty-state icon brand-700 on brand-50 instead of neutral-400 on neutral-100. --}}
                        <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mb-2">
                            <ion-icon name="time-outline" class="text-lg" aria-hidden="true"></ion-icon>
                        </div>
                        <p class="text-xs font-bold text-ink-800 mb-0.5">No evaluation history yet</p>
                        <p class="text-[11px] text-ink-500 max-w-[200px] mb-3">Your submitted reviews will appear here.</p>
                        {{-- CHANGED: the "Rate your first stall" link became a QR hint; the evaluation page opens only from a scan. --}}
                        <p class="text-[11px] font-semibold text-brand-700 inline-flex items-center gap-1">
                            <ion-icon name="qr-code-outline" class="text-sm" aria-hidden="true"></ion-icon>
                            Scan a stall's QR code to start
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-ink-50">
                        @foreach($myStudentEvals->take(5) as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                            @endphp
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-2.5">
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-ink-900 truncate">{{ $eval->stall_name ?? 'Stall' }}</h3>
                                    <p class="text-[11px] text-ink-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-ink-50 text-ink-800 tabular-nums border border-ink-100/60">
                                    {{-- CHANGED: amber star ion-icon replaced with the ★ glyph used elsewhere on the page. --}}
                                    {{ number_format($avg, 1) }}{!! $star !!}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($totalEvalsCount > 5)
                        <div class="mt-3 pt-3 border-t border-ink-50 text-center">
                            {{-- CHANGED: inline-flex with 44px of height makes this a thumb-sized target instead of a 16px line of text. --}}
                            <a href="{{ route('student.history') }}" class="inline-flex items-center justify-center min-h-11 px-3 text-xs font-bold text-ink-600 hover:text-brand-700 transition-colors">
                                + {{ $totalEvalsCount - 5 }} more in history &rarr;
                            </a>
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

</div>

{{-- ── 3. Floating Scan Trigger ───────────────────────────────────────────── --}}
{{-- ADDED: the scanner is out of the page flow now. This stays within thumb reach on every scroll position, which is where a student holding a phone at a counter needs it. --}}
{{-- CHANGED: once this month's evaluation is used, the button turns white with a green border and opens a notice instead of the scanner. --}}
{{-- CHANGED: in the used state the accessible name says where the tap leads, since the visible label still reads "Scan QR". --}}
<button type="button"
        @if($thisMonthEval) aria-label="Scan QR. Next evaluation opens {{ $nextOpens }}" @endif
        class="js-open-scanner fixed bottom-5 right-5 z-40 inline-flex items-center gap-2 pl-5 pr-6 min-h-14 rounded-full font-bold text-sm transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-brand-600/35 {{ $thisMonthEval ? 'bg-white hover:bg-brand-50 text-brand-800 border border-brand-200 shadow-lg shadow-ink-900/10' : 'bg-brand-700 hover:bg-brand-800 text-white shadow-lg shadow-brand-900/25' }}">
    <ion-icon name="scan-outline" class="text-2xl" aria-hidden="true"></ion-icon>
    Scan QR
</button>

{{-- ── 4. Scanner Dialog ──────────────────────────────────────────────────── --}}
{{-- ADDED: a native <dialog>, the same confirm-modal treatment the logout prompt uses. Scanning wants the
     screen to itself: the viewfinder is the task, and showModal() traps focus and dims everything else. --}}
<dialog id="qr-scan-modal" class="confirm-modal" aria-labelledby="qr-scan-modal-title">
    <div class="flex items-start justify-between gap-3 mb-1">
        {{-- CHANGED: in the used state the dialog says when the next evaluation opens and has no camera; a scan would only reach the limit screen. The close button is labelled "Close" in both states. --}}
        <div>
            {{-- CHANGED: the font-display class replaces the inline font-family style. --}}
            <h2 id="qr-scan-modal-title" class="font-display text-base font-bold text-ink-900 leading-tight">
                {{ $thisMonthEval ? $monthStart->format('F') . ' evaluation done' : "Scan the stall's QR code" }}
            </h2>
            <p class="text-xs text-ink-500 mt-1">
                {{ $thisMonthEval ? "Next evaluation opens $nextOpens." : 'Point your camera at the code posted at the counter.' }}
            </p>
        </div>
        <button type="button" class="js-close-scanner shrink-0 -mt-1 -mr-1 p-2 rounded-md text-ink-500 hover:text-ink-900 hover:bg-ink-50 transition-colors cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl" aria-hidden="true"></ion-icon>
        </button>
    </div>

    @unless($thisMonthEval)
        <div class="flex flex-col items-center text-center">
            @include('partials.qr-scanner')
        </div>
    @endunless
</dialog>

{{-- ── 5. Client-Side Stall Search & Filtering Logic ──────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('stallSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    const filterPills = document.querySelectorAll('.filter-pill');
    const stallCards = document.querySelectorAll('.stall-card');
    const noResults = document.getElementById('noSearchResults');
    // ADDED: the live region that reports how many stalls a search or filter left visible.
    const resultCount = document.getElementById('stallsResultCount');

    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        let visibleCount = 0;

        stallCards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const status = card.getAttribute('data-status') || '';

            const matchesSearch = !currentSearch || name.includes(currentSearch);
            const matchesFilter = (currentFilter === 'all') || (status === currentFilter);

            if (matchesSearch && matchesFilter) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (noResults) {
            if (visibleCount === 0 && stallCards.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        // ADDED: announce the result of the change for anyone who cannot see the cards appear.
        if (resultCount) {
            resultCount.textContent = visibleCount === 1
                ? '1 stall shown'
                : visibleCount + ' stalls shown';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.toLowerCase().trim();
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', currentSearch.length === 0);
            }
            applyFilters();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                currentSearch = '';
                clearBtn.classList.add('hidden');
                searchInput.focus();
                applyFilters();
            }
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            // CHANGED: active pill uses bg-brand-700 instead of bg-ink-900.
            // CHANGED: aria-pressed moves with the colour, so the selected filter is not signalled by colour alone.
            filterPills.forEach(p => {
                p.classList.remove('active', 'bg-brand-700', 'text-white', 'shadow-2xs');
                p.classList.add('bg-ink-50', 'text-ink-600');
                p.setAttribute('aria-pressed', 'false');
            });

            pill.classList.add('active', 'bg-brand-700', 'text-white', 'shadow-2xs');
            pill.classList.remove('bg-ink-50', 'text-ink-600');
            pill.setAttribute('aria-pressed', 'true');

            currentFilter = pill.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });

    // ADDED: opens the scanner dialog from the floating button and starts the camera straight away - the
    // student already said what they wanted by tapping. The partial stops the camera when the dialog closes.
    const scanModal = document.getElementById('qr-scan-modal');
    const openScanner = document.querySelector('.js-open-scanner');

    if (scanModal && openScanner) {
        openScanner.addEventListener('click', () => {
            scanModal.showModal();
            document.getElementById('qr-start')?.click();
        });

        scanModal.querySelector('.js-close-scanner').addEventListener('click', () => scanModal.close());

        // A click on the dimmed area targets the dialog itself; clicks inside it target its children.
        scanModal.addEventListener('click', (e) => {
            if (e.target === scanModal) scanModal.close();
        });
    }
});
</script>
@endsection