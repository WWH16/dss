@extends('layouts.focused')

@section('title', 'Evaluate Food Stall | DSS')

@section('header_title', 'Evaluate Food Stall')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(!session('success'))
        <!-- Back to Dashboard Arrow Button -->
        <div class="mb-6">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-500 hover:text-brand-600 transition-colors bg-white px-4 py-2 rounded-lg border border-neutral-200 shadow-sm">
                <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
                Back to Dashboard
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        @if(!session('success'))
            <!-- Form Header (Paper style) -->
            <div class="p-6 md:p-10 border-b border-neutral-100 bg-neutral-50/30">
                <div class="flex flex-col md:flex-row items-center justify-center md:justify-between gap-6 md:gap-8 text-center">
                    <!-- Mobile Logos (Side-by-side) -->
                    <div class="flex md:hidden w-full justify-center gap-8 mb-2">
                        <img src="{{ asset('assets/images/isu_logo.png') }}" class="w-16 h-16 object-contain drop-shadow-sm" alt="ISU Logo">
                        <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="w-16 h-16 object-contain drop-shadow-sm" alt="Bagong Pilipinas">
                    </div>

                    <!-- Desktop Left Logo -->
                    <img src="{{ asset('assets/images/isu_logo.png') }}" class="hidden md:block w-24 h-24 object-contain shrink-0 drop-shadow-sm" alt="ISU Logo">
                    
                    <div class="flex-1 min-w-0">
                        <p class="uppercase font-bold text-neutral-800 text-xs sm:text-sm tracking-widest mb-1">Isabela State University</p>
                        <p class="text-neutral-500 text-xs sm:text-sm mb-4 font-medium uppercase tracking-wider">Cauayan Campus</p>
                        <h1 class="text-2xl sm:text-3xl font-display font-extrabold text-brand-800 leading-tight mb-3">Citizen / Client Satisfaction Survey</h1>
                        <p class="text-sm text-neutral-500 max-w-lg mx-auto">Please answer each statement by selecting the rating that best reflects your opinion.</p>
                    </div>

                    <!-- Desktop Right Logo -->
                    <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="hidden md:block w-24 h-24 object-contain shrink-0 drop-shadow-sm" alt="Bagong Pilipinas">
                </div>
            </div>
        @endif

        <div class="p-8 md:p-10">


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
                </style>
                <div class="py-16 md:py-24 flex flex-col items-center justify-center text-center">
                    <div class="relative w-24 h-24 mb-8 animate-pop-in">
                        <div class="absolute inset-0 bg-brand-200 rounded-full animate-ping opacity-60" style="animation-duration: 2.5s;"></div>
                        <div class="relative flex items-center justify-center w-full h-full bg-brand-600 rounded-full shadow-2xl shadow-brand-600/40">
                            <span class="material-symbols-outlined text-white text-5xl">task_alt</span>
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
                            <span class="material-symbols-outlined text-[18px] group-hover:-rotate-12 transition-transform">refresh</span>
                            <span>Evaluate Another</span>
                        </a>
                    </div>
                </div>
            @elseif($stalls->isEmpty())
                <div class="p-6 text-center bg-amber-50 border border-amber-100 rounded-xl text-amber-800">
                    <span class="material-symbols-outlined text-3xl mb-2">warning</span>
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
                    <div class="mb-8 p-6 bg-brand-50/50 rounded-xl border border-brand-100/50">
                        <label class="block text-sm font-bold text-neutral-800 mb-2">Select Food Stall to Evaluate</label>
                        <select name="stall_id" class="w-full md:w-1/2 px-4 py-2.5 bg-white border border-neutral-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" required>
                            <option value="">Choose a Stall...</option>
                            @foreach($stalls as $stall)
                                <option value="{{ $stall->id }}">{{ $stall->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Scale Info -->
                    <div class="mb-8 flex flex-wrap gap-3 text-xs font-medium text-neutral-600 bg-neutral-50 p-4 rounded-xl border border-neutral-100">
                        <strong class="text-neutral-900 mr-2">Rating Scale:</strong>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">5</span> Excellent</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">4</span> Very Good</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">3</span> Good</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">2</span> Fair</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-[10px] tabular-nums">1</span> Poor</span>
                    </div>

                    <!-- Survey Table -->
                    <div class="mb-8 overflow-x-auto rounded-xl border border-neutral-200 shadow-sm">
                        <table class="w-full text-left border-collapse min-w-[650px]">
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

                    <!-- Comments -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-neutral-800 mb-2">Compliments / Suggestions / Complaints <span class="text-neutral-400 font-normal">(Optional)</span></label>
                        <textarea name="comment" rows="4" class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all resize-none shadow-sm" placeholder="Share your honest thoughts here..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-neutral-200">
                        <p class="text-xs text-neutral-500 font-medium flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-neutral-400">lock</span>
                            This evaluation is completely confidential.
                        </p>
                        <button type="submit" id="submitEvaluationBtn" class="btn btn-primary px-8 py-3 w-full sm:w-auto text-sm flex items-center justify-center gap-2 group transition-all">
                            <span id="submitEvaluationText">Submit Evaluation</span>
                            <span id="submitEvaluationIcon" class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform duration-300 ease-out-quint">arrow_forward</span>
                            <span id="submitEvaluationLoader" class="material-symbols-outlined text-[18px] animate-spin" style="display: none;">progress_activity</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
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
                // If HTML5 validation passes, show loading state
                if (form.checkValidity()) {
                    // Slight delay to ensure the form actually submits before freezing the button
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
    });
</script>
@endsection