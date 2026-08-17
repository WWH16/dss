@extends('layouts.dashboard')

@section('title', 'My Profile | DSS')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Main Profile Card (Classic Layout in Brand Theme) ────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Profile Header with Avatar & Action CTAs --}}
        <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 relative">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                {{-- Avatar Circle --}}
                <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full bg-brand-50 border-2 border-brand-200/80 text-brand-700 font-black text-3xl shadow-xs flex items-center justify-center ring-4 ring-white shrink-0">
                    {{ strtoupper(substr($profile->name ?? ($profile->student_number ?? 'S'), 0, 1)) }}
                </div>

                {{-- Name & Identity Info --}}
                <div class="pt-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight">
                        {{ $profile->name ?? 'Student' }}
                    </h1>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-brand-50 text-brand-800 border border-brand-200/70">
                            ID: {{ $profile->student_number ?? 'N/A' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                            <ion-icon name="school-outline" class="text-xs text-neutral-500"></ion-icon>
                            Student Account
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
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    Edit Profile
                </button>
                <button type="button" onclick="openChangePasswordModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-neutral-50 text-neutral-700 border border-neutral-200/90 text-xs font-bold rounded-lg transition-colors shadow-2xs">
                    <ion-icon name="lock-closed-outline" class="text-sm text-neutral-500"></ion-icon>
                    Password
                </button>
            </div>
        </div>

        {{-- Academic & Profile Details List --}}
        <div class="p-6 sm:p-8">
            <div class="flex items-center justify-between pb-3 mb-6 border-b border-neutral-100">
                <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                    <ion-icon name="school-outline" class="text-lg text-brand-700"></ion-icon>
                    Academic &amp; Student Information
                </h2>
                <span class="text-[11px] font-semibold text-neutral-400">ISU DSS Portal</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 sm:gap-y-8 gap-x-8">
                {{-- Student Number --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="id-card-outline" class="text-neutral-400"></ion-icon>
                        Student ID Number
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base tabular-nums font-mono">
                        {{ $profile->student_number ?? 'Not set' }}
                    </dd>
                </div>

                {{-- Email Address --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="mail-outline" class="text-neutral-400"></ion-icon>
                        University Email Address
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base break-all">
                        {{ $profile->email }}
                    </dd>
                </div>

                {{-- Course / Degree --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="book-outline" class="text-neutral-400"></ion-icon>
                        Course / Program
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ $profile->course ?: 'Not set' }}
                    </dd>
                </div>

                {{-- Year Level --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-1 flex items-center gap-1.5">
                        <ion-icon name="calendar-outline" class="text-neutral-400"></ion-icon>
                        Year Level
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ $profile->year_level ?: 'Not set' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Footer Activity Bar --}}
        <div class="px-6 py-4 sm:px-8 sm:py-4 bg-neutral-50/70 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-4 text-neutral-600 font-medium">
                <span class="flex items-center gap-1">
                    <strong class="text-neutral-900 font-bold tabular-nums">{{ $totalEvals ?? 0 }}</strong> Reviews Logged
                </span>
                <span class="text-neutral-300">•</span>
                <span class="flex items-center gap-1">
                    <strong class="text-neutral-900 font-bold tabular-nums">{{ $uniqueStalls ?? 0 }}</strong> Stalls Evaluated
                </span>
                @if(($totalEvals ?? 0) > 0)
                    <span class="text-neutral-300">•</span>
                    <span class="flex items-center gap-1">
                        <strong class="text-neutral-900 font-bold tabular-nums">{{ number_format($avgGiven, 1) }}★</strong> Avg Given
                    </span>
                @endif
            </div>

            <a href="{{ route('student.history') }}" class="text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 transition-colors">
                View Evaluation History &rarr;
            </a>
        </div>

    </div>

</div>

{{-- ── 1. Edit Academic Profile Modal ────────────────────────────────────── --}}
<dialog id="edit-profile-modal" class="confirm-modal modal-sharp max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="px-6 py-4 bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 leading-tight">Edit Academic Profile</h3>
                    <p class="text-[11px] text-neutral-500">Update your student name and academic details</p>
                </div>
            </div>
            <button type="button" class="js-close-profile-modal text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        {{-- Modal Form --}}
        <form action="{{ route('student.profile.update') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="modal-name" class="block text-xs font-bold text-neutral-700 mb-1.5">Full Name</label>
                <input type="text" id="modal-name" name="name" value="{{ old('name', $profile->name) }}" required
                    class="w-full px-3 py-2 bg-neutral-50 border @error('name') border-rose-500 bg-rose-50/20 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('name')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Course / Degree --}}
            <div>
                <label for="modal-course" class="block text-xs font-bold text-neutral-700 mb-1.5">Course / Degree Program</label>
                <input type="text" id="modal-course" name="course" value="{{ old('course', $profile->course) }}" placeholder="e.g. BS Computer Science, BSIT, BSHM"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('course') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('course')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Year Level --}}
            <div>
                <label for="modal-year-level" class="block text-xs font-bold text-neutral-700 mb-1.5">Year Level</label>
                <select id="modal-year-level" name="year_level"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('year_level') border-rose-500 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="">Select Year Level</option>
                    <option value="1st Year" {{ old('year_level', $profile->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd Year" {{ old('year_level', $profile->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd Year" {{ old('year_level', $profile->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th Year" {{ old('year_level', $profile->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
                    <option value="Graduate" {{ old('year_level', $profile->year_level) === 'Graduate' ? 'selected' : '' }}>Graduate</option>
                </select>
                @error('year_level')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Readonly Reference Chips --}}
            <div class="p-3 rounded-lg bg-neutral-50 border border-neutral-200/70 text-[11px] text-neutral-500 space-y-1">
                <p><strong>ID Number:</strong> <span class="font-mono">{{ $profile->student_number }}</span> (Locked)</p>
                <p><strong>Email:</strong> {{ $profile->email }}</p>
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
        <form action="{{ route('student.profile.password') }}" method="POST" class="p-6 space-y-4">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="modal-current-password" class="block text-xs font-bold text-neutral-700 mb-1.5">Current Password</label>
                <input type="password" id="modal-current-password" name="current_password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('current_password') border-rose-500 bg-rose-50/20 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('current_password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="modal-new-password" class="block text-xs font-bold text-neutral-700 mb-1.5">New Password</label>
                <input type="password" id="modal-new-password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 bg-neutral-50 border @error('password') border-rose-500 bg-rose-50/20 @else border-neutral-200 @enderror rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p class="text-[10px] text-neutral-400 mt-1">Minimum 8 characters with letters, numbers, and symbols.</p>
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
    @if($errors->has('name') || $errors->has('course') || $errors->has('year_level'))
        if (editProfileModal) editProfileModal.showModal();
    @endif

    @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        if (changePasswordModal) changePasswordModal.showModal();
    @endif
});
</script>
@endsection


