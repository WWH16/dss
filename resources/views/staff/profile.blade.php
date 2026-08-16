@extends('layouts.dashboard')
@section('title', 'My Profile | Staff — DSS')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Staff Account Profile</h1>
            <p class="text-neutral-500 text-sm mt-0.5">Manage your staff credentials and security settings.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-50 border border-brand-200/60 rounded text-xs font-bold text-brand-800 uppercase tracking-wider">
            Canteen Staff
        </span>
    </div>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200/70 text-emerald-800 rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <ion-icon name="checkmark-circle" class="text-lg leading-none text-emerald-600"></ion-icon>
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Details --}}
    <div class="bg-white rounded-lg border border-neutral-200/80 shadow-xs p-6">
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-neutral-100">
            <div class="w-14 h-14 rounded-md bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold text-xl shrink-0">
                {{ substr($profile->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-neutral-900 tracking-tight">{{ $profile->name }}</h2>
                <p class="text-xs font-mono text-neutral-500 mt-0.5">{{ $profile->email }}</p>
            </div>
        </div>

        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider mb-4">Update Profile Information</h3>
        <form action="{{ route('staff.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $profile->name) }}" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('name') border-red-500 @else border-neutral-200 @enderror rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                @error('name')<p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $profile->email) }}" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('email') border-red-500 @else border-neutral-200 @enderror rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                @error('email')<p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>
            <button class="btn btn-primary text-sm py-2 px-4 font-bold flex items-center gap-1.5">
                <ion-icon name="save-outline" class="text-sm leading-none"></ion-icon>
                Save Changes
            </button>
        </form>
    </div>

    {{-- Password Update --}}
    <div class="bg-white rounded-lg border border-neutral-200/80 shadow-xs p-6">
        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider mb-4 pb-2 border-b border-neutral-100">Change Password</h3>
        <form action="{{ route('staff.profile.password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('current_password') border-red-500 @else border-neutral-200 @enderror rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                @error('current_password')<p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">New Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('password') border-red-500 @else border-neutral-200 @enderror rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                <p class="text-[11px] text-neutral-400 mt-1">Minimum 8 characters with upper &amp; lowercase, a number, and a symbol.</p>
                @error('password')<p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
            </div>
            <button class="btn btn-primary text-sm py-2 px-4 font-bold flex items-center gap-1.5">
                <ion-icon name="lock-closed-outline" class="text-sm leading-none"></ion-icon>
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
