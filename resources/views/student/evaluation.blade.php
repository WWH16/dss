@extends('layouts.focused')

@section('title', 'Evaluate Food Stall | DSS')

@section('header_title', 'Evaluate Food Stall')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Back to Dashboard Arrow Button -->
    <div class="mb-6">
        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-500 hover:text-brand-600 transition-colors bg-white px-4 py-2 rounded-lg border border-neutral-200 shadow-sm">
            <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200/60 shadow-sm overflow-hidden">
        
        <!-- Form Header (Paper style) -->
        <div class="p-8 md:p-10 border-b border-neutral-100 bg-neutral-50/30">
            <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                <img src="{{ asset('assets/images/isu_logo.png') }}" class="w-20 h-20 object-contain" alt="ISU Logo">
                <div class="flex-1">
                    <p class="uppercase font-bold text-neutral-800 text-sm tracking-wider mb-0.5">Isabela State University</p>
                    <p class="text-neutral-500 text-sm mb-3">Cauayan Campus</p>
                    <h1 class="text-2xl font-display font-bold text-brand-700 leading-tight">Citizen / Client Satisfaction Survey</h1>
                    <p class="text-sm text-neutral-500 mt-2">Please answer each statement by selecting the rating that best reflects your opinion.</p>
                </div>
                <img src="{{ asset('assets/images/bagong-pilipinas-logo.png') }}" class="w-20 h-20 object-contain" alt="Bagong Pilipinas">
            </div>
        </div>

        <div class="p-8 md:p-10">


            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-xl text-sm">
                    <ul class="list-disc pl-5 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($stalls->isEmpty())
                <div class="p-6 text-center bg-amber-50 border border-amber-100 rounded-xl text-amber-800">
                    <span class="material-symbols-outlined text-3xl mb-2">warning</span>
                    <p class="font-semibold">No stalls are available yet.</p>
                    <p class="text-sm">Please ask an administrator to add stall information first.</p>
                </div>
            @else
                <form action="{{ route('student.evaluation.store') }}" method="POST">
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
                    <div class="mb-8 overflow-x-auto rounded-xl border border-neutral-200">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-neutral-50 border-b border-neutral-200 text-[10px] text-neutral-500 font-bold uppercase tracking-wider">
                                    <th class="p-4">Statement</th>
                                    <th class="p-4 text-center w-14">5</th>
                                    <th class="p-4 text-center w-14">4</th>
                                    <th class="p-4 text-center w-14">3</th>
                                    <th class="p-4 text-center w-14">2</th>
                                    <th class="p-4 text-center w-14">1</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach($displayStatements as $index => $statement)
                                    <tr class="hover:bg-neutral-50/50 transition-colors">
                                        <td class="p-4 text-sm font-medium text-neutral-800">
                                            <span class="text-neutral-400 mr-2">{{ $index + 1 }}.</span>
                                            {{ $statement['statement'] }}
                                        </td>
                                        @for($val = 5; $val >= 1; $val--)
                                            <td class="p-4 text-center align-middle">
                                                <input type="radio" name="responses[{{ $statement['id'] }}]" value="{{ $val }}" required 
                                                    class="appearance-auto w-5 h-5 cursor-pointer accent-brand-600">
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Comments -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-neutral-800 mb-2">Compliments / Suggestions / Complaints</label>
                        <textarea name="comment" rows="4" class="w-full px-4 py-3 bg-white border border-neutral-300 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none shadow-sm" placeholder="Share compliments, suggestions, or complaints here...">{{ old('comment') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-neutral-200">
                        <p class="text-xs text-neutral-400 font-medium">This information is treated confidentially.</p>
                        <button type="submit" class="btn btn-primary px-8 w-full sm:w-auto text-sm">
                            Submit Evaluation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection