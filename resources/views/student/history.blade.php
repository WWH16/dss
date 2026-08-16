@extends('layouts.dashboard')

@section('title', 'My Evaluations | DSS')
@section('header_title', 'My Evaluations')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight mb-1" style="text-wrap: balance">Evaluation History</h1>
            <p class="text-sm font-medium text-neutral-500">A complete record of all the food stalls you have rated.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <span class="bg-brand-50 text-brand-700 text-sm font-bold px-3 py-1.5 rounded-full tabular-nums border border-brand-100">
                {{ $myStudentEvals->count() }} Total
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        @if($myStudentEvals->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-neutral-50 flex items-center justify-center mb-4 text-neutral-400">
                    <ion-icon name="time-outline" class="text-3xl text-neutral-400 opacity-60"></ion-icon>
                </div>
                <p class="text-lg font-bold text-neutral-900 mb-2">No evaluations yet</p>
                <p class="text-sm text-neutral-500 max-w-sm mb-6">You haven't submitted any stall evaluations. When you do, they will appear here.</p>
                <a href="{{ route('student.evaluation') }}" class="inline-flex items-center justify-center gap-1.5 w-auto px-6 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
                    <ion-icon name="add-outline" class="text-base"></ion-icon>
                    Evaluate a Stall
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] hidden md:table">
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
                                            <ion-icon name="storefront-outline" class="text-base text-orange-600"></ion-icon>
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
                                    <div class="inline-flex items-center gap-1.5 bg-neutral-50 px-2.5 py-1 rounded-md text-sm font-bold text-neutral-900 border border-neutral-200/60 whitespace-nowrap">
                                        {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($myStudentEvals as $eval)
                        @php
                            $avg = round(($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4, 1);
                        @endphp
                        <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                        <ion-icon name="storefront-outline" class="text-base text-orange-600"></ion-icon>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-900">{{ $eval->stall_name ?? 'Unknown Stall' }}</h3>
                                        <p class="text-[11px] font-medium text-neutral-500">{{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y • h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="shrink-0 inline-flex items-center gap-1 bg-neutral-50 px-2 py-1 rounded-md text-xs font-bold text-neutral-900 border border-neutral-200/60">
                                    {{ $avg }} <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                </div>
                            </div>
                            
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
