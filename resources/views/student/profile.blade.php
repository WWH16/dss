@extends('layouts.dashboard')

@section('title', 'My Profile | DSS')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-neutral-200/60 shadow-sm overflow-hidden">
        
        <!-- Profile Header -->
        <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="w-24 h-24 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-3xl border-4 border-white shadow-sm shrink-0">
                {{ substr($profile->name ?? ($profile->student_number ?? 'U'), 0, 1) }}
            </div>
            <div class="text-center sm:text-left pt-2">
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight leading-none mb-2">
                    {{ $profile->name ?? 'Student' }}
                </h1>
                <p class="text-neutral-500 text-sm font-medium">
                    Student Account
                </p>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="p-6 sm:p-8">
            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider mb-6 pb-2 border-b border-neutral-100">Personal Information</h2>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                <div>
                    <dt class="text-[11px] text-neutral-500 uppercase tracking-wider font-bold mb-1">Student Number</dt>
                    <dd class="text-neutral-900 font-semibold text-base tabular-nums">{{ $profile->student_number }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] text-neutral-500 uppercase tracking-wider font-bold mb-1">Email Address</dt>
                    <dd class="text-neutral-900 font-semibold text-base break-all">{{ $profile->email }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] text-neutral-500 uppercase tracking-wider font-bold mb-1">Course</dt>
                    <dd class="text-neutral-900 font-semibold text-base">{{ $profile->course }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] text-neutral-500 uppercase tracking-wider font-bold mb-1">Year Level</dt>
                    <dd class="text-neutral-900 font-semibold text-base">{{ $profile->year_level }}</dd>
                </div>
            </dl>
        </div>

    </div>
</div>
@endsection
