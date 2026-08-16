@extends('layouts.focused')

@section('title', 'Evaluate Food Stall | DSS')

@section('header_title', 'Evaluate Food Stall')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(!session('success'))
        <!-- Back to Dashboard Arrow Button -->
        <div class="mb-4 sm:mb-6">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-500 hover:text-brand-600 transition-colors bg-white px-4 py-2 rounded-lg border border-neutral-200 shadow-sm">
                <ion-icon name="arrow-back-outline" class="text-lg leading-none"></ion-icon>
                Back to Dashboard
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        @if(!session('success'))
            <!-- Form Header -->
            <div class="p-4 sm:p-6 md:p-10 border-b border-neutral-100 bg-neutral-50/30">
                <div class="flex flex-col md:flex-row items-center justify-center md:justify-between gap-4 md:gap-8 text-center">
                    <!-- Mobile Logos (Side-by-side) -->
                    <div class="flex md:hidden w-full justify-center gap-6 mb-1">
                        <img src="{{ asset('assets/images/isu_logo.png') }}" class="w-14 h-14 object-contain drop-shadow-sm" alt="ISU Logo">
                        <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="w-14 h-14 object-contain drop-shadow-sm" alt="Bagong Pilipinas">
                    </div>

                    <!-- Desktop Left Logo -->
                    <img src="{{ asset('assets/images/isu_logo.png') }}" class="hidden md:block w-24 h-24 object-contain shrink-0 drop-shadow-sm" alt="ISU Logo">
                    
                    <div class="flex-1 min-w-0">
                        <p class="uppercase font-bold text-neutral-800 text-xs sm:text-sm tracking-widest mb-1">Isabela State University</p>
                        <p class="text-neutral-500 text-xs sm:text-sm mb-3 font-medium uppercase tracking-wider">Cauayan Campus</p>
                        <h1 class="text-xl sm:text-3xl font-display font-extrabold text-brand-800 leading-tight mb-2" style="text-wrap: balance;">Citizen / Client Satisfaction Survey</h1>
                        <p class="text-sm text-neutral-500 max-w-lg mx-auto">Please answer each statement by selecting the rating that best reflects your opinion.</p>
                    </div>

                    <!-- Desktop Right Logo -->
                    <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="hidden md:block w-24 h-24 object-contain shrink-0 drop-shadow-sm" alt="Bagong Pilipinas">
                </div>
            </div>
        @endif

        <div class="p-4 sm:p-8 md:p-10">

            @if(session('success'))
                <style>
                    @keyframes popIn {
                        0% { transform: scale(0.8); opacity: 0; }
                        50% { transform: scale(1.1); }
                        100% { transform: scale(1); opacity: 1; }
                    }
                    @keyframes slideUpFade {
                        from { transform: translateY(20px); opacity: 0; }
                        to { transform: translateY(0); opacity: 1; }
                    }
                    .animate-pop-in { animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
                    .animate-slide-up-1 { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards; opacity: 0; }
                    .animate-slide-up-2 { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards; opacity: 0; }
                    .animate-slide-up-3 { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards; opacity: 0; }
                    @media (prefers-reduced-motion: reduce) {
                        .animate-pop-in, .animate-slide-up-1, .animate-slide-up-2, .animate-slide-up-3 {
                            animation: none; opacity: 1; transform: none;
                        }
                    }
                </style>
                <div class="py-16 md:py-24 flex flex-col items-center justify-center text-center">
                    <div class="relative w-24 h-24 mb-8 animate-pop-in">
                        <div class="absolute inset-0 bg-brand-200 rounded-full animate-ping opacity-60" style="animation-duration: 2.5s;"></div>
                        <div class="relative flex items-center justify-center w-full h-full bg-brand-600 rounded-full shadow-2xl shadow-brand-600/40 p-4">
                            <ion-icon name="checkmark-circle" class="text-5xl text-white"></ion-icon>
                        </div>
                    </div>
                    
                    <h2 class="text-3xl md:text-4xl font-display font-extrabold text-brand-800 mb-4 animate-slide-up-1">Evaluation Submitted!</h2>
                    
                    <p class="text-neutral-500 max-w-sm mx-auto mb-10 text-base animate-slide-up-2">
                        Thank you for your feedback. Your insights directly help us maintain excellent dining standards at the Cauayan Campus!
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto animate-slide-up-3">
                        <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-6 py-3 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-bold rounded-lg transition-colors text-sm text-center">
                            Back to Dashboard
                        </a>
                        <a href="{{ route('student.evaluation') }}" class="btn btn-primary w-full sm:w-auto px-6 py-3 rounded-lg text-sm text-center flex items-center justify-center gap-2 group">
                            <ion-icon name="refresh-outline" class="text-lg group-hover:-rotate-12 transition-transform"></ion-icon>
                            <span>Evaluate Another</span>
                        </a>
                    </div>
                </div>
            @elseif($stalls->isEmpty())
                <div class="p-6 text-center bg-amber-50 border border-amber-100 rounded-xl text-amber-800">
                    <ion-icon name="warning-outline" class="text-3xl mb-2"></ion-icon>
                    <p class="font-semibold">No stalls are available yet.</p>
                    <p class="text-sm">Please ask an administrator to add stall information first.</p>
                </div>
            @else
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl text-sm">
                        <ul class="list-disc pl-5 mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="evaluationForm" action="{{ route('student.evaluation.store') }}" method="POST">
                    @csrf
                    
                    <!-- Stall Selection -->
                    <div class="mb-6 p-4 sm:p-6 bg-brand-50/50 rounded-xl border border-brand-100/50">
                        <label class="block text-sm font-bold text-neutral-800 mb-2">Select Food Stall to Evaluate</label>
                        <select name="stall_id" class="w-full sm:w-1/2 px-4 py-3 sm:py-2.5 bg-white border border-neutral-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" required>
                            <option value="">Choose a Stall...</option>
                            @foreach($stalls as $stall)
                                <option value="{{ $stall->id }}" {{ request('stall') == $stall->id ? 'selected' : '' }}>{{ $stall->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Scale Info — compact grid on mobile, flex on sm+ -->
                    <div class="mb-6 bg-neutral-50 p-3 sm:p-4 rounded-xl border border-neutral-100">
                        <p class="text-xs font-bold text-neutral-700 mb-2 sm:hidden">Rating Scale</p>
                        <div class="grid grid-cols-5 gap-1 sm:hidden">
                            @foreach([[5,'Excellent'],[4,'Very Good'],[3,'Good'],[2,'Fair'],[1,'Poor']] as [$n,$label])
                            <div class="flex flex-col items-center gap-1">
                                <span class="w-7 h-7 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-xs tabular-nums">{{ $n }}</span>
                                <span class="text-[10px] font-medium text-neutral-500 text-center leading-tight">{{ $label }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="hidden sm:flex flex-wrap gap-3 text-xs font-medium text-neutral-600">
                            <strong class="text-neutral-900 mr-2">Rating Scale:</strong>
                            <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">5</span> Excellent</span>
                            <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">4</span> Very Good</span>
                            <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">3</span> Good</span>
                            <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">2</span> Fair</span>
                            <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">1</span> Poor</span>
                        </div>
                    </div>

                    {{-- ── MOBILE CARD LAYOUT (hidden on sm+) ─────────────────────── --}}
                    <div class="sm:hidden mb-6 space-y-3" id="mobileQuestions">
                        <!-- Mobile progress bar -->
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-semibold text-neutral-500">Progress</p>
                            <p class="text-xs font-bold text-brand-700" id="mobileProgressLabel">0 / {{ count($displayStatements) }} answered</p>
                        </div>
                        <div class="h-1.5 bg-neutral-100 rounded-full overflow-hidden mb-4">
                            <div id="mobileProgressBar" class="h-full bg-brand-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>

                        @foreach($displayStatements as $index => $statement)
                        <div class="mobile-question-card border border-neutral-200 rounded-xl overflow-hidden transition-colors duration-200" data-question="{{ $index }}">
                            <!-- Question header -->
                            <div class="px-4 py-3 bg-neutral-50 border-b border-neutral-100 flex items-start gap-2">
                                <span class="shrink-0 w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center tabular-nums mt-0.5">{{ $index + 1 }}</span>
                                <p class="text-sm font-medium text-neutral-800 leading-snug">{{ $statement['statement'] }}</p>
                            </div>
                            <!-- Rating pills row -->
                            <div class="px-3 py-3 flex gap-2">
                                @for($val = 5; $val >= 1; $val--)
                                <label class="mobile-rating-label flex-1 relative cursor-pointer" for="mob_q{{ $statement['id'] }}_v{{ $val }}">
                                    <input
                                        type="radio"
                                        id="mob_q{{ $statement['id'] }}_v{{ $val }}"
                                        name="responses[{{ $statement['id'] }}]"
                                        value="{{ $val }}"
                                        required
                                        class="sr-only peer"
                                        data-qindex="{{ $index }}"
                                        data-total="{{ count($displayStatements) }}"
                                    >
                                    <div class="mobile-pill peer-checked:bg-brand-600 peer-checked:text-white peer-checked:border-brand-600 peer-checked:shadow-[0_2px_8px_rgba(0,0,0,0.15)] peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-1 flex flex-col items-center justify-center min-h-[52px] rounded-lg border-2 border-neutral-200 bg-white transition-all duration-150 active:scale-95 select-none">
                                        <span class="text-base font-bold leading-none">{{ $val }}</span>
                                        <span class="text-[9px] font-medium leading-tight mt-0.5 opacity-70 peer-checked:opacity-100">
                                            @if($val === 5) Excel @elseif($val === 4) V.Good @elseif($val === 3) Good @elseif($val === 2) Fair @else Poor @endif
                                        </span>
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- ── DESKTOP TABLE LAYOUT (hidden on mobile) ─────────────────── --}}
                    <div class="hidden sm:block mb-8">
                        <div class="relative rounded-xl border border-neutral-200 shadow-sm">
                            <div class="overflow-x-auto rounded-xl">
                            <table class="w-full text-left border-collapse min-w-[620px]">
                                <thead>
                                    <tr class="bg-neutral-50/80 border-b border-neutral-200 text-[10px] text-neutral-500 font-bold uppercase tracking-wider">
                                        <th class="p-4 sticky left-0 bg-neutral-50/95 backdrop-blur-sm z-20 shadow-[2px_0_8px_rgba(0,0,0,0.04)] border-r border-neutral-200">Statement</th>
                                        <th class="p-4 text-center w-16">5</th>
                                        <th class="p-4 text-center w-16">4</th>
                                        <th class="p-4 text-center w-16">3</th>
                                        <th class="p-4 text-center w-16">2</th>
                                        <th class="p-4 text-center w-16">1</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100">
                                    @foreach($displayStatements as $index => $statement)
                                        <tr class="group hover:bg-brand-50/20 transition-colors">
                                            <td class="p-4 text-sm font-medium text-neutral-800 sticky left-0 bg-white group-hover:bg-neutral-50/90 transition-colors z-10 shadow-[2px_0_8px_rgba(0,0,0,0.04)] border-r border-neutral-100">
                                                <span class="text-neutral-400 mr-2 tabular-nums">{{ $index + 1 }}.</span>
                                                {{ $statement['statement'] }}
                                            </td>
                                            @for($val = 5; $val >= 1; $val--)
                                                <td class="p-0 text-center align-middle border-l border-neutral-50">
                                                    <label class="flex items-center justify-center w-full h-full min-h-[56px] cursor-pointer hover:bg-brand-50/40 transition-colors">
                                                        <input type="radio" name="responses[{{ $statement['id'] }}]" value="{{ $val }}" required class="appearance-auto w-5 h-5 cursor-pointer accent-brand-600">
                                                    </label>
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="mb-6 sm:mb-8">
                        <label class="block text-sm font-bold text-neutral-800 mb-2">Compliments / Suggestions / Complaints <span class="text-neutral-400 font-normal">(Optional)</span></label>
                        <textarea name="comment" rows="4" class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all resize-none shadow-sm" placeholder="Share your honest thoughts here..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-5 border-t border-neutral-200">
                        <p class="text-xs text-neutral-500 font-medium flex items-center gap-1.5">
                            <ion-icon name="lock-closed-outline" class="text-base text-neutral-400"></ion-icon>
                            This evaluation is completely confidential.
                        </p>
                        <button type="submit" id="submitEvaluationBtn" class="btn btn-primary px-8 py-3.5 sm:py-3 w-full sm:w-auto text-sm flex items-center justify-center gap-2 group transition-all">
                            <span id="submitEvaluationText">Submit Evaluation</span>
                            <ion-icon id="submitEvaluationIcon" name="arrow-forward-outline" class="text-lg group-hover:translate-x-1 transition-transform duration-300"></ion-icon>
                            <ion-icon id="submitEvaluationLoader" name="hourglass-outline" class="text-lg animate-spin" style="display: none;"></ion-icon>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
/* Mobile pill hover (touch devices: use :active instead of :hover) */
@media (hover: hover) {
    .mobile-pill:hover {
        border-color: var(--color-brand-300, #86efac);
        background-color: var(--color-brand-50, #f0fdf4);
        color: var(--color-brand-700, #15803d);
    }
}
/* Answered card highlight */
.mobile-question-card.is-answered {
    border-color: oklch(0.84 0.10 155);
    background-color: oklch(0.97 0.03 155 / 0.4);
}
.mobile-question-card.is-answered .mobile-pill-label-header {
    background-color: oklch(0.93 0.06 155 / 0.5);
}
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('evaluationForm');
        const submitBtn = document.getElementById('submitEvaluationBtn');
        const btnText = document.getElementById('submitEvaluationText');
        const btnIcon = document.getElementById('submitEvaluationIcon');
        const btnLoader = document.getElementById('submitEvaluationLoader');

        if(form && submitBtn) {
            form.addEventListener('submit', function(e) {
                if (form.checkValidity()) {
                    setTimeout(() => {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                        btnText.textContent = 'Submitting...';
                        btnIcon.style.display = 'none';
                        btnLoader.style.display = 'inline-block';
                    }, 10);
                }
            });
        }

        // ── Mobile progress tracker ───────────────────────────────────────
        const progressBar = document.getElementById('mobileProgressBar');
        const progressLabel = document.getElementById('mobileProgressLabel');
        const totalQuestions = parseInt(document.querySelector('[data-total]')?.dataset.total ?? 0);

        function updateProgress() {
            if (!progressBar || !progressLabel || totalQuestions === 0) return;
            // Count unique question groups answered
            const answered = new Set();
            document.querySelectorAll('#mobileQuestions input[type="radio"]:checked').forEach(r => {
                answered.add(r.dataset.qindex);
            });
            const count = answered.size;
            const pct = Math.round((count / totalQuestions) * 100);
            progressBar.style.width = pct + '%';
            progressLabel.textContent = count + ' / ' + totalQuestions + ' answered';

            // Highlight answered cards
            document.querySelectorAll('.mobile-question-card').forEach(card => {
                const idx = card.dataset.question;
                if (answered.has(String(idx))) {
                    card.classList.add('is-answered');
                } else {
                    card.classList.remove('is-answered');
                }
            });
        }

        document.querySelectorAll('#mobileQuestions input[type="radio"]').forEach(r => {
            r.addEventListener('change', updateProgress);
        });
    });
</script>
@endsection