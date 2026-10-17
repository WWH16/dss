@extends('layouts.dashboard')

@section('title', 'My Profile | DSS')
@section('header_title', 'My Profile')
{{-- CHANGED (whole file): every rounded-lg became rounded-md (cards stay rounded-xl); font-black became font-bold; field labels, the portal tag and the password hint went from neutral-400 to neutral-500 (the hint also 10px to 11px); the "Update Password" button went from neutral-900 to brand-600, like "Save Changes" in the other modal. --}}

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Main Profile Card (Classic Layout in Brand Theme) ────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-xs overflow-hidden">
        
        {{-- Profile Header with Avatar & Action CTAs --}}
        {{-- CHANGED: p-5 on phones (was p-6). --}}
        {{-- CHANGED: on phones the avatar sits beside the name, left-aligned like the details list below, instead of a centered stack (avatar 56px there, was 80px; name text-lg, was text-xl; gap-5 to the buttons, was gap-6). --}}
        <div class="p-5 sm:p-8 border-b border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-start justify-between gap-5 sm:gap-6 relative">
            <div class="flex items-center sm:items-start gap-4 sm:gap-5">
                {{-- Avatar Circle --}}
                <div class="w-14 h-14 sm:w-22 sm:h-22 rounded-full bg-brand-50 border-2 border-brand-200/80 text-brand-700 font-bold text-2xl sm:text-3xl shadow-xs flex items-center justify-center ring-4 ring-white shrink-0">
                    {{ strtoupper(substr($profile->name ?? ($profile->student_number ?? 'S'), 0, 1)) }}
                </div>

                {{-- Name & Identity Info --}}
                <div class="sm:pt-1 min-w-0">
                    <h1 class="text-lg sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight text-balance">
                        {{ $profile->name ?? 'Student' }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-brand-50 text-brand-800 border border-brand-200/70">
                            ID: {{ $profile->student_number ?? 'N/A' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                            <ion-icon name="school-outline" class="text-xs text-neutral-500"></ion-icon>
                            Student Account
                        </span>
                    </div>

                    {{-- CHANGED: break-all so a long school email wraps instead of pushing the card wider than a phone screen. Hidden on phones, where the Email row in the list below already shows it. --}}
                    <p class="text-xs text-neutral-500 mt-2 hidden sm:flex items-center gap-1.5 break-all">
                        <ion-icon name="mail-outline" class="text-neutral-400 text-sm"></ion-icon>
                        {{ $profile->email }}
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            {{-- CHANGED: both buttons are 44px tall on phones (min-h-11, was ~32px), with 14px labels there (text-sm, 12px from sm up). Labels stay on one line; Password wraps to its own row if both do not fit. --}}
            <div class="flex flex-row flex-wrap sm:flex-col gap-2 shrink-0 w-full sm:w-auto">
                <button type="button" onclick="openEditProfileModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 whitespace-nowrap px-3.5 py-2 min-h-11 sm:min-h-0 bg-brand-600 hover:bg-brand-700 text-white text-sm sm:text-xs font-bold rounded-md transition-colors shadow-2xs">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    Edit Profile
                </button>
                <button type="button" onclick="openChangePasswordModal()" 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 whitespace-nowrap px-3.5 py-2 min-h-11 sm:min-h-0 bg-white hover:bg-neutral-50 text-neutral-700 border border-neutral-200/90 text-sm sm:text-xs font-bold rounded-md transition-colors shadow-2xs">
                    <ion-icon name="lock-closed-outline" class="text-sm text-neutral-500"></ion-icon>
                    Password
                </button>
            </div>
        </div>

        {{-- Academic & Profile Details List --}}
        {{-- CHANGED: p-5 on phones (was p-6); the "ISU DSS Portal" tag shows from sm up so the heading keeps one line. On phones the heading has mb-5 (was mb-6) and the rows gap-y-5 (was gap-y-6), so the five rows read as one tighter group. --}}
        <div class="p-5 sm:p-8">
            <div class="flex items-center justify-between pb-3 mb-5 sm:mb-6 border-b border-neutral-100">
                <h2 class="text-sm font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                    <ion-icon name="school-outline" class="text-lg text-brand-700"></ion-icon>
                    Academic &amp; Student Information
                </h2>
                <span class="hidden sm:inline text-[11px] font-semibold text-neutral-500">ISU DSS Portal</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 sm:gap-y-8 gap-x-8">
                {{-- Student Number --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="id-card-outline" class="text-neutral-400"></ion-icon>
                        Student ID Number
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base tabular-nums font-mono">
                        {{ $profile->student_number ?? 'Not set' }}
                    </dd>
                </div>

                {{-- Email --}}
                {{-- CHANGED: the email wraps at the @ first (<wbr>, after e() escapes it) and only breaks inside a word when one part is still too long (break-words, was break-all, which split ".ph" onto its own line). --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="mail-outline" class="text-neutral-400"></ion-icon>
                        Email
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base break-words">
                        {!! str_replace('@', '<wbr>@', e($profile->email)) !!}
                    </dd>
                </div>

                {{-- Course / Degree --}}
                {{-- CHANGED: labelled "Program" instead of "Course / Program". --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="book-outline" class="text-neutral-400"></ion-icon>
                        Program
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ $profile->course ?: 'Not set' }}
                    </dd>
                </div>

                {{-- CHANGED: added the specialization track, shown by its full name. --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="git-branch-outline" class="text-neutral-400"></ion-icon>
                        Specialization Track
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{ \App\Models\User::PROGRAMS[$profile->course][$profile->track] ?? ($profile->track ?: 'Not set') }}
                    </dd>
                </div>

                {{-- Year Level --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
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
        {{-- CHANGED: the three stats wrap (flex-wrap) on phones instead of running off the screen, and the bullets between them show from sm up. --}}
        <div class="px-5 py-4 sm:px-8 sm:py-4 bg-neutral-50/70 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-neutral-600 font-medium">
                <span class="flex items-center gap-1">
                    {{-- CHANGED: "Reviews Logged" became "Evaluations", the one word the student pages use for a submission. --}}
                    <strong class="text-neutral-900 font-bold tabular-nums">{{ $totalEvals ?? 0 }}</strong> Evaluations
                </span>
                <span class="hidden sm:inline text-neutral-300" aria-hidden="true">•</span>
                <span class="flex items-center gap-1">
                    <strong class="text-neutral-900 font-bold tabular-nums">{{ $uniqueStalls ?? 0 }}</strong> Stalls Evaluated
                </span>
                @if(($totalEvals ?? 0) > 0)
                    <span class="hidden sm:inline text-neutral-300" aria-hidden="true">•</span>
                    <span class="flex items-center gap-1">
                        {{-- CHANGED: the ★ glyph is hidden from screen readers, which read it as "black star". --}}
                        <strong class="text-neutral-900 font-bold tabular-nums">{{ number_format($avgGiven, 1) }}<span aria-hidden="true">★</span></strong> Avg Given
                    </span>
                @endif
            </div>

            {{-- CHANGED: 44px tall on phones (min-h-11), a full thumb target. --}}
            <a href="{{ route('student.history') }}" class="text-brand-700 hover:text-brand-800 font-bold inline-flex items-center gap-1 min-h-11 sm:min-h-0 transition-colors">
                View Evaluation History &rarr;
            </a>
        </div>

    </div>

</div>

{{-- ── 1. Edit Academic Profile Modal ────────────────────────────────────── --}}
{{-- CHANGED (both modals): on phones they open as bottom sheets (sheet-on-phone in resources/css/components/modal.css). The sheet scrolls, which the old clipped dialog could not, so Save stays reachable on short screens; the header (sheet-head, now opaque on phones) and the button row (sheet-actions) stay pinned. On phones the padding is px-5, Cancel and Save/Update are 44px tall with 14px labels, and Save/Update fills the rest of the row (flex-1) as the thumb target, and the email in the reference box wraps (break-all). --}}
<dialog id="edit-profile-modal" class="confirm-modal modal-sharp sheet-on-phone max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="sheet-head px-5 sm:px-6 py-4 bg-neutral-50 sm:bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 leading-tight">Edit Academic Profile</h3>
                    <p class="text-[11px] text-neutral-500">Update your student name and academic details</p>
                </div>
            </div>
            <button type="button" class="js-close-profile-modal text-neutral-400 hover:text-neutral-700 w-11 h-11 -mr-2 flex items-center justify-center rounded-md transition-colors cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        {{-- Modal Form --}}
        {{-- CHANGED (both modals): fields use 16px text on phones (text-base, was text-xs), since iOS Safari zooms the page into any field under 16px, and py-2.5 makes them 44px tall. The close button is a 44px square, and Cancel and Save/Update are 44px tall on phones. --}}
        <form action="{{ route('student.profile.update') }}" method="POST" class="px-5 pt-5 sm:p-6 space-y-4">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="modal-name" class="block text-xs font-bold text-neutral-700 mb-1.5">Full Name</label>
                <input type="text" id="modal-name" name="name" value="{{ old('name', $profile->name) }}" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('name') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                @error('name')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Course / Degree --}}
            {{-- CHANGED: the free-text course input became a "Program" select of User::PROGRAMS, followed by a track select that lists only the chosen program's tracks. resources/js/app.js keeps the track disabled until a program is selected; while disabled the field fades to half opacity. --}}
            @php
                $currentCourse = old('course', $profile->course);
                $currentTrack = old('track', $profile->track);
            @endphp
            <div>
                <label for="modal-course" class="block text-xs font-bold text-neutral-700 mb-1.5">Program</label>
                <select id="modal-course" name="course" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('course') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="">Select Program</option>
                    @foreach(array_keys(\App\Models\User::PROGRAMS) as $program)
                        <option value="{{ $program }}" {{ $currentCourse === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
                @error('course')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div data-track-field="modal-course" class="has-[select:disabled]:opacity-50 has-[select:disabled]:[&_select]:cursor-not-allowed transition-opacity">
                <label for="modal-track" class="block text-xs font-bold text-neutral-700 mb-1.5">Specialization Track</label>
                <select id="modal-track" name="track" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('track') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    {{-- CHANGED: the placeholder shows but cannot be picked (disabled); a selected track below overrides its "selected". --}}
                    <option value="" disabled selected>Select Track</option>
                    {{-- CHANGED: no optgroup per program; the program is already picked, so its heading in the list was redundant. --}}
                    @foreach(\App\Models\User::PROGRAMS as $program => $tracks)
                        @foreach($tracks as $code => $trackName)
                            <option value="{{ $code }}" data-program="{{ $program }}" {{ $currentCourse === $program && $currentTrack === $code ? 'selected' : '' }}>{{ $code }} - {{ $trackName }}</option>
                        @endforeach
                    @endforeach
                </select>
                @error('track')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Year Level --}}
            <div>
                <label for="modal-year-level" class="block text-xs font-bold text-neutral-700 mb-1.5">Year Level</label>
                <select id="modal-year-level" name="year_level"
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('year_level') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="">Select Year Level</option>
                    {{-- CHANGED: option values are lower-case ("1st year"), the same values the sign-up form saves, and the current value is matched case-insensitively. Before, a student who signed up with "1st year" opened this form with no year selected, and saving it erased their year level. --}}
                    @php $currentYear = strtolower((string) old('year_level', $profile->year_level)); @endphp
                    <option value="1st year" {{ $currentYear === '1st year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd year" {{ $currentYear === '2nd year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd year" {{ $currentYear === '3rd year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th year" {{ $currentYear === '4th year' ? 'selected' : '' }}>4th Year</option>
                    <option value="Graduate" {{ $currentYear === 'graduate' ? 'selected' : '' }}>Graduate</option>
                </select>
                @error('year_level')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Readonly Reference Chips --}}
            <div class="p-3 rounded-md bg-neutral-50 border border-neutral-200/70 text-[11px] text-neutral-500 space-y-1">
                <p><strong>ID Number:</strong> <span class="font-mono">{{ $profile->student_number }}</span> (Locked)</p>
                <p class="break-all"><strong>Email:</strong> {{ $profile->email }}</p>
            </div>

            {{-- Footer Buttons --}}
            <div class="sheet-actions -mx-5 px-5 sm:mx-0 sm:px-0 pt-3 border-t border-neutral-100 flex items-center justify-end gap-2">
                <button type="button" class="js-close-profile-modal px-3.5 py-2 min-h-11 sm:min-h-0 text-sm sm:text-xs font-bold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-md transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 whitespace-nowrap px-4 py-2 min-h-11 sm:min-h-0 bg-brand-600 hover:bg-brand-700 text-white text-sm sm:text-xs font-bold rounded-md transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="save-outline" class="text-sm"></ion-icon>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 2. Change Password Modal ────────────────────────────────────────── --}}
<dialog id="change-password-modal" class="confirm-modal modal-sharp sheet-on-phone max-w-md w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    <div class="bg-white">
        {{-- Modal Header --}}
        <div class="sheet-head px-5 sm:px-6 py-4 bg-neutral-50 sm:bg-neutral-50/70 border-b border-neutral-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-neutral-100 border border-neutral-200 text-neutral-700 flex items-center justify-center shrink-0">
                    <ion-icon name="lock-closed-outline" class="text-base"></ion-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 leading-tight">Change Password</h3>
                    <p class="text-[11px] text-neutral-500">Update your security credentials</p>
                </div>
            </div>
            {{-- CHANGED: disabled:* classes added; the script locks this button while the password is being updated. --}}
            <button type="button" class="js-close-password-modal text-neutral-400 hover:text-neutral-700 w-11 h-11 -mr-2 flex items-center justify-center rounded-md transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        {{-- Modal Form --}}
        {{-- CHANGED: id added so the script below can show a spinner on submit. --}}
        <form id="change-password-form" action="{{ route('student.profile.password') }}" method="POST" class="px-5 pt-5 sm:p-6 space-y-4">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="modal-current-password" class="block text-xs font-bold text-neutral-700 mb-1.5">Current Password</label>
                {{-- CHANGED (all three password fields): wrapped with an eye button to show or hide the text (togglePasswordVisibility in resources/js/app.js); right padding px-3 became pr-11 to clear it. --}}
                <div class="relative">
                    <input type="password" id="modal-current-password" name="current_password" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border @error('current_password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-current-password', 'modal-current-password-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-400 hover:text-neutral-600 transition-colors cursor-pointer" aria-label="Toggle current password visibility">
                        <ion-icon id="modal-current-password-icon" name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="modal-new-password" class="block text-xs font-bold text-neutral-700 mb-1.5">New Password</label>
                <div class="relative">
                    <input type="password" id="modal-new-password" name="password" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border @error('password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-new-password', 'modal-new-password-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-400 hover:text-neutral-600 transition-colors cursor-pointer" aria-label="Toggle new password visibility">
                        <ion-icon id="modal-new-password-icon" name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
                {{-- CHANGED: the "Minimum 8 characters..." hint became the shared rule checklist, which ticks each rule as it is met. --}}
                @include('partials.password-rules')
                @error('password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="modal-password-confirmation" class="block text-xs font-bold text-neutral-700 mb-1.5">Confirm New Password</label>
                <div class="relative">
                    <input type="password" id="modal-password-confirmation" name="password_confirmation" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-password-confirmation', 'modal-password-confirmation-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-400 hover:text-neutral-600 transition-colors cursor-pointer" aria-label="Toggle password confirmation visibility">
                        <ion-icon id="modal-password-confirmation-icon" name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
                {{-- ADDED: live match hint, see data-password-match in resources/js/app.js. --}}
                <p data-password-match aria-live="polite" hidden class="text-rose-600 data-[match=yes]:text-emerald-700 text-[11px] font-semibold mt-1 flex items-center gap-1"></p>
            </div>

            {{-- Footer Buttons --}}
            <div class="sheet-actions -mx-5 px-5 sm:mx-0 sm:px-0 pt-3 border-t border-neutral-100 flex items-center justify-end gap-2">
                {{-- CHANGED: disabled:* classes added; the script locks Cancel while the password is being updated. --}}
                <button type="button" class="js-close-password-modal px-3.5 py-2 min-h-11 sm:min-h-0 text-sm sm:text-xs font-bold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-md transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent">
                    Cancel
                </button>
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 whitespace-nowrap px-4 py-2 min-h-11 sm:min-h-0 bg-brand-600 hover:bg-brand-700 text-white text-sm sm:text-xs font-bold rounded-md transition-colors shadow-2xs cursor-pointer">
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
        // ADDED: while the password is being updated (busy), Escape and a backdrop click no longer close the modal.
        changePasswordModal.addEventListener('cancel', (e) => {
            if (changePasswordModal.dataset.busy) e.preventDefault();
        });
        changePasswordModal.addEventListener('click', (e) => {
            if (changePasswordModal.dataset.busy) return;
            const rect = changePasswordModal.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
                changePasswordModal.close();
            }
        });
    }

    // ADDED: spinner on Update Password while the change and the notification email are sent.
    // Same spinner markup as the admin users page; submit only fires after the required fields pass.
    const changePasswordForm = document.getElementById('change-password-form');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', () => {
            // ADDED: lock the modal so it cannot be closed half-way through the update.
            changePasswordModal.dataset.busy = 'true';
            document.querySelectorAll('.js-close-password-modal').forEach(b => b.disabled = true);
            const btn = changePasswordForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = '<svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Updating...</span>';
        });
    }

    // Auto-reopen on validation errors
    // CHANGED: also reopens on a track error.
    @if($errors->has('name') || $errors->has('course') || $errors->has('track') || $errors->has('year_level'))
        if (editProfileModal) editProfileModal.showModal();
    @endif

    @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        if (changePasswordModal) changePasswordModal.showModal();
    @endif
});
</script>
@endsection


