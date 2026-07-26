@extends('layouts.dashboard')

@section('title', 'Student Dashboard | Decision Support System: ISU Cauayan Canteen Client Evaluation System')

@section('content')
@php
    $totalEvals = $myStudentEvals->count();
    $averageRating = 0;
    if ($totalEvals > 0) {
        $totalScore = $myStudentEvals->sum(function($eval) {
            return ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
        });
        $averageRating = round($totalScore / $totalEvals, 1);
    }
    $lastEval = $myStudentEvals->first();
@endphp

<div class="max-w-5xl mx-auto">

    {{-- ── Hero Zone ───────────────────────────────────────────────────── --}}
    <div class="rounded-xl overflow-hidden mb-6" style="background: oklch(0.22 0.09 155);">
        <div class="px-6 py-8 sm:px-10 sm:py-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: oklch(0.65 0.12 155);">Welcome back</p>
                <h1 class="font-display font-extrabold leading-tight mb-1" style="color: #fff; font-size: clamp(1.5rem, 4vw, 2rem);">
                    {{ $profile->name ?? $profile->student_number }}
                </h1>
                <p class="text-sm" style="color: oklch(0.72 0.08 155);">ISU Cauayan — Canteen Evaluation System</p>
            </div>

            <a href="{{ route('student.evaluation') }}"
               class="inline-flex items-center justify-center gap-2 font-bold text-sm px-6 py-3 rounded-lg shrink-0 transition-all"
               style="background: oklch(0.56 0.17 155); color: #fff; box-shadow: 0 4px 16px oklch(0.48 0.15 155 / 0.45);">
                <span class="material-symbols-outlined text-[20px] leading-none">rate_review</span>
                Evaluate a Food Stall
            </a>
        </div>

        {{-- Stats bar --}}
        <div class="grid grid-cols-3 border-t" style="border-color: oklch(0.30 0.10 155);">
            <div class="px-6 py-4 text-center" style="border-right: 1px solid oklch(0.30 0.10 155);">
                <p class="tabular-nums font-extrabold font-display text-xl" style="color: #fff;">{{ $totalEvals }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider mt-0.5" style="color: oklch(0.60 0.10 155);">Evaluations</p>
            </div>
            <div class="px-6 py-4 text-center" style="border-right: 1px solid oklch(0.30 0.10 155);">
                <p class="tabular-nums font-extrabold font-display text-xl" style="color: #fff;">{{ $totalEvals > 0 ? $averageRating : '—' }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider mt-0.5" style="color: oklch(0.60 0.10 155);">Avg Rating</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="font-extrabold font-display text-sm leading-tight" style="color: #fff;">
                    {{ $lastEval ? \Carbon\Carbon::parse($lastEval->created_at)->diffForHumans() : 'Never' }}
                </p>
                <p class="text-[11px] font-semibold uppercase tracking-wider mt-0.5" style="color: oklch(0.60 0.10 155);">Last Eval</p>
            </div>
        </div>
    </div>

    {{-- ── Evaluation History ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
            <h2 class="font-bold text-neutral-800 text-sm">Evaluation History</h2>
            <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                {{ $myStudentEvals->count() }} {{ Str::plural('eval', $myStudentEvals->count()) }}
            </span>
        </div>

        @if($myStudentEvals->isEmpty())
            <div class="p-6">
                <div class="flex flex-col items-center justify-center py-16 text-center px-6 rounded-xl border border-dashed border-brand-200 bg-brand-50/30">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5 bg-white shadow-sm border border-brand-100">
                        <span class="material-symbols-outlined text-3xl text-brand-600">rate_review</span>
                    </div>
                    <p class="font-bold text-neutral-900 mb-2 text-lg tracking-tight">Your history is empty</p>
                    <p class="text-sm text-neutral-500 max-w-sm mb-6 leading-relaxed">
                        Submit your first evaluation to help improve the canteen quality at ISU Cauayan. Your feedback is entirely anonymous.
                    </p>
                    <a href="{{ route('student.evaluation') }}" class="inline-flex items-center gap-2 text-sm font-bold bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px] leading-none">add_circle</span>
                        Evaluate a stall now
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="px-6 py-3">Food Stall</th>
                            <th class="px-6 py-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach($myStudentEvals as $eval)
                            <tr class="hover:bg-neutral-50/60 transition-colors">
                                <td class="px-6 py-3.5 text-sm font-semibold text-neutral-900">
                                    {{ $eval->stall_name ?? 'Unknown Stall' }}
                                </td>
                                <td class="px-6 py-3.5 text-right text-xs text-neutral-500 tabular-nums">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection