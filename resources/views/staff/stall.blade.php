@extends('layouts.dashboard')
@section('title', 'My Stall | Staff — DSS')
@section('header_title', 'My Stall')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Assigned Food Stall</h1>
            <p class="text-neutral-500 text-sm mt-0.5">Details and status of your affiliated canteen stall.</p>
        </div>
    </div>

    @if(!$stall)
        <div class="bg-white rounded-lg border border-neutral-200/80 p-12 text-center shadow-xs">
            <div class="w-14 h-14 rounded-md bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400 mx-auto">
                <span class="material-symbols-outlined text-2xl">storefront</span>
            </div>
            <p class="text-base font-bold text-neutral-900 mb-1">No stall assigned yet</p>
            <p class="text-xs text-neutral-500 max-w-sm mx-auto">An administrator needs to link your account to a canteen stall.</p>
        </div>
    @else
        <div class="bg-white rounded-lg border border-neutral-200/80 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-neutral-100 bg-neutral-50/40 flex items-start gap-5">
                <div class="w-14 h-14 rounded-md bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-700 shrink-0">
                    <span class="material-symbols-outlined text-2xl">storefront</span>
                </div>
                <div class="pt-0.5">
                    <h2 class="text-xl font-bold text-neutral-900 tracking-tight mb-1.5">{{ $stall->name }}</h2>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border {{ $stall->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-neutral-100 text-neutral-600 border-neutral-200' }}">
                        {{ $stall->is_active ? 'Active Stall' : 'Inactive Stall' }}
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-7">
                <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-brand-600">info</span>
                    Stall Information
                </h3>
                <dl class="space-y-4">
                    <div class="p-3.5 rounded-md bg-neutral-50 border border-neutral-200/80">
                        <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Description</dt>
                        <dd class="text-neutral-900 font-medium text-sm">{{ $stall->description ?: 'No description provided.' }}</dd>
                    </div>
                    <div class="p-3.5 rounded-md bg-neutral-50 border border-neutral-200/80">
                        <dt class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Registered Since</dt>
                        <dd class="text-neutral-900 font-mono font-bold text-sm tabular-nums">{{ $stall->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif
</div>
@endsection
