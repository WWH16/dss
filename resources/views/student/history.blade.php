@extends('layouts.dashboard')

@section('title', 'My Evaluations | DSS')
@section('header_title', 'My Evaluations')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight leading-tight mb-1">Evaluation History</h1>
            <p class="text-sm font-medium text-neutral-500">A complete record of all the food stalls you have rated.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="bg-brand-50 text-brand-700 text-sm font-bold px-3 py-1 rounded-full tabular-nums">
                {{ $myStudentEvals->count() }} Total
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        @if($myStudentEvals->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-neutral-50 flex items-center justify-center mb-4 text-neutral-400">
                    <span class="material-symbols-outlined text-[32px]">history</span>
                </div>
                <p class="text-lg font-bold text-neutral-900 mb-2">No evaluations yet</p>
                <p class="text-sm text-neutral-500 max-w-sm mb-6">You haven't submitted any stall evaluations. When you do, they will appear here.</p>
                <a href="{{ route('student.evaluation') }}" class="inline-flex items-center justify-center gap-1.5 w-auto px-6 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Evaluate a Stall
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-neutral-50/50 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="px-6 py-4">Food Stall</th>
                            <th class="px-6 py-4">Date Submitted</th>
                            <th class="px-6 py-4 text-center">Cleanliness</th>
                            <th class="px-6 py-4 text-center">Service</th>
                            <th class="px-6 py-4 text-center">Taste</th>
                            <th class="px-6 py-4 text-center">Price</th>
                            <th class="px-6 py-4 text-right">Avg Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($myStudentEvals as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                                $avg = round($avg, 1);
                            @endphp
                            <tr class="hover:bg-neutral-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">storefront</span>
                                        </div>
                                        <span class="text-sm font-bold text-neutral-900 whitespace-nowrap">{{ $eval->stall_name ?? 'Unknown Stall' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-600 tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                    <span class="text-xs text-neutral-400 block mt-0.5">{{ \Carbon\Carbon::parse($eval->created_at)->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->cleanliness >= 4 ? 'text-emerald-600' : ($eval->cleanliness <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->cleanliness }} <span class="material-symbols-outlined text-[12px] opacity-70">star</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->service >= 4 ? 'text-emerald-600' : ($eval->service <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->service }} <span class="material-symbols-outlined text-[12px] opacity-70">star</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->taste >= 4 ? 'text-emerald-600' : ($eval->taste <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->taste }} <span class="material-symbols-outlined text-[12px] opacity-70">star</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->price >= 4 ? 'text-emerald-600' : ($eval->price <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->price }} <span class="material-symbols-outlined text-[12px] opacity-70">star</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 bg-neutral-50 px-2.5 py-1 rounded-md text-sm font-bold text-neutral-900 border border-neutral-200/60 whitespace-nowrap">
                                        {{ $avg }} <span class="material-symbols-outlined text-[14px] text-amber-500">star</span>
                                    </div>
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
