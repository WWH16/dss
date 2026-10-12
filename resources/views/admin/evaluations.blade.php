@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Evaluations | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Evaluations" instead of the layout default "Dashboard". --}}
@section('header_title', 'Evaluations')

@section('content')
{{-- CHANGED: was max-w-7xl mx-auto, which centred the page and left dead space beside the sidebar; same fix as the overview and stalls pages. --}}
<div class="max-w-[1600px] space-y-6">

    {{-- CHANGED: the visible "Evaluations" heading repeated the top bar; the h1 stays for screen readers, as on the overview and stalls pages. --}}
    <h1 class="sr-only">Evaluations</h1>

    @php
        $sort = request('sort') ?: 'latest';
        $hasFilters = request('q') || request('stall_id') || $sort !== 'latest';
        $sortUrl = fn ($s) => request()->fullUrlWithQuery(['sort' => $s, 'page' => null]);
        // CHANGED: one threshold rule for every score on the page, the same one the overview uses: below 3 needs attention (AdminController), 4 and up is good.
        // Low scores also carry a caret so the colour is not the only cue.
        $score = function ($v, $class = '') {
            $tone = $v < 3 ? 'text-rose-700' : ($v >= 4 ? 'text-emerald-700' : 'text-neutral-900');
            $mark = $v < 3 ? '<ion-icon name="caret-down" class="text-[10px]" aria-hidden="true"></ion-icon><span class="sr-only">Low:</span>' : '';
            return '<span class="inline-flex items-center gap-0.5 tabular-nums ' . $tone . ' ' . $class . '">' . $mark . number_format($v, 2) . '</span>';
        };
        $evaluations->getCollection()->each(function ($e) {
            $e->avg = ($e->cleanliness + $e->service + $e->taste + $e->price) / 4;
            // CHANGED: created_at is stored in UTC; convert to Philippine time, as the printable report does.
            $e->at = \Carbon\Carbon::parse($e->created_at, 'UTC')->timezone('Asia/Manila');
            $e->at_label = $e->at->format('M d, Y, h:i A');
        });
    @endphp

    {{-- CHANGED: search, filters and the table share one card. The "Show Filters" drawer is gone; its two filters sit inline. --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        <div class="p-4 sm:px-5 border-b border-neutral-200/70 space-y-3">
            <form method="GET" action="{{ route('admin.evaluations') }}" class="flex flex-col lg:flex-row lg:items-center gap-2.5">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                <div class="flex-1 relative">
                    <label for="filter_q" class="sr-only">Search</label>
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input id="filter_q" type="search" name="q" value="{{ request('q') }}" placeholder="Search stall or comment…"
                        class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                </div>

                <div class="grid grid-cols-2 lg:flex gap-2.5">
                    <div>
                        <label for="filter_stall_id" class="sr-only">Stall</label>
                        <select id="filter_stall_id" name="stall_id" onchange="this.form.submit()" class="w-full lg:w-52 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 cursor-pointer {{ request('stall_id') ? 'border-brand-300 bg-brand-50 text-brand-900 font-semibold' : 'text-neutral-700' }}">
                            <option value="">All stalls</option>
                            @foreach($stalls as $s)
                                <option value="{{ $s->id }}" {{ request('stall_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_sort" class="sr-only">Sort</label>
                        <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full lg:w-48 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 cursor-pointer {{ $sort !== 'latest' ? 'border-brand-300 bg-brand-50 text-brand-900 font-semibold' : 'text-neutral-700' }}">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest first</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest first</option>
                            <option value="rating_low" {{ $sort === 'rating_low' ? 'selected' : '' }}>Lowest average first</option>
                            <option value="rating_high" {{ $sort === 'rating_high' ? 'selected' : '' }}>Highest average first</option>
                        </select>
                    </div>
                </div>

                @if($hasFilters)
                    <a href="{{ route('admin.evaluations') }}" class="self-start lg:self-auto inline-flex items-center gap-1 px-3 py-2 rounded-md text-sm font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50">
                        <ion-icon name="close-outline" class="text-base"></ion-icon>
                        Clear
                    </a>
                @endif
            </form>

            {{-- CHANGED: legend for the score colours, and what "Average" means next to the leaderboard's weighted composite. --}}
            <p class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-neutral-500">
                <span>Scores out of 5. Average is the mean of the four criteria.</span>
                <span class="inline-flex items-center gap-1 text-rose-700 font-semibold"><ion-icon name="caret-down" aria-hidden="true"></ion-icon>Below 3.00</span>
                <span class="text-emerald-700">4.00 and up</span>
            </p>
        </div>

        @if($evaluations->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                {{-- CHANGED: empty-state icon neutral-400 on neutral-50 to brand-700 on brand-50, matching the overview and stalls empty states. --}}
                <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-3 text-brand-700">
                    <ion-icon name="receipt-outline" class="text-3xl text-brand-700"></ion-icon>
                </div>
                <p class="text-base font-bold text-neutral-900 mb-1">No evaluations found</p>
                <p class="text-xs text-neutral-500 max-w-sm">
                    No evaluations match your search query or filter parameters. Try clearing your filters.
                </p>
                @if($hasFilters)
                    <a href="{{ route('admin.evaluations') }}" class="btn btn-primary btn-sm mt-4 font-bold inline-flex items-center gap-1 rounded-md">
                        <ion-icon name="refresh-outline"></ion-icon>
                        Reset Filters
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                {{-- CHANGED: dropped the "Student Evaluator" column (every row read "Anonymous student"; the details dialog still says so). Average moved next to the stall; Average and Date headers sort through the existing sort parameter. Criteria headers do not sort: the server has no per-criterion order. --}}
                <table class="w-full text-left border-collapse min-w-[760px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/80 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-200/80">
                            <th class="px-5 py-3">Stall</th>
                            <th class="px-4 py-3 text-right" aria-sort="{{ $sort === 'rating_low' ? 'ascending' : ($sort === 'rating_high' ? 'descending' : 'none') }}">
                                <a href="{{ $sortUrl($sort === 'rating_low' ? 'rating_high' : 'rating_low') }}" class="inline-flex items-center gap-1 uppercase hover:text-neutral-900 {{ str_starts_with($sort, 'rating') ? 'text-neutral-900' : '' }}">
                                    Average
                                    <ion-icon name="{{ $sort === 'rating_low' ? 'arrow-up-outline' : ($sort === 'rating_high' ? 'arrow-down-outline' : 'swap-vertical-outline') }}" class="text-xs" aria-hidden="true"></ion-icon>
                                </a>
                            </th>
                            <th class="px-3 py-3 text-right">Cleanliness</th>
                            <th class="px-3 py-3 text-right">Service</th>
                            <th class="px-3 py-3 text-right">Taste</th>
                            <th class="px-3 py-3 text-right">Price</th>
                            <th class="pl-6 pr-5 py-3">Feedback</th>
                            <th class="px-5 py-3 text-right" aria-sort="{{ $sort === 'oldest' ? 'ascending' : ($sort === 'latest' ? 'descending' : 'none') }}">
                                <a href="{{ $sortUrl($sort === 'latest' ? 'oldest' : 'latest') }}" class="inline-flex items-center gap-1 uppercase hover:text-neutral-900 {{ in_array($sort, ['latest', 'oldest']) ? 'text-neutral-900' : '' }}">
                                    Date
                                    <ion-icon name="{{ $sort === 'oldest' ? 'arrow-up-outline' : ($sort === 'latest' ? 'arrow-down-outline' : 'swap-vertical-outline') }}" class="text-xs" aria-hidden="true"></ion-icon>
                                </a>
                            </th>
                            <th class="w-12 px-3 py-3"><span class="sr-only">Details</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($evaluations as $eval)
                            {{-- CHANGED: the whole row opens the details dialog; the eye button is the keyboard target and its click bubbles here. --}}
                            <tr class="hover:bg-neutral-50 transition-colors cursor-pointer" onclick='openEvalDetailsModal(@json($eval))'>
                                <td class="px-5 py-3 font-medium text-neutral-900 whitespace-nowrap">{{ $eval->stall_name }}</td>

                                {{-- CHANGED: plain numbers, two decimals everywhere; colour only marks outliers. Average is the only bold score. --}}
                                <td class="px-4 py-3 text-right">{!! $score($eval->avg, 'font-bold') !!}</td>
                                @foreach(['cleanliness', 'service', 'taste', 'price'] as $c)
                                    <td class="px-3 py-3 text-right">{!! $score($eval->$c) !!}</td>
                                @endforeach

                                {{-- CHANGED: one-line snippet when there is a comment, a dash when there is none. --}}
                                <td class="pl-6 pr-5 py-3 text-xs text-neutral-700 max-w-[280px]">
                                    @if($eval->comment)
                                        <p class="truncate" title="{{ $eval->comment }}">{{ $eval->comment }}</p>
                                    @else
                                        <span class="text-neutral-500" aria-label="No comment">–</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-right text-xs text-neutral-500 whitespace-nowrap tabular-nums">
                                    <time datetime="{{ $eval->at->toIso8601String() }}" title="{{ $eval->at_label }}">{{ $eval->at->format('M d, Y') }}</time>
                                </td>

                                <td class="px-3 py-3 text-right">
                                    <button type="button" aria-label="View evaluation for {{ $eval->stall_name }}"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-md text-neutral-500 hover:text-brand-700 hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-600 cursor-pointer">
                                        <ion-icon name="eye-outline" class="text-base" aria-hidden="true"></ion-icon>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($evaluations as $eval)
                        {{-- CHANGED: the stall name leads the card instead of "Anonymous student"; the whole card opens the details dialog. --}}
                        <div class="p-4 flex flex-col gap-2.5 hover:bg-neutral-50 transition-colors cursor-pointer" onclick='openEvalDetailsModal(@json($eval))'>
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $eval->stall_name }}</h3>
                                    <time datetime="{{ $eval->at->toIso8601String() }}" class="block text-[11px] text-neutral-500 mt-0.5">{{ $eval->at->format('M d, Y • h:i A') }}</time>
                                </div>
                                <div class="shrink-0 flex items-center gap-1">
                                    {!! $score($eval->avg, 'text-sm font-bold') !!}
                                    <button type="button" aria-label="View evaluation for {{ $eval->stall_name }}"
                                        class="w-9 h-9 -mr-2 inline-flex items-center justify-center rounded-md text-neutral-500 hover:text-brand-700 cursor-pointer">
                                        <ion-icon name="eye-outline" class="text-base" aria-hidden="true"></ion-icon>
                                    </button>
                                </div>
                            </div>

                            @if($eval->comment)
                                <p class="text-xs text-neutral-700 line-clamp-2">{{ $eval->comment }}</p>
                            @endif

                            {{-- CHANGED: criteria labels raised from 9px neutral-400 to 11px neutral-500. "Service" shortened to "Serv" so it fits the narrow column at 11px, as on the overview. --}}
                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs bg-neutral-50 p-2 rounded-md border border-neutral-100">
                                @foreach(['cleanliness' => 'Clean', 'service' => 'Serv', 'taste' => 'Taste', 'price' => 'Price'] as $c => $label)
                                    <div>
                                        <span class="block text-[11px] font-semibold text-neutral-500 uppercase">{{ $label }}</span>
                                        {!! $score($eval->$c) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            <div class="px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                    <span>
                        Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->total() }}</strong> evaluations
                    </span>

                    {{-- Per Page Selector --}}
                    {{-- CHANGED: "PER PAGE" uppercase bold became sentence-case "Per page", like the rest of the footer. --}}
                    <div class="flex items-center gap-1.5 border-l border-neutral-200 pl-3">
                        <label for="per_page_select" class="text-xs text-neutral-500">Per page</label>
                        <select id="per_page_select" onchange="window.location.href = this.value" class="bg-white border border-neutral-200 rounded-md px-2 py-1 text-xs font-semibold focus:outline-none focus:border-brand-600 cursor-pointer">
                            @foreach([10, 25, 50] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $evaluations->perPage() == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Pagination Controls --}}
                {{-- CHANGED: Previous, Next and page-number buttons rounded-lg to rounded-md (controls are md). --}}
                <div class="flex items-center gap-1 order-1 sm:order-2">
                    {{-- Previous Page Link --}}
                    @if($evaluations->onFirstPage())
                        <span class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </span>
                    @else
                        <a href="{{ $evaluations->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="hidden sm:flex items-center gap-1">
                        @if($evaluations->hasPages())
                            @foreach($evaluations->getUrlRange(max(1, $evaluations->currentPage() - 2), min($evaluations->lastPage(), $evaluations->currentPage() + 2)) as $page => $url)
                                @if($page == $evaluations->currentPage())
                                    <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                1
                            </span>
                        @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if($evaluations->hasMorePages())
                        <a href="{{ $evaluations->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </a>
                    @else
                        <span class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ── 4. Evaluation Breakdown & Feedback Modal ────────────────────── --}}
<dialog id="eval-details-modal" class="confirm-modal modal-sharp max-w-lg w-full rounded-lg p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-brand-900 text-white px-5 py-4 flex items-center justify-between border-b border-brand-950">
        <div class="flex items-center gap-3">
            {{-- CHANGED: static person icon and "Anonymous student"; the modal no longer receives a name. --}}
            <div class="w-10 h-10 rounded-md bg-brand-800 border border-brand-700/80 text-white text-lg flex items-center justify-center shrink-0">
                <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white leading-tight tracking-tight">Anonymous student</h3>
                <p id="modal-eval-stall" class="text-xs text-brand-200 font-semibold mt-0.5"></p>
            </div>
        </div>
        <button type="button" class="js-close-eval-modal text-white/70 hover:text-white hover:bg-brand-800 p-1.5 rounded-md transition-colors cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="p-5 sm:p-6 space-y-4 bg-white text-xs">
        {{-- Criteria Breakdown Sheet --}}
        <div>
            {{-- CHANGED: section label raised from 10px neutral-400 to 11px neutral-500. --}}
            <span class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-2">
                4-Criteria Evaluation Breakdown
            </span>
            <div class="grid grid-cols-2 gap-px bg-neutral-200 border border-neutral-200 rounded-md overflow-hidden">
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Cleanliness</span>
                    <span id="modal-eval-cleanliness" class="font-bold text-neutral-900 flex items-center gap-1 tabular-nums"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Service Quality</span>
                    <span id="modal-eval-service" class="font-bold text-neutral-900 flex items-center gap-1 tabular-nums"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Food Taste</span>
                    <span id="modal-eval-taste" class="font-bold text-neutral-900 flex items-center gap-1 tabular-nums"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Price Affordability</span>
                    <span id="modal-eval-price" class="font-bold text-neutral-900 flex items-center gap-1 tabular-nums"></span>
                </div>
            </div>
        </div>

        {{-- Overall Score Box --}}
        <div class="p-3.5 bg-neutral-50 border border-neutral-200 rounded-md flex items-center justify-between">
            <div>
                {{-- CHANGED: "Overall Average Score" to "Average score", matching the table header. --}}
                <p class="text-xs font-bold text-neutral-900">Average score</p>
                <p class="text-[11px] text-neutral-500" id="modal-eval-date"></p>
            </div>
            {{-- CHANGED: font-extrabold to font-bold; star ion-icon to ★ glyph. --}}
            <div class="flex items-center gap-0.5 px-3 py-1 bg-brand-50 border border-brand-200 text-brand-900 font-bold text-sm rounded-md tabular-nums">
                <span id="modal-eval-avg"></span>★
            </div>
        </div>

        {{-- Student Feedback Comment --}}
        <div>
            {{-- CHANGED: section label raised from 10px neutral-400 to 11px neutral-500. --}}
            <span class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-2">
                Written Feedback & Observations
            </span>
            <div id="modal-eval-comment-box" class="bg-neutral-50/80 p-3.5 rounded-md border border-neutral-200 text-neutral-700 italic text-xs leading-relaxed">
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end px-5 py-3.5 bg-neutral-50 border-t border-neutral-200">
        <button type="button" class="btn btn-primary text-xs px-4 py-2 font-bold rounded-md js-close-eval-modal cursor-pointer">
            Done
        </button>
    </div>
</dialog>

@section('scripts')
<script>
var evalModal = document.getElementById('eval-details-modal');

// CHANGED: the average and the Philippine-time date label arrive on evalData (avg, at_label), set by the server.
function openEvalDetailsModal(evalData) {
    // CHANGED: removed the student name and avatar letter lines; the modal header is static "Anonymous student".
    document.getElementById('modal-eval-stall').textContent = 'Rated: ' + (evalData.stall_name || 'Stall');

    // CHANGED: two decimals, matching the table and the leaderboard.
    ['cleanliness', 'service', 'taste', 'price'].forEach(function(k) {
        document.getElementById('modal-eval-' + k).textContent = Number(evalData[k]).toFixed(2) + '★';
    });
    document.getElementById('modal-eval-avg').textContent = Number(evalData.avg).toFixed(2);

    // CHANGED: uses the server-formatted date. new Date() on the raw "YYYY-MM-DD HH:MM:SS" value read the UTC time as local time, and Safari cannot parse that format at all, so it showed "Invalid Date".
    document.getElementById('modal-eval-date').textContent = evalData.at_label ? 'Submitted on ' + evalData.at_label : '';

    var commentBox = document.getElementById('modal-eval-comment-box');
    if (evalData.comment && evalData.comment.trim() !== '') {
        // CHANGED: typographic quotes.
        commentBox.textContent = '“' + evalData.comment + '”';
        commentBox.className = 'bg-neutral-50/80 p-3.5 rounded-md border border-neutral-200 text-neutral-700 italic text-xs leading-relaxed';
    } else {
        commentBox.textContent = 'No written comment provided with this evaluation submission.';
        // CHANGED: neutral-400 to neutral-500 for contrast.
        commentBox.className = 'bg-neutral-50/80 p-3.5 rounded-md border border-neutral-200 text-neutral-500 italic text-xs leading-relaxed';
    }

    evalModal.showModal();
}

document.querySelectorAll('.js-close-eval-modal').forEach(function(b) {
    b.addEventListener('click', function() { evalModal.close(); });
});

evalModal.addEventListener('click', function(e) {
    var r = evalModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        evalModal.close();
    }
});
</script>
@endsection
@endsection
