@extends('layouts.app')

@section('title', 'Student Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-4xl">
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Left Info Panel (Flat, no card wrapper needed) -->
            <div class="md:col-span-1 space-y-4 text-sm">
                <div class="pb-3 border-b border-neutral-200">
                    <h2 class="font-bold text-neutral-800 uppercase tracking-wider text-[10px]">My Details</h2>
                </div>
                <div class="space-y-3.5">
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-0.5">Student Number</span>
                        <span class="text-neutral-800 font-semibold tabular-nums">{{ $profile->student_number }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-0.5">Email</span>
                        <span class="text-neutral-800 font-semibold break-all leading-tight">{{ $profile->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-0.5">Course</span>
                        <span class="text-neutral-800 font-semibold">{{ $profile->course }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-0.5">Year Level</span>
                        <span class="text-neutral-800 font-semibold">{{ $profile->year_level }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Content Panel -->
            <div class="md:col-span-3 bg-white rounded-xl border border-neutral-200/60 p-6">
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
</div>
@endsection