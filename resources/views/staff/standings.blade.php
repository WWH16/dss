@extends('layouts.dashboard')

@section('title', 'Campus Standings | Staff — DSS')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                <span>Campus Stall Standings</span>
                <span class="text-xs font-semibold text-brand-700 bg-brand-50 border border-brand-200 px-2 py-0.5 rounded-md">DSS Rankings</span>
            </h1>
            <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">
                Official composite performance rankings of all canteen food stalls across campus.
            </p>
        </div>

        @if($myStall)
            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                <a href="{{ route('staff.dashboard') }}" class="btn btn-primary text-xs px-3.5 py-2 rounded-lg font-bold inline-flex items-center gap-1.5 shadow-2xs">
                    <ion-icon name="storefront-outline" class="text-sm"></ion-icon>
                    Back to My Stall
                </a>
            </div>
        @endif
    </div>

    {{-- ── 2. Staff Standing Notice ────────────────────────────────────────── --}}
    @if($myStall)
        @php
            $myRank = null;
            foreach ($standings as $idx => $s) {
                if ($s->id == $myStall->id) {
                    $myRank = $idx + 1;
                    break;
                }
            }
        @endphp
        <div class="p-4 bg-brand-50/70 border border-brand-200/80 text-brand-950 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-brand-600 text-white flex items-center justify-center font-black text-sm shrink-0">
                    #{{ $myRank ?? '-' }}
                </div>
                <div>
                    <h4 class="text-xs font-bold text-brand-950">Your Stall: <strong>{{ $myStall->name }}</strong></h4>
                    <p class="text-[11px] text-brand-800 mt-0.5">
                        Your stall is currently ranked <strong>#{{ $myRank ?? '-' }}</strong> out of {{ $standings->count() }} canteen vendors.
                    </p>
                </div>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-primary text-[11px] font-bold px-3 py-1.5 rounded-md self-start sm:self-auto shrink-0 shadow-2xs">
                Manage My Stall
            </a>
        </div>
    @endif

    {{-- ── 3. Standings Leaderboard Table ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 pb-4 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-neutral-900 tracking-tight">Canteen Leaderboard</h2>
                <p class="text-xs text-neutral-500 mt-0.5">Stalls sorted by overall composite score across all criteria</p>
            </div>
            <span class="text-xs text-neutral-400 font-semibold tabular-nums">
                {{ $standings->count() }} {{ Str::plural('Vendor', $standings->count()) }} Total
            </span>
        </div>

        @if($standings->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm font-bold text-neutral-800 mb-1">No stalls registered yet</p>
                <p class="text-xs text-neutral-500">Standings will appear here once food stalls are added to the system.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold text-center w-20">Rank</th>
                            <th class="py-3.5 px-5 font-semibold">Food Stall</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-5 text-center font-semibold">Overall Composite</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($standings as $index => $stall)
                            @php
                                $rank = $index + 1;
                                $composite = (float)$stall->overall_score;
                                $isMyStall = $myStall && $myStall->id == $stall->id;
                            @endphp
                            <tr class="hover:bg-neutral-50/60 transition-colors {{ $isMyStall ? 'bg-brand-50/30 font-medium' : '' }}">
                                {{-- Rank Badge --}}
                                <td class="py-3.5 px-5 text-center">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-100 to-amber-50 text-amber-950 border border-amber-300 font-black text-sm shadow-xs">
                                            <ion-icon name="trophy" class="text-amber-600 text-base"></ion-icon>
                                            <span>#1</span>
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon>
                                            <span>#2</span>
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-md bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-xs shadow-2xs">
                                            <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon>
                                            <span>#3</span>
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-semibold text-neutral-400 tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Stall Name & My Stall Tag --}}
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $rank === 1 ? 'font-black text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $stall->name }}</span>
                                        @if($isMyStall)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-100 text-brand-900 text-[10px] font-extrabold border border-brand-300">
                                                Your Stall
                                            </span>
                                        @endif
                                        <span class="text-[10px] font-semibold text-neutral-400 font-mono">({{ $stall->eval_count }} {{ Str::plural('eval', $stall->eval_count) }})</span>
                                    </div>
                                </td>

                                {{-- Criteria Scores --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $stall->cleanliness >= 4 ? 'text-emerald-700 bg-emerald-50' : ($stall->cleanliness && $stall->cleanliness <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->cleanliness ? number_format($stall->cleanliness, 2) . '★' : '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $stall->service >= 4 ? 'text-emerald-700 bg-emerald-50' : ($stall->service && $stall->service <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->service ? number_format($stall->service, 2) . '★' : '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $stall->taste >= 4 ? 'text-emerald-700 bg-emerald-50' : ($stall->taste && $stall->taste <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->taste ? number_format($stall->taste, 2) . '★' : '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-xs font-semibold {{ $stall->price >= 4 ? 'text-emerald-700 bg-emerald-50' : ($stall->price && $stall->price <= 2.9 ? 'text-rose-600 bg-rose-50' : 'text-neutral-700') }}">
                                        {{ $stall->price ? number_format($stall->price, 2) . '★' : '-' }}
                                    </span>
                                </td>

                                {{-- Overall Composite Score --}}
                                <td class="py-3.5 px-5 text-center">
                                    @if($composite > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-black {{ $composite >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200/90' : ($composite >= 3 ? 'bg-neutral-100 text-neutral-800' : 'bg-rose-50 text-rose-700 border border-rose-200') }} tabular-nums">
                                            {{ number_format($composite, 2) }} <ion-icon name="star" class="text-amber-500 text-xs"></ion-icon>
                                        </span>
                                    @else
                                        <span class="text-xs text-neutral-400 font-medium italic">No evaluations</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($standings as $index => $stall)
                        @php
                            $rank = $index + 1;
                            $composite = (float)$stall->overall_score;
                            $isMyStall = $myStall && $myStall->id == $stall->id;
                        @endphp
                        <div class="p-4 flex flex-col gap-2.5 {{ $isMyStall ? 'bg-brand-50/20' : '' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gradient-to-r from-amber-100 to-amber-50 text-amber-950 border border-amber-300 font-black text-xs shadow-2xs">
                                            <ion-icon name="trophy" class="text-amber-600 text-sm"></ion-icon> #1
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-900 border border-slate-300 font-extrabold text-[11px]">
                                            <ion-icon name="medal" class="text-slate-500 text-xs"></ion-icon> #2
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-50/90 text-amber-900 border border-amber-600/30 font-bold text-[11px]">
                                            <ion-icon name="medal" class="text-amber-700 text-xs"></ion-icon> #3
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-neutral-100 text-neutral-500 font-semibold text-[10px] tabular-nums">
                                            #{{ $rank }}
                                        </span>
                                    @endif
                                    <h3 class="{{ $rank === 1 ? 'font-black text-neutral-900 text-base' : 'font-bold text-neutral-900 text-sm' }}">{{ $stall->name }}</h3>
                                    @if($isMyStall)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-brand-100 text-brand-900 text-[10px] font-bold border border-brand-200">
                                            You
                                        </span>
                                    @endif
                                </div>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-black text-brand-900 border border-brand-200">
                                    {{ $composite > 0 ? number_format($composite, 2) . '★' : '-' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900">{{ $stall->cleanliness ? number_format($stall->cleanliness, 1) . '★' : '-' }}</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Serv</span>
                                    <span class="font-bold text-neutral-900">{{ $stall->service ? number_format($stall->service, 1) . '★' : '-' }}</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900">{{ $stall->taste ? number_format($stall->taste, 1) . '★' : '-' }}</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5 border border-neutral-100">
                                    <span class="block text-[9px] font-bold text-neutral-400 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900">{{ $stall->price ? number_format($stall->price, 1) . '★' : '-' }}</span>
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
