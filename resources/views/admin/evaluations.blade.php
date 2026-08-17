@extends('layouts.dashboard')
@section('title', 'Evaluations | Admin — DSS')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────── --}}
    <div class="pb-2 border-b border-neutral-200/70">
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2.5">
            <ion-icon name="receipt-outline" class="text-brand-700 text-2xl"></ion-icon>
            Evaluations
        </h1>
        <p class="text-xs sm:text-sm font-medium text-neutral-500 mt-0.5">
            Comprehensive history of student feedback, stall ratings, and criteria breakdowns.
        </p>
    </div>

    @php
        $activeFilterCount = 0;
        if (request('stall_id')) $activeFilterCount++;
        if (request('sort') && request('sort') !== 'latest') $activeFilterCount++;
        $hasFilters = $activeFilterCount > 0 || request('q');
    @endphp

    {{-- ── 2. Search & Multi-Filter Toolbar ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-2xs p-4 sm:p-5">
        <form id="evaluations-filter-form" method="GET" action="{{ route('admin.evaluations') }}" class="space-y-4">
            
            {{-- Top Row: Search & Toggle --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                {{-- Search Bar --}}
                <div class="flex-1 relative">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search evaluator name, stall, or comment keywords…"
                        class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                </div>

                {{-- Action Controls --}}
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    {{-- Toggle Filters Button --}}
                    <button type="button" id="toggle-filter-btn" onclick="toggleEvalFilterDrawer()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all border cursor-pointer {{ $activeFilterCount > 0 ? 'bg-brand-50 text-brand-800 border-brand-300 shadow-2xs' : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50' }}">
                        <ion-icon name="options-outline" class="text-base text-brand-700"></ion-icon>
                        <span id="filter-toggle-label">{{ $activeFilterCount > 0 ? 'Hide Filters' : 'Show Filters' }}</span>
                        @if($activeFilterCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-brand-600 text-white font-bold tabular-nums">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Clear Filters Button --}}
                    @if($hasFilters)
                        <a href="{{ route('admin.evaluations') }}" class="btn btn-ghost text-xs sm:text-sm py-2 px-3 font-semibold flex items-center gap-1 border border-neutral-200 rounded-lg text-neutral-600 hover:text-neutral-900" title="Reset all filters">
                            <ion-icon name="close-circle-outline" class="text-sm text-neutral-400"></ion-icon>
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            {{-- Collapsible Filter Drawer --}}
            <div id="eval-filter-drawer" class="{{ $activeFilterCount > 0 ? '' : 'hidden' }} pt-3 border-t border-neutral-100/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {{-- Food Stall Dropdown --}}
                    <div>
                        <label for="filter_stall_id" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Food Stall</label>
                        <select id="filter_stall_id" name="stall_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Canteen Stalls</option>
                            @foreach($stalls as $s)
                                <option value="{{ $s->id }}" {{ request('stall_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Order Dropdown --}}
                    <div>
                        <label for="filter_sort" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Sort Order</label>
                        <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Evaluations First</option>
                            <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Highest Overall Rating</option>
                            <option value="rating_low" {{ request('sort') == 'rating_low' ? 'selected' : '' }}>Lowest Overall Rating</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Evaluations First</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results Count & Active Filters Indicator --}}
            <div class="flex items-center justify-between text-xs text-neutral-500 pt-1">
                <div>
                    @if($hasFilters)
                        <span>Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->total() }}</strong> of <span class="tabular-nums">{{ $totalEvaluationsCount }}</span> filtered evaluations</span>
                    @else
                        <span><strong class="text-neutral-900 font-bold tabular-nums">{{ $totalEvaluationsCount }}</strong> {{ Str::plural('evaluation', $totalEvaluationsCount) }} recorded</span>
                    @endif
                </div>
                @if(request('stall_id'))
                    @php $activeStall = $stalls->firstWhere('id', request('stall_id')); @endphp
                    @if($activeStall)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-50 border border-brand-200/60 text-brand-800 text-[10px] font-bold">
                            Stall: {{ $activeStall->name }}
                        </span>
                    @endif
                @endif
            </div>
        </form>
    </div>

    {{-- ── 3. Evaluations Ledger Table Card ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        @if($evaluations->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-xl bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400">
                    <ion-icon name="receipt-outline" class="text-3xl text-neutral-400"></ion-icon>
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
                <table class="w-full text-left border-collapse min-w-[900px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/80 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-200/80">
                            <th class="px-5 py-3.5">Student Evaluator</th>
                            <th class="px-5 py-3.5">Canteen Stall</th>
                            <th class="px-3 py-3.5 text-center">Cleanliness</th>
                            <th class="px-3 py-3.5 text-center">Service</th>
                            <th class="px-3 py-3.5 text-center">Taste</th>
                            <th class="px-3 py-3.5 text-center">Price</th>
                            <th class="px-4 py-3.5 text-center">Overall</th>
                            <th class="px-5 py-3.5">Feedback</th>
                            <th class="px-5 py-3.5 text-right">Date</th>
                            <th class="px-4 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($evaluations as $eval)
                            @php
                                $avg = round(($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4, 1);
                            @endphp
                            <tr class="hover:bg-neutral-50/70 transition-colors">
                                {{-- Student --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-200/70 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($eval->student_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-neutral-900 leading-tight truncate">{{ $eval->student_name }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Stall --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-800 border border-neutral-200/80">
                                        <ion-icon name="storefront-outline" class="text-xs text-neutral-500"></ion-icon>
                                        {{ $eval->stall_name }}
                                    </span>
                                </td>

                                {{-- Criteria Ratings --}}
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $eval->cleanliness >= 4 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($eval->cleanliness <= 2 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-neutral-50 text-neutral-700 border border-neutral-200/60') }}">
                                        {{ $eval->cleanliness }} <ion-icon name="star" class="text-amber-500 text-[10px]"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $eval->service >= 4 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($eval->service <= 2 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-neutral-50 text-neutral-700 border border-neutral-200/60') }}">
                                        {{ $eval->service }} <ion-icon name="star" class="text-amber-500 text-[10px]"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $eval->taste >= 4 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($eval->taste <= 2 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-neutral-50 text-neutral-700 border border-neutral-200/60') }}">
                                        {{ $eval->taste }} <ion-icon name="star" class="text-amber-500 text-[10px]"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $eval->price >= 4 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($eval->price <= 2 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-neutral-50 text-neutral-700 border border-neutral-200/60') }}">
                                        {{ $eval->price }} <ion-icon name="star" class="text-amber-500 text-[10px]"></ion-icon>
                                    </span>
                                </td>

                                {{-- Overall Rating --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-extrabold bg-brand-50 text-brand-900 border border-brand-200/80 tabular-nums">
                                        {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                    </span>
                                </td>

                                {{-- Feedback Comment --}}
                                <td class="px-5 py-3.5 text-xs text-neutral-600 max-w-[220px]">
                                    @if($eval->comment)
                                        <p class="truncate italic" title="{{ $eval->comment }}">
                                            "{{ $eval->comment }}"
                                        </p>
                                    @else
                                        <span class="text-neutral-400 font-normal">No comment</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-5 py-3.5 text-right text-xs text-neutral-500 font-medium whitespace-nowrap tabular-nums">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                </td>

                                {{-- Action --}}
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <button type="button"
                                        onclick='openEvalDetailsModal(@json($eval), {{ $avg }})'
                                        class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 transition-all bg-white px-2.5 py-1.5 rounded-md border border-brand-200 hover:bg-brand-50 shadow-2xs cursor-pointer">
                                        <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($evaluations as $eval)
                        @php
                            $avg = round(($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4, 1);
                        @endphp
                        <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50/70 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-200 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($eval->student_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $eval->student_name }}</h3>
                                        <p class="text-[11px] font-bold text-brand-700 mt-0.5 truncate">{{ $eval->stall_name }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 bg-brand-50 px-2 py-1 rounded-md text-xs font-extrabold text-brand-900 border border-brand-200 tabular-nums">
                                    {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                </span>
                            </div>

                            @if($eval->comment)
                                <div class="bg-neutral-50 p-3 rounded-md text-xs text-neutral-700 italic border border-neutral-200/60">
                                    "{{ $eval->comment }}"
                                </div>
                            @endif

                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs bg-neutral-50 p-2.5 rounded-md border border-neutral-100">
                                <div>
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900 text-xs">{{ $eval->cleanliness }}★</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Service</span>
                                    <span class="font-bold text-neutral-900 text-xs">{{ $eval->service }}★</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900 text-xs">{{ $eval->taste }}★</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900 text-xs">{{ $eval->price }}★</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 text-[11px] text-neutral-400">
                                <span>{{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y • h:i A') }}</span>
                                <button type="button"
                                    onclick='openEvalDetailsModal(@json($eval), {{ $avg }})'
                                    class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 bg-white px-2.5 py-1 rounded-md border border-brand-200 shadow-2xs">
                                    <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                                    Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            @if($evaluations->hasPages() || $evaluations->total() > 10)
                <div class="px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                        <span>
                            Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $evaluations->total() }}</strong> evaluations
                        </span>

                        {{-- Per Page Selector --}}
                        <div class="flex items-center gap-1.5 border-l border-neutral-200 pl-3">
                            <label for="per_page_select" class="text-[11px] font-bold text-neutral-400 uppercase">Per Page</label>
                            <select id="per_page_select" onchange="window.location.href = this.value" class="bg-white border border-neutral-200 rounded px-2 py-1 text-xs font-semibold focus:outline-none focus:border-brand-600">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $evaluations->perPage() == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Pagination Controls --}}
                    @if($evaluations->hasPages())
                        <div class="flex items-center gap-1 order-1 sm:order-2">
                            {{-- Previous Page Link --}}
                            @if($evaluations->onFirstPage())
                                <span class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                                    <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                                    Previous
                                </span>
                            @else
                                <a href="{{ $evaluations->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                    <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                                    Previous
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            <div class="hidden sm:flex items-center gap-1">
                                @foreach($evaluations->getUrlRange(max(1, $evaluations->currentPage() - 2), min($evaluations->lastPage(), $evaluations->currentPage() + 2)) as $page => $url)
                                    @if($page == $evaluations->currentPage())
                                        <span class="w-8 h-8 rounded-lg bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Next Page Link --}}
                            @if($evaluations->hasMorePages())
                                <a href="{{ $evaluations->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                                    Next
                                    <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                                </a>
                            @else
                                <span class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                                    Next
                                    <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>

</div>

{{-- ── 4. Evaluation Breakdown & Feedback Modal ────────────────────── --}}
<dialog id="eval-details-modal" class="confirm-modal modal-sharp max-w-lg w-full rounded-lg p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-brand-900 text-white px-5 py-4 flex items-center justify-between border-b border-brand-950">
        <div class="flex items-center gap-3">
            <div id="modal-eval-avatar" class="w-10 h-10 rounded-md bg-brand-800 border border-brand-700/80 text-white font-bold text-base flex items-center justify-center shrink-0">
            </div>
            <div>
                <h3 id="modal-eval-student" class="text-sm font-bold text-white leading-tight tracking-tight"></h3>
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
            <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-2">
                4-Criteria Evaluation Breakdown
            </span>
            <div class="grid grid-cols-2 gap-px bg-neutral-200 border border-neutral-200 rounded-md overflow-hidden">
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Cleanliness</span>
                    <span id="modal-eval-cleanliness" class="font-bold text-neutral-900 flex items-center gap-1"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Service Quality</span>
                    <span id="modal-eval-service" class="font-bold text-neutral-900 flex items-center gap-1"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Food Taste</span>
                    <span id="modal-eval-taste" class="font-bold text-neutral-900 flex items-center gap-1"></span>
                </div>
                <div class="bg-white p-3 flex items-center justify-between">
                    <span class="text-neutral-500 font-medium">Price Affordability</span>
                    <span id="modal-eval-price" class="font-bold text-neutral-900 flex items-center gap-1"></span>
                </div>
            </div>
        </div>

        {{-- Overall Score Box --}}
        <div class="p-3.5 bg-neutral-50 border border-neutral-200 rounded-md flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-neutral-900">Overall Average Score</p>
                <p class="text-[11px] text-neutral-500" id="modal-eval-date"></p>
            </div>
            <div class="flex items-center gap-1 px-3 py-1 bg-brand-50 border border-brand-200 text-brand-900 font-extrabold text-sm rounded-md tabular-nums">
                <span id="modal-eval-avg"></span>
                <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
            </div>
        </div>

        {{-- Student Feedback Comment --}}
        <div>
            <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-2">
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

function toggleEvalFilterDrawer() {
    var drawer = document.getElementById('eval-filter-drawer');
    var label = document.getElementById('filter-toggle-label');
    if (!drawer) return;

    var isHidden = drawer.classList.contains('hidden');
    if (isHidden) {
        drawer.classList.remove('hidden');
        if (label) label.textContent = 'Hide Filters';
    } else {
        drawer.classList.add('hidden');
        if (label) label.textContent = 'Show Filters';
    }
}

function openEvalDetailsModal(evalData, avgScore) {
    document.getElementById('modal-eval-student').textContent = evalData.student_name || 'Student';
    document.getElementById('modal-eval-stall').textContent = 'Rated: ' + (evalData.stall_name || 'Stall');
    document.getElementById('modal-eval-avatar').textContent = (evalData.student_name || 'S').charAt(0).toUpperCase();

    document.getElementById('modal-eval-cleanliness').innerHTML = evalData.cleanliness + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
    document.getElementById('modal-eval-service').innerHTML = evalData.service + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
    document.getElementById('modal-eval-taste').innerHTML = evalData.taste + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
    document.getElementById('modal-eval-price').innerHTML = evalData.price + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';

    document.getElementById('modal-eval-avg').textContent = avgScore;

    if (evalData.created_at) {
        var d = new Date(evalData.created_at);
        document.getElementById('modal-eval-date').textContent = 'Submitted on ' + d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    } else {
        document.getElementById('modal-eval-date').textContent = '';
    }

    var commentBox = document.getElementById('modal-eval-comment-box');
    if (evalData.comment && evalData.comment.trim() !== '') {
        commentBox.textContent = '"' + evalData.comment + '"';
        commentBox.className = 'bg-neutral-50/80 p-3.5 rounded-md border border-neutral-200 text-neutral-700 italic text-xs leading-relaxed';
    } else {
        commentBox.textContent = 'No written comment provided with this evaluation submission.';
        commentBox.className = 'bg-neutral-50/80 p-3.5 rounded-md border border-neutral-200 text-neutral-400 italic text-xs leading-relaxed';
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
