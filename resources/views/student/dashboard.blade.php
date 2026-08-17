@extends('layouts.dashboard')

@section('title', 'Student Dashboard | DSS')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── 1. Page Header & Greeting Bar ───────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-brand-50 border border-brand-200/80 text-brand-700 flex items-center justify-center font-black text-lg shrink-0 shadow-2xs">
                {{ strtoupper(substr($profile->name ?? ($profile->student_number ?? 'S'), 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-bold text-neutral-900 truncate tracking-tight leading-tight">
                        Hello, {{ $profile->name ?? 'Student' }}
                    </h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                        {{ $profile->student_number ?? 'Student' }}
                    </span>
                </div>
                <p class="text-xs text-neutral-500 mt-0.5">
                    Evaluate campus food stalls to help maintain quality dining standards.
                </p>
            </div>
        </div>

        <a href="{{ route('student.evaluation') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs shrink-0 self-start sm:self-auto">
            <ion-icon name="create-outline" class="text-sm"></ion-icon>
            Evaluate a Stall
        </a>
    </div>

    {{-- ── 2. Summary Metric Cards (3 Balanced Columns) ───────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Metric 1: Total Reviews --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Reviews Submitted</span>
                <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-100/70 flex items-center justify-center text-brand-700">
                    <ion-icon name="receipt-outline" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                {{ $totalEvalsCount }}
            </div>
            <div class="mt-2 pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                {{ $totalEvalsCount === 1 ? '1 evaluation logged' : $totalEvalsCount . ' evaluations logged' }}
            </div>
        </div>

        {{-- Metric 2: Stall Coverage --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Campus Coverage</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200/70 flex items-center justify-center text-emerald-700">
                    <ion-icon name="storefront-outline" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $uniqueEvaluatedCount }} <span class="text-xs text-neutral-400 font-semibold font-sans">/ {{ $totalStallsCount }}</span>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md tabular-nums">
                    {{ $coveragePct }}%
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-neutral-100">
                <div class="w-full bg-neutral-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $coveragePct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Metric 3: Avg Rating Given --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs flex flex-col justify-between hover:border-neutral-300 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Avg Rating Given</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200/70 flex items-center justify-center text-amber-700">
                    <ion-icon name="star" class="text-base text-amber-500"></ion-icon>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $totalEvalsCount > 0 ? number_format($overallAvgGiven, 2) . '★' : '—' }}
                </span>
                @if($totalEvalsCount > 0)
                    <span class="text-xs text-neutral-400 font-semibold">/ 5.00</span>
                @endif
            </div>
            <div class="mt-2 pt-2 border-t border-neutral-100 text-[11px] text-neutral-500 font-medium">
                {{ $totalEvalsCount > 0 ? 'Your evaluation baseline' : 'No ratings yet' }}
            </div>
        </div>
    </div>

    {{-- ── 2. Main Analytics & Actions (2-Column Asymmetric Grid) ──────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        {{-- LEFT: Food Stalls Directory (2 Cols) --}}
        <div class="lg:col-span-2 space-y-4">
            
            {{-- Stalls Directory Header Card with Search & Filters --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-neutral-100 gap-3">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="restaurant-outline" class="text-brand-700 text-base"></ion-icon>
                            Campus Food Stalls
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            Select a stall below to submit or update your evaluation
                        </p>
                    </div>

                    <a href="{{ route('student.evaluation') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-800 text-xs font-bold rounded-lg border border-brand-200/80 transition-colors self-start sm:self-auto shrink-0 shadow-2xs">
                        <ion-icon name="create-outline" class="text-sm"></ion-icon>
                        Open Evaluation Form
                    </a>
                </div>

                {{-- Search & Filter Bar --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></ion-icon>
                        <input type="text" id="stallSearchInput" placeholder="Search stall by name..."
                            aria-label="Search stalls by name"
                            class="w-full bg-neutral-50 border border-neutral-200 rounded-lg pl-9 pr-8 py-1.5 text-xs font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                        <button type="button" id="clearSearchBtn" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 text-xs">
                            <ion-icon name="close-circle"></ion-icon>
                        </button>
                    </div>

                    {{-- Filter Chips --}}
                    <div class="flex items-center gap-1.5 shrink-0" role="tablist" aria-label="Filter stalls by rating status">
                        <button type="button" class="filter-pill active px-3 py-1 rounded-md text-xs font-bold bg-neutral-900 text-white transition-all shadow-2xs" data-filter="all">
                            All ({{ $totalStallsCount }})
                        </button>
                        <button type="button" class="filter-pill px-3 py-1 rounded-md text-xs font-bold bg-neutral-100 text-neutral-600 hover:bg-neutral-200/70 transition-all" data-filter="needs_rating">
                            Needs Rating ({{ max(0, $totalStallsCount - $uniqueEvaluatedCount) }})
                        </button>
                        <button type="button" class="filter-pill px-3 py-1 rounded-md text-xs font-bold bg-neutral-100 text-neutral-600 hover:bg-neutral-200/70 transition-all" data-filter="rated">
                            Rated ({{ $uniqueEvaluatedCount }})
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stalls Grid --}}
            @if($stalls->isEmpty())
                <div class="bg-white border border-neutral-200/80 rounded-xl p-10 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-xl bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="storefront-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h3 class="text-sm font-bold text-neutral-900">No Food Stalls Available</h3>
                    <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">There are currently no food stalls registered in the system for evaluation.</p>
                </div>
            @else
                <div id="stallsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($stalls as $stall)
                        @php
                            $evalInfo = $evaluatedStallsMap->get($stall->id);
                            $isRated = !is_null($evalInfo);
                        @endphp
                        <div class="stall-card bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-xs hover:border-brand-400/80 hover:shadow-sm transition-all duration-200 flex flex-col justify-between"
                            data-name="{{ strtolower($stall->name) }}"
                            data-status="{{ $isRated ? 'rated' : 'needs_rating' }}">
                            
                            <div>
                                {{-- Card Header: Icon & Status Badge --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-neutral-50 border border-neutral-200/70 text-neutral-700 flex items-center justify-center shrink-0">
                                        <ion-icon name="storefront-outline" class="text-lg text-brand-700"></ion-icon>
                                    </div>

                                    @if($isRated)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shrink-0">
                                            <ion-icon name="checkmark-circle" class="text-xs text-emerald-600"></ion-icon>
                                            Rated ({{ number_format($evalInfo['latest_avg'], 1) }}★)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Needs Rating
                                        </span>
                                    @endif
                                </div>

                                {{-- Stall Name & Description --}}
                                <h3 class="font-bold text-neutral-900 text-sm leading-snug line-clamp-1">
                                    {{ $stall->name }}
                                </h3>
                                <p class="text-xs text-neutral-500 mt-1 line-clamp-2">
                                    {{ $stall->description ?? 'Campus food stall offering meals and refreshments.' }}
                                </p>

                                @if($isRated)
                                    <p class="text-[10px] text-neutral-400 font-medium mt-2">
                                        Last evaluated {{ \Carbon\Carbon::parse($evalInfo['latest_date'])->diffForHumans() }} ({{ $evalInfo['eval_count'] }} {{ Str::plural('time', $evalInfo['eval_count']) }})
                                    </p>
                                @endif
                            </div>

                            {{-- Action Button --}}
                            <div class="mt-4 pt-3 border-t border-neutral-100">
                                <a href="{{ route('student.evaluation', ['stall' => $stall->id]) }}" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-bold transition-all {{ $isRated ? 'bg-neutral-50 hover:bg-neutral-100 text-neutral-700 border border-neutral-200' : 'bg-brand-600 hover:bg-brand-700 text-white shadow-2xs' }}">
                                    <ion-icon name="{{ $isRated ? 'sync-outline' : 'star-outline' }}" class="text-sm"></ion-icon>
                                    <span>{{ $isRated ? 'Rate Again' : 'Rate Stall' }}</span>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Empty Search Results Notice --}}
                <div id="noSearchResults" class="hidden bg-white border border-neutral-200/80 rounded-xl p-8 text-center shadow-xs">
                    <ion-icon name="search-outline" class="text-2xl text-neutral-400 mx-auto mb-2"></ion-icon>
                    <p class="text-xs font-bold text-neutral-800">No matching food stalls found</p>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Try adjusting your search terms or filter selection.</p>
                </div>
            @endif

        </div>

        {{-- RIGHT: Campus Spotlight & Review History (1 Col) --}}
        <div class="space-y-5">

            {{-- 1. Campus Top Stall Spotlight --}}
            @if($topCampusStall)
                <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-neutral-100">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-300 font-extrabold text-[11px] shadow-2xs">
                            <ion-icon name="trophy" class="text-amber-600 text-xs"></ion-icon>
                            #1 Campus Favorite
                        </span>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">DSS Benchmark</span>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-neutral-900 tracking-tight leading-tight">
                            {{ $topCampusStall->name }}
                        </h3>
                        <p class="text-xs text-neutral-500 mt-1 line-clamp-2">
                            {{ $topCampusStall->description ?? 'Highest rated campus dining establishment.' }}
                        </p>

                        <div class="flex items-center justify-between bg-neutral-50 border border-neutral-200/70 rounded-lg p-2.5 mt-3">
                            <div>
                                <span class="text-[10px] font-semibold text-neutral-500 block">Overall Score</span>
                                <span class="text-sm font-black text-neutral-900 tabular-nums">
                                    {{ number_format((float)$topCampusStall->overall_score, 2) }}★
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-semibold text-neutral-500 block">Evaluations</span>
                                <span class="text-sm font-bold text-neutral-700 tabular-nums">
                                    {{ $topCampusStall->eval_count }} {{ Str::plural('review', $topCampusStall->eval_count) }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('student.evaluation', ['stall' => $topCampusStall->id]) }}" 
                            class="mt-3.5 w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-bold bg-neutral-900 hover:bg-neutral-800 text-white transition-colors shadow-2xs">
                            <ion-icon name="create-outline" class="text-sm"></ion-icon>
                            Evaluate {{ Str::words($topCampusStall->name, 2, '') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- 2. Recent Evaluations Ledger --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-neutral-100">
                    <div>
                        <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-1.5">
                            <ion-icon name="time-outline" class="text-neutral-500 text-sm"></ion-icon>
                            Your Recent Ratings
                        </h2>
                        <p class="text-[11px] text-neutral-500 mt-0.5">Latest reviews submitted</p>
                    </div>
                    @if($totalEvalsCount > 0)
                        <a href="{{ route('student.history') }}" class="text-[11px] text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-0.5 transition-colors">
                            History <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </a>
                    @endif
                </div>

                @if($myStudentEvals->isEmpty())
                    <div class="py-8 text-center flex flex-col items-center justify-center">
                        <div class="w-10 h-10 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mb-2">
                            <ion-icon name="time-outline" class="text-lg"></ion-icon>
                        </div>
                        <p class="text-xs font-bold text-neutral-800 mb-0.5">No evaluation history yet</p>
                        <p class="text-[11px] text-neutral-500 max-w-[200px] mb-3">Your submitted reviews will appear here.</p>
                        <a href="{{ route('student.evaluation') }}" class="text-xs font-bold text-brand-700 hover:underline">
                            Rate your first stall &rarr;
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-neutral-100">
                        @foreach($myStudentEvals->take(5) as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                            @endphp
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-2.5">
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-neutral-900 truncate">{{ $eval->stall_name ?? 'Stall' }}</h3>
                                    <p class="text-[10px] text-neutral-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-neutral-100 text-neutral-800 tabular-nums border border-neutral-200/60">
                                    {{ number_format($avg, 1) }} <ion-icon name="star" class="text-amber-500 text-[10px]"></ion-icon>
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($totalEvalsCount > 5)
                        <div class="mt-3 pt-3 border-t border-neutral-100 text-center">
                            <a href="{{ route('student.history') }}" class="text-xs font-bold text-neutral-600 hover:text-brand-700 transition-colors">
                                + {{ $totalEvalsCount - 5 }} more in history &rarr;
                            </a>
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

</div>

{{-- ── 3. Client-Side Stall Search & Filtering Logic ──────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('stallSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    const filterPills = document.querySelectorAll('.filter-pill');
    const stallCards = document.querySelectorAll('.stall-card');
    const noResults = document.getElementById('noSearchResults');

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
            filterPills.forEach(p => {
                p.classList.remove('active', 'bg-neutral-900', 'text-white', 'shadow-2xs');
                p.classList.add('bg-neutral-100', 'text-neutral-600');
            });

            pill.classList.add('active', 'bg-neutral-900', 'text-white', 'shadow-2xs');
            pill.classList.remove('bg-neutral-100', 'text-neutral-600');

            currentFilter = pill.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });
});
</script>
@endsection