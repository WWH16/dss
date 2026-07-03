@extends('layouts.app')

@section('title', 'Staff Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-5xl">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Staff Dashboard</h1>
                <p class="text-neutral-500 text-sm">Welcome back, <span class="font-semibold text-brand-700">{{ $profile->name }}</span></p>
            </div>
            <div class="bg-brand-50 text-brand-800 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border border-brand-100/50 self-start sm:self-auto">
                Assigned Stall: {{ $profile->stall_name ?? 'Not Assigned' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sidebar Panel: Info & Warnings (Flat layout) -->
            <div class="md:col-span-1 space-y-6 text-sm">
                <!-- Profile -->
                <div class="space-y-4">
                    <div class="pb-2 border-b border-neutral-200">
                        <h2 class="font-bold text-neutral-800 uppercase tracking-wider text-[10px]">Staff Info</h2>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-0.5">Email</span>
                            <span class="text-neutral-800 font-semibold break-all leading-tight">{{ $profile->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Warnings / Improvements -->
                @if(count($improvements))
                    <div class="space-y-3">
                        <div class="pb-2 border-b border-neutral-200">
                            <h2 class="font-bold text-amber-800 uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm leading-none">warning</span>
                                Needs Attention
                            </h2>
                        </div>
                        <ul class="space-y-2 text-xs">
                            @foreach($improvements as $item)
                                <li class="text-neutral-600 font-medium">
                                    Score registered: <strong class="text-amber-700 font-bold tabular-nums">{{ number_format($item['score'], 2) }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Main Content Area -->
            <div class="md:col-span-3 space-y-8">
                <!-- Evaluation Summary Card -->
                <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b border-neutral-100">
                        <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation Scores</h2>
                        <span class="text-xs text-neutral-400 font-medium">Out of 5.0 max</span>
                    </div>

                    @if($ratings && $ratings->isNotEmpty())
                        @php
                            $firstRating = $ratings->first();
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                            @foreach([
                                ['label' => 'Cleanliness', 'value' => $firstRating->cleanliness ?? 0, 'color' => 'text-emerald-600'],
                                ['label' => 'Service', 'value' => $firstRating->service ?? 0, 'color' => 'text-blue-600'],
                                ['label' => 'Taste', 'value' => $firstRating->taste ?? 0, 'color' => 'text-amber-600'],
                                ['label' => 'Price', 'value' => $firstRating->price ?? 0, 'color' => 'text-purple-600']
                            ] as $metric)
                                <div class="bg-neutral-50/50 p-4 rounded-xl border border-neutral-100/50 text-center">
                                    <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold mb-1">{{ $metric['label'] }}</span>
                                    <span class="text-xl font-extrabold tabular-nums {{ $metric['color'] }}">{{ number_format($metric['value'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-neutral-400 text-sm">
                            No evaluations recorded yet.
                        </div>
                    @endif
                </div>

                <!-- Recent Evaluations Table -->
                <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-neutral-100">
                        <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Recent Evaluations</h2>
                        <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                            {{ $evaluations->count() }} total
                        </span>
                    </div>

                    @if($evaluations->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[500px]">
                                <thead>
                                    <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                                        <th class="pb-2">Date</th>
                                        <th class="pb-2 text-center">Cleanliness</th>
                                        <th class="pb-2 text-center">Service</th>
                                        <th class="pb-2 text-center">Taste</th>
                                        <th class="pb-2 text-center">Price</th>
                                        <th class="pb-2">Comment</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-50">
                                    @foreach($evaluations as $eval)
                                        <tr class="text-sm hover:bg-neutral-50/30">
                                            <td class="py-2.5 whitespace-nowrap text-xs text-neutral-500 font-semibold tabular-nums">
                                                {{ \Carbon\Carbon::parse($eval->created_at)->format('M d, Y') }}
                                            </td>
                                            <td class="py-2.5 text-center font-bold text-neutral-900 tabular-nums">{{ $eval->cleanliness }}</td>
                                            <td class="py-2.5 text-center font-bold text-neutral-900 tabular-nums">{{ $eval->service }}</td>
                                            <td class="py-2.5 text-center font-bold text-neutral-900 tabular-nums">{{ $eval->taste }}</td>
                                            <td class="py-2.5 text-center font-bold text-neutral-900 tabular-nums">{{ $eval->price }}</td>
                                            <td class="py-2.5 text-neutral-600 text-xs max-w-[180px] truncate" title="{{ $eval->comment }}">
                                                {{ $eval->comment ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-neutral-400 text-sm">
                            No evaluations found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection