@extends('layouts.dashboard')
@section('title', 'Evaluations | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">All Evaluations</h1>
        <p class="text-neutral-500 text-sm">Review detailed feedback submitted by students.</p>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider">Evaluation Data</h2>
            <span class="bg-brand-50 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full tabular-nums">
                {{ $evaluations->count() }} total
            </span>
        </div>

        @if($evaluations->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center px-6">
                <div class="w-14 h-14 rounded-full bg-brand-50 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-2xl text-brand-500">rate_review</span>
                </div>
                <p class="font-semibold text-neutral-700 mb-1">No evaluations yet</p>
                <p class="text-sm text-neutral-400">Evaluations submitted by students will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[680px]">
                    <thead>
                        <tr class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider border-b border-neutral-100 bg-neutral-50/50">
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Stall</th>
                            <th class="px-6 py-3 text-center">Clean</th>
                            <th class="px-6 py-3 text-center">Serv</th>
                            <th class="px-6 py-3 text-center">Taste</th>
                            <th class="px-6 py-3 text-center">Price</th>
                            <th class="px-6 py-3">Comment</th>
                            <th class="px-6 py-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-50">
                        @foreach($evaluations as $eval)
                            <tr class="text-xs text-neutral-800 hover:bg-neutral-50/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-neutral-900">{{ $eval->student_name }}</td>
                                <td class="px-6 py-4 text-neutral-500 font-medium">{{ $eval->stall_name }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->cleanliness }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->service }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->taste }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-brand-700 tabular-nums">{{ $eval->price }}</td>
                                <td class="px-6 py-4 text-neutral-500 max-w-[220px] truncate" title="{{ $eval->comment }}">
                                    {{ $eval->comment ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-neutral-400 tabular-nums whitespace-nowrap">
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
@endsection
