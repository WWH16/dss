@extends('layouts.dashboard')

@section('title', 'Evaluation History | DSS')
@section('header_title', 'Evaluation History')
{{-- CHANGED (whole file): every rounded-lg became rounded-md (cards stay rounded-xl); font-black became font-bold; the modal section labels went from neutral-400 to neutral-500 for contrast. --}}

@section('content')
<div class="space-y-6">

    {{-- CHANGED: the header card is gone. Its title repeated the top bar ("Evaluation History") and its count
         repeated the rows below, so the heading stays for screen readers only and the action moved into the
         search row. --}}
    <h1 class="sr-only">My Evaluation History</h1>

    {{-- ── 2. Table Container Card ────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Search & Sort Toolbar --}}
        {{-- CHANGED: shown only once the student has an evaluation; with none there is nothing to search or sort. --}}
        @if($hasEvaluations)
        <div class="p-4 sm:p-5 border-b border-neutral-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('student.history') }}" method="GET" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2.5">
                {{-- Search Input --}}
                <div class="relative w-full sm:max-w-xs">
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></ion-icon>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search stall or feedback..."
                        class="w-full bg-neutral-50 border border-neutral-200 rounded-md pl-9 pr-3 py-1.5 text-xs font-medium text-neutral-800 placeholder:text-neutral-400 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                </div>

                {{-- Sort Dropdown --}}
                <div class="w-full sm:w-auto">
                    <select name="sort" onchange="this.form.submit()"
                        class="w-full sm:w-auto bg-neutral-50 border border-neutral-200 rounded-md px-3 py-1.5 text-xs font-medium text-neutral-800 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors cursor-pointer">
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

            {{-- CHANGED: moved here from the removed header card. It shows only once the student has an evaluation
                 (the empty state carries its own), says "Scan a stall's QR" since the evaluation page opens the
                 scanner. CHANGED: it always shows; the "Next evaluation opens" notice is gone since the limit is per stall. --}}
            <a href="{{ route('student.evaluation') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-md transition-colors shadow-2xs shrink-0 w-full sm:w-auto">
                <ion-icon name="qr-code-outline" class="text-sm" aria-hidden="true"></ion-icon>
                Scan a stall's QR
            </a>
        </div>
        @endif

        {{-- Table / List Content --}}
        @if($myStudentEvals->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                {{-- CHANGED: empty-state icon brand-700 on brand-50 instead of neutral-400 on neutral-50, like the admin empty states. --}}
                <div class="w-14 h-14 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-3 text-brand-700">
                    <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                </div>
                {{-- CHANGED: with no evaluations at all, the empty state is titled "No evaluations yet" and states the once-per-stall-each-month rule. --}}
                <h3 class="text-sm font-bold text-neutral-900 mb-1">{{ request('q') ? 'No evaluations found' : 'No evaluations yet' }}</h3>
                <p class="text-xs text-neutral-500 max-w-sm mb-5">
                    {{ request('q') ? 'No submitted evaluations matched your search criteria.' : 'You can evaluate each stall once a month. Your evaluations will show here.' }}
                </p>
                {{-- CHANGED: when a search matched nothing, the button now clears the search instead of saying "Evaluate your first stall" to a student who already has evaluations. --}}
                @if(request('q'))
                    <a href="{{ route('student.history') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-md transition-colors shadow-2xs">
                        <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
                        Clear search
                    </a>
                @else
                    {{-- CHANGED: "Evaluate your first stall" became "Scan a stall's QR"; the evaluation page opens the scanner. --}}
                    <a href="{{ route('student.evaluation') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-md transition-colors shadow-2xs">
                        <ion-icon name="qr-code-outline" class="text-sm" aria-hidden="true"></ion-icon>
                        Scan a stall's QR
                    </a>
                @endif
            </div>
        @else
            {{-- Desktop Table View (Dashboard standard) --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[760px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3 px-5 font-semibold">Food Stall</th>
                            <th class="py-3 px-5 font-semibold">Date Submitted</th>
                            <th class="py-3 px-5 text-center font-semibold">Cleanliness</th>
                            <th class="py-3 px-5 text-center font-semibold">Service</th>
                            <th class="py-3 px-5 text-center font-semibold">Taste</th>
                            <th class="py-3 px-5 text-center font-semibold">Price</th>
                            <th class="py-3 px-5 text-center font-semibold">Average</th>
                            <th class="py-3 px-5 font-semibold">Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($myStudentEvals as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                                // CHANGED: created_at is stored in UTC; convert to Philippine time for display and for the modal.
                                $evalAt = \Carbon\Carbon::parse($eval->created_at, 'UTC')->timezone('Asia/Manila');
                                $evalAtLabel = $evalAt->format('M d, Y, h:i A');
                            @endphp
                            {{-- CHANGED: the whole row opens the details dialog, as on the admin pages; the stall name button is the keyboard target and its click bubbles here. The View button and the Action column are gone. --}}
                            <tr class="hover:bg-neutral-50 transition-colors cursor-pointer" onclick='openEvalDetailsModal(@json($eval), @json($evalAtLabel))'>
                                {{-- Stall Name --}}
                                {{-- CHANGED: removed the storefront icon box repeated on every row; the column is already "Food Stall". --}}
                                <td class="py-3 px-5 text-xs font-bold text-neutral-900">
                                    <button type="button" aria-label="View evaluation for {{ $eval->stall_name ?? 'Food Stall' }}" class="text-left rounded-sm cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">{{ $eval->stall_name ?? 'Food Stall' }}</button>
                                </td>

                                {{-- Date --}}
                                {{-- CHANGED: date shown in Philippine time; the "x ago" line raised from 10px neutral-400 to 11px neutral-500. --}}
                                <td class="py-3 px-5 text-xs text-neutral-600 font-medium tabular-nums whitespace-nowrap">
                                    {{-- CHANGED: removed the "x ago" line under the date; the date already says when. --}}
                                    {{ $evalAt->format('M d, Y') }}
                                </td>

                                {{-- Criteria Scores --}}
                                <td class="py-3 px-5 text-center text-xs font-bold {{ $eval->cleanliness >= 4 ? 'text-emerald-700' : ($eval->cleanliness <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->cleanliness }}★
                                </td>
                                <td class="py-3 px-5 text-center text-xs font-bold {{ $eval->service >= 4 ? 'text-emerald-700' : ($eval->service <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->service }}★
                                </td>
                                <td class="py-3 px-5 text-center text-xs font-bold {{ $eval->taste >= 4 ? 'text-emerald-700' : ($eval->taste <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->taste }}★
                                </td>
                                <td class="py-3 px-5 text-center text-xs font-bold {{ $eval->price >= 4 ? 'text-emerald-700' : ($eval->price <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->price }}★
                                </td>

                                {{-- Average --}}
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800 border border-neutral-200' }}">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </td>

                                {{-- Feedback Comment --}}
                                <td class="py-3 px-5">
                                    {{-- CHANGED: typographic quotes around the comment; "No comment" neutral-400 to neutral-500. --}}
                                    @if($eval->comment)
                                        <p class="text-xs text-neutral-700 line-clamp-1 max-w-xs font-normal" title="{{ $eval->comment }}">
                                            “{{ $eval->comment }}”
                                        </p>
                                    @else
                                        <span class="text-neutral-500 text-xs italic">No comment</span>
                                    @endif
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
                            // CHANGED: same UTC to Philippine time conversion as the desktop table.
                            $evalAt = \Carbon\Carbon::parse($eval->created_at, 'UTC')->timezone('Asia/Manila');
                            $evalAtLabel = $evalAt->format('M d, Y, h:i A');
                        @endphp
                        {{-- CHANGED: the whole card opens the details dialog; the stall name button replaces the View button as the keyboard target. --}}
                        <div class="p-4 flex flex-col gap-2.5 hover:bg-neutral-50 transition-colors cursor-pointer" onclick='openEvalDetailsModal(@json($eval), @json($evalAtLabel))'>
                            <div class="flex items-center justify-between">
                                {{-- CHANGED: removed the storefront icon box repeated on every card. --}}
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-neutral-900 truncate"><button type="button" aria-label="View evaluation for {{ $eval->stall_name ?? 'Food Stall' }}" class="text-left rounded-sm cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">{{ $eval->stall_name ?? 'Food Stall' }}</button></h3>
                                    {{-- CHANGED: Philippine time; 10px neutral-400 to 11px neutral-500. --}}
                                    <span class="text-[11px] text-neutral-500">{{ $evalAt->format('M d, Y • h:i A') }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200 shrink-0">
                                    {{ number_format($avg, 1) }}★
                                </span>
                            </div>

                            {{-- 4 Criteria Grid --}}
                            {{-- CHANGED: criteria labels raised from 9px neutral-400 to 11px neutral-500, and the boxes lost their inner border (as on the admin overview). --}}
                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->cleanliness }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Serv</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->service }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->taste }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->price }}★</span>
                                </div>
                            </div>

                            {{-- CHANGED: typographic quotes around the comment. --}}
                            @if($eval->comment)
                                <p class="text-xs text-neutral-600 bg-neutral-50/70 p-2.5 rounded-md border border-neutral-100 italic">
                                    “{{ $eval->comment }}”
                                </p>
                            @endif
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
{{-- CHANGED: rebuilt to match the admin evaluation dialog. Dark green header band running to the edge (no padding or pale border on the dialog itself) with the stall and submission date; the average sits on top in a panel tinted by its band and names the band in words; each criterion is a row with a five-step meter and a two-decimal score; star glyphs, uppercase section labels, the boxed criteria grid and the italic quoted comment are gone; the black "Close" button became the small "Done" button. --}}
<dialog id="eval-details-modal" class="confirm-modal relative p-0! border-0!" aria-labelledby="modal-stall-name">
    <div class="bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="storefront-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="modal-stall-name" class="text-sm font-bold text-white leading-tight truncate"></h3>
                <p id="modal-eval-date" class="text-[11px] text-brand-200 tabular-nums"></p>
            </div>
        </div>
        <button type="button" class="js-close-eval-modal text-white/70 hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="px-6 pb-6">
        <div id="modal-eval-avg-panel" class="mt-5 rounded-lg px-4 py-3.5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-neutral-900">Average of four criteria</p>
                <p id="modal-eval-band" class="text-[11px] font-semibold mt-0.5"></p>
            </div>
            <span id="modal-eval-avg" class="text-3xl leading-none"></span>
        </div>

        <dl class="mt-2 divide-y divide-neutral-100 text-sm">
            @foreach(['cleanliness' => 'Cleanliness', 'service' => 'Service', 'taste' => 'Taste', 'price' => 'Price'] as $c => $label)
                <div class="flex items-center justify-between gap-4 py-2.5">
                    <dt class="text-neutral-600">{{ $label }}</dt>
                    <dd class="flex items-center gap-3">
                        <span id="modal-{{ $c }}-meter" class="flex gap-0.5" aria-hidden="true"></span>
                        <span id="modal-{{ $c }}" class="w-11 text-right"></span>
                    </dd>
                </div>
            @endforeach
        </dl>

        <div class="pt-3">
            <p class="text-xs font-semibold text-neutral-900 mb-1.5">Your comment</p>
            <p id="modal-comment-box" class="bg-surface-alt rounded-lg px-3.5 py-3 text-sm leading-relaxed whitespace-pre-line break-words"></p>
        </div>

        {{-- CHANGED: removed "Rate this stall again" (evaluations open only from the stall's QR code). --}}
        <div class="flex justify-end pt-4 mt-4 border-t border-neutral-100">
            <button type="button" class="btn btn-primary btn-sm text-xs font-semibold rounded-md js-close-eval-modal cursor-pointer">Done</button>
        </div>
    </div>
</dialog>

{{-- ── Evaluation Detail Modal Script ──────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const evalModal = document.getElementById('eval-details-modal');

    // CHANGED: same score rule and markup as the admin evaluation dialog: two decimals, below 3 red with a caret, 4 and up green.
    const evalScore = (v, cls) => {
        const tone = v < 3 ? 'text-rose-700' : (v >= 4 ? 'text-emerald-700' : 'text-neutral-900');
        const mark = v < 3 ? '<ion-icon name="caret-down" class="text-[length:max(10px,0.5em)]" aria-hidden="true"></ion-icon><span class="sr-only">Low:</span>' : '';
        return '<span class="inline-flex items-center gap-0.5 tabular-nums ' + tone + ' ' + (cls || '') + '">' + mark + v.toFixed(2) + '</span>';
    };
    const bands = {
        low:  { panel: 'bg-rose-50',     label: 'text-rose-700',    fill: 'bg-rose-500',    text: 'Below 3.00' },
        mid:  { panel: 'bg-surface-alt', label: 'text-neutral-600', fill: 'bg-neutral-400', text: '3.00 to 3.99' },
        high: { panel: 'bg-emerald-50',  label: 'text-emerald-800', fill: 'bg-emerald-600', text: '4.00 and up' }
    };
    const bandOf = v => bands[v < 3 ? 'low' : (v >= 4 ? 'high' : 'mid')];

    // CHANGED: takes the evaluation and the submission date already formatted in Philippine time by the server; the average is worked out here from the four scores instead of arriving rounded to one decimal. new Date() on the raw UTC string read it as local time and shows "Invalid Date" in Safari.
    window.openEvalDetailsModal = function(evalData, submittedAt) {
        document.getElementById('modal-stall-name').textContent = evalData.stall_name || 'Food Stall';
        document.getElementById('modal-eval-date').textContent = submittedAt ? 'Submitted on ' + submittedAt : '';

        const keys = ['cleanliness', 'service', 'taste', 'price'];
        const avg = keys.reduce((sum, k) => sum + Number(evalData[k]), 0) / keys.length;
        const band = bandOf(avg);
        document.getElementById('modal-eval-avg-panel').className = 'mt-5 rounded-lg px-4 py-3.5 flex items-center justify-between gap-4 ' + band.panel;
        const bandLabel = document.getElementById('modal-eval-band');
        bandLabel.textContent = band.text;
        bandLabel.className = 'text-[11px] font-semibold mt-0.5 ' + band.label;
        document.getElementById('modal-eval-avg').innerHTML = evalScore(avg, 'font-bold');

        // CHANGED: each criterion gets a five-step meter in its band colour; the last step fills partly for scores such as 3.50.
        keys.forEach(k => {
            const v = Number(evalData[k]);
            let steps = '';
            for (let i = 1; i <= 5; i++) {
                const pct = Math.round(Math.max(0, Math.min(1, v - i + 1)) * 100);
                steps += '<span class="relative w-3.5 h-1.5 rounded-full bg-neutral-200 overflow-hidden"><span class="absolute inset-y-0 left-0 ' + bandOf(v).fill + '" style="width:' + pct + '%"></span></span>';
            }
            document.getElementById('modal-' + k + '-meter').innerHTML = steps;
            document.getElementById('modal-' + k).innerHTML = evalScore(v, 'font-semibold');
        });

        // CHANGED: plain comment text in the green-grey box instead of italic with typographic quotes; an empty comment reads "No comment."
        const commentBox = document.getElementById('modal-comment-box');
        const hasComment = evalData.comment && evalData.comment.trim().length > 0;
        commentBox.textContent = hasComment ? evalData.comment : 'No comment.';
        commentBox.classList.toggle('text-neutral-800', hasComment);
        commentBox.classList.toggle('text-neutral-600', !hasComment);

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

