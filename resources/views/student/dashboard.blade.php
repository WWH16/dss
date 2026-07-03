@extends('layouts.app')

@section('title', 'Student Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-5xl">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-display font-bold tracking-tight text-neutral-900 leading-tight">Student Dashboard</h1>
                <p class="text-neutral-500 mt-1 text-sm leading-relaxed">Welcome back, <span class="font-semibold text-brand-700">{{ $profile->name ?? $profile->student_number }}</span></p>
            </div>
            <div>
                <a href="{{ route('student.evaluation') }}" class="btn btn-primary inline-flex items-center gap-2 text-sm font-semibold tracking-wide">
                    <span class="material-symbols-outlined text-lg">rate_review</span>
                    Evaluate Food Stall
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Profile Details Card (left column on desktop) -->
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-neutral-100">
                    <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">account_circle</span>
                    <div>
                        <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">My Profile</h2>
                        <p class="text-xs text-neutral-400 font-medium">Student Account</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Student Number</span>
                        <span class="text-sm text-neutral-800 font-semibold tabular-nums">{{ $profile->student_number }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Email Address</span>
                        <span class="text-sm text-neutral-800 font-semibold break-all leading-normal">{{ $profile->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Course</span>
                        <span class="text-sm text-neutral-800 font-semibold">{{ $profile->course }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Year Level</span>
                        <span class="text-sm text-neutral-800 font-semibold">{{ $profile->year_level }}</span>
                    </div>
                </div>
            </div>

            <!-- My Evaluations (right column on desktop) -->
            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">history</span>
                    <div>
                        <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">My Evaluated Stalls</h2>
                        <p class="text-xs text-neutral-400 font-medium">Recent stall feedback history</p>
                    </div>
                </div>

                @if($myStudentEvals->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="material-symbols-outlined text-4xl text-neutral-300 mb-3">ballot</span>
                        <p class="text-neutral-500 font-medium text-sm leading-relaxed">You haven't evaluated any food stall yet.</p>
                        <a href="{{ route('student.evaluation') }}" class="text-brand-600 hover:text-brand-700 font-semibold text-xs tracking-wider uppercase mt-3 inline-flex items-center gap-1">
                            Submit your first evaluation <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-neutral-100 text-[10px] text-neutral-400 font-bold uppercase tracking-widest">
                                    <th class="pb-3 font-bold">Food Stall</th>
                                    <th class="pb-3 font-bold text-right">Date Evaluated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                @foreach($myStudentEvals as $eval)
                                    @php
                                        $stall = $stalls->firstWhere('id', $eval->stall_id);
                                    @endphp
                                    <tr class="text-sm text-neutral-800 hover:bg-neutral-50/50">
                                        <td class="py-3.5 font-semibold text-neutral-900 leading-snug">
                                            {{ $stall->name ?? 'Unknown Stall' }}
                                        </td>
                                        <td class="py-3.5 text-right text-xs text-neutral-500 tabular-nums font-medium">
                                            {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y h:i A') }}
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
</div>
@endsection