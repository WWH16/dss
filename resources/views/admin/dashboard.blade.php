@extends('layouts.app')

@section('title', 'Admin Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-6xl">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Admin Dashboard</h1>
                <p class="text-neutral-500 text-sm">Welcome back, <span class="font-semibold text-brand-700">{{ Auth::user()->name }}</span></p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- SUMMARY METRICS --}}
        <div class="grid grid-cols-3 gap-6 mb-8">
            @foreach([
                ['label' => 'Total Students', 'value' => $studentCount, 'icon' => 'group'],
                ['label' => 'Total Stalls', 'value' => $stallCount, 'icon' => 'storefront'],
                ['label' => 'Total Evaluations', 'value' => $evaluationCount, 'icon' => 'rate_review']
            ] as $stat)
                <div class="bg-white rounded-xl border border-neutral-200/60 p-5 flex items-center gap-4">
                    <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-lg text-xl leading-none">{{ $stat['icon'] }}</span>
                    <div>
                        <span class="text-[10px] text-neutral-400 block uppercase tracking-wider font-bold">{{ $stat['label'] }}</span>
                        <span class="text-2xl font-extrabold text-neutral-900 tabular-nums leading-none mt-1 block">{{ $stat['value'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- COMBINED MANAGE STALLS SECTION --}}
            <div class="lg:col-span-1 bg-white rounded-xl border border-neutral-200/60 p-6">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider mb-4 pb-2 border-b border-neutral-100">Manage Stalls</h2>

                <!-- Inline Add Form -->
                <form action="{{ route('admin.stall.add') }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input
                        type="text"
                        name="name"
                        class="flex-1 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-semibold focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/15"
                        placeholder="New Stall Name"
                        required>
                    <button class="btn btn-primary text-xs py-2 px-3 font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm leading-none">add</span>
                        Add
                    </button>
                </form>

                <!-- Scrollable list -->
                <div class="max-h-[240px] overflow-y-auto pr-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[9px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-1">
                                <th class="pb-1">Name</th>
                                <th class="pb-1 text-right" width="60">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @forelse($stalls as $stall)
                                <tr class="text-xs hover:bg-neutral-50/50">
                                    <td class="py-2 font-semibold text-neutral-800">{{ $stall->name }}</td>
                                    <td class="py-2 text-right">
                                        <form action="{{ route('admin.stall.delete', $stall->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-600 font-bold text-[10px] tracking-wider uppercase inline-flex items-center">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-xs text-neutral-400 font-medium">
                                        No stalls available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- AVERAGE RESULT PER STALL --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-neutral-200/60 p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-neutral-100">
                    <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Average Stall Scores</h2>
                    <span class="text-[10px] text-neutral-400 font-bold uppercase">Out of 5.0 max</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[450px]">
                        <thead>
                            <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                                <th class="pb-2">Food Stall</th>
                                <th class="pb-2 text-center">Cleanliness</th>
                                <th class="pb-2 text-center">Service</th>
                                <th class="pb-2 text-center">Taste</th>
                                <th class="pb-2 text-center">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-50">
                            @forelse($results as $result)
                                <tr class="text-xs text-neutral-800 hover:bg-neutral-50/50">
                                    <td class="py-2.5 font-bold text-neutral-950">{{ $result->name }}</td>
                                    <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->cleanliness, 2) }}</td>
                                    <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->service, 2) }}</td>
                                    <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->taste, 2) }}</td>
                                    <td class="py-2.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-xs text-neutral-400 font-medium">
                                        No evaluation results yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DETAILED STUDENT EVALUATIONS TABLE --}}
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-neutral-100">
                <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Detailed Evaluations</h2>
                <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                    {{ $evaluations->count() }} total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px]">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 pb-2">
                            <th class="pb-2">Student</th>
                            <th class="pb-2">Stall</th>
                            <th class="pb-2 text-center">Clean</th>
                            <th class="pb-2 text-center">Serv</th>
                            <th class="pb-2 text-center">Taste</th>
                            <th class="pb-2 text-center">Price</th>
                            <th class="pb-2">Comment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse($evaluations as $eval)
                            <tr class="text-xs text-neutral-800 hover:bg-neutral-50/30">
                                <td class="py-2.5 font-semibold text-neutral-900 leading-none">{{ $eval->student_name }}</td>
                                <td class="py-2.5 text-neutral-500 font-medium leading-none">{{ $eval->stall_name }}</td>
                                <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->cleanliness }}</td>
                                <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->service }}</td>
                                <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->taste }}</td>
                                <td class="py-2.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->price }}</td>
                                <td class="py-2.5 text-neutral-600 text-xs max-w-[200px] truncate" title="{{ $eval->comment }}">{{ $eval->comment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-xs text-neutral-400 font-medium">
                                    No evaluations yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection