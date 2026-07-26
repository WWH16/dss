@extends('layouts.dashboard')

@section('title', 'Student Dashboard | DSS')
@section('header_title', 'Dashboard')

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

<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── Header Stats Row ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Welcome Card -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-neutral-200/60 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xl shrink-0 ring-2 ring-brand-100">
                {{ substr($profile->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-medium text-neutral-500 mb-0.5">Welcome back,</p>
                <h1 class="text-xl font-bold text-neutral-900 tracking-tight leading-tight">
                    {{ $profile->name ?? $profile->student_number }}
                </h1>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="bg-white p-6 rounded-xl border border-neutral-200/60 shadow-sm flex flex-col justify-center">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[18px] text-brand-600">task_alt</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Evaluations</span>
            </div>
            <p class="text-2xl font-bold font-display text-neutral-900">{{ $totalEvals }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-neutral-200/60 shadow-sm flex flex-col justify-center">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-[18px] text-amber-500">star</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Avg Rating Given</span>
            </div>
            <p class="text-2xl font-bold font-display text-neutral-900">{{ $totalEvals > 0 ? $averageRating : '—' }}</p>
        </div>
    </div>

    {{-- ── Main Layout (Stalls + History) ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Actionable Stalls -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-neutral-900 tracking-tight">Available Food Stalls</h2>
                <a href="{{ route('student.evaluation') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Evaluate Any &rarr;</a>
            </div>

            @if($stalls->isEmpty())
                <div class="bg-neutral-50 border border-neutral-200 border-dashed rounded-xl p-8 text-center text-neutral-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-neutral-400">storefront</span>
                    <p class="font-medium">No stalls available to evaluate.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($stalls as $stall)
                        <div class="bg-white p-5 rounded-xl border border-neutral-200/60 shadow-sm hover:shadow-md transition-shadow group flex flex-col justify-between h-full">
                            <div>
                                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                                </div>
                                <h3 class="font-bold text-neutral-900 mb-1 line-clamp-1">{{ $stall->name }}</h3>
                                <p class="text-xs text-neutral-500 mb-4 line-clamp-2">{{ $stall->description ?? 'Campus food stall' }}</p>
                            </div>
                            <a href="{{ route('student.evaluation', ['stall' => $stall->id]) }}" class="inline-flex items-center justify-center gap-1.5 w-full bg-brand-50 hover:bg-brand-100 text-brand-700 text-sm font-semibold py-2.5 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[16px]">rate_review</span>
                                Rate Stall
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Recent Activity -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-neutral-900 tracking-tight">Recent Evaluations</h2>
            
            <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
                @if($myStudentEvals->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-neutral-50 flex items-center justify-center mb-3 text-neutral-400">
                            <span class="material-symbols-outlined text-[24px]">history</span>
                        </div>
                        <p class="text-sm font-medium text-neutral-900 mb-1">No history yet</p>
                        <p class="text-xs text-neutral-500">Your recent ratings will appear here.</p>
                    </div>
                @else
                    <ul class="divide-y divide-neutral-100">
                        @foreach($myStudentEvals->take(5) as $eval)
                            <li class="p-4 hover:bg-neutral-50/50 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 mt-0.5">
                                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-neutral-900 line-clamp-1">{{ $eval->stall_name ?? 'Unknown Stall' }}</p>
                                            <p class="text-[11px] font-medium text-neutral-500">{{ \Carbon\Carbon::parse($eval->created_at)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @php
                                        $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                                        $avg = round($avg, 1);
                                    @endphp
                                    <div class="flex items-center gap-1 bg-neutral-50 px-2 py-1 rounded text-xs font-bold text-neutral-700">
                                        {{ $avg }} <span class="material-symbols-outlined text-[12px] text-amber-500">star</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @if($myStudentEvals->count() > 5)
                        <div class="p-3 text-center border-t border-neutral-100 bg-neutral-50/50">
                            <span class="text-xs font-medium text-neutral-500">+ {{ $myStudentEvals->count() - 5 }} more</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>
@endsection