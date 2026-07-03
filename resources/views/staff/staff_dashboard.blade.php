@extends('layouts.app')

@section('title', 'Staff Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-6xl">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-display font-bold tracking-tight text-neutral-900 leading-tight">Staff Dashboard</h1>
                <p class="text-neutral-500 mt-1 text-sm leading-relaxed">Welcome back, <span class="font-semibold text-brand-700">{{ $profile->name }}</span></p>
            </div>
            <div class="bg-brand-50 text-brand-800 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 border border-brand-100 self-start md:self-auto">
                <span class="material-symbols-outlined text-lg">storefront</span>
                Assigned Stall: <span class="underline font-extrabold">{{ $profile->stall_name ?? 'Not Assigned' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Profile & Evaluation Summary -->
            <div class="space-y-8">
                <!-- Profile details -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-neutral-100">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">account_circle</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Staff Profile</h2>
                            <p class="text-xs text-neutral-400 font-medium">Canteen Staff Account</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Name</span>
                            <span class="text-sm text-neutral-800 font-semibold leading-normal">{{ $profile->name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-neutral-400 block uppercase tracking-widest font-bold mb-1">Email Address</span>
                            <span class="text-sm text-neutral-800 font-semibold break-all leading-normal">{{ $profile->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Evaluation Summary (Progress Bars/Grid) -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-neutral-100">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">analytics</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Evaluation Summary</h2>
                            <p class="text-xs text-neutral-400 font-medium">Average score out of 5.0</p>
                        </div>
                    </div>

                    @if($ratings && $ratings->isNotEmpty())
                        @php
                            $firstRating = $ratings->first();
                        @endphp
                        <div class="space-y-5">
                            @foreach([
                                ['label' => 'Cleanliness', 'value' => $firstRating->cleanliness ?? 0, 'icon' => 'cleaning_services', 'color' => 'bg-emerald-500'],
                                ['label' => 'Service Quality', 'value' => $firstRating->service ?? 0, 'icon' => 'support_agent', 'color' => 'bg-blue-500'],
                                ['label' => 'Taste', 'value' => $firstRating->taste ?? 0, 'icon' => 'restaurant', 'color' => 'bg-amber-500'],
                                ['label' => 'Price Fairness', 'value' => $firstRating->price ?? 0, 'icon' => 'payments', 'color' => 'bg-purple-500']
                            ] as $metric)
                                <div>
                                    <div class="flex justify-between items-center text-sm mb-1.5">
                                        <span class="text-neutral-700 font-medium inline-flex items-center gap-2">
                                            <span class="material-symbols-outlined text-neutral-400 text-base leading-none">{{ $metric['icon'] }}</span>
                                            {{ $metric['label'] }}
                                        </span>
                                        <span class="font-bold text-neutral-900 tabular-nums">{{ number_format($metric['value'], 2) }}</span>
                                    </div>
                                    <div class="w-full bg-neutral-100 rounded-full h-2">
                                        <div class="{{ $metric['color'] }} h-2 rounded-full" style="width: {{ min(100, ($metric['value'] / 5) * 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-neutral-400">
                            <span class="material-symbols-outlined text-3xl mb-1">info</span>
                            <p class="text-sm font-medium">No evaluations yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Things to Improve -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-neutral-100">
                        <span class="material-symbols-outlined text-amber-600 bg-amber-50 p-2.5 rounded-xl">campaign</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Things to Improve</h2>
                            <p class="text-xs text-neutral-400 font-medium">Action items based on scores</p>
                        </div>
                    </div>

                    @if(count($improvements))
                        <ul class="space-y-3">
                            @foreach($improvements as $item)
                                <li class="text-sm text-neutral-700 flex items-start gap-2.5 leading-relaxed">
                                    <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 leading-none">warning</span>
                                    <span>Score registered: <strong class="font-bold tabular-nums">{{ number_format($item['score'], 2) }}</strong></span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex items-center gap-3 text-emerald-700 bg-emerald-50/50 border border-emerald-100 p-4 rounded-xl">
                            <span class="material-symbols-outlined text-xl leading-none">check_circle</span>
                            <span class="text-xs font-bold uppercase tracking-wider">Great! No major improvements needed.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Recent Evaluations (Table) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">history</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Recent Evaluations</h2>
                            <p class="text-xs text-neutral-400 font-medium">Student feedback records</p>
                        </div>
                    </div>

                    @if($evaluations->isNotEmpty())
                        <div class="overflow-x-auto -mx-6 px-6">
                            <table class="w-full text-left border-collapse min-w-[600px]">
                                <thead>
                                    <tr class="border-b border-neutral-100 text-[10px] text-neutral-400 font-bold uppercase tracking-widest">
                                        <th class="pb-3 font-bold">Date</th>
                                        <th class="pb-3 font-bold text-center">Cleanliness</th>
                                        <th class="pb-3 font-bold text-center">Service</th>
                                        <th class="pb-3 font-bold text-center">Taste</th>
                                        <th class="pb-3 font-bold text-center">Price</th>
                                        <th class="pb-3 font-bold">Comment</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-50">
                                    @foreach($evaluations as $eval)
                                        <tr class="text-sm text-neutral-800 hover:bg-neutral-50/30">
                                            <td class="py-3.5 whitespace-nowrap text-xs text-neutral-500 font-semibold tabular-nums">
                                                {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                            </td>
                                            <td class="py-3.5 text-center font-bold text-neutral-900 tabular-nums">
                                                {{ $eval->cleanliness }}
                                            </td>
                                            <td class="py-3.5 text-center font-bold text-neutral-900 tabular-nums">
                                                {{ $eval->service }}
                                            </td>
                                            <td class="py-3.5 text-center font-bold text-neutral-900 tabular-nums">
                                                {{ $eval->taste }}
                                            </td>
                                            <td class="py-3.5 text-center font-bold text-neutral-900 tabular-nums">
                                                {{ $eval->price }}
                                            </td>
                                            <td class="py-3.5 text-neutral-600 text-sm max-w-[200px] truncate leading-normal" title="{{ $eval->comment }}">
                                                {{ $eval->comment ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 text-neutral-400">
                            <span class="material-symbols-outlined text-4xl mb-2">rate_review</span>
                            <p class="font-medium text-sm leading-relaxed">No evaluations found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection