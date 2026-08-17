@extends('layouts.focused')

@section('title', 'Evaluate Food Stall | DSS')
@section('header_title', 'Evaluate Food Stall')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    
    @if(!session('success'))
        {{-- Back Navigation --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-neutral-600 hover:text-brand-700 transition-colors bg-white px-3.5 py-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
                Back to Dashboard
            </a>
            <span class="text-xs text-neutral-400 font-medium hidden sm:inline-flex items-center gap-1">
                <ion-icon name="shield-checkmark-outline" class="text-emerald-600 text-sm"></ion-icon>
                Official Campus Survey
            </span>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        @if(session('success'))
            {{-- ── Success Screen ──────────────────────────────────────────── --}}
            <div class="p-8 sm:p-14 md:p-20 text-center flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-2xl bg-emerald-50 border-2 border-emerald-200/80 text-emerald-600 flex items-center justify-center mb-6 shadow-xs">
                    <ion-icon name="checkmark-circle" class="text-5xl"></ion-icon>
                </div>
                
                <h2 class="text-2xl sm:text-3xl font-black text-neutral-900 tracking-tight mb-2">
                    Evaluation Submitted!
                </h2>
                
                <p class="text-neutral-500 max-w-md mx-auto mb-8 text-xs sm:text-sm leading-relaxed">
                    Thank you for your valuable feedback. Your rating has been logged anonymously to help maintain high quality dining standards across Isabela State University.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 bg-neutral-100 hover:bg-neutral-200/80 text-neutral-700 font-bold rounded-lg transition-colors text-xs text-center">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('student.evaluation') }}" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg transition-colors text-xs text-center flex items-center justify-center gap-1.5 shadow-2xs">
                        <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
                        Evaluate Another Stall
                    </a>
                </div>
            </div>
        @elseif($stalls->isEmpty())
            {{-- Empty Stalls Notice --}}
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center mx-auto mb-3">
                    <ion-icon name="alert-circle-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-900">No Food Stalls Available</h3>
                <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">There are currently no food stalls registered in the system for evaluation.</p>
            </div>
        @else
            {{-- ── Form Header & Institutional Identity ────────────────────── --}}
            <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/50">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                    {{-- Logos --}}
                    <div class="flex items-center gap-4 shrink-0">
                        <img src="{{ asset('assets/images/isu_logo.png') }}" class="w-14 h-14 sm:w-16 sm:h-16 object-contain" alt="ISU Logo">
                        <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="w-14 h-14 sm:w-16 sm:h-16 object-contain" alt="Bagong Pilipinas">
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-neutral-500">Isabela State University • Cauayan Campus</p>
                        <h1 class="text-lg sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight mt-0.5">
                            Citizen / Client Satisfaction Survey
                        </h1>
                        <p class="text-xs text-neutral-500 mt-1 max-w-xl">
                            Please answer each statement by selecting the rating that best reflects your dining experience.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Evaluation Survey Form ──────────────────────────────────── --}}
            <form id="evaluationForm" action="{{ route('student.evaluation.store') }}" method="POST" class="p-5 sm:p-8 space-y-6">
                @csrf

                {{-- Validation Error Alert --}}
                <div id="formValidationAlert" class="hidden p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                    <p class="font-bold mb-1 flex items-center gap-1.5">
                        <ion-icon name="alert-circle" class="text-base text-rose-600"></ion-icon>
                        <span id="formValidationErrorMsg">Please answer all survey statements before submitting.</span>
                    </p>
                </div>

                @if($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-xl text-rose-800 text-xs">
                        <p class="font-bold mb-1">Please correct the following errors:</p>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 1. Stall Selection Section --}}
                <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-2xs space-y-2.5">
                    <label for="stall_id" class="block text-xs font-bold uppercase tracking-wider text-neutral-700">
                        1. Select Food Stall to Evaluate <span class="text-rose-500">*</span>
                    </label>
                    
                    <div class="relative max-w-md">
                        <select id="stall_id" name="stall_id" required
                            class="w-full px-3.5 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-semibold text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors cursor-pointer">
                            <option value="">Choose a Food Stall...</option>
                            @foreach($stalls as $stall)
                                <option value="{{ $stall->id }}" {{ (string)request('stall') === (string)$stall->id ? 'selected' : '' }}>
                                    {{ $stall->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[11px] text-neutral-400">Choose the specific campus dining stall where you purchased your meal.</p>
                </div>

                {{-- 2. Rating Scale Legend --}}
                <div class="bg-neutral-50/70 rounded-xl border border-neutral-200/70 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2 mb-3 border-b border-neutral-200/60">
                        <span class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Rating Scale Reference</span>
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-600" id="progressIndicator">
                            <span>Completion:</span>
                            <span id="progressText" class="text-brand-700 font-mono">0 / {{ count($displayStatements) }} answered</span>
                        </div>
                    </div>

                    {{-- Scale Badges --}}
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        <div class="bg-white p-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                            <span class="block font-black text-neutral-900 text-sm">5★</span>
                            <span class="text-[10px] font-semibold text-neutral-500">Excellent</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                            <span class="block font-black text-neutral-900 text-sm">4★</span>
                            <span class="text-[10px] font-semibold text-neutral-500">Very Good</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                            <span class="block font-black text-neutral-900 text-sm">3★</span>
                            <span class="text-[10px] font-semibold text-neutral-500">Good</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                            <span class="block font-black text-neutral-900 text-sm">2★</span>
                            <span class="text-[10px] font-semibold text-neutral-500">Fair</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-neutral-200/80 shadow-2xs">
                            <span class="block font-black text-neutral-900 text-sm">1★</span>
                            <span class="text-[10px] font-semibold text-neutral-500">Poor</span>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-3">
                        <div class="w-full bg-neutral-200/80 h-1.5 rounded-full overflow-hidden">
                            <div id="surveyProgressBar" class="bg-brand-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                {{-- ── DESKTOP TABLE LAYOUT (hidden on mobile) ─────────────────── --}}
                <div class="hidden sm:block">
                    <div class="rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden bg-white">
                        <table class="w-full text-left border-collapse min-w-[620px]">
                            <thead>
                                <tr class="bg-neutral-50/80 border-b border-neutral-200 text-[10px] text-neutral-500 font-bold uppercase tracking-wider">
                                    <th class="py-3.5 px-5">Survey Statement</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-black text-neutral-800 text-xs">5★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-black text-neutral-800 text-xs">4★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-black text-neutral-800 text-xs">3★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-black text-neutral-800 text-xs">2★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-black text-neutral-800 text-xs">1★</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-xs">
                                @foreach($displayStatements as $index => $statement)
                                    <tr class="survey-row group hover:bg-neutral-50/50 transition-colors" id="desktop_row_{{ $statement['id'] }}">
                                        <td class="py-4 px-5">
                                            <div class="flex items-start gap-2.5">
                                                <span class="w-5 h-5 rounded-full bg-neutral-100 text-neutral-600 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5 tabular-nums">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <p class="font-medium text-neutral-800 leading-relaxed text-xs sm:text-[13px]">
                                                        {{ $statement['statement'] }}
                                                    </p>
                                                    <span class="inline-block text-[9px] font-bold uppercase tracking-wider text-neutral-400 mt-1">
                                                        Criterion: {{ ucfirst($statement['criterion_key']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        @for($val = 5; $val >= 1; $val--)
                                            <td class="p-0 text-center align-middle border-l border-neutral-100">
                                                <label for="dt_q{{ $statement['id'] }}_v{{ $val }}" class="flex items-center justify-center w-full h-full min-h-[58px] cursor-pointer hover:bg-brand-50/50 transition-colors">
                                                    <input type="radio" 
                                                        id="dt_q{{ $statement['id'] }}_v{{ $val }}"
                                                        name="responses[{{ $statement['id'] }}]" 
                                                        value="{{ $val }}" 
                                                        class="peer sr-only survey-radio-dt"
                                                        data-statement-id="{{ $statement['id'] }}"
                                                        data-val="{{ $val }}">
                                                    {{-- Custom Styled Circle --}}
                                                    <div class="w-6 h-6 rounded-full border-2 border-neutral-300 bg-white peer-checked:border-brand-600 peer-checked:bg-brand-600 flex items-center justify-center transition-all duration-150 shadow-2xs">
                                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                                    </div>
                                                </label>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── MOBILE CARD LAYOUT (hidden on sm+) ─────────────────────── --}}
                <div class="sm:hidden space-y-3" id="mobileQuestions">
                    @foreach($displayStatements as $index => $statement)
                        <div class="mobile-question-card bg-white border border-neutral-200/80 rounded-xl overflow-hidden shadow-2xs transition-colors" id="mobile_card_{{ $statement['id'] }}">
                            <div class="p-3.5 bg-neutral-50/70 border-b border-neutral-100 flex items-start gap-2">
                                <span class="shrink-0 w-5 h-5 rounded-full bg-neutral-200 text-neutral-700 text-[10px] font-bold flex items-center justify-center tabular-nums mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-xs font-medium text-neutral-800 leading-snug">
                                        {{ $statement['statement'] }}
                                    </p>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-neutral-400">
                                        {{ ucfirst($statement['criterion_key']) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3 flex gap-1.5">
                                @for($val = 5; $val >= 1; $val--)
                                    <label for="mb_q{{ $statement['id'] }}_v{{ $val }}" class="flex-1 relative cursor-pointer">
                                        <input
                                            type="radio"
                                            id="mb_q{{ $statement['id'] }}_v{{ $val }}"
                                            name="responses_mobile[{{ $statement['id'] }}]"
                                            value="{{ $val }}"
                                            class="peer sr-only survey-radio-mb"
                                            data-statement-id="{{ $statement['id'] }}"
                                            data-val="{{ $val }}">
                                        <div class="peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 peer-checked:shadow-2xs flex flex-col items-center justify-center min-h-[46px] rounded-lg border border-neutral-200 bg-neutral-50/50 transition-all active:scale-95 select-none">
                                            <span class="text-xs font-bold leading-none">{{ $val }}★</span>
                                            <span class="text-[8px] font-medium leading-tight mt-0.5 opacity-70 peer-checked:opacity-100">
                                                @if($val === 5) Excel @elseif($val === 4) V.Good @elseif($val === 3) Good @elseif($val === 2) Fair @else Poor @endif
                                            </span>
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 3. Comments & Suggestions --}}
                <div class="bg-white rounded-xl border border-neutral-200/80 p-5 shadow-2xs space-y-2">
                    <label for="comment" class="block text-xs font-bold uppercase tracking-wider text-neutral-700">
                        Comments / Suggestions / Compliments <span class="text-neutral-400 font-normal">(Optional)</span>
                    </label>
                    <textarea id="comment" name="comment" rows="3" 
                        class="w-full px-3.5 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-xs text-neutral-900 placeholder:text-neutral-400 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors resize-none"
                        placeholder="Share any specific feedback about food quality, taste, cleanliness, or staff service..."></textarea>
                    <p class="text-[11px] text-neutral-400">All comments are strictly anonymous and assist stall managers in continuous improvement.</p>
                </div>

                {{-- 4. Submit Bar --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-neutral-100">
                    <div class="flex items-center gap-1.5 text-xs text-neutral-500 font-medium">
                        <ion-icon name="lock-closed-outline" class="text-sm text-neutral-400"></ion-icon>
                        Your evaluation response is confidential.
                    </div>

                    <button type="submit" id="submitEvaluationBtn" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                        <span id="submitText">Submit Evaluation</span>
                        <ion-icon id="submitIcon" name="paper-plane-outline" class="text-sm"></ion-icon>
                        <ion-icon id="submitLoader" name="sync-outline" class="text-sm animate-spin hidden"></ion-icon>
                    </button>
                </div>

            </form>
        @endif

    </div>

</div>

{{-- ── Real-Time Progress & Sync Script ───────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const totalStatements = {{ count($displayStatements ?? []) }};
    const progressBar = document.getElementById('surveyProgressBar');
    const progressText = document.getElementById('progressText');
    const form = document.getElementById('evaluationForm');
    const submitBtn = document.getElementById('submitEvaluationBtn');
    const submitText = document.getElementById('submitText');
    const submitIcon = document.getElementById('submitIcon');
    const submitLoader = document.getElementById('submitLoader');
    const alertBox = document.getElementById('formValidationAlert');
    const alertMsg = document.getElementById('formValidationErrorMsg');

    // Sync state between desktop radio and mobile radio
    function updateRating(statementId, val) {
        // Desktop radio
        const dtRadio = document.getElementById('dt_q' + statementId + '_v' + val);
        if (dtRadio) dtRadio.checked = true;

        // Mobile radio
        const mbRadio = document.getElementById('mb_q' + statementId + '_v' + val);
        if (mbRadio) mbRadio.checked = true;

        // Update progress counter
        updateProgress();
    }

    function updateProgress() {
        const answeredIds = new Set();
        document.querySelectorAll('.survey-radio-dt:checked').forEach(r => {
            answeredIds.add(r.dataset.statementId);
        });

        const count = answeredIds.size;
        const pct = totalStatements > 0 ? Math.round((count / totalStatements) * 100) : 0;

        if (progressBar) progressBar.style.width = pct + '%';
        if (progressText) progressText.textContent = count + ' / ' + totalStatements + ' answered (' + pct + '%)';
    }

    // Attach listeners to Desktop radios
    document.querySelectorAll('.survey-radio-dt').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const sid = e.target.dataset.statementId;
            const val = e.target.dataset.val;
            const mbRadio = document.getElementById('mb_q' + sid + '_v' + val);
            if (mbRadio) mbRadio.checked = true;
            updateProgress();
        });
    });

    // Attach listeners to Mobile radios
    document.querySelectorAll('.survey-radio-mb').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const sid = e.target.dataset.statementId;
            const val = e.target.dataset.val;
            const dtRadio = document.getElementById('dt_q' + sid + '_v' + val);
            if (dtRadio) dtRadio.checked = true;
            updateProgress();
        });
    });

    // Form Submission Validation
    if (form && submitBtn) {
        form.addEventListener('submit', (e) => {
            // Check stall
            const stallSelect = document.getElementById('stall_id');
            if (!stallSelect || !stallSelect.value) {
                e.preventDefault();
                if (alertBox && alertMsg) {
                    alertMsg.textContent = 'Please select a Food Stall to evaluate.';
                    alertBox.classList.remove('hidden');
                    stallSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    stallSelect.focus();
                }
                return false;
            }

            // Check that all statements are answered in the canonical desktop inputs
            const missingStatementIds = [];
            @foreach($displayStatements as $statement)
                if (!document.querySelector('input[name="responses[{{ $statement['id'] }}]"]:checked')) {
                    missingStatementIds.push({{ $statement['id'] }});
                }
            @endforeach

            if (missingStatementIds.length > 0) {
                e.preventDefault();
                if (alertBox && alertMsg) {
                    alertMsg.textContent = 'Please answer all ' + totalStatements + ' statements before submitting (' + (totalStatements - missingStatementIds.length) + ' of ' + totalStatements + ' completed).';
                    alertBox.classList.remove('hidden');

                    const firstMissing = missingStatementIds[0];
                    const targetEl = document.getElementById('desktop_row_' + firstMissing) || document.getElementById('mobile_card_' + firstMissing);
                    if (targetEl) {
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                return false;
            }

            // If valid, hide alert and show loading state
            if (alertBox) alertBox.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            if (submitText) submitText.textContent = 'Submitting...';
            if (submitIcon) submitIcon.classList.add('hidden');
            if (submitLoader) submitLoader.classList.remove('hidden');
        });
    }

    updateProgress();
});
</script>
@endsection