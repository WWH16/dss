@extends('layouts.dashboard')

@section('title', 'Staff Dashboard | Decision Support System')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Unassigned Stall State ───────────────────────────────────────── --}}
    @if(!$hasStall || !$stall)
        <div class="bg-white rounded-xl border border-neutral-200/80 p-8 sm:p-12 text-center shadow-xs">
            <div class="w-16 h-16 rounded-xl bg-amber-50 border border-amber-200/80 flex items-center justify-center mb-4 text-amber-700 mx-auto">
                <ion-icon name="storefront-outline" class="text-3xl"></ion-icon>
            </div>
            <h2 class="text-xl font-bold text-neutral-900 tracking-tight mb-2">No Canteen Stall Assigned</h2>
            <p class="text-xs sm:text-sm text-neutral-500 max-w-md mx-auto leading-relaxed mb-6">
                Your staff account is currently not linked to any active food stall. Detailed evaluations and performance metrics are scoped to assigned stalls.
            </p>
            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="{{ route('staff.profile') }}" class="btn btn-secondary text-xs px-4 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs border border-neutral-200">
                    <ion-icon name="person-outline" class="text-sm"></ion-icon>
                    View Account Profile
                </a>
                <a href="{{ route('staff.standings') }}" class="btn btn-primary text-xs px-4 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs">
                    <ion-icon name="podium-outline" class="text-sm"></ion-icon>
                    View Campus Standings
                </a>
            </div>
        </div>
    @else

        {{-- ── 2. Page Header with Assigned Stall Scope ───────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-neutral-200/70">
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">{{ $stall->name }}</h1>
                    @if($stallRank)
                        @if($stallRank === 1)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-950 border border-amber-300 font-extrabold text-xs shadow-2xs">
                                <ion-icon name="trophy" class="text-amber-600 text-xs"></ion-icon>
                                #1 on Campus
                            </span>
                        @elseif($stallRank === 2)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon>
                                #2 on Campus
                            </span>
                        @elseif($stallRank === 3)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-600/30 font-bold text-xs shadow-2xs">
                                <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon>
                                #3 on Campus
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-700 font-semibold text-xs border border-neutral-200">
                                Rank #{{ $stallRank }} of {{ $totalStalls }}
                            </span>
                        @endif
                    @endif
                </div>
                <p class="text-xs text-neutral-500">
                    Canteen Staff Portal • Evaluation analytics for <strong class="text-neutral-800 font-semibold">{{ $stall->name }}</strong>
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <a href="{{ route('staff.standings') }}" class="btn btn-secondary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 border border-neutral-200 shadow-2xs hover:bg-neutral-50">
                    <ion-icon name="podium-outline" class="text-sm text-neutral-600"></ion-icon>
                    Campus Standings
                </a>
            </div>
        </div>

        {{-- ── 3. Stall Metric Cards ───────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            {{-- Overall Average --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Overall Score</span>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200/70 flex items-center justify-center text-amber-700">
                        <ion-icon name="star" class="text-lg text-amber-500"></ion-icon>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                        {{ number_format($averages ? (float)$averages->overall : 0, 2) }}
                    </span>
                    <span class="text-xs text-neutral-400 font-semibold">/ 5.00</span>
                </div>
                <div class="mt-3 pt-2 border-t border-neutral-100 text-xs text-neutral-400 font-medium">
                    Composite score across all 4 criteria
                </div>
            </div>

            {{-- Total Evaluations --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Total Evaluations</span>
                    <div class="w-9 h-9 rounded-lg bg-brand-50 border border-brand-100/70 flex items-center justify-center text-brand-700">
                        <ion-icon name="create-outline" class="text-lg"></ion-icon>
                    </div>
                </div>
                <div class="text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $totalEvaluations }}
                </div>
                <div class="mt-3 pt-2 border-t border-neutral-100 text-xs text-neutral-400 font-medium">
                    Feedback records logged for this stall
                </div>
            </div>

            {{-- Unique Student Evaluators --}}
            <div class="bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Student Evaluators</span>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200/70 flex items-center justify-center text-emerald-700">
                        <ion-icon name="people-outline" class="text-lg"></ion-icon>
                    </div>
                </div>
                <div class="text-3xl font-black text-neutral-900 tabular-nums tracking-tight">
                    {{ $uniqueStudents }}
                </div>
                <div class="mt-3 pt-2 border-t border-neutral-100 text-xs text-neutral-400 font-medium">
                    Unique student respondents
                </div>
            </div>
        </div>

        {{-- ── 4. Criteria Performance Breakdown ───────────────────────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 p-5 sm:p-6 shadow-sm">
            <div class="pb-3 mb-4 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight">Performance by Criteria</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Average student ratings per evaluation category</p>
                </div>
                <span class="text-xs text-neutral-400 font-medium">Max rating: 5.00★</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                {{-- Cleanliness --}}
                @php $clean = $averages ? (float)$averages->cleanliness : 0; @endphp
                <div class="p-3.5 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5">
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Cleanliness</span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xl font-bold tabular-nums {{ $clean >= 4 ? 'text-emerald-700' : ($clean >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                            {{ number_format($clean, 2) }}
                        </span>
                        <span class="text-[10px] font-bold text-neutral-400">/ 5.00</span>
                    </div>
                    <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full {{ $clean >= 4 ? 'bg-emerald-500' : ($clean >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($clean / 5) * 100 }}%"></div>
                    </div>
                </div>

                {{-- Service --}}
                @php $serv = $averages ? (float)$averages->service : 0; @endphp
                <div class="p-3.5 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5">
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Service</span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xl font-bold tabular-nums {{ $serv >= 4 ? 'text-emerald-700' : ($serv >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                            {{ number_format($serv, 2) }}
                        </span>
                        <span class="text-[10px] font-bold text-neutral-400">/ 5.00</span>
                    </div>
                    <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full {{ $serv >= 4 ? 'bg-emerald-500' : ($serv >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($serv / 5) * 100 }}%"></div>
                    </div>
                </div>

                {{-- Taste --}}
                @php $tst = $averages ? (float)$averages->taste : 0; @endphp
                <div class="p-3.5 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5">
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Food Taste</span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xl font-bold tabular-nums {{ $tst >= 4 ? 'text-emerald-700' : ($tst >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                            {{ number_format($tst, 2) }}
                        </span>
                        <span class="text-[10px] font-bold text-neutral-400">/ 5.00</span>
                    </div>
                    <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full {{ $tst >= 4 ? 'bg-emerald-500' : ($tst >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($tst / 5) * 100 }}%"></div>
                    </div>
                </div>

                {{-- Price --}}
                @php $prc = $averages ? (float)$averages->price : 0; @endphp
                <div class="p-3.5 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5">
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Price Value</span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xl font-bold tabular-nums {{ $prc >= 4 ? 'text-emerald-700' : ($prc >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                            {{ number_format($prc, 2) }}
                        </span>
                        <span class="text-[10px] font-bold text-neutral-400">/ 5.00</span>
                    </div>
                    <div class="w-full bg-neutral-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full {{ $prc >= 4 ? 'bg-emerald-500' : ($prc >= 3 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ ($prc / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Detailed Evaluations List (Strict Student Privacy) ────────── --}}
        <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
            {{-- Header with Search & Filter Controls --}}
            <div class="p-5 sm:p-6 pb-4 border-b border-neutral-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                            <span>Student Evaluations</span>
                            <span class="text-[11px] font-semibold text-neutral-500 bg-neutral-100 px-2 py-0.5 rounded-md">Anonymous Submissions</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Feedback logs submitted by students for your stall</p>
                    </div>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('staff.dashboard') }}" class="flex items-center gap-2">
                        <div class="relative w-full sm:w-56">
                            <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm pointer-events-none"></ion-icon>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search in comments…"
                                class="w-full pl-8 pr-3 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
                        </div>
                        <select name="sort" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="rating_high" {{ request('sort') === 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                            <option value="rating_low" {{ request('sort') === 'rating_low' ? 'selected' : '' }}>Lowest Rating</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                        @if(request('q') || request('sort'))
                            <a href="{{ route('staff.dashboard') }}" class="text-xs text-neutral-500 hover:text-neutral-900 font-semibold shrink-0">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            @if($evaluations->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-lg bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400 mx-auto">
                        <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                    </div>
                    <p class="text-sm font-bold text-neutral-900 mb-0.5">No evaluations found</p>
                    <p class="text-xs text-neutral-500 max-w-xs mx-auto">
                        {{ request('q') ? 'No student feedback matched your search criteria.' : 'Your stall has not received any student evaluations yet.' }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[650px] hidden md:table">
                        <thead>
                            <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                                <th class="py-3.5 px-5 font-semibold">Date</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                                <th class="py-3.5 px-3 text-center font-semibold">Average</th>
                                <th class="py-3.5 px-5 font-semibold">Student Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            @foreach($evaluations as $eval)
                                @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                                <tr class="hover:bg-neutral-50/60 transition-colors">
                                    <td class="py-3.5 px-5 font-medium text-neutral-600 text-xs tabular-nums whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                        <span class="block text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->cleanliness >= 4 ? 'text-emerald-700' : ($eval->cleanliness <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->cleanliness }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->service >= 4 ? 'text-emerald-700' : ($eval->service <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->service }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->taste >= 4 ? 'text-emerald-700' : ($eval->taste <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->taste }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->price >= 4 ? 'text-emerald-700' : ($eval->price <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                        {{ $eval->price }}★
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800' }}">
                                            {{ number_format($avg, 1) }}★
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @if($eval->comment)
                                            <p class="text-xs text-neutral-700 line-clamp-2 max-w-xs font-normal" title="{{ $eval->comment }}">
                                                "{{ $eval->comment }}"
                                            </p>
                                        @else
                                            <span class="text-neutral-400 text-xs italic">No comment provided</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Mobile View -->
                    <div class="md:hidden divide-y divide-neutral-100">
                        @foreach($evaluations as $eval)
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <div class="p-4 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-neutral-600 tabular-nums">
                                        {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </div>
                                <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->cleanliness }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Serv</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->service }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->taste }}★</span>
                                    </div>
                                    <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                        <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                        <span class="font-bold text-neutral-900">{{ $eval->price }}★</span>
                                    </div>
                                </div>
                                @if($eval->comment)
                                    <p class="text-xs text-neutral-700 bg-neutral-50 p-2.5 rounded-md border border-neutral-200/60 mt-1 italic">
                                        "{{ $eval->comment }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-4 border-t border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-neutral-500">
                    <div>
                        Showing <strong class="text-neutral-900">{{ $evaluations->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900">{{ $evaluations->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900">{{ $evaluations->total() }}</strong> submissions
                    </div>
                    <div>
                        {{ $evaluations->links() }}
                    </div>
                </div>
            @endif
        </div>

    @endif
</div>
@endsection