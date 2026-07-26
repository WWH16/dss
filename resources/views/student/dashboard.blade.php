@extends('layouts.dashboard')

@section('title', 'Student Dashboard | Decision Support System: ISU Cauayan Canteen Client Evaluation System')

@section('content')
<div class="max-w-5xl mx-auto">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Student Dashboard</h1>
                <p class="text-neutral-500 text-sm">Welcome back, <span class="font-semibold text-brand-700">{{ $profile->name ?? $profile->student_number }}</span></p>
            </div>
            <div>
                <a href="{{ route('student.evaluation') }}" class="btn btn-primary inline-flex items-center gap-2 text-sm font-semibold">
                    <span class="material-symbols-outlined text-lg leading-none">rate_review</span>
                    Evaluate Food Stall
                </a>
            </div>
        </div>
        <!-- Dashboard Metrics (True Dashboard Features) -->
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

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <!-- Metric 1 -->
            <div class="bg-white rounded-xl border border-neutral-200/60 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                    <span class="material-symbols-outlined">ballot</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-0.5">Total Submitted</p>
                    <p class="text-2xl font-bold text-neutral-900 tabular-nums leading-none">{{ $totalEvals }}</p>
                </div>
            </div>

            <!-- Metric 2 -->
            <div class="bg-white rounded-xl border border-neutral-200/60 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
                    <span class="material-symbols-outlined">star</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-0.5">Avg Rating Given</p>
                    <p class="text-2xl font-bold text-neutral-900 tabular-nums leading-none">{{ $totalEvals > 0 ? $averageRating : '--' }}</p>
                </div>
            </div>

            <!-- Metric 3 -->
            <div class="bg-white rounded-xl border border-neutral-200/60 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                    <span class="material-symbols-outlined">history</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-0.5">Last Submission</p>
                    <p class="text-sm font-bold text-neutral-900 leading-tight mt-1">
                        {{ $lastEval ? \Carbon\Carbon::parse($lastEval->created_at)->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>
        </div>
        <div>
            <!-- Evaluation History Panel -->
            <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-neutral-100">
                    <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation History</h2>
                    <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                        {{ $myStudentEvals->count() }} {{ Str::plural('eval', $myStudentEvals->count()) }}
                    </span>
                </div>

                @if($myStudentEvals->isEmpty())
                    <div class="text-center py-10">
                        <span class="material-symbols-outlined text-3xl text-neutral-300 mb-2">ballot</span>
                        <p class="text-neutral-500 text-sm">You haven't evaluated any stalls yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                                    <th class="pb-2">Food Stall</th>
                                    <th class="pb-2 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                @foreach($myStudentEvals as $eval)
                                    @php
                                        $stall = $stalls->firstWhere('id', $eval->stall_id);
                                    @endphp
                                    <tr class="text-sm hover:bg-neutral-50/40">
                                        <td class="py-3 font-semibold text-neutral-900">
                                            {{ $stall->name ?? 'Unknown Stall' }}
                                        </td>
                                        <td class="py-3 text-right text-xs text-neutral-500 tabular-nums">
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
    </div>
@endsection