@extends('layouts.dashboard')

{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'My Profile | Staff - DSS')
{{-- CHANGED (whole file): every rounded-lg became rounded-md (cards stay rounded-xl); font-black became font-bold; field labels, the portal tag, the no-stall note and the password hint went from neutral-400 to neutral-500 (the hint also 10px to 11px). --}}
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Main Profile Card (Matching Student Profile Theme) ────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Profile Header with Avatar & Action CTAs --}}
        <div class="p-6 sm:p-8 border-b border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 relative">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                {{-- Avatar Circle --}}
                <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full bg-brand-50 border-2 border-brand-200/80 text-brand-700 font-bold text-3xl shadow-xs flex items-center justify-center ring-4 ring-white shrink-0">
                    {{-- CHANGED: dropped the 'S' fallback; email is required, so it is always set. --}}
                    {{ strtoupper(substr($profile->name ?? $profile->email, 0, 1)) }}
                </div>

                {{-- Name & Identity Info --}}
                <div class="pt-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight">
                        {{ $profile->name ?? 'Staff Member' }}
                    </h1>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                        {{-- CHANGED: isset() dropped; the controller always passes $stall. --}}
                        @if($stall)
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
                <button type="button" onclick="document.getElementById('edit-profile-modal').showModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-md transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    Edit Profile
                </button>
                <button type="button" onclick="document.getElementById('change-password-modal').showModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white hover:bg-neutral-50 text-neutral-700 border border-neutral-200/90 text-xs font-bold rounded-md transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="lock-closed-outline" class="text-sm text-neutral-500"></ion-icon>
                    Password
                </button>
            </div>
        </div>

        {{-- Staff & Account Details List --}}
        <div class="p-6 sm:p-8">
            {{-- CHANGED: removed the decorative "ISU DSS Portal" tag beside the heading. --}}
            <div class="flex items-center justify-between pb-3 mb-6 border-b border-neutral-100">
                <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                    <ion-icon name="briefcase-outline" class="text-lg text-brand-700"></ion-icon>
                    Staff &amp; Stall Information
                </h2>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 sm:gap-y-8 gap-x-8">
                {{-- Staff Name --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="person-outline" class="text-neutral-400"></ion-icon>
                        Full Name
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ $profile->name ?? 'Not set' }}
                    </dd>
                </div>

                {{-- Email --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="mail-outline" class="text-neutral-400"></ion-icon>
                        Email
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base break-all">
                        {{ $profile->email }}
                    </dd>
                </div>

                {{-- Assigned Stall --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="storefront-outline" class="text-neutral-400"></ion-icon>
                        Assigned Canteen Stall
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{-- CHANGED: isset() dropped; the controller always passes $stall. --}}
                        @if($stall)
                            {{ $stall->name }}
                        @else
                            <span class="text-neutral-500 font-normal italic">No stall assigned</span>
                        @endif
                    </dd>
                </div>

                {{-- Role / Account Type --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="shield-checkmark-outline" class="text-neutral-400"></ion-icon>
                        Account Role
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        Canteen Staff
                    </dd>
                </div>
            </dl>
        </div>

        {{-- CHANGED: removed the footer's hardcoded "Status: Active" / "Account: Staff Portal" labels, which no database column backs. --}}
        {{-- Footer Activity Bar --}}
        <div class="px-6 py-4 sm:px-8 sm:py-4 bg-neutral-50/70 border-t border-neutral-100 flex justify-end text-xs">
            @if($stall)
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
{{-- CHANGED (both modals): rebuilt in the shared dialog frame of the admin pages and the student profile. Plain confirm-modal width with no padding or border (modal-sharp max-w-md and the white wrapper dropped), a dark green header band with a tile, the title and a pale green subtitle, the content padded below, and a hairline-topped row of the shared btn buttons, plain text. Labels are sentence case in semibold neutral-900 (were bold neutral-700 in title case), and fields are 14px from 640px up (were 12px) with the green focus ring. --}}
{{-- CHANGED (both modals): on phones they open as bottom sheets (sheet-on-phone) with the header (sheet-head) and the button row (sheet-actions) pinned while the fields scroll. Fields are 16px there, since iOS Safari zooms into any field under 16px, and 44px tall; the X is a 44px square, Cancel and Save/Update are 44px tall, and Save/Update fills the rest of the row. --}}
<dialog id="edit-profile-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="edit-profile-title">
    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="create-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="edit-profile-title" class="text-sm font-bold text-white leading-tight">Edit Staff Profile</h3>
                <p class="text-[11px] text-brand-200">Update your staff account information</p>
            </div>
        </div>
        <button type="button" onclick="this.closest('dialog').close()" class="text-white/70 enabled:hover:text-white w-11 h-11 -mr-2 -my-1 flex items-center justify-center rounded-md transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    {{-- Modal Form --}}
    <form action="{{ route('staff.profile.update') }}" method="POST" class="px-6 pb-6 max-sm:pb-0">
        @csrf

        <div class="mt-5 space-y-4">
            {{-- Full Name --}}
            <div>
                <label for="modal-name" class="block text-xs font-semibold text-neutral-900 mb-1.5">Full name</label>
                <input type="text" id="modal-name" name="name" value="{{ old('name', $profile->name) }}" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('name') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                @error('name')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="modal-email" class="block text-xs font-semibold text-neutral-900 mb-1.5">Email address</label>
                <input type="email" id="modal-email" name="email" value="{{ old('email', $profile->email) }}" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('email') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                @error('email')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- CHANGED: removed the read-only stall/role box; the page behind the modal already shows both. --}}
        {{-- Footer Buttons --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost btn-sm text-xs max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold max-sm:flex-1 max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer">Save Changes</button>
        </div>
    </form>
</dialog>

{{-- ── 2. Change Password Modal ────────────────────────────────────────── --}}
<dialog id="change-password-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="change-password-title">
    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="lock-closed-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="change-password-title" class="text-sm font-bold text-white leading-tight">Change Password</h3>
                <p class="text-[11px] text-brand-200">Update your security credentials</p>
            </div>
        </div>
        <button type="button" onclick="this.closest('dialog').close()" class="text-white/70 enabled:hover:text-white w-11 h-11 -mr-2 -my-1 flex items-center justify-center rounded-md transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    {{-- Modal Form --}}
    <form action="{{ route('staff.profile.password') }}" method="POST" class="px-6 pb-6 max-sm:pb-0">
        @csrf

        <div class="mt-5 space-y-4">
            {{-- Current Password --}}
            <div>
                <label for="modal-current-password" class="block text-xs font-semibold text-neutral-900 mb-1.5">Current password</label>
                <input type="password" id="modal-current-password" name="current_password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('current_password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                @error('current_password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="modal-new-password" class="block text-xs font-semibold text-neutral-900 mb-1.5">New password</label>
                <input type="password" id="modal-new-password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                {{-- CHANGED: hint neutral-500 to neutral-600, the hint colour of the other dialogs. --}}
                <p class="text-[11px] text-neutral-600 mt-1">Minimum 8 characters with upper &amp; lowercase, a number, and a symbol.</p>
                @error('password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="modal-password-confirmation" class="block text-xs font-semibold text-neutral-900 mb-1.5">Confirm new password</label>
                <input type="password" id="modal-password-confirmation" name="password_confirmation" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                {{-- ADDED: live match hint, see data-password-match in resources/js/app.js. --}}
                <p data-password-match aria-live="polite" hidden class="text-rose-600 data-[match=yes]:text-emerald-700 text-[11px] font-semibold mt-1 flex items-center gap-1"></p>
            </div>
        </div>

        {{-- Footer Buttons --}}
        {{-- CHANGED: "Update Password" button neutral-900 to brand-600, the same as the student profile and the "Save Changes" button. --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" onclick="this.closest('dialog').close()" class="btn btn-ghost btn-sm text-xs max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold max-sm:flex-1 max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer">Update Password</button>
        </div>
    </form>
</dialog>

{{-- ── Modal Controller Script ────────────────────────────────────────── --}}
{{-- CHANGED: the open/close helpers became inline onclick handlers on the buttons, the two backdrop handlers became one loop, and the DOMContentLoaded wrapper and null guards are gone (the script runs after both dialogs, which are always on the page). --}}
<script>
// CHANGED: one loop per modal wires the backdrop click, the busy lock and the submit spinner (was a second loop over the forms).
document.querySelectorAll('#edit-profile-modal, #change-password-modal').forEach(modal => {
    // ADDED: while its form is being sent (busy), Escape and a backdrop click no longer close the modal.
    modal.addEventListener('cancel', (e) => { if (modal.dataset.busy) e.preventDefault(); });
    modal.addEventListener('click', (e) => {
        if (modal.dataset.busy) return;
        const rect = modal.getBoundingClientRect();
        if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
            modal.close();
        }
    });

    // ADDED: spinner on Save Changes / Update Password while the form submits. Same spinner markup as the
    // student profile and admin users pages; submit only fires after the required fields pass.
    modal.querySelector('form').addEventListener('submit', () => {
        const btn = modal.querySelector('button[type="submit"]');
        const label = modal.id === 'edit-profile-modal' ? 'Saving...' : 'Updating...';
        // ADDED: lock the modal so it cannot be closed half-way through; X and Cancel are disabled.
        modal.dataset.busy = 'true';
        modal.querySelectorAll('button[type="button"]').forEach(b => b.disabled = true);
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.innerHTML = '<svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>' + label + '</span>';
    });
});

// ADDED: Chrome closes a dialog on a second Esc even when cancel was prevented, so the key itself is blocked while a form is being sent.
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.querySelector('dialog[data-busy]')) e.preventDefault();
});

// Auto-reopen on validation errors
@if($errors->hasAny(['name', 'email']))
    document.getElementById('edit-profile-modal').showModal();
@endif

@if($errors->hasAny(['current_password', 'password', 'password_confirmation']))
    document.getElementById('change-password-modal').showModal();
@endif
</script>
@endsection
