@extends('layouts.focused')

@section('title', 'Evaluate Food Stall | DSS')
@section('header_title', 'Evaluate Food Stall')
{{-- CHANGED (whole file): every rounded-lg and plain rounded became rounded-md (cards stay rounded-xl); every 8px, 9px and 10px text became 11px; font-black became font-bold; grey helper, criterion and reCAPTCHA text went from neutral-400 to neutral-500; the "Official Campus Survey" shield icon went from emerald to brand green. --}}

@section('head')
    <style>
        @keyframes error-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .error-pulse {
            animation: error-pulse 0.35s ease-in-out 2;
        }
        .rating-touch-pill {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
    </style>

    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
@endsection

@section('content')
    {{-- CHANGED: on lg+ screens the back link sits in the left gutter beside the card instead of stacking above the centered max-w-4xl container. --}}
    <div class="w-full max-w-4xl mx-auto space-y-5 @if(!session('success') && ! $limit) lg:max-w-none lg:space-y-0 lg:grid lg:grid-cols-[auto_minmax(0,1fr)] xl:grid-cols-[1fr_minmax(0,56rem)_1fr] lg:items-start lg:gap-6 @endif">

        {{-- CHANGED: also hidden on the monthly limit screen, which has its own Back to Dashboard button. --}}
        @if(!session('success') && ! $limit)
            {{-- Back Navigation --}}
            <div class="flex items-center justify-between lg:justify-self-start">
                {{-- CHANGED: uses the shared back link, which is 44px tall instead of about 32px. --}}
                @include('partials.back-link', ['href' => route('student.dashboard'), 'label' => 'Back to Dashboard', 'class' => 'whitespace-nowrap'])
                <span class="text-xs text-neutral-500 font-medium hidden sm:inline-flex lg:hidden items-center gap-1">
                <ion-icon name="shield-checkmark-outline" class="text-brand-600 text-sm"></ion-icon>
                Official Campus Survey
            </span>
            </div>
        @endif

        {{-- CHANGED: overflow-clip instead of overflow-hidden; it still clips the rounded corners but lets the progress bar below stick to the top of the screen. --}}
        <div class="min-w-0 bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-clip">

            {{-- CHANGED: the success screen also serves as the monthly limit screen (ADDED); only the icon, heading and first sentence differ. --}}
            @if(session('success') || $limit)
                {{-- ── Success Screen ──────────────────────────────────────────── --}}
                <div class="p-8 sm:p-14 md:p-20 text-center flex flex-col items-center justify-center">
                    {{-- CHANGED: the success icon box uses the limit screen's neutral box and brand-green icon (was an emerald box), so both screens match. --}}
                    <div class="w-20 h-20 rounded-2xl border-2 flex items-center justify-center mb-6 shadow-xs bg-neutral-50 border-neutral-200 text-brand-600">
                        <ion-icon name="{{ session('success') ? 'checkmark-circle' : 'calendar-outline' }}" class="text-5xl"></ion-icon>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-bold text-neutral-900 tracking-tight mb-2">
                        {{-- CHANGED: the limit heading says "this stall"; the limit is per stall now. --}}
                        {{ session('success') ? 'Evaluation Submitted!' : 'Already evaluated this stall this month' }}
                    </h2>

                    {{-- CHANGED: removed "logged anonymously". Each evaluation is stored with the student's account, and administrators see the student's name next to it on the Evaluations page, so the claim was not true. --}}
                    <p class="text-neutral-500 max-w-md mx-auto mb-8 text-xs sm:text-sm leading-relaxed">
                        @if(session('success'))
                            Thank you for your valuable feedback. Your rating has been recorded to help maintain high quality dining standards across Isabela State University.
                            {{-- CHANGED: students get one evaluation per stall per month, so the screen says other stalls stay open. --}}
                            You can evaluate this stall again on {{ now('Asia/Manila')->startOfMonth()->addMonth()->format('F j') }}. Other stalls are still open this month.
                        @else
                            {{-- CHANGED: the rule is one evaluation per stall per month. --}}
                            Each student can evaluate each stall once per month. {{ $limit }}
                        @endif
                    </p>

                    {{-- CHANGED: removed the "Evaluate Another Stall" button; the dashboard's Scan QR button opens the next stall. --}}
                    <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md transition-colors text-xs text-center shadow-2xs">
                        Back to Dashboard
                    </a>
                </div>
                {{-- CHANGED: the "scan the QR code" notice is now an in-page scanner. The camera reads the stall's QR code and opens its evaluation; CHANGED: uploading a saved image of the code works too, for phones where live camera access is blocked. --}}
            @elseif(! $stall)
                <div class="px-5 py-8 sm:p-12 flex flex-col items-center text-center">
                    <h1 class="text-lg sm:text-xl font-bold text-neutral-900 tracking-tight">Scan the stall's QR code</h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1 max-w-sm">Point your camera at the QR code posted at the stall's counter. The evaluation opens as soon as it is read.</p>

                    @if(session('error'))
                        <p class="mt-4 inline-flex items-center gap-1.5 px-3 py-2 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold">
                            <ion-icon name="alert-circle" class="text-sm text-rose-600 shrink-0" aria-hidden="true"></ion-icon>
                            {{ session('error') }}
                        </p>
                    @endif

                    {{-- CHANGED: the scanner markup, CSS and script moved to partials/qr-scanner.blade.php; the student dashboard includes the same partial. --}}
                    @include('partials.qr-scanner')

                    {{-- CHANGED: removed the second Back to Dashboard link under the scanner; the back bar above the card already has one. --}}
                </div>
            @else
                {{-- ── Form Header & Institutional Identity ────────────────────── --}}
                {{-- CHANGED: p-5 and a 48px logo on phones (were p-6 and 56px), so the first statement starts higher on the screen. --}}
                <div class="p-5 sm:p-8 border-b border-neutral-100 bg-neutral-50/50">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                        {{-- Logos --}}
                        <div class="flex items-center gap-4 shrink-0">
                            <img src="{{ asset('assets/images/isu_logo.png') }}" class="w-12 h-12 sm:w-16 sm:h-16 object-contain" alt="ISU Logo">
                            {{-- CHANGED: removed the Bagong Pilipinas logo; only the ISU logo remains. --}}
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
                <form id="evaluationForm" action="{{ route('student.evaluation.store') }}" method="POST" class="p-3.5 sm:p-8 space-y-5 sm:space-y-6">
                    @csrf
                    <input type="hidden" name="g_recaptcha_response" id="evaluation_g_recaptcha_response">

                    {{-- Validation Error Alert --}}
                    <div id="formValidationAlert" class="hidden p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                        <p class="font-bold mb-1 flex items-center gap-1.5">
                            <ion-icon name="alert-circle" class="text-base text-rose-600"></ion-icon>
                            <span id="formValidationErrorMsg">Please answer all survey statements before submitting.</span>
                        </p>
                    </div>

                    @if(session('error'))
                        <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-xl text-rose-800 text-xs flex items-center gap-2">
                            <ion-icon name="alert-circle" class="text-base text-rose-600 shrink-0"></ion-icon>
                            <p class="font-bold">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-xl text-rose-800 text-xs">
                            <p class="font-bold mb-1">Please correct the following errors:</p>
                            <ul class="list-disc pl-4 space-y-0.5">
                                {{-- CHANGED: array_unique so ten missing ratings show one "Please answer all survey statements" line, not ten. --}}
                                @foreach(array_unique($errors->all()) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- CHANGED: the stall dropdown is gone; the stall comes from the scanned QR code and is shown here. --}}
                    <div class="bg-white rounded-xl border border-neutral-200/80 p-3.5 sm:p-5 shadow-2xs flex items-center gap-3" id="stallSelectCard">
                        <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center shrink-0">
                            <ion-icon name="storefront-outline" class="text-lg" aria-hidden="true"></ion-icon>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">You are evaluating</p>
                            <p class="text-sm font-bold text-neutral-900 truncate">{{ $stall->name }}</p>
                        </div>
                    </div>

                    {{-- 2. Rating Scale Legend --}}
                    <div class="bg-neutral-50/70 rounded-xl border border-neutral-200/70 p-3.5 sm:p-4">
                        {{-- CHANGED: the completion count and progress bar moved out of this legend into the sticky bar below it. --}}
                        <span class="block pb-2 mb-2.5 border-b border-neutral-200/60 text-xs font-bold text-neutral-700 uppercase tracking-wider">Rating Scale Reference</span>

                        {{-- Scale Badges --}}
                        {{-- CHANGED: on phones the legend is a wrapping row of "5★ Excellent" chips instead of a fixed 5-column grid; at 9px the labels were unreadable and "Excellent" / "Very Good" were cut off. From sm up it is the same 5-column grid. Labels are 11px, font-bold became font-bold, and the chips use rounded-md. --}}
                        <div class="flex flex-wrap gap-1.5 sm:grid sm:grid-cols-5 sm:gap-2 text-center text-xs">
                            @foreach([5 => 'Excellent', 4 => 'Very Good', 3 => 'Good', 2 => 'Fair', 1 => 'Poor'] as $legendVal => $legendLabel)
                                <div class="bg-white px-2 py-1 sm:p-2 rounded-md border border-neutral-200/80 shadow-2xs flex items-center gap-1 sm:block">
                                    <span class="sm:block font-bold text-neutral-900 text-xs sm:text-sm">{{ $legendVal }}★</span>
                                    <span class="text-[11px] font-semibold text-neutral-600 sm:block">{{ $legendLabel }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ADDED: the completion count and bar stick to the top of the screen while the student scrolls
                         through the statements, so on a phone they stay in view instead of scrolling away with the legend. --}}
                    <div class="sticky top-0 z-10 -mx-3.5 sm:mx-0 px-3.5 sm:px-4 py-2.5 bg-white border-b sm:border border-neutral-200/80 sm:rounded-xl shadow-2xs">
                        <div class="flex items-center justify-between gap-2 text-xs font-bold text-neutral-600">
                            <span>Completion</span>
                            <span id="progressText" class="text-brand-700 font-mono">0 / {{ count($displayStatements) }} answered</span>
                        </div>
                        <div class="mt-2 w-full bg-neutral-200/80 h-1.5 rounded-full overflow-hidden">
                            <div id="surveyProgressBar" class="bg-brand-600 h-full rounded-full transition-[width] duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    {{-- ── DESKTOP TABLE LAYOUT (hidden on mobile) ─────────────────── --}}
                    <div class="hidden sm:block">
                        <div class="rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden bg-white">
                            <table class="w-full text-left border-collapse min-w-[620px]">
                                <thead>
                                <tr class="bg-neutral-50/80 border-b border-neutral-200 text-[11px] text-neutral-500 font-bold uppercase tracking-wider">
                                    <th class="py-3.5 px-5">Survey Statement</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-bold text-neutral-800 text-xs">5★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-bold text-neutral-800 text-xs">4★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-bold text-neutral-800 text-xs">3★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-bold text-neutral-800 text-xs">2★</th>
                                    <th class="py-3.5 px-2 text-center w-16 font-bold text-neutral-800 text-xs">1★</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 text-xs">
                                @foreach($displayStatements as $index => $statement)
                                    <tr class="survey-row group hover:bg-neutral-50/50 transition-colors" id="desktop_row_{{ $statement['id'] }}">
                                        <td class="py-4 px-5">
                                            <div class="flex items-start gap-2.5">
                                                <span class="w-5 h-5 rounded-full bg-neutral-100 text-neutral-600 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5 tabular-nums">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <p class="font-medium text-neutral-800 leading-relaxed text-xs sm:text-[13px]">
                                                        {{ $statement['statement'] }}
                                                    </p>
                                                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-neutral-500 mt-1">
                                                        Criterion: {{ ucfirst($statement['criterion_key']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        @for($val = 5; $val >= 1; $val--)
                                            <td class="p-0 text-center align-middle border-l border-neutral-100">
                                                <label for="dt_q{{ $statement['id'] }}_v{{ $val }}" class="flex items-center justify-center w-full h-full min-h-[58px] cursor-pointer hover:bg-brand-50/50 transition-colors">
                                                    {{-- CHANGED: @checked(old(...)) restores the student's answers after a failed submission; the existing sync script then copies them to the mobile radios. --}}
                                                    <input type="radio"
                                                           id="dt_q{{ $statement['id'] }}_v{{ $val }}"
                                                           name="responses[{{ $statement['id'] }}]"
                                                           value="{{ $val }}"
                                                           @checked((string) old('responses.' . $statement['id']) === (string) $val)
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
                            <div class="mobile-question-card bg-white border border-neutral-200/90 rounded-xl overflow-hidden shadow-2xs transition-all duration-200" id="mobile_card_{{ $statement['id'] }}">
                                {{-- Card Header --}}
                                <div class="p-3 bg-neutral-50/80 border-b border-neutral-100 flex items-start gap-2.5">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-neutral-200 text-neutral-700 text-xs font-bold flex items-center justify-center tabular-nums">
                                    {{ $index + 1 }}
                                </span>
                                    <div>
                                        {{-- CHANGED: text-sm (14px, was 12px) for the statement, the part a student actually reads on a phone. --}}
                                        <p class="text-sm font-semibold text-neutral-900 leading-snug">
                                            {{ $statement['statement'] }}
                                        </p>
                                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-neutral-500 mt-1">
                                        Criterion: {{ ucfirst($statement['criterion_key']) }}
                                    </span>
                                    </div>
                                </div>

                                {{-- 5-Rating Segmented Buttons --}}
                                <div class="p-3">
                                    <div class="grid grid-cols-5 gap-1.5" role="radiogroup" aria-label="Rating for statement {{ $index + 1 }}">
                                        @for($val = 5; $val >= 1; $val--)
                                            <label for="mb_q{{ $statement['id'] }}_v{{ $val }}" class="relative cursor-pointer select-none rating-touch-pill">
                                                <input
                                                    type="radio"
                                                    id="mb_q{{ $statement['id'] }}_v{{ $val }}"
                                                    name="responses_mobile[{{ $statement['id'] }}]"
                                                    value="{{ $val }}"
                                                    class="peer sr-only survey-radio-mb"
                                                    data-statement-id="{{ $statement['id'] }}"
                                                    data-val="{{ $val }}">
                                                {{-- CHANGED: the visible 8px sub-labels ("Excel", "V.Good") were too small to read and do not fit five columns at 11px, so the rating number is shown larger and the full label is kept for screen readers; the legend above gives the words. rounded-md to rounded-md. --}}
                                                <div class="peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 peer-checked:shadow-2xs flex flex-col items-center justify-center min-h-12 rounded-md border border-neutral-200 bg-neutral-50/50 transition-all active:scale-95 select-none text-center">
                                                    {{-- CHANGED: 48px tall (min-h-12, was 46px); the ★ is hidden from screen readers, which hear the number and the label. --}}
                                                    <span class="text-sm font-bold leading-none">{{ $val }}<span aria-hidden="true">★</span></span>
                                                    <span class="sr-only">
                                                    @if($val === 5) Excellent @elseif($val === 4) Very Good @elseif($val === 3) Good @elseif($val === 2) Fair @else Poor @endif
                                                </span>
                                                </div>
                                            </label>
                                        @endfor
                                    </div>

                                    {{-- Inline Error Message --}}
                                    <div id="mb_error_msg_{{ $statement['id'] }}" class="hidden mt-2.5 px-2.5 py-1.5 bg-rose-50 border border-rose-200 rounded-md text-[11px] font-semibold text-rose-800 items-center gap-1.5">
                                        <ion-icon name="alert-circle" class="text-sm text-rose-600 shrink-0"></ion-icon>
                                        <span>Please choose a rating for statement #{{ $index + 1 }}.</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 3. Comments & Suggestions --}}
                    <div class="bg-white rounded-xl border border-neutral-200/80 p-3.5 sm:p-5 shadow-2xs space-y-2">
                        <label for="comment" class="block text-xs font-bold uppercase tracking-wider text-neutral-700">
                            Comments / Suggestions / Compliments <span class="text-neutral-500 font-normal">(Optional)</span>
                        </label>
                        {{-- CHANGED: text-base on phones; iOS Safari zooms the page into any field under 16px. The placeholder is neutral-500 (was neutral-400, ~2.6:1) for contrast. --}}
                        <textarea id="comment" name="comment" rows="3"
                                  class="w-full px-3.5 py-2.5 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-xs text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors resize-none"
                                  placeholder="Share any specific feedback about food quality, taste, cleanliness, or staff service...">{{ old('comment') }}</textarea>
                        {{-- CHANGED: the textarea now restores old('comment') after a failed submission. The helper no longer says comments are "strictly anonymous" (administrators see the student's name); it now says who reads them, and is neutral-500 for contrast. --}}
                        <p class="text-[11px] text-neutral-500">Comments are read by the canteen administrators to help stalls improve.</p>
                    </div>

                    {{-- 4. Submit Bar --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-neutral-100">
                        <div class="flex items-center gap-1.5 text-xs text-neutral-500 font-medium">
                            <ion-icon name="lock-closed-outline" class="text-sm text-neutral-400"></ion-icon>
                            Your evaluation response is confidential.
                        </div>

                        <div class="flex flex-col items-center sm:items-end gap-1 w-full sm:w-auto">
                            {{-- CHANGED: 48px tall with 14px text on phones (min-h-12, text-sm); it is the last thumb tap of the survey. --}}
                            <button type="submit" id="submitEvaluationBtn"
                                    class="btn btn-primary w-full sm:w-auto text-sm sm:text-xs px-6 py-2.5 min-h-12 sm:min-h-0 shadow-2xs flex items-center justify-center gap-2">
                                <span id="submitText">Submit Evaluation</span>
                                <ion-icon id="submitIcon" name="paper-plane-outline" class="text-sm"></ion-icon>
                                <ion-icon id="submitLoader" name="hourglass-outline" class="text-sm leading-none btn-hourglass" aria-hidden="true" style="display:none;"></ion-icon>
                            </button>
                            @if(config('services.recaptcha.site_key'))
                                <span class="text-[11px] text-neutral-500 text-center sm:text-right">
                                Protected by reCAPTCHA (<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="underline hover:text-neutral-600">Privacy</a> & <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" class="underline hover:text-neutral-600">Terms</a>)
                            </span>
                            @endif
                        </div>
                    </div>

                </form>
            @endif

        </div>

    </div>

    {{-- ── Real-Time Progress & Sync Script ───────────────────────────────── --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            {{-- CHANGED: the ids come from one @json call instead of a Blade loop inside the array literal, which the editor could not parse. --}}
            const statementIds = @json(array_column($displayStatements, 'id'));
            const totalStatements = statementIds.length;

            const progressBar = document.getElementById('surveyProgressBar');
            const progressText = document.getElementById('progressText');
            const form = document.getElementById('evaluationForm');
            const submitBtn = document.getElementById('submitEvaluationBtn');
            const submitText = document.getElementById('submitText');
            const submitIcon = document.getElementById('submitIcon');
            const submitLoader = document.getElementById('submitLoader');
            const alertBox = document.getElementById('formValidationAlert');
            const alertMsg = document.getElementById('formValidationErrorMsg');

            function markStatementAnswered(statementId, val) {
                // Mobile card border & clear error
                const card = document.getElementById('mobile_card_' + statementId);
                if (card) {
                    card.classList.remove('border-rose-400', 'ring-2', 'ring-rose-300', 'bg-rose-50/20');
                    card.classList.add('border-neutral-200/90');
                }

                // Hide inline error message
                const errMsg = document.getElementById('mb_error_msg_' + statementId);
                if (errMsg) {
                    errMsg.classList.add('hidden');
                    errMsg.classList.remove('flex');
                }

                // Desktop row clean
                const dtRow = document.getElementById('desktop_row_' + statementId);
                if (dtRow) {
                    dtRow.classList.remove('bg-rose-50/40');
                }
            }

            // Sync state between desktop radio and mobile radio
            function updateRating(statementId, val) {
                // Desktop radio
                const dtRadio = document.getElementById('dt_q' + statementId + '_v' + val);
                if (dtRadio) dtRadio.checked = true;

                // Mobile radio
                const mbRadio = document.getElementById('mb_q' + statementId + '_v' + val);
                if (mbRadio) mbRadio.checked = true;

                markStatementAnswered(statementId, val);
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
                // CHANGED: dropped the "(30%)" suffix; the progress bar beside it shows the same share.
                if (progressText) progressText.textContent = count + ' / ' + totalStatements + ' answered';
            }

            // Attach listeners to Desktop radios
            document.querySelectorAll('.survey-radio-dt').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const sid = e.target.dataset.statementId;
                    const val = e.target.dataset.val;
                    updateRating(sid, val);
                });
            });

            // Attach listeners to Mobile radios
            document.querySelectorAll('.survey-radio-mb').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const sid = e.target.dataset.statementId;
                    const val = e.target.dataset.val;
                    updateRating(sid, val);
                });
            });

            // Form Submission Validation
            if (form && submitBtn) {
                form.addEventListener('submit', (e) => {
                    // CHANGED: removed the stall dropdown check; the stall comes from the scanned QR code.

                    // Check that all statements are answered in the canonical desktop inputs
                    const missingStatementIds = [];
                    statementIds.forEach(id => {
                        if (!document.querySelector('input[name="responses[' + id + ']"]:checked')) {
                            missingStatementIds.push(id);
                        }
                    });

                    if (missingStatementIds.length > 0) {
                        e.preventDefault();

                        // Highlight all missing statements
                        missingStatementIds.forEach(id => {
                            const card = document.getElementById('mobile_card_' + id);
                            const errMsg = document.getElementById('mb_error_msg_' + id);
                            if (card) {
                                card.classList.add('border-rose-400', 'ring-2', 'ring-rose-300', 'bg-rose-50/20');
                            }
                            if (errMsg) {
                                errMsg.classList.remove('hidden');
                                errMsg.classList.add('flex');
                            }

                            const dtRow = document.getElementById('desktop_row_' + id);
                            if (dtRow) {
                                dtRow.classList.add('bg-rose-50/40');
                            }
                        });

                        if (alertBox && alertMsg) {
                            alertMsg.textContent = 'Please answer all ' + totalStatements + ' statements before submitting (' + (totalStatements - missingStatementIds.length) + ' of ' + totalStatements + ' completed).';
                            alertBox.classList.remove('hidden');
                        }

                        // Identify the first missing statement
                        const firstMissing = missingStatementIds[0];
                        const isMobile = window.matchMedia('(max-width: 639px)').matches;
                        const targetEl = isMobile
                            ? document.getElementById('mobile_card_' + firstMissing)
                            : document.getElementById('desktop_row_' + firstMissing);

                        if (targetEl) {
                            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            targetEl.classList.add('error-pulse');
                            setTimeout(() => targetEl.classList.remove('error-pulse'), 1500);
                        }
                        return false;
                    }

                    // If valid, hide alert and show loading state
                    if (alertBox) alertBox.classList.add('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('btn-loading');
                    if (submitText) submitText.textContent = 'Submitting…';
                    if (submitIcon) submitIcon.style.display = 'none';
                    if (submitLoader) submitLoader.style.display = 'inline-flex';

                    {{-- CHANGED: @json instead of "{{ ... }}" inside a JS string; the single quotes inside the double-quoted string broke the editor's JS parser (") expected"). --}}
                    const siteKey = @json(config('services.recaptcha.site_key'));
                    if (siteKey && typeof grecaptcha !== 'undefined') {
                        e.preventDefault();
                        grecaptcha.ready(function() {
                            grecaptcha.execute(siteKey, { action: 'evaluation' }).then(function(token) {
                                const tokenInput = document.getElementById('evaluation_g_recaptcha_response');
                                if (tokenInput) tokenInput.value = token;
                                form.submit();
                            }).catch(function(err) {
                                console.warn('reCAPTCHA execution error:', err);
                                form.submit();
                            });
                        });
                        return false;
                    }
                });
            }

            // Sync any initial state (e.g. on page restore)
            document.querySelectorAll('.survey-radio-dt:checked').forEach(r => {
                updateRating(r.dataset.statementId, r.dataset.val);
            });

            updateProgress();
        });
    </script>
@endsection
