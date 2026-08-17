@extends('layouts.dashboard')

@section('title', 'Evaluation History | DSS')
@section('header_title', 'Evaluation History')

@section('content')
<div class="space-y-6">

    {{-- ── 1. Page Header & Action Bar ───────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-neutral-900 tracking-tight leading-tight">
                    My Evaluation History
                </h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-brand-50 text-brand-800 border border-brand-200/70 tabular-nums">
                    {{ $myStudentEvals->total() }} {{ Str::plural('Review', $myStudentEvals->total()) }}
                </span>
            </div>
            <p class="text-xs text-neutral-500 mt-1">
                A complete record of all your submitted ratings, criteria scores, and feedback for campus food stalls.
            </p>
        </div>

        <a href="{{ route('student.evaluation') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs shrink-0 self-start sm:self-auto">
            <ion-icon name="create-outline" class="text-sm"></ion-icon>
            Evaluate a Stall
        </a>
    </div>

    {{-- ── 2. Table Container Card ────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Search & Sort Toolbar --}}
        <div class="p-4 sm:p-5 border-b border-neutral-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('student.history') }}" method="GET" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2.5">
                {{-- Search Input --}}
                <div class="relative w-full sm:max-w-xs">
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></ion-icon>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search stall or feedback..."
                        class="w-full bg-neutral-50 border border-neutral-200 rounded-lg pl-9 pr-3 py-1.5 text-xs font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                </div>

                {{-- Sort Dropdown --}}
                <div class="w-full sm:w-auto">
                    <select name="sort" onchange="this.form.submit()"
                        class="w-full sm:w-auto bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-1.5 text-xs font-medium text-neutral-800 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors cursor-pointer">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="rating_high" {{ request('sort') === 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                        <option value="rating_low" {{ request('sort') === 'rating_low' ? 'selected' : '' }}>Lowest Rating</option>
                    </select>
                </div>

                @if(request('q') || request('sort'))
                    <a href="{{ route('student.history') }}" class="text-xs text-neutral-500 hover:text-rose-600 font-medium inline-flex items-center gap-1">
                        <ion-icon name="close-circle-outline"></ion-icon>
                        Reset filters
                    </a>
                @endif
            </form>
        </div>

        {{-- Table / List Content --}}
        @if($myStudentEvals->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-14 h-14 rounded-xl bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400">
                    <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-900 mb-1">No evaluations found</h3>
                <p class="text-xs text-neutral-500 max-w-sm mb-5">
                    {{ request('q') ? 'No submitted evaluations matched your search criteria.' : "You haven't submitted any stall evaluations yet. Your reviews will appear here." }}
                </p>
                <a href="{{ route('student.evaluation') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs">
                    <ion-icon name="add-outline" class="text-sm"></ion-icon>
                    Evaluate your first stall
                </a>
            </div>
        @else
            {{-- Desktop Table View (Dashboard standard) --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[760px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold">Food Stall</th>
                            <th class="py-3.5 px-4 font-semibold">Date Submitted</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Average</th>
                            <th class="py-3.5 px-5 font-semibold">Feedback</th>
                            <th class="py-3.5 px-4 text-right font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($myStudentEvals as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                            @endphp
                            <tr class="hover:bg-neutral-50/60 transition-colors group">
                                {{-- Stall Name --}}
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-neutral-50 border border-neutral-200 text-brand-700 flex items-center justify-center shrink-0">
                                            <ion-icon name="storefront-outline" class="text-base"></ion-icon>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-neutral-900 block truncate">{{ $eval->stall_name ?? 'Food Stall' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="py-3.5 px-4 text-xs text-neutral-600 font-medium tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                    <span class="block text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</span>
                                </td>

                                {{-- Criteria Scores --}}
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

                                {{-- Average --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800 border border-neutral-200' }}">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </td>

                                {{-- Feedback Comment --}}
                                <td class="py-3.5 px-5">
                                    @if($eval->comment)
                                        <p class="text-xs text-neutral-700 line-clamp-1 max-w-xs font-normal" title="{{ $eval->comment }}">
                                            "{{ $eval->comment }}"
                                        </p>
                                    @else
                                        <span class="text-neutral-400 text-xs italic">No comment</span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button type="button" 
                                        onclick='openEvalDetailsModal(@json($eval), {{ number_format($avg, 1) }})'
                                        class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:text-brand-800 hover:bg-brand-50 px-2.5 py-1 rounded-md transition-colors cursor-pointer">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Mobile Stacked List View --}}
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($myStudentEvals as $eval)
                        @php
                            $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                        @endphp
                        <div class="p-4 flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-neutral-50 border border-neutral-200 text-brand-700 flex items-center justify-center shrink-0">
                                        <ion-icon name="storefront-outline" class="text-base"></ion-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-neutral-900 truncate">{{ $eval->stall_name ?? 'Food Stall' }}</h3>
                                        <span class="text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y • h:i A') }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200 shrink-0">
                                    {{ number_format($avg, 1) }}★
                                </span>
                            </div>

                            {{-- 4 Criteria Grid --}}
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
                                <p class="text-xs text-neutral-600 bg-neutral-50/70 p-2.5 rounded-md border border-neutral-100 italic">
                                    "{{ $eval->comment }}"
                                </p>
                            @endif

                            <div class="flex justify-end pt-1">
                                <button type="button" 
                                    onclick='openEvalDetailsModal(@json($eval), {{ number_format($avg, 1) }})'
                                    class="text-xs font-bold text-brand-700 hover:underline">
                                    View Full Details &rarr;
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination Links --}}
            @if($myStudentEvals->hasPages())
                <div class="p-4 border-t border-neutral-100 bg-neutral-50/50">
                    {{ $myStudentEvals->links() }}
                </div>
            @endif
        @endif

    </div>

</div>

{{-- ── 3. Evaluation Detail Dialog Modal ───────────────────────────────── --}}
<dialog id="eval-details-modal" class="confirm-modal modal-sharp max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="px-6 py-4 bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                    <ion-icon name="storefront-outline" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h3 id="modal-stall-name" class="text-sm font-bold text-neutral-900 leading-tight"></h3>
                    <p id="modal-eval-date" class="text-[11px] text-neutral-500 mt-0.5"></p>
                </div>
            </div>
            <button type="button" class="js-close-eval-modal text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <div class="p-6 space-y-4">
            {{-- Overall Score Banner --}}
            <div class="flex items-center justify-between p-3.5 rounded-lg bg-brand-50/50 border border-brand-200/80">
                <span class="text-xs font-bold text-brand-900">Overall Rating Given</span>
                <span id="modal-eval-avg" class="text-base font-black text-brand-900 tabular-nums"></span>
            </div>

            {{-- 4 Criteria Breakdown --}}
            <div>
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2">Criteria Breakdown</h4>
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 rounded-lg bg-neutral-50 border border-neutral-200/70 flex items-center justify-between">
                        <span class="text-xs font-medium text-neutral-600">Cleanliness</span>
                        <span id="modal-cleanliness" class="text-xs font-bold text-neutral-900"></span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-neutral-50 border border-neutral-200/70 flex items-center justify-between">
                        <span class="text-xs font-medium text-neutral-600">Service</span>
                        <span id="modal-service" class="text-xs font-bold text-neutral-900"></span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-neutral-50 border border-neutral-200/70 flex items-center justify-between">
                        <span class="text-xs font-medium text-neutral-600">Taste</span>
                        <span id="modal-taste" class="text-xs font-bold text-neutral-900"></span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-neutral-50 border border-neutral-200/70 flex items-center justify-between">
                        <span class="text-xs font-medium text-neutral-600">Price</span>
                        <span id="modal-price" class="text-xs font-bold text-neutral-900"></span>
                    </div>
                </div>
            </div>

            {{-- Comment / Feedback --}}
            <div>
                <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Your Feedback &amp; Comments</h4>
                <div id="modal-comment-box" class="p-3 rounded-lg bg-neutral-50 border border-neutral-200/70 text-xs text-neutral-700 italic leading-relaxed">
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="pt-3 border-t border-neutral-100 flex items-center justify-between gap-2">
                <a id="modal-rate-again-btn" href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 hover:text-brand-800">
                    <ion-icon name="create-outline"></ion-icon>
                    Rate this stall again
                </a>
                <button type="button" class="js-close-eval-modal px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</dialog>

{{-- ── Evaluation Detail Modal Script ──────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const evalModal = document.getElementById('eval-details-modal');

    window.openEvalDetailsModal = function(evalData, avgScore) {
        document.getElementById('modal-stall-name').textContent = evalData.stall_name || 'Food Stall';
        
        if (evalData.created_at) {
            const d = new Date(evalData.created_at);
            document.getElementById('modal-eval-date').textContent = 'Submitted on ' + d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        } else {
            document.getElementById('modal-eval-date').textContent = '';
        }

        document.getElementById('modal-eval-avg').textContent = avgScore + '★';
        document.getElementById('modal-cleanliness').innerHTML = evalData.cleanliness + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
        document.getElementById('modal-service').innerHTML = evalData.service + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
        document.getElementById('modal-taste').innerHTML = evalData.taste + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';
        document.getElementById('modal-price').innerHTML = evalData.price + ' <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>';

        const commentBox = document.getElementById('modal-comment-box');
        if (evalData.comment && evalData.comment.trim().length > 0) {
            commentBox.textContent = '"' + evalData.comment + '"';
            commentBox.classList.remove('text-neutral-400', 'not-italic');
        } else {
            commentBox.textContent = 'No written feedback was included with this evaluation.';
            commentBox.classList.add('text-neutral-400', 'not-italic');
        }

        const rateAgainBtn = document.getElementById('modal-rate-again-btn');
        if (evalData.stall_id) {
            rateAgainBtn.href = "{{ url('/student/evaluation') }}?stall=" + evalData.stall_id;
            rateAgainBtn.classList.remove('hidden');
        } else {
            rateAgainBtn.classList.add('hidden');
        }

        if (evalModal) evalModal.showModal();
    };

    document.querySelectorAll('.js-close-eval-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (evalModal) evalModal.close();
        });
    });

    if (evalModal) {
        evalModal.addEventListener('click', (e) => {
            const rect = evalModal.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
                evalModal.close();
            }
        });
    }
});
</script>
@endsection

