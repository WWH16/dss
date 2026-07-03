@extends('layouts.app')

@section('title', 'Admin Dashboard | ISU Canteen DSS')

@section('content')
<div class="py-10 bg-neutral-50/50 min-h-screen">
    <div class="container max-w-6xl">
        <!-- Dashboard Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-display font-bold tracking-tight text-neutral-900 leading-tight">Admin Dashboard</h1>
                <p class="text-neutral-500 mt-1 text-sm leading-relaxed">Welcome back, <span class="font-semibold text-brand-700">{{ Auth::user()->name }}</span></p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- SUMMARY METRICS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 flex items-center gap-4">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-3 rounded-xl text-2xl leading-none">group</span>
                <div>
                    <h5 class="text-[10px] text-neutral-400 uppercase tracking-widest font-bold">Total Students</h5>
                    <h2 class="text-3xl font-extrabold text-neutral-900 mt-0.5 tabular-nums leading-none">{{ $studentCount }}</h2>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 flex items-center gap-4">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-3 rounded-xl text-2xl leading-none">storefront</span>
                <div>
                    <h5 class="text-[10px] text-neutral-400 uppercase tracking-widest font-bold">Total Stalls</h5>
                    <h2 class="text-3xl font-extrabold text-neutral-900 mt-0.5 tabular-nums leading-none">{{ $stallCount }}</h2>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 flex items-center gap-4">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-3 rounded-xl text-2xl leading-none">rate_review</span>
                <div>
                    <h5 class="text-[10px] text-neutral-400 uppercase tracking-widest font-bold">Total Evaluations</h5>
                    <h2 class="text-3xl font-extrabold text-neutral-900 mt-0.5 tabular-nums leading-none">{{ $evaluationCount }}</h2>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- ADD STALL FORM & STALL LIST --}}
            <div class="lg:col-span-1 space-y-8">
                <!-- Add Food Stall -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-neutral-100">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">add_box</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Add Food Stall</h2>
                            <p class="text-xs text-neutral-400 font-medium">Insert new canteen vendor</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.stall.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <input
                                type="text"
                                name="name"
                                class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 font-medium text-neutral-800"
                                placeholder="Stall Name"
                                required>
                        </div>
                        <button class="btn btn-primary w-100 flex justify-center items-center gap-2 text-sm font-semibold tracking-wide">
                            <span class="material-symbols-outlined text-base leading-none">add</span>
                            Add Stall
                        </button>
                    </form>
                </div>

                <!-- Food Stalls List -->
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-neutral-100">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">list_alt</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Food Stalls</h2>
                            <p class="text-xs text-neutral-400 font-medium">Manage current stalls</p>
                        </div>
                    </div>

                    <div class="max-h-[280px] overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-neutral-100 text-[10px] text-neutral-400 font-bold uppercase tracking-widest">
                                    <th class="pb-2 font-bold">Name</th>
                                    <th class="pb-2 font-bold text-right" width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                @forelse($stalls as $stall)
                                    <tr class="text-sm hover:bg-neutral-50/50">
                                        <td class="py-2.5 font-semibold text-neutral-800 leading-snug">{{ $stall->name }}</td>
                                        <td class="py-2.5 text-right">
                                            <form action="{{ route('admin.stall.delete', $stall->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-500 hover:text-red-600 font-bold text-xs tracking-wider uppercase inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm leading-none">delete</span>
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-sm text-neutral-400 font-medium">
                                            No stalls available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- AVERAGE RESULT PER STALL --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6 h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">bar_chart</span>
                        <div>
                            <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Average Stall Evaluation</h2>
                            <p class="text-xs text-neutral-400 font-medium">Summary averages across all evaluations</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[500px]">
                            <thead>
                                <tr class="border-b border-neutral-100 text-[10px] text-neutral-400 font-bold uppercase tracking-widest">
                                    <th class="pb-3 font-bold">Food Stall</th>
                                    <th class="pb-3 font-bold text-center">Cleanliness</th>
                                    <th class="pb-3 font-bold text-center">Service</th>
                                    <th class="pb-3 font-bold text-center">Taste</th>
                                    <th class="pb-3 font-bold text-center">Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-50">
                                @forelse($results as $result)
                                    <tr class="text-sm text-neutral-800 hover:bg-neutral-50/50">
                                        <td class="py-3.5 font-bold text-neutral-950 leading-snug">{{ $result->name }}</td>
                                        <td class="py-3.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->cleanliness, 2) }}</td>
                                        <td class="py-3.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->service, 2) }}</td>
                                        <td class="py-3.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->taste, 2) }}</td>
                                        <td class="py-3.5 text-center font-bold text-neutral-800 tabular-nums">{{ number_format($result->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-sm text-neutral-400 font-medium">
                                            No evaluation results yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAILED STUDENT EVALUATIONS TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2.5 rounded-xl">database</span>
                <div>
                    <h2 class="font-bold text-neutral-900 tracking-tight leading-snug">Student Evaluation Ratings</h2>
                    <p class="text-xs text-neutral-400 font-medium">Full logs of all submissions</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="border-b border-neutral-100 text-[10px] text-neutral-400 font-bold uppercase tracking-widest">
                            <th class="pb-3 font-bold">Student</th>
                            <th class="pb-3 font-bold">Stall</th>
                            <th class="pb-3 font-bold text-center">Cleanliness</th>
                            <th class="pb-3 font-bold text-center">Service</th>
                            <th class="pb-3 font-bold text-center">Taste</th>
                            <th class="pb-3 font-bold text-center">Price</th>
                            <th class="pb-3 font-bold">Comment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @forelse($evaluations as $eval)
                            <tr class="text-sm text-neutral-800 hover:bg-neutral-50/30">
                                <td class="py-3.5 font-semibold text-neutral-900 leading-snug">{{ $eval->student_name }}</td>
                                <td class="py-3.5 text-neutral-500 font-semibold leading-snug">{{ $eval->stall_name }}</td>
                                <td class="py-3.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->cleanliness }}</td>
                                <td class="py-3.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->service }}</td>
                                <td class="py-3.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->taste }}</td>
                                <td class="py-3.5 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->price }}</td>
                                <td class="py-3.5 text-neutral-600 text-sm max-w-[240px] truncate leading-normal" title="{{ $eval->comment }}">{{ $eval->comment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-sm text-neutral-400 font-medium">
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