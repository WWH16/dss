@extends('layouts.dashboard')

@section('title', 'My Profile | Staff — DSS')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Main Profile Card (Matching Student Profile Theme) ────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Profile Header with Avatar & Action CTAs --}}
        <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 relative">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                {{-- Avatar Circle --}}
                <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full bg-brand-50 border-2 border-brand-200/80 text-brand-700 font-black text-3xl shadow-xs flex items-center justify-center ring-4 ring-white shrink-0">
                    {{ strtoupper(substr($profile->name ?? ($profile->email ?? 'S'), 0, 1)) }}
                </div>

                {{-- Name & Identity Info --}}
                <div class="pt-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight">
                        {{ $profile->name ?? 'Staff Member' }}
                    </h1>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                        @if(isset($stall) && $stall)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/70">
                                <ion-icon name="storefront-outline" class="text-xs text-brand-700"></ion-icon>
                                {{ $stall->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                                <ion-icon name="alert-circle-outline" class="text-xs text-neutral-500"></ion-icon>
                                No Stall Assigned
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                            <ion-icon name="shield-checkmark-outline" class="text-xs text-neutral-500"></ion-icon>
                            Canteen Staff
                        </span>
                    </div>

                    <p class="text-xs text-neutral-500 mt-2 flex items-center justify-center sm:justify-start gap-1.5">
                        <ion-icon name="mail-outline" class="text-neutral-400 text-sm"></ion-icon>
                        {{ $profile->email }}
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-row sm:flex-col gap-2 shrink-0 w-full sm:w-auto">
                <button type="button" onclick="openEditProfileModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    Edit Profile
                </button>
                <button type="button" onclick="openChangePasswordModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-neutral-50 text-neutral-700 border border-neutral-200/90 text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="lock-closed-outline" class="text-sm text-neutral-500"></ion-icon>
                    Password
                </button>
            </div>
        </div>

        {{-- Staff & Account Details List --}}
        <div class="p-6 sm:p-8">
            <div class="flex items-center justify-between pb-3 mb-6 border-b border-neutral-100">
                <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                    <ion-icon name="briefcase-outline" class="text-lg text-brand-700"></ion-icon>
                    Staff &amp; Stall Information
                </h2>
                <span class="text-[11px] font-semibold text-neutral-400">ISU DSS Portal</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 sm:gap-y-8 gap-x-8">
                {{-- Staff Name --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="person-outline" class="text-neutral-400"></ion-icon>
                        Full Name
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ $profile->name ?? 'Not set' }}
                    </dd>
                </div>

                {{-- Email --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="mail-outline" class="text-neutral-400"></ion-icon>
                        Email
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base break-all">
                        {{ $profile->email }}
                    </dd>
                </div>

                {{-- Assigned Stall --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="storefront-outline" class="text-neutral-400"></ion-icon>
                        Assigned Canteen Stall
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        @if(isset($stall) && $stall)
                            {{ $stall->name }}
                        @else
                            <span class="text-neutral-400 font-normal italic">No stall assigned</span>
                        @endif
                    </dd>
                </div>

                {{-- Role / Account Type --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="shield-checkmark-outline" class="text-neutral-400"></ion-icon>
                        Account Role
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        Canteen Staff
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Footer Activity Bar --}}
        <div class="px-6 py-4 sm:px-8 sm:py-4 bg-neutral-50/70 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-4 text-neutral-600 font-medium">
                <span class="flex items-center gap-1">
                    <strong class="text-neutral-900 font-bold">Status:</strong>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active
                    </span>
                </span>
                <span class="text-neutral-300">•</span>
                <span class="flex items-center gap-1">
                    <strong class="text-neutral-900 font-bold">Account:</strong> Staff Portal
                </span>
            </div>

            @if(isset($stall) && $stall)
                <a href="{{ route('staff.standings') }}" class="text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors">
                    View Stall Standings &rarr;
                </a>
            @else
                <a href="{{ route('staff.dashboard') }}" class="text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors">
                    Go to Dashboard &rarr;
                </a>
            @endif
        </div>

    </div>

</div>

{{-- ── 1. Edit Staff Profile Modal ────────────────────────────────────── --}}
<dialog id="edit-profile-modal" class="confirm-modal modal-sharp max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="px-6 py-4 bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 leading-tight">Edit Staff Profile</h3>
                    <p class="text-[11px] text-neutral-500">Update your staff account information</p>
                </div>
            </div>
            <button type="button" class="js-close-profile-modal text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        {{-- Modal Form --}}
        <form action="{{ route('staff.profile.update') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="modal-name" class="block text-xs font-bold text-neutral-700 mb-1.5">Full Name</label>
                <input type="text" id="modal-name" name="name" value="{{ old('name', $profile->name) }}" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('name') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('name')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="modal-email" class="block text-xs font-bold text-neutral-700 mb-1.5">Email Address</label>
                <input type="email" id="modal-email" name="email" value="{{ old('email', $profile->email) }}" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('email') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('email')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Readonly Reference Chips --}}
            <div class="p-3 rounded-lg bg-neutral-50 border border-neutral-200/70 text-[11px] text-neutral-500 space-y-1">
                <p><strong>Assigned Stall:</strong> {{ $stall->name ?? 'No stall assigned' }} (Managed by Admin)</p>
                <p><strong>Role:</strong> Canteen Staff</p>
            </div>

            {{-- Footer Buttons --}}
            <div class="pt-3 border-t border-neutral-100 flex items-center justify-end gap-2">
                <button type="button" class="js-close-profile-modal px-3.5 py-2 text-xs font-bold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="save-outline" class="text-sm"></ion-icon>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 2. Change Password Modal ────────────────────────────────────────── --}}
<dialog id="change-password-modal" class="confirm-modal modal-sharp max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="px-6 py-4 bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-neutral-100 border border-neutral-200 text-neutral-700 flex items-center justify-center shrink-0">
                    <ion-icon name="lock-closed-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 leading-tight">Change Password</h3>
                    <p class="text-[11px] text-neutral-500">Update your security credentials</p>
                </div>
            </div>
            <button type="button" class="js-close-password-modal text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        {{-- Modal Form --}}
        <form action="{{ route('staff.profile.password') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="modal-current-password" class="block text-xs font-bold text-neutral-700 mb-1.5">Current Password</label>
                <input type="password" id="modal-current-password" name="current_password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('current_password') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('current_password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="modal-new-password" class="block text-xs font-bold text-neutral-700 mb-1.5">New Password</label>
                <input type="password" id="modal-new-password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('password') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p class="text-[10px] text-neutral-400 mt-1">Minimum 8 characters with upper &amp; lowercase, a number, and a symbol.</p>
                @error('password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="modal-password-confirmation" class="block text-xs font-bold text-neutral-700 mb-1.5">Confirm New Password</label>
                <input type="password" id="modal-password-confirmation" name="password_confirmation" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
            </div>

            {{-- Footer Buttons --}}
            <div class="pt-3 border-t border-neutral-100 flex items-center justify-end gap-2">
                <button type="button" class="js-close-password-modal px-3.5 py-2 text-xs font-bold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="shield-checkmark-outline" class="text-sm"></ion-icon>
                    Update Password
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── Modal Controller Script ────────────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editProfileModal = document.getElementById('edit-profile-modal');
    const changePasswordModal = document.getElementById('change-password-modal');

    window.openEditProfileModal = function() {
        if (editProfileModal) editProfileModal.showModal();
    };

    window.openChangePasswordModal = function() {
        if (changePasswordModal) changePasswordModal.showModal();
    };

    // Close buttons
    document.querySelectorAll('.js-close-profile-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (editProfileModal) editProfileModal.close();
        });
    });

    document.querySelectorAll('.js-close-password-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (changePasswordModal) changePasswordModal.close();
        });
    });

    // Backdrop clicks
    if (editProfileModal) {
        editProfileModal.addEventListener('click', (e) => {
            const rect = editProfileModal.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
                editProfileModal.close();
            }
        });
    }

    if (changePasswordModal) {
        changePasswordModal.addEventListener('click', (e) => {
            const rect = changePasswordModal.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
                changePasswordModal.close();
            }
        });
    }

    // Auto-reopen on validation errors
    @if($errors->has('name') || $errors->has('email'))
        if (editProfileModal) editProfileModal.showModal();
    @endif

    @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        if (changePasswordModal) changePasswordModal.showModal();
    @endif
});
</script>
@endsection
