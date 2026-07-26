@extends('layouts.dashboard')

@section('title', 'My Profile | DSS')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-neutral-200/60 shadow-sm overflow-hidden">
        
        <!-- Profile Header -->
        <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/30 flex flex-col sm:flex-row items-center sm:items-start gap-6 relative">
            <div class="absolute top-0 left-0 w-full h-24 bg-brand-600/5"></div>
            
            <div class="relative w-24 h-24 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-3xl shadow-sm shrink-0 ring-4 ring-white">
                {{ substr($profile->name ?? ($profile->student_number ?? 'U'), 0, 1) }}
            </div>
            
            <div class="relative text-center sm:text-left pt-2 flex-1">
                <h1 class="text-2xl font-bold text-neutral-900 tracking-tight leading-none mb-3">
                    {{ $profile->name ?? 'Student' }}
                </h1>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm font-medium">
                    <span class="text-neutral-500 flex items-center justify-center sm:justify-start gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        Student Account
                    </span>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="p-6 sm:p-8">
            <h2 class="text-base font-bold text-neutral-900 tracking-tight mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-brand-600">person_book</span>
                Academic Information
            </h2>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-8">
                <div class="group">
                    <dt class="text-sm font-medium text-neutral-500 mb-1 flex items-center gap-2">
                        Student Number
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-base tabular-nums group-hover:text-brand-700 transition-colors">{{ $profile->student_number }}</dd>
                </div>
                <div class="group">
                    <dt class="text-sm font-medium text-neutral-500 mb-1 flex items-center gap-2">
                        Email Address
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-base break-all group-hover:text-brand-700 transition-colors">{{ $profile->email }}</dd>
                </div>
                <div class="group">
                    <dt class="text-sm font-medium text-neutral-500 mb-1 flex items-center gap-2">
                        Course
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-base group-hover:text-brand-700 transition-colors">{{ $profile->course ?: 'Not set' }}</dd>
                </div>
                <div class="group">
                    <dt class="text-sm font-medium text-neutral-500 mb-1 flex items-center gap-2">
                        Year Level
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-base group-hover:text-brand-700 transition-colors">{{ $profile->year_level ?: 'Not set' }}</dd>
                </div>
            </dl>
        </div>

    </div>
</div>
@endsection
