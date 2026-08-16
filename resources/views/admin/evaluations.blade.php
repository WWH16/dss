@extends('layouts.dashboard')
@section('title', 'Evaluations | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight mb-1">All Evaluations</h1>
            <p class="text-sm font-medium text-neutral-500">A comprehensive ledger of all feedback submitted by students.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-brand-50 text-brand-700 text-sm font-bold px-3 py-1 rounded-full tabular-nums">
                {{ $evaluations->count() }} Total
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        @if($evaluations->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-neutral-50 flex items-center justify-center mb-4 text-neutral-400">
                    <ion-icon name="time-outline" class="text-3xl text-neutral-400 opacity-60"></ion-icon>
                </div>
                <p class="text-lg font-bold text-neutral-900 mb-2">No evaluations yet</p>
                <p class="text-sm text-neutral-500 max-w-sm">Evaluations submitted by students will automatically appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/50 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-100">
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Food Stall</th>
                            <th class="px-6 py-4 text-center">Clean</th>
                            <th class="px-6 py-4 text-center">Serv</th>
                            <th class="px-6 py-4 text-center">Taste</th>
                            <th class="px-6 py-4 text-center">Price</th>
                            <th class="px-6 py-4 text-right">Avg Rating</th>
                            <th class="px-6 py-4">Comment</th>
                            <th class="px-6 py-4 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($evaluations as $eval)
                            @php
                                $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4;
                                $avg = round($avg, 1);
                            @endphp
                            <tr class="hover:bg-neutral-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-neutral-900">
                                    {{ $eval->student_name }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-neutral-700">
                                    {{ $eval->stall_name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->cleanliness >= 4 ? 'text-emerald-600' : ($eval->cleanliness <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->cleanliness }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->service >= 4 ? 'text-emerald-600' : ($eval->service <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->service }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->taste >= 4 ? 'text-emerald-600' : ($eval->taste <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->taste }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm {{ $eval->price >= 4 ? 'text-emerald-600' : ($eval->price <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">
                                        {{ $eval->price }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 bg-neutral-50 px-2.5 py-1 rounded-md text-sm font-bold text-neutral-900 border border-neutral-200/60">
                                        {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-neutral-500 max-w-[200px] truncate" title="{{ $eval->comment }}">
                                    {{ $eval->comment ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-neutral-600 tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($evaluations as $eval)
                        @php
                            $avg = round(($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4, 1);
                        @endphp
                        <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-bold text-neutral-900 leading-tight mb-0.5">{{ $eval->student_name }}</h3>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-brand-600">{{ $eval->stall_name }}</p>
                                    <p class="text-[10px] text-neutral-400 mt-1">{{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y • h:i A') }}</p>
                                </div>
                                <div class="shrink-0 inline-flex items-center gap-1 bg-neutral-50 px-2 py-1 rounded-md text-xs font-bold text-neutral-900 border border-neutral-200/60">
                                    {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                </div>
                            </div>
                            @if($eval->comment)
                                <div class="bg-neutral-50/70 p-3 rounded-lg text-[13px] text-neutral-700 italic border border-neutral-100 mt-1">
                                    "{{ $eval->comment }}"
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-1 pt-3 border-t border-neutral-100/80">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-neutral-500 font-medium">Cleanliness</span>
                                    <span class="font-bold flex items-center gap-0.5 {{ $eval->cleanliness >= 4 ? 'text-emerald-600' : ($eval->cleanliness <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">{{ $eval->cleanliness }} <ion-icon name="star" class="text-amber-500 text-[10px] inline-block"></ion-icon></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-neutral-500 font-medium">Service</span>
                                    <span class="font-bold flex items-center gap-0.5 {{ $eval->service >= 4 ? 'text-emerald-600' : ($eval->service <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">{{ $eval->service }} <ion-icon name="star" class="text-amber-500 text-[10px] inline-block"></ion-icon></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-neutral-500 font-medium">Taste</span>
                                    <span class="font-bold flex items-center gap-0.5 {{ $eval->taste >= 4 ? 'text-emerald-600' : ($eval->taste <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">{{ $eval->taste }} <ion-icon name="star" class="text-amber-500 text-[10px] inline-block"></ion-icon></span>
                                </div>
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-neutral-500 font-medium">Price</span>
                                    <span class="font-bold flex items-center gap-0.5 {{ $eval->price >= 4 ? 'text-emerald-600' : ($eval->price <= 2 ? 'text-rose-500' : 'text-neutral-700') }}">{{ $eval->price }} <ion-icon name="star" class="text-amber-500 text-[10px] inline-block"></ion-icon></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
