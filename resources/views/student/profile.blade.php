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
                    {{-- CHANGED: the student number is read from the students table. --}}
                    {{ strtoupper(substr($profile->name ?? ($profile->student?->student_number ?? 'S'), 0, 1)) }}
                </div>

                {{-- Name & Identity Info --}}
                <div class="sm:pt-1 min-w-0">
                    <h1 class="text-lg sm:text-2xl font-bold text-neutral-900 tracking-tight leading-tight text-balance">
                        {{ $profile->name ?? 'Student' }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-brand-50 text-brand-800 border border-brand-200/70">
                            {{-- CHANGED: read from the students table. --}}
                            ID: {{ $profile->student?->student_number ?? 'N/A' }}
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
                        {{-- CHANGED: read from the students table. --}}
                        {{ $profile->student?->student_number ?? 'Not set' }}
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
                        {{-- CHANGED: the program code comes from the programs table. --}}
                        {{ $profile->student?->program?->code ?? 'Not set' }}
                    </dd>
                </div>

                {{-- CHANGED: added the specialization track, shown by its full name. --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="git-branch-outline" class="text-neutral-400"></ion-icon>
                        Specialization Track
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{-- CHANGED: the track name comes from the tracks table. --}}
                        {{ $profile->student?->track?->name ?? 'Not set' }}
                    </dd>
                </div>

                {{-- Year Level --}}
                <div class="group">
                    <dt class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-1 flex items-center gap-1.5">
                        <ion-icon name="calendar-outline" class="text-neutral-400"></ion-icon>
                        Year Level
                    </dt>
                    <dd class="text-neutral-900 font-semibold text-sm sm:text-base">
                        {{-- CHANGED: read from the students table. --}}
                        {{ $profile->student?->year_level ?: 'Not set' }}
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
{{-- CHANGED (both modals): on phones they open as bottom sheets (sheet-on-phone in resources/css/components/modal.css). The sheet scrolls, which the old clipped dialog could not, so Save stays reachable on short screens; the header (sheet-head) and the button row (sheet-actions) stay pinned. On phones Cancel and Save/Update are 44px tall with 14px labels, and Save/Update fills the rest of the row (flex-1) as the thumb target, and the email in the reference box wraps (break-all). --}}
{{-- CHANGED (both modals): rebuilt in the shared dialog frame of the admin pages and the history dialog. Plain confirm-modal width with no padding or border (modal-sharp max-w-md and the white wrapper dropped), a dark green header band with a tile, the title and a pale green subtitle, the content padded below, and a hairline-topped row of the shared btn buttons, plain text. Labels are sentence case in semibold neutral-900 (were bold neutral-700 in title case), fields are 14px from 640px up (were 12px) with the green focus ring, and the reference box is the tinted panel. The X stays a 44px square, white on the band. --}}
<dialog id="edit-profile-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="edit-profile-title">
    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="create-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="edit-profile-title" class="text-sm font-bold text-white leading-tight">Edit Academic Profile</h3>
                <p class="text-[11px] text-brand-200">Update your student name and academic details</p>
            </div>
        </div>
        <button type="button" class="js-close-profile-modal text-white/70 enabled:hover:text-white w-11 h-11 -mr-2 -my-1 flex items-center justify-center rounded-md transition-colors cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    {{-- Modal Form --}}
    {{-- CHANGED (both modals): fields use 16px text on phones (text-base, was text-xs), since iOS Safari zooms the page into any field under 16px, and py-2.5 makes them 44px tall. The close button is a 44px square, and Cancel and Save/Update are 44px tall on phones. --}}
    <form action="{{ route('student.profile.update') }}" method="POST" class="px-6 pb-6 max-sm:pb-0">
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

            {{-- Course / Degree --}}
            {{-- CHANGED: the free-text course input became a "Program" select of the programs table, followed by a track select that lists only the chosen program's tracks. resources/js/app.js keeps the track disabled until a program is selected; while disabled the field fades to half opacity. --}}
            {{-- CHANGED: the selects post program_id and track_id; options come from the programs and tracks tables. --}}
            @php
                $programs = \App\Models\Program::with('tracks')->orderBy('id')->get();
                $currentProgram = (int) old('program_id', $profile->student?->program_id);
                $currentTrack = (int) old('track_id', $profile->student?->track_id);
            @endphp
            <div>
                <label for="modal-program" class="block text-xs font-semibold text-neutral-900 mb-1.5">Program</label>
                <select id="modal-program" name="program_id" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('program_id') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">Select Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ $currentProgram === $program->id ? 'selected' : '' }}>{{ $program->code }}</option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div data-track-field="modal-program" class="has-[select:disabled]:opacity-50 has-[select:disabled]:[&_select]:cursor-not-allowed transition-opacity">
                <label for="modal-track" class="block text-xs font-semibold text-neutral-900 mb-1.5">Specialization track</label>
                <select id="modal-track" name="track_id" required
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('track_id') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    {{-- CHANGED: the placeholder shows but cannot be picked (disabled); a selected track below overrides its "selected". --}}
                    <option value="" disabled selected>Select Track</option>
                    {{-- CHANGED: no optgroup per program; the program is already picked, so its heading in the list was redundant. --}}
                    @foreach($programs as $program)
                        @foreach($program->tracks as $track)
                            <option value="{{ $track->id }}" data-program="{{ $program->id }}" {{ $currentTrack === $track->id ? 'selected' : '' }}>{{ $track->code }} - {{ $track->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                @error('track_id')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Year Level --}}
            <div>
                <label for="modal-year-level" class="block text-xs font-semibold text-neutral-900 mb-1.5">Year level</label>
                <select id="modal-year-level" name="year_level"
                    class="w-full px-3 py-2.5 sm:py-2 bg-neutral-50 border @error('year_level') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">Select Year Level</option>
                    {{-- CHANGED: option values are lower-case ("1st year"), the same values the sign-up form saves, and the current value is matched case-insensitively. Before, a student who signed up with "1st year" opened this form with no year selected, and saving it erased their year level. --}}
                    {{-- CHANGED: the year level is read from the students table. --}}
                    @php $currentYear = strtolower((string) old('year_level', $profile->student?->year_level)); @endphp
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
            <div class="px-3.5 py-3 rounded-lg bg-surface-alt text-[11px] text-neutral-600 space-y-1">
                {{-- CHANGED: the student number is read from the students table. --}}
                <p><strong class="text-neutral-900">ID Number:</strong> <span class="font-mono">{{ $profile->student?->student_number }}</span> (Locked)</p>
                <p class="break-all"><strong class="text-neutral-900">Email:</strong> {{ $profile->email }}</p>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" class="js-close-profile-modal btn btn-ghost btn-sm text-xs max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer">Cancel</button>
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
        {{-- CHANGED: disabled:* classes added; the script locks this button while the password is being updated. --}}
        <button type="button" class="js-close-password-modal text-white/70 enabled:hover:text-white w-11 h-11 -mr-2 -my-1 flex items-center justify-center rounded-md transition-colors cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    {{-- Modal Form --}}
    {{-- CHANGED: id added so the script below can show a spinner on submit. --}}
    <form id="change-password-form" action="{{ route('student.profile.password') }}" method="POST" class="px-6 pb-6 max-sm:pb-0">
        @csrf

        <div class="mt-5 space-y-4">
            {{-- Current Password --}}
            <div>
                <label for="modal-current-password" class="block text-xs font-semibold text-neutral-900 mb-1.5">Current password</label>
                {{-- CHANGED (all three password fields): wrapped with an eye button to show or hide the text (togglePasswordVisibility in resources/js/app.js); right padding px-3 became pr-11 to clear it. --}}
                <div class="relative">
                    <input type="password" id="modal-current-password" name="current_password" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border @error('current_password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-current-password', 'modal-current-password-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-500 hover:text-neutral-800 transition-colors cursor-pointer" aria-label="Toggle current password visibility">
                        <ion-icon id="modal-current-password-icon" name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="modal-new-password" class="block text-xs font-semibold text-neutral-900 mb-1.5">New password</label>
                <div class="relative">
                    <input type="password" id="modal-new-password" name="password" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border @error('password') border-rose-500 @else border-neutral-200 @enderror rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-new-password', 'modal-new-password-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-500 hover:text-neutral-800 transition-colors cursor-pointer" aria-label="Toggle new password visibility">
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
                <label for="modal-password-confirmation" class="block text-xs font-semibold text-neutral-900 mb-1.5">Confirm new password</label>
                <div class="relative">
                    <input type="password" id="modal-password-confirmation" name="password_confirmation" required
                        placeholder="••••••••"
                        class="w-full pl-3 pr-11 py-2.5 sm:py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <button type="button" onclick="togglePasswordVisibility('modal-password-confirmation', 'modal-password-confirmation-icon')" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-neutral-500 hover:text-neutral-800 transition-colors cursor-pointer" aria-label="Toggle password confirmation visibility">
                        <ion-icon id="modal-password-confirmation-icon" name="eye-outline" class="text-base"></ion-icon>
                    </button>
                </div>
                {{-- ADDED: live match hint, see data-password-match in resources/js/app.js. --}}
                <p data-password-match aria-live="polite" hidden class="text-rose-600 data-[match=yes]:text-emerald-700 text-[11px] font-semibold mt-1 flex items-center gap-1"></p>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            {{-- CHANGED: disabled:* classes added; the script locks Cancel while the password is being updated. --}}
            <button type="button" class="js-close-password-modal btn btn-ghost btn-sm text-xs max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold max-sm:flex-1 max-sm:min-h-11 max-sm:text-sm rounded-md cursor-pointer">Update Password</button>
        </div>
    </form>
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
    // CHANGED: also reopens on a track error. The fields are now program_id and track_id.
    @if($errors->has('name') || $errors->has('program_id') || $errors->has('track_id') || $errors->has('year_level'))
        if (editProfileModal) editProfileModal.showModal();
    @endif

    @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        if (changePasswordModal) changePasswordModal.showModal();
    @endif
});
</script>
@endsection


