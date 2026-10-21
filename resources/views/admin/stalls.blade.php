@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Manage Stalls | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Stalls" (the sidebar label) instead of the layout default "Dashboard". --}}
@section('header_title', 'Stalls')
@section('content')
{{-- CHANGED: was max-w-7xl mx-auto, which centred the page and left dead space beside the sidebar; same fix as the overview. --}}
<div class="max-w-[1600px] space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-neutral-200/80">
        <div>
            {{-- CHANGED: the visible "Canteen Stalls" heading repeated the top bar's "Stalls". The h1 stays for screen readers; the description is the visible lead. --}}
            <h1 class="sr-only">Canteen Stalls</h1>
            {{-- CHANGED: "status controls" named a row control that does not exist; the copy now says where status is set (the Edit dialog). --}}
            <p class="text-sm text-neutral-600 max-w-2xl">Register stalls, assign staff, and open or close a stall for evaluations from Edit.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            {{-- CHANGED: green filled badge became the neutral metadata badge; green is kept for primary actions and ratings. --}}
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-white text-neutral-700 border border-neutral-300">
                {{ $stalls->count() }} {{ Str::plural('stall', $stalls->count()) }} total
            </span>
            {{-- CHANGED: removed the amber "Unassigned Staff" badge; the pending-assignment banner directly below states the same count. --}}
        </div>
    </div>

    {{-- ── 2. Pending Staff Assignment Banner (Quick Onboarding) ───────────── --}}
    @if($unassignedStaff->isNotEmpty())
        {{-- CHANGED: amber fill became a white card with an amber icon, matching the Overview's attention banner. --}}
        <div class="p-4 bg-white border border-neutral-200/70 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                    <ion-icon name="people" class="text-lg text-amber-700"></ion-icon>
                </div>
                <div>
                    {{-- CHANGED: "N Staff Accounts Pending Assignment" became "N Unassigned Staff Accounts", the word the Staff & Admins page and the staff pickers use. --}}
                    <h3 class="text-xs font-bold text-neutral-900">
                        {{ $unassignedStaff->count() }} Unassigned {{ Str::plural('Staff Account', $unassignedStaff->count()) }}
                    </h3>
                    <p class="text-[11px] text-neutral-600 mt-0.5">
                        These registered staff members cannot view their stall evaluations or rankings until assigned to an active stall.
                    </p>
                </div>
            </div>
            {{-- CHANGED: full width and 44px tall on phones, where it was a 30px button hugging the left edge under
                 two lines of warning text; the compact self-sizing button returns from sm up. --}}
            {{-- CHANGED: the shared btn-primary green, like the Overview banner's Review Evaluations button. --}}
            <button type="button" onclick="openQuickAssignModal()"
                class="btn btn-sm text-xs font-bold px-3 min-h-11 sm:min-h-0 sm:py-1.5 rounded-md w-full sm:w-auto self-stretch sm:self-auto shrink-0 btn-primary flex items-center justify-center sm:justify-start gap-1.5 cursor-pointer">
                <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                <span>Assign Staff</span>
            </button>
        </div>
    @endif

    {{-- ── 3. Main 2-Column Layout ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Creation Form (4 cols) --}}
        {{-- CHANGED: sticky from lg up so the form stays in reach while the directory scrolls; it scrolls on its own if taller than the window, with the thin custom-scrollbar instead of the browser default. --}}
        <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-[5rem] lg:max-h-[calc(100dvh-6rem)] lg:overflow-y-auto custom-scrollbar">

            {{-- Add Stall Card --}}
            {{-- CHANGED: card radius rounded-lg to rounded-xl, matching the overview's card rule (cards xl, controls md). --}}
            {{-- CHANGED: the card is a <details> so this two-screen form is one 44px row on a phone instead of
                 everything between the header and the stall list. The summary is hidden from lg up, where the form
                 stays open in its own column as before; the script at the bottom of this page collapses it on
                 phones on load, unless the form came back with a validation error. --}}
            <details id="add-stall-panel" class="group bg-white rounded-xl border border-neutral-200/80 shadow-2xs" open @error('name') data-has-error="1" @enderror>
                <summary class="lg:hidden list-none [&::-webkit-details-marker]:hidden cursor-pointer select-none flex items-center justify-between gap-3 p-5 min-h-14 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600/40 rounded-xl">
                    <span class="flex items-start gap-2 min-w-0">
                        <ion-icon name="add-circle-outline" class="text-base text-brand-700 mt-0.5 shrink-0" aria-hidden="true"></ion-icon>
                        <span class="min-w-0">
                            <span class="block text-sm font-bold text-neutral-900 tracking-tight">Add New Stall</span>
                            <span class="block text-[11px] text-neutral-500 mt-0.5">Register a vendor &amp; optionally assign staff members.</span>
                        </span>
                    </span>
                    <ion-icon name="chevron-down-outline" class="text-sm text-neutral-500 shrink-0 transition-transform duration-200 group-open:rotate-180" aria-hidden="true"></ion-icon>
                </summary>

                <div class="px-5 pb-5 lg:pt-5">
                {{-- CHANGED: hidden on phones, where the summary above already carries this title and subtitle. --}}
                <div class="hidden lg:block mb-3.5 pb-2.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Add New Stall</h2>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Register a vendor &amp; optionally assign staff members.</p>
                </div>

                <form id="add-stall-form" action="{{ route('admin.stall.add') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label for="add-stall-name" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Stall Name <span class="text-rose-500">*</span>
                        </label>
                        {{-- CHANGED: em-dash in the placeholder replaced with a hyphen. --}}
                        <input type="text" id="add-stall-name" name="name"
                            value="{{ old('name') }}"
                            class="w-full px-3 py-2 bg-white border @error('name') border-rose-300 bg-rose-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors"
                            placeholder="e.g. Stall #1 - Food Hub" required>
                        @error('name')
                            <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                                <ion-icon name="alert-circle" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Scalable Staff Selector (Add Modal) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-semibold text-neutral-700 uppercase tracking-wider">
                                Assign Staff Members
                            </label>
                            {{-- CHANGED: 10px to 11px (page minimum). --}}
                            <span id="add-staff-selected-count" class="text-[11px] font-bold text-brand-700">0 selected</span>
                        </div>

                        @if($staffUsers->isEmpty())
                            <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-md text-center">
                                <p class="text-[11px] text-neutral-500">No staff accounts registered yet.</p>
                            </div>
                        @else
                            {{-- Search in Add Form --}}
                            <div class="relative mb-1.5">
                                <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-xs pointer-events-none"></ion-icon>
                                {{-- CHANGED: py-1 (~24px) became py-2 for thumbs; the compact height returns from sm up. --}}
                                <input type="text" id="add-staff-search" placeholder="Search staff…"
                                    class="w-full pl-7 pr-3 py-2 sm:py-1 bg-white border border-neutral-300 rounded text-[11px] font-medium focus:outline-none focus:border-brand-700">
                            </div>

                            {{-- CHANGED: a 144px scroller inside the page scroll is a trap under a thumb, and each row
                                 was a ~26px label. The list is taller on phones and each row is 44px there. --}}
                            <div id="add-staff-list" class="max-h-64 sm:max-h-36 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-300 rounded-md space-y-1 custom-scrollbar">
                                @foreach($staffUsers as $staff)
                                    <label class="add-staff-item flex items-center justify-between gap-2 p-2.5 sm:p-1.5 min-h-11 sm:min-h-0 rounded hover:bg-white border border-transparent hover:border-neutral-200 transition-colors cursor-pointer text-xs"
                                        data-staff-name="{{ strtolower($staff->name) }}" data-staff-email="{{ strtolower($staff->email) }}">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}"
                                                class="add-staff-checkbox rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-3.5 w-3.5">
                                            {{-- CHANGED: email raised from 10px neutral-400 to 11px neutral-500 for contrast. --}}
                                            <div class="min-w-0">
                                                <span class="font-bold text-neutral-900 truncate block text-[11px]">{{ $staff->name }}</span>
                                                {{-- CHANGED: email 11px to 12px, neutral-500 to neutral-600. --}}
                                                <span class="text-xs text-neutral-600 truncate block font-mono">{{ $staff->email }}</span>
                                            </div>
                                        </div>
                                        {{-- CHANGED: status tags raised from 9px to 11px; py-0.2 (not a Tailwind v4 step, so no padding was generated) to py-0.5; rounded to rounded-md. --}}
                                        {{-- CHANGED: "Unassigned" is amber (needs a stall) instead of green, and the bare stall name reads "Assigned to …" so a checked box is clearly a move. --}}
                                        @if(!$staff->stall_id)
                                            <span class="px-1.5 py-0.5 inline-flex items-center rounded-md bg-white text-neutral-700 border border-neutral-200 text-[11px] font-semibold shrink-0"><span class="w-1.5 h-1.5 mr-1 rounded-full shrink-0 bg-amber-500" aria-hidden="true"></span>Unassigned</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded-md bg-white text-neutral-700 border border-neutral-300 text-[11px] font-semibold shrink-0 truncate max-w-[9rem]" title="Assigned to {{ $staff->current_stall_name }}">Assigned to {{ $staff->current_stall_name }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        {{-- CHANGED: helper text raised from 10px neutral-400 to 11px neutral-500. --}}
                        {{-- CHANGED: neutral-500 to neutral-600 for contrast. --}}
                        <p class="text-[11px] text-neutral-600 mt-1">Multiple staff accounts can manage the same food stall.</p>
                    </div>

                    <div>
                        <label for="add-stall-desc" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Description (Optional)
                        </label>
                        <textarea id="add-stall-desc" name="description" rows="2"
                            class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors"
                            placeholder="Short description of offerings or location">{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" id="add-stall-submit-btn" class="btn btn-primary text-xs py-2 px-4 font-semibold flex items-center justify-center gap-1.5 w-full rounded-md shadow-2xs cursor-pointer transition-all">
                        <ion-icon name="add-outline" class="text-base"></ion-icon>
                        <span>Create Stall</span>
                    </button>
                </form>

                {{-- CHANGED: the guidelines card moved here, under the form, from the bottom of the page below every stall row. Inside the panel it collapses with the form on phones and stays beside the directory on desktop. Bullet 2 now names the staff names instead of the removed staff badges. Bullets use list-disc instead of hand-made "•" spans. --}}
                <div class="mt-5 pt-4 border-t border-neutral-100 space-y-2 text-xs">
                    <div class="flex items-center gap-1.5 text-neutral-800 font-bold text-[11px] uppercase tracking-wider">
                        <ion-icon name="information-circle-outline" class="text-base text-brand-700" aria-hidden="true"></ion-icon>
                        <span>Staff &amp; Stall Guidelines</span>
                    </div>
                    <ul class="list-disc pl-4 marker:text-neutral-400 space-y-1.5 text-neutral-600 text-[11px] font-medium leading-relaxed">
                        <li>A single stall can have multiple staff members assigned to it.</li>
                        <li>Click a stall's staff names to view the full roster &amp; manage assignments.</li>
                        <li>Unassigned staff are blocked from viewing campus standings until assigned.</li>
                    </ul>
                </div>
                </div>
            </details>

        </div>

        {{-- Right Column: Stalls Directory (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- CHANGED: rounded-lg to rounded-xl (card rule). --}}
            <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
                {{-- Header & Search Bar --}}
                <div class="px-5 py-4 border-b border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-neutral-50/50">
                    <div>
                        {{-- CHANGED: was "Active Stalls Directory", but the list includes inactive stalls. --}}
                        <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Stalls Directory</h2>
                        <p class="text-[11px] text-neutral-500 mt-0.5">Manage existing vendor accounts, staff assignments, and ratings</p>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-56">
                        <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none" aria-hidden="true"></ion-icon>
                        {{-- CHANGED: py-1.5 (~30px) became py-2.5 (44px) for thumbs; the compact height returns from sm up. --}}
                        <input type="text" id="stall-search-input"
                            placeholder="Filter stalls…"
                            aria-label="Filter stalls by name"
                            class="w-full pl-8 pr-3 py-2.5 sm:py-1.5 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
                    </div>
                </div>

                {{-- ADDED: column labels for the grid the rows form from lg up. The list is tabular data - name,
                     status, rating, staff - and without a legend the aligned columns read as coincidence. The
                     grid template here must stay identical to the one on .stall-item below. --}}
                {{-- CHANGED: the actions column shrank from 13rem to 7.5rem (three icon buttons: QR, Edit, Delete) and rating from 7rem to 6rem; the stall name gets the space, so long names stop truncating. --}}
                <div class="hidden lg:grid lg:grid-cols-[minmax(0,1fr)_6rem_11rem_7.5rem] lg:items-center lg:gap-4 px-5 py-2 border-b border-neutral-100 text-[11px] font-bold uppercase tracking-wider text-neutral-500">
                    <span>Stall</span>
                    <span>Rating</span>
                    <span>Staff</span>
                    <span class="sr-only">Actions</span>
                </div>

                {{-- Stalls List --}}
                <div id="stalls-list-container" class="divide-y divide-neutral-100">
                    @forelse($stalls as $stall)
                        @php
                            $stallScore = $results->get($stall->id);
                            $avgRating = null;
                            if ($stallScore) {
                                $avgRating = ($stallScore->cleanliness + $stallScore->service + $stallScore->taste + $stallScore->price) / 4;
                            }
                            $assignedStaffList = $stallStaffMap->get($stall->id, collect());
                            $staffCount = $assignedStaffList->count();
                            $staffIdsJson = json_encode($assignedStaffList->pluck('id')->toArray());
                            $staffRosterJson = json_encode($assignedStaffList->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'email' => $s->email])->toArray());
                            $allStaffNames = $assignedStaffList->pluck('name')->join(', ');
                        @endphp
                        {{-- CHANGED: one row at every width. The actions used to drop to a second line on phones,
                             which cost a whole row of height per stall; icon-only buttons fit beside the content. --}}
                        {{-- CHANGED: from lg up the row is a four-column grid - stall, rating, staff, actions - so
                             each field sits at the same x position down the whole list and an admin can scan one
                             attribute at a time. Rating and staff used to trail the stall name on a shared line, so
                             they started at a different place in every row. The two wrappers below become
                             lg:contents, which promotes their children to grid items without changing the phone
                             layout, where the row stays a flex line with the meta pair wrapping under the name. --}}
                        <div class="stall-item flex items-start justify-between py-3.5 px-4 gap-2.5 lg:grid lg:grid-cols-[minmax(0,1fr)_6rem_11rem_7.5rem] lg:items-center lg:gap-4 lg:px-5 hover:bg-neutral-50/70 transition-colors" data-stall-name="{{ strtolower($stall->name) }}">
                            <div class="flex items-start gap-2.5 min-w-0 flex-1 flex-wrap lg:contents">
                                {{-- Cell 1 from lg up: the stall's identity. --}}
                                {{-- CHANGED: dropped the storefront icon repeated on every row and the "Active" badge every active stall carried; only "Inactive" shows, in the neutral metadata badge. The name and badge share one line that never wraps, so every row is the same height. --}}
                                <div class="flex items-center gap-2 min-w-0">
                                    {{-- CHANGED: 12px to 14px. The stall name is the row's identity and was set at the same size as its own metadata, so no element led. --}}
                                    <span class="text-sm font-bold text-neutral-900 truncate" title="{{ $stall->name }}">{{ $stall->name }}</span>
                                    @unless($stall->is_active)
                                        <span class="shrink-0 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-white text-neutral-700 border border-neutral-300">Inactive</span>
                                    @endunless
                                </div>

                                {{-- CHANGED: on phones this meta pair wraps to its own full-width line under the stall name. From
                                     lg up it is lg:contents, so the rating and the staff control become grid cells 2 and 3. --}}
                                    <div class="flex items-center gap-x-3 gap-y-1 text-[11px] text-neutral-500 flex-wrap w-full lg:w-auto lg:contents">
                                        {{-- Rating --}}
                                        {{-- CHANGED: rating colours use the overview's status set (emerald-700 / amber-700 / rose-600) instead of brand-700 / amber-700 / red-600, and the amber star ion-icon became the ★ glyph used on the overview. "No ratings yet" darkened from neutral-400 to neutral-500. --}}
                                        @if($avgRating !== null)
                                            <span class="inline-flex items-center gap-0.5 font-bold tabular-nums {{ $avgRating >= 4 ? 'text-emerald-700' : ($avgRating >= 3 ? 'text-amber-700' : 'text-rose-600') }}">
                                                {{ number_format($avgRating, 2) }}★ / 5.00
                                            </span>
                                        @else
                                            {{-- CHANGED: "No ratings yet" on nearly every row became a dash, with the words kept for screen readers. --}}
                                            <span class="text-neutral-600"><span aria-hidden="true">-</span><span class="sr-only">No ratings yet</span></span>
                                        @endif

                                        {{-- CHANGED: hidden from lg up, where the rating and the staff control are
                                             separate columns and a separator between them would be noise. --}}
                                        <span class="text-neutral-300 lg:hidden">•</span>

                                        {{-- Scalable Compact Staff Presentation with Interactive Roster --}}
                                        {{-- CHANGED: the roster pills and "Assign staff" were ~20px tall, under the 24px
                                             minimum. They are py-1.5 on phones and keep the compact py-0.5 from sm up. --}}
                                        {{-- CHANGED (all four staff buttons): JS arguments now use Js::from() instead of quoted addslashes() output. addslashes does not escape line breaks, so a stall whose description has a newline broke the whole onclick handler. The roster buttons now also pass description and is_active (see openRosterModal). Text raised from 10px/9px to 11px, rounded to rounded-md, and "Assign staff" darkened from neutral-400 to neutral-500. --}}
                                        {{-- CHANGED: emphasis flipped. Unassigned was faint grey text and assigned staff a filled green pill; now "Assign staff" is an amber outlined button, the state that needs action, and assigned staff is plain text. The three assigned branches (1, 2, 3+ names) became one: the first name in full plus "+N", with every name in the tooltip and the roster on click. --}}
                                        @if($staffCount === 0)
                                            <button type="button" onclick="openEditModal({{ $stall->id }}, {{ Js::from($stall->name) }}, {{ $staffIdsJson }}, {{ Js::from($stall->description ?? '') }}, {{ $stall->is_active ? 1 : 0 }})"
                                                class="inline-flex items-center gap-1 px-2 py-1.5 sm:py-0.5 rounded-md text-[11px] font-semibold bg-white hover:bg-amber-50 text-amber-900 border border-amber-300 cursor-pointer transition-colors">
                                                <ion-icon name="person-add-outline" class="text-xs" aria-hidden="true"></ion-icon>
                                                <span>Assign staff</span>
                                            </button>
                                        @else
                                            <button type="button" onclick="openRosterModal({{ $stall->id }}, {{ Js::from($stall->name) }}, {{ $staffRosterJson }}, {{ Js::from($stall->description ?? '') }}, {{ $stall->is_active ? 1 : 0 }})"
                                                class="inline-flex items-center gap-1 min-w-0 max-w-full py-1.5 sm:py-0 text-[11px] font-semibold text-neutral-800 hover:text-brand-800 hover:underline underline-offset-2 cursor-pointer transition-colors"
                                                title="{{ $allStaffNames }}" aria-label="Staff roster for {{ $stall->name }}: {{ $allStaffNames }}">
                                                <span class="truncate">{{ $assignedStaffList->first()->name }}</span>
                                                @if($staffCount > 1)
                                                    <span class="shrink-0 text-neutral-600">+{{ $staffCount - 1 }}</span>
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                            </div>

                            {{-- CHANGED: QR, Edit and Delete are 36px icon buttons at every width (labels in aria-label and title). Delete is neutral at rest and turns red only on hover, so a column of red buttons does not become the loudest thing in the table; it still opens the confirmation dialog, and tells it whether the stall has evaluations. --}}
                            <div class="flex items-center gap-1.5 shrink-0 lg:justify-end">
                                {{-- ADDED: opens the stall's QR code page (print or download) in a new tab. --}}
                                <a href="{{ route('admin.stall.qr', $stall->id) }}" target="_blank" rel="noopener"
                                    aria-label="QR code for {{ $stall->name }}" title="QR code"
                                    class="w-9 h-9 inline-flex items-center justify-center text-neutral-700 hover:text-brand-800 bg-white hover:bg-neutral-50 rounded-md border border-neutral-200 hover:border-brand-300 transition-colors cursor-pointer">
                                    <ion-icon name="qr-code-outline" class="text-base" aria-hidden="true"></ion-icon>
                                </a>

                                {{-- Edit Button --}}
                                {{-- CHANGED: Js::from() instead of addslashes for the name and description arguments (same newline fix as the staff buttons). --}}
                                <button type="button"
                                    onclick="openEditModal({{ $stall->id }}, {{ Js::from($stall->name) }}, {{ $staffIdsJson }}, {{ Js::from($stall->description ?? '') }}, {{ $stall->is_active ? 1 : 0 }})"
                                    aria-label="Edit {{ $stall->name }}" title="Edit"
                                    class="w-9 h-9 inline-flex items-center justify-center text-neutral-700 hover:text-brand-800 bg-white hover:bg-neutral-50 rounded-md border border-neutral-200 hover:border-brand-300 transition-colors cursor-pointer">
                                    <ion-icon name="pencil-outline" class="text-base" aria-hidden="true"></ion-icon>
                                </button>
                                <button type="button"
                                    onclick="openDeleteModal({{ $stall->id }}, {{ Js::from($stall->name) }}, {{ $results->has($stall->id) ? 1 : 0 }})"
                                    aria-label="Delete {{ $stall->name }}" title="Delete"
                                    class="w-9 h-9 inline-flex items-center justify-center text-neutral-700 hover:text-rose-700 bg-white hover:bg-rose-50 rounded-md border border-neutral-200 hover:border-rose-300 transition-colors cursor-pointer">
                                    <ion-icon name="trash-outline" class="text-base" aria-hidden="true"></ion-icon>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 px-6 text-center">
                            {{-- CHANGED: empty-state icon was neutral-400 at 40% opacity on neutral-100 (nearly invisible); now brand-700 on brand-50, like the overview's empty state. --}}
                            <div class="w-10 h-10 rounded-md flex items-center justify-center mx-auto mb-2.5 bg-brand-50 text-brand-700">
                                <ion-icon name="storefront-outline" class="text-2xl text-brand-700"></ion-icon>
                            </div>
                            <p class="text-xs font-bold text-neutral-700 mb-0.5">No stalls registered yet</p>
                            {{-- CHANGED: the form is above this list on a phone, not to the left, so the copy names the panel instead of a position. --}}
                            <p class="text-[11px] text-neutral-500 max-w-xs mx-auto">Open the Add New Stall panel to register your first canteen vendor.</p>
                        </div>
                    @endforelse
                </div>

                {{-- No search results message --}}
                <div id="no-search-results" class="hidden py-8 px-6 text-center">
                    <p class="text-xs font-semibold text-neutral-500">No matching stalls found.</p>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- ── 4. Edit Stall Modal (Scalable Searchable Multi-Staff Picker) ──────── --}}
{{-- CHANGED (all five dialogs): rebuilt in the Users page's dialog frame so every admin dialog looks the same. No padding or border on the dialog, a dark green header band (sheet-head) with a tile, the title and a pale green subtitle, the content padded below it, and a hairline-topped row of small plain-text buttons (sheet-actions). Labels are sentence case at 12px (were uppercase 11px), fields take the Users inputs (grey fill, white on focus, green ring), and the Delete and Remove dialogs gained the X button the others have. Below 640px each dialog is a bottom sheet with the header and the button row pinned while the content scrolls. The "relative" class is removed: a modal dialog with position relative is laid out against the top of the page, so on a scrolled phone it opened out of view; the fixed dialog still anchors the loader bar. Inputs and selects are 16px on phones (were 12px), so iOS Safari no longer zooms on focus. The Edit dialog keeps its wider max-w-lg for the staff picker. --}}
<dialog id="edit-modal" class="confirm-modal max-w-lg sheet-on-phone p-0! border-0!" aria-labelledby="edit-modal-title">
    {{-- Indeterminate Modal Progress Bar --}}
    <div id="edit-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="create-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="edit-modal-title" class="text-sm font-bold text-white leading-tight">Edit Food Stall</h3>
                <p class="text-[11px] text-brand-200 truncate">Update vendor details &amp; assigned staff members</p>
            </div>
        </div>
        <button type="button" id="edit-modal-close-btn" class="text-white/70 enabled:hover:text-white js-close-edit-modal p-1 cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <form id="edit-form" method="POST" class="px-6 pb-6 max-sm:pb-0">
        @csrf
        @method('PUT')

        <div class="mt-5 space-y-4 text-left">
            <div>
                <label for="edit-stall-name-input" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Stall name <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="edit-stall-name-input" name="name"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors"
                    required>
            </div>

            {{-- Searchable Staff Picker in Modal --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-neutral-900">
                        Assigned staff members
                    </label>
                    {{-- CHANGED: 10px to 11px (page minimum). --}}
                    <span id="edit-staff-count" class="text-[11px] font-bold text-brand-700">0 selected</span>
                </div>

                @if($staffUsers->isEmpty())
                    <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-md text-center">
                        <p class="text-[11px] text-neutral-500">No staff accounts registered yet.</p>
                    </div>
                @else
                    {{-- Search Bar & Filter Chips in Modal --}}
                    <div class="space-y-2 mb-2">
                        <div class="relative">
                            <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-xs pointer-events-none"></ion-icon>
                            <input type="text" id="edit-staff-search" placeholder="Filter staff by name or email…"
                                class="w-full pl-7 pr-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                        </div>

                        {{-- Filter Tabs --}}
                        {{-- CHANGED: tabs raised from 10px to 11px, rounded to rounded-md, the active tab recoloured from neutral-900 to brand-700 (the one accent), and the ⭐ emoji dropped from "Unassigned". filterModalStaff() applies the same classes. --}}
                        {{-- CHANGED: py-1.5 on phones, where these were ~20px tall inside a modal; they wrap instead of overflowing a 320px dialog. The same classes in filterModalStaff() moved with them. --}}
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <button type="button" id="tab-all-staff" onclick="filterModalStaff('all')" class="px-2 py-1.5 sm:py-0.5 rounded-md font-bold bg-brand-700 text-white transition-colors cursor-pointer">All ({{ $staffUsers->count() }})</button>
                            <button type="button" id="tab-unassigned-staff" onclick="filterModalStaff('unassigned')" class="px-2 py-1.5 sm:py-0.5 rounded-md font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer">Unassigned ({{ $unassignedStaff->count() }})</button>
                            <button type="button" id="tab-selected-staff" onclick="filterModalStaff('selected')" class="px-2 py-1.5 sm:py-0.5 rounded-md font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer">Selected Only</button>
                        </div>
                    </div>

                    {{-- Scrollable Staff Checkbox List --}}
                    <div id="edit-staff-list" class="max-h-48 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-200 rounded-md space-y-1 divide-y divide-neutral-200/40 custom-scrollbar">
                        @foreach($staffUsers as $staff)
                            <label class="edit-staff-item flex items-center justify-between p-2 rounded hover:bg-white border border-transparent hover:border-neutral-200 transition-colors cursor-pointer text-xs"
                                id="edit-staff-label-{{ $staff->id }}"
                                data-staff-id="{{ $staff->id }}"
                                data-staff-name="{{ strtolower($staff->name) }}"
                                data-staff-email="{{ strtolower($staff->email) }}"
                                data-is-unassigned="{{ $staff->stall_id ? '0' : '1' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" id="edit-staff-{{ $staff->id }}"
                                        onchange="updateEditStaffCount()"
                                        class="edit-staff-checkbox rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                                    {{-- CHANGED: email darkened from neutral-400 to neutral-500 for contrast. --}}
                                    <div class="min-w-0">
                                        <span class="font-bold text-neutral-900 truncate block text-xs">{{ $staff->name }}</span>
                                        {{-- CHANGED: email 11px to 12px, neutral-500 to neutral-600. --}}
                                        <span class="text-xs text-neutral-600 truncate block font-mono">{{ $staff->email }}</span>
                                    </div>
                                </div>
                                {{-- CHANGED: tags raised from 10px to 11px and rounded to rounded-md. --}}
                                <div class="shrink-0 text-right">
                                    {{-- CHANGED: same tags as the Add form: amber "Unassigned", neutral "Assigned to …". --}}
                                    @if(!$staff->stall_id)
                                        <span class="px-2 py-0.5 inline-flex items-center rounded-md bg-white text-neutral-700 border border-neutral-200 text-[11px] font-semibold"><span class="w-1.5 h-1.5 mr-1 rounded-full shrink-0 bg-amber-500" aria-hidden="true"></span>Unassigned</span>
                                    @else
                                        <span id="staff-stall-tag-{{ $staff->id }}" class="px-2 py-0.5 rounded-md bg-white text-neutral-700 border border-neutral-300 text-[11px] font-semibold truncate max-w-[10rem] inline-block" title="Assigned to {{ $staff->current_stall_name }}">
                                            Assigned to {{ $staff->current_stall_name }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                {{-- CHANGED: helper text raised from 10px neutral-400 to 11px neutral-500. --}}
                {{-- CHANGED: neutral-500 to neutral-600 for contrast. --}}
                <p class="text-[11px] text-neutral-600 mt-1">Checking a staff member assigned elsewhere will reassign them to this stall.</p>
            </div>

            <div>
                <label for="edit-stall-desc-input" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Description
                </label>
                <textarea id="edit-stall-desc-input" name="description" rows="2"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="edit-stall-active-input" name="is_active" value="1"
                    class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                <label for="edit-stall-active-input" class="text-xs font-semibold text-neutral-800 cursor-pointer">
                    Stall is active &amp; open for student evaluations
                </label>
            </div>
        </div>

        {{-- CHANGED: the Delete button left this footer and went back to the directory row. --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" id="edit-modal-cancel-btn" class="btn btn-ghost btn-sm text-xs rounded-md js-close-edit-modal cursor-pointer">Cancel</button>
            <button type="submit" id="edit-modal-submit-btn" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Save Changes</button>
        </div>
    </form>
</dialog>

{{-- ── 5. Dedicated Stall Staff Roster Modal (Scrollable at Scale) ───────── --}}
<dialog id="roster-modal" class="confirm-modal max-w-md sheet-on-phone p-0! border-0!" aria-labelledby="roster-modal-title">
    {{-- Indeterminate Modal Progress Bar --}}
    <div id="roster-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="people-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="roster-modal-title" class="text-sm font-bold text-white leading-tight">Staff Roster</h3>
                <p id="roster-modal-subtitle" class="text-[11px] text-brand-200 truncate">Assigned members for this vendor</p>
            </div>
        </div>
        <button type="button" id="roster-modal-close-btn" class="text-white/70 enabled:hover:text-white js-close-roster-modal p-1 cursor-pointer" aria-label="Close roster">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="px-6 pb-6 max-sm:pb-0">
        <div class="mt-5 space-y-4">
            {{-- Filter within Roster --}}
            <div class="relative">
                <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-xs pointer-events-none"></ion-icon>
                <input type="text" id="roster-search-input" placeholder="Search members in roster…"
                    class="w-full pl-7 pr-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
            </div>

            {{-- Scrollable List of Assigned Staff --}}
            <div id="roster-staff-list" class="max-h-60 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-200 rounded-md space-y-1.5 custom-scrollbar">
                <!-- Rendered dynamically by JavaScript -->
            </div>

            {{-- Hidden Form for Unassign Action --}}
            <form id="unassign-staff-form" action="{{ route('admin.staff.unassign') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="staff_id" id="unassign-staff-id">
            </form>
        </div>

        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-between gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" id="roster-modal-cancel-btn" class="btn btn-ghost btn-sm text-xs rounded-md js-close-roster-modal cursor-pointer">Close</button>
            <button type="button" id="roster-edit-stall-btn" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Edit All Assignments</button>
        </div>
    </div>
</dialog>

{{-- ── 6. Quick Assign Staff Modal ───────────────────────────────────────── --}}
<dialog id="quick-assign-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="quick-assign-title">
    {{-- Indeterminate Modal Progress Bar --}}
    <div id="quick-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="person-add-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="quick-assign-title" class="text-sm font-bold text-white leading-tight">Quick Assign Staff</h3>
                <p class="text-[11px] text-brand-200 truncate">Link unassigned staff directly to a food stall</p>
            </div>
        </div>
        <button type="button" id="quick-modal-close-btn" class="text-white/70 enabled:hover:text-white js-close-quick-modal p-1 cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <form id="quick-assign-form" action="{{ route('admin.staff.assign') }}" method="POST" class="px-6 pb-6 max-sm:pb-0">
        @csrf

        <div class="mt-5 space-y-4 text-left">
            <div>
                <label for="quick-staff-id" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Staff member <span class="text-rose-600">*</span>
                </label>
                <select id="quick-staff-id" name="staff_id" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">-- Select Staff Account --</option>
                    @foreach($unassignedStaff as $unStaff)
                        <option value="{{ $unStaff->id }}">{{ $unStaff->name }} ({{ $unStaff->email }})</option>
                    @endforeach
                    @foreach($staffUsers->whereNotNull('stall_id') as $asStaff)
                        <option value="{{ $asStaff->id }}">{{ $asStaff->name }} (Currently: {{ $asStaff->current_stall_name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="quick-stall-id" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Target food stall <span class="text-rose-600">*</span>
                </label>
                <select id="quick-stall-id" name="stall_id" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">-- Select Food Stall --</option>
                    @foreach($stalls as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" id="quick-modal-cancel-btn" class="btn btn-ghost btn-sm text-xs rounded-md js-close-quick-modal cursor-pointer">Cancel</button>
            <button type="submit" id="quick-modal-submit-btn" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Assign Staff</button>
        </div>
    </form>
</dialog>

{{-- ── 7. Delete Confirmation Modal ─────────────────────────────────────── --}}
{{-- CHANGED: one delete form instead of a hidden form per row; openDeleteModal() sets its action. --}}
<form id="delete-stall-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
{{-- CHANGED: the stall's name is the header subtitle, and the warning is the Users delete dialog's rose panel. "Yes, Delete" became "Delete Stall", as the Users dialog says "Delete Account". --}}
<dialog id="delete-confirm-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="delete-modal-title">
    {{-- Indeterminate Modal Progress Bar --}}
    <div id="delete-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="trash-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="delete-modal-title" class="text-sm font-bold text-white leading-tight">Delete Stall</h3>
                <p id="delete-stall-name" class="text-[11px] text-brand-200 truncate"></p>
            </div>
        </div>
        <button type="button" id="delete-modal-close-btn" class="text-white/70 enabled:hover:text-white js-close-delete-modal p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="px-6 pb-6 max-sm:pb-0">
        <div class="mt-5 rounded-lg px-4 py-3.5 bg-rose-50">
            <p class="text-xs font-semibold text-rose-900">This cannot be undone.</p>
            {{-- CHANGED: the evaluation-records sentence is its own span, hidden by openDeleteModal() for a stall with no evaluations; "cannot be undone" always shows. --}}
            <p class="text-xs text-rose-800 mt-0.5 leading-relaxed">
                The stall is permanently deleted.
                <span id="delete-stall-evals-note">This will remove all associated evaluation records.</span>
            </p>
        </div>

        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" id="delete-modal-cancel-btn" class="btn btn-ghost btn-sm text-xs rounded-md js-close-delete-modal cursor-pointer">Cancel</button>
            <button type="button" id="confirm-delete-btn" class="btn btn-sm text-xs font-semibold rounded-md bg-rose-600 hover:bg-rose-700 text-white cursor-pointer">Delete Stall</button>
        </div>
    </div>
</dialog>

{{-- CHANGED: added a styled confirmation dialog for removing a staff member from a stall; it replaces the browser's native confirm() popup, which did not match the other modals on this page. --}}
{{-- CHANGED: "Yes, Remove" became "Remove Staff", and the dialog gained an X button. --}}
<dialog id="unassign-confirm-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="unassign-modal-title">
    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="person-remove-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <h3 id="unassign-modal-title" class="text-sm font-bold text-white leading-tight">Remove Staff Member</h3>
        </div>
        <button type="button" id="unassign-modal-close-btn" class="text-white/70 enabled:hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="px-6 pb-6 max-sm:pb-0">
        <p class="mt-5 text-xs text-neutral-700 leading-relaxed">
            Remove <strong id="unassign-staff-name" class="text-neutral-900 font-semibold"></strong> from <strong id="unassign-stall-name" class="text-neutral-900 font-semibold"></strong>? The staff account will become unassigned.
        </p>

        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" id="unassign-modal-cancel-btn" class="btn btn-ghost btn-sm text-xs rounded-md cursor-pointer">Cancel</button>
            <button type="button" id="confirm-unassign-btn" class="btn btn-sm text-xs font-semibold rounded-md bg-rose-600 hover:bg-rose-700 text-white cursor-pointer">Remove Staff</button>
        </div>
    </div>
</dialog>

@section('scripts')
<script>
// Helper for spinning SVG icon
function makeSpinnerSvg(sizeClass = 'h-3.5 w-3.5') {
    return `<svg class="animate-spin ${sizeClass} inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>`;
}

// ── Global Flag to Prevent Modal Closing During Active Requests ──────────
var isSubmittingAction = false;

// ── Collapse the Add New Stall panel on phones ────────────────────────────
// ADDED: the panel ships open so it is expanded on desktop and still usable without JavaScript. Below the
// lg breakpoint it collapses to its summary row, unless the form came back with a validation error to show.
(function () {
    var addPanel = document.getElementById('add-stall-panel');
    if (addPanel && !addPanel.dataset.hasError && window.matchMedia('(max-width: 1023px)').matches) {
        addPanel.open = false;
    }
})();

// ── Quick Filter Stalls in Directory ──────────────────────────────────────
var searchInput = document.getElementById('stall-search-input');
var stallItems  = document.querySelectorAll('.stall-item');
var noResults   = document.getElementById('no-search-results');

if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        var query = e.target.value.toLowerCase().trim();
        var visibleCount = 0;

        stallItems.forEach(function(item) {
            var name = item.getAttribute('data-stall-name') || '';
            if (name.includes(query)) {
                // CHANGED: '' instead of 'flex', so a matching row falls back to its classes; 'flex' broke the lg grid row layout while filtering.
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 && query.length > 0 ? 'block' : 'none';
        }
    });
}

// ── Add Stall Form Submission with Loader ─────────────────────────────────
var addStallForm   = document.getElementById('add-stall-form');
var addStallBtn    = document.getElementById('add-stall-submit-btn');
var addStaffSearch = document.getElementById('add-staff-search');
var addStaffItems  = document.querySelectorAll('.add-staff-item');
var addStaffCbs    = document.querySelectorAll('.add-staff-checkbox');
var addCountEl     = document.getElementById('add-staff-selected-count');

if (addStaffSearch) {
    addStaffSearch.addEventListener('input', function(e) {
        var q = e.target.value.toLowerCase().trim();
        addStaffItems.forEach(function(item) {
            var name = item.getAttribute('data-staff-name') || '';
            var email = item.getAttribute('data-staff-email') || '';
            item.style.display = (name.includes(q) || email.includes(q)) ? 'flex' : 'none';
        });
    });
}

addStaffCbs.forEach(function(cb) {
    cb.addEventListener('change', function() {
        var checked = document.querySelectorAll('.add-staff-checkbox:checked').length;
        if (addCountEl) addCountEl.textContent = checked + ' selected';
    });
});

if (addStallForm) {
    addStallForm.addEventListener('submit', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
            return;
        }
        isSubmittingAction = true;
        if (addStallBtn) {
            addStallBtn.disabled = true;
            addStallBtn.classList.add('btn-loading');
            addStallBtn.innerHTML = makeSpinnerSvg('h-3.5 w-3.5 text-white') + ' <span>Creating Stall…</span>';
        }
    });
}

// ── Edit Modal & Scalable Staff Selector ─────────────────────────────────
var editModal       = document.getElementById('edit-modal');
var editForm        = document.getElementById('edit-form');
var editNameInput   = document.getElementById('edit-stall-name-input');
var editDescInput   = document.getElementById('edit-stall-desc-input');
var editActiveInput = document.getElementById('edit-stall-active-input');
var editStaffSearch = document.getElementById('edit-staff-search');
var editSubmitBtn   = document.getElementById('edit-modal-submit-btn');
var editCancelBtn   = document.getElementById('edit-modal-cancel-btn');
var editCloseBtn    = document.getElementById('edit-modal-close-btn');
var editLoader      = document.getElementById('edit-modal-loader');
var currentFilterTab = 'all';

function openEditModal(stallId, stallName, staffIdsArray, desc, isActive) {
    if (isSubmittingAction) return;

    editForm.action = '/admin/stall/' + stallId;
    editNameInput.value = stallName;
    editDescInput.value = desc || '';
    editActiveInput.checked = isActive === 1;

    // Reset loader & buttons
    if (editLoader) editLoader.classList.add('hidden');
    if (editSubmitBtn) {
        editSubmitBtn.disabled = false;
        editSubmitBtn.classList.remove('btn-loading');
        editSubmitBtn.innerHTML = 'Save Changes'; // CHANGED: plain text, as in the markup (was icon plus span)
    }
    if (editCancelBtn) editCancelBtn.disabled = false;
    if (editCloseBtn) editCloseBtn.disabled = false;

    // Reset search & filter tabs
    if (editStaffSearch) editStaffSearch.value = '';
    filterModalStaff('all');

    // Reset all staff checkboxes
    document.querySelectorAll('.edit-staff-checkbox').forEach(function(cb) {
        cb.checked = false;
    });

    // Check currently assigned staff
    if (Array.isArray(staffIdsArray)) {
        staffIdsArray.forEach(function(id) {
            var cb = document.getElementById('edit-staff-' + id);
            if (cb) cb.checked = true;
        });
    }

    updateEditStaffCount();
    editModal.showModal();
}

function updateEditStaffCount() {
    var checked = document.querySelectorAll('.edit-staff-checkbox:checked').length;
    var countEl = document.getElementById('edit-staff-count');
    if (countEl) countEl.textContent = checked + ' selected';
}

function filterModalStaff(tab) {
    currentFilterTab = tab;
    
    // Tab button styles
    var btnAll = document.getElementById('tab-all-staff');
    var btnUn = document.getElementById('tab-unassigned-staff');
    var btnSel = document.getElementById('tab-selected-staff');

    if (btnAll && btnUn && btnSel) {
        // CHANGED: same tab classes as the markup: rounded-md, and brand-700 instead of neutral-900 for the active tab.
        btnAll.className = tab === 'all' ? 'px-2 py-1.5 sm:py-0.5 rounded-md font-bold bg-brand-700 text-white transition-colors cursor-pointer' : 'px-2 py-1.5 sm:py-0.5 rounded-md font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
        btnUn.className = tab === 'unassigned' ? 'px-2 py-1.5 sm:py-0.5 rounded-md font-bold bg-brand-700 text-white transition-colors cursor-pointer' : 'px-2 py-1.5 sm:py-0.5 rounded-md font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
        btnSel.className = tab === 'selected' ? 'px-2 py-1.5 sm:py-0.5 rounded-md font-bold bg-brand-700 text-white transition-colors cursor-pointer' : 'px-2 py-1.5 sm:py-0.5 rounded-md font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
    }

    applyModalStaffVisibility();
}

function applyModalStaffVisibility() {
    var q = editStaffSearch ? editStaffSearch.value.toLowerCase().trim() : '';
    var items = document.querySelectorAll('.edit-staff-item');

    items.forEach(function(item) {
        var name = item.getAttribute('data-staff-name') || '';
        var email = item.getAttribute('data-staff-email') || '';
        var isUn = item.getAttribute('data-is-unassigned') === '1';
        var cb = item.querySelector('.edit-staff-checkbox');
        var isChecked = cb && cb.checked;

        var matchesQuery = name.includes(q) || email.includes(q);
        var matchesTab = true;

        if (currentFilterTab === 'unassigned') {
            matchesTab = isUn || isChecked;
        } else if (currentFilterTab === 'selected') {
            matchesTab = isChecked;
        }

        item.style.display = (matchesQuery && matchesTab) ? 'flex' : 'none';
    });
}

if (editStaffSearch) {
    editStaffSearch.addEventListener('input', applyModalStaffVisibility);
}

// Handle Form Submit on Edit Modal
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
            return;
        }
        isSubmittingAction = true;

        // Show Loader & Lock Modal
        if (editLoader) editLoader.classList.remove('hidden');
        if (editSubmitBtn) {
            editSubmitBtn.disabled = true;
            editSubmitBtn.classList.add('btn-loading');
            editSubmitBtn.innerHTML = makeSpinnerSvg('h-3.5 w-3.5 text-white') + ' <span>Saving Changes…</span>';
        }
        if (editCancelBtn) editCancelBtn.disabled = true;
        if (editCloseBtn) editCloseBtn.disabled = true;
    });
}

document.querySelectorAll('.js-close-edit-modal').forEach(function(b) {
    b.addEventListener('click', function() {
        if (!isSubmittingAction) editModal.close();
    });
});

editModal.addEventListener('click', function(e) {
    if (isSubmittingAction) return; // Prevent closing while loader is running
    var r = editModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        editModal.close();
    }
});

editModal.addEventListener('cancel', function(e) {
    if (isSubmittingAction) {
        e.preventDefault(); // Prevent ESC dismissal while loading
    }
});

// ── Stall Staff Roster Modal ─────────────────────────────────────────────
var rosterModal       = document.getElementById('roster-modal');
var rosterTitle       = document.getElementById('roster-modal-title');
var rosterSubtitle    = document.getElementById('roster-modal-subtitle');
var rosterList        = document.getElementById('roster-staff-list');
var rosterSearch      = document.getElementById('roster-search-input');
var rosterEditBtn     = document.getElementById('roster-edit-stall-btn');
var rosterCancelBtn   = document.getElementById('roster-modal-cancel-btn');
var rosterCloseBtn    = document.getElementById('roster-modal-close-btn');
var rosterLoader      = document.getElementById('roster-modal-loader');
var unassignForm      = document.getElementById('unassign-staff-form');
var unassignStaffIdEl = document.getElementById('unassign-staff-id');
var currentRosterStallId = null;
var currentRosterStallName = '';
var currentRosterStaffData = [];

// CHANGED: takes the stall's description and is_active so "Edit All Assignments" can pass them on. Before, it opened the edit modal with an empty description and the Active box ticked, so saving from there erased the description and reactivated an inactive stall.
function openRosterModal(stallId, stallName, staffMembersArray, desc, isActive) {
    if (isSubmittingAction) return;

    currentRosterStallId = stallId;
    currentRosterStallName = stallName;
    currentRosterStaffData = Array.isArray(staffMembersArray) ? staffMembersArray : [];

    if (rosterLoader) rosterLoader.classList.add('hidden');
    if (rosterCancelBtn) rosterCancelBtn.disabled = false;
    if (rosterCloseBtn) rosterCloseBtn.disabled = false;
    if (rosterEditBtn) rosterEditBtn.disabled = false;

    // CHANGED: em-dash in the modal title replaced with a colon.
    rosterTitle.textContent = 'Staff Roster: ' + stallName;
    rosterSubtitle.textContent = currentRosterStaffData.length + ' assigned ' + (currentRosterStaffData.length === 1 ? 'member' : 'members');
    if (rosterSearch) rosterSearch.value = '';

    renderRosterItems(currentRosterStaffData);

    rosterEditBtn.onclick = function() {
        if (isSubmittingAction) return;
        rosterModal.close();
        var staffIds = currentRosterStaffData.map(function(s) { return s.id; });
        // CHANGED: passes the real description and is_active instead of '' and 1.
        openEditModal(stallId, stallName, staffIds, desc, isActive);
    };

    rosterModal.showModal();
}

function renderRosterItems(list) {
    if (!rosterList) return;
    rosterList.innerHTML = '';

    if (list.length === 0) {
        // CHANGED: neutral-400 to neutral-500 for contrast.
        rosterList.innerHTML = '<div class="p-6 text-center text-neutral-500 text-xs font-medium">No staff members found matching criteria.</div>';
        return;
    }

    list.forEach(function(member) {
        var card = document.createElement('div');
        card.className = 'flex items-center justify-between p-2.5 bg-white rounded-md border border-neutral-200 shadow-2xs';
        // CHANGED: name, email and initial go through escapeHtml(). They were inserted into innerHTML raw, so a staff name containing HTML would run as markup on this admin page. The Remove button no longer uses an inline onclick string; a listener is attached below. Email raised from 10px neutral-400 to 11px neutral-500; Remove button rounded to rounded-md.
        card.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-brand-50 border border-brand-200 text-brand-800 flex items-center justify-center font-bold text-xs shrink-0">
                    ${escapeHtml((member.name || 'U').charAt(0).toUpperCase())}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-neutral-900 truncate">${escapeHtml(member.name)}</h4>
                    <p class="text-[11px] text-neutral-500 truncate font-mono">${escapeHtml(member.email)}</p>
                </div>
            </div>
            <button type="button" id="unassign-btn-${member.id}"
                class="roster-remove-btn text-neutral-500 hover:text-rose-700 bg-neutral-50 hover:bg-rose-50 border border-neutral-200 hover:border-rose-200 px-2.5 py-2.5 sm:py-1 min-h-11 sm:min-h-0 rounded-md text-[11px] font-semibold flex items-center gap-1 transition-colors cursor-pointer"
                title="Remove staff from this stall">
                <ion-icon name="close-circle-outline" class="text-xs"></ion-icon>
                <span>Remove</span>
            </button>
        `;
        card.querySelector('.roster-remove-btn').addEventListener('click', function() {
            triggerUnassignStaff(member.id, member.name);
        });
        rosterList.appendChild(card);
    });
}

// CHANGED: replaced escapeQuotes(), which only escaped single quotes, with escapeHtml() for text inserted into innerHTML.
function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
}

// CHANGED: opens the styled confirmation dialog instead of calling confirm(); the removal itself moved to performUnassignStaff(), which runs when "Yes, Remove" is clicked.
var unassignModal      = document.getElementById('unassign-confirm-modal');
var unassignConfirmBtn = document.getElementById('confirm-unassign-btn');
var unassignCancelBtn  = document.getElementById('unassign-modal-cancel-btn');
var pendingUnassignId  = null;

function triggerUnassignStaff(staffId, staffName) {
    if (isSubmittingAction || !unassignModal) return;

    pendingUnassignId = staffId;
    document.getElementById('unassign-staff-name').textContent = staffName;
    document.getElementById('unassign-stall-name').textContent = currentRosterStallName;
    unassignModal.showModal();
}

if (unassignModal) {
    unassignCancelBtn.addEventListener('click', function() { unassignModal.close(); });
    document.getElementById('unassign-modal-close-btn').addEventListener('click', function() { unassignModal.close(); }); // ADDED: the dialog's new X button
    unassignModal.addEventListener('click', function(e) {
        var r = unassignModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            unassignModal.close();
        }
    });
    unassignConfirmBtn.addEventListener('click', function() {
        unassignModal.close();
        if (pendingUnassignId !== null) performUnassignStaff(pendingUnassignId);
    });
}

function performUnassignStaff(staffId) {
    if (isSubmittingAction) return;

    isSubmittingAction = true;

    // Show Roster Loader & Disable Controls
    if (rosterLoader) rosterLoader.classList.remove('hidden');
    if (rosterCancelBtn) rosterCancelBtn.disabled = true;
    if (rosterCloseBtn) rosterCloseBtn.disabled = true;
    if (rosterEditBtn) rosterEditBtn.disabled = true;

    var clickedBtn = document.getElementById('unassign-btn-' + staffId);
    if (clickedBtn) {
        clickedBtn.disabled = true;
        clickedBtn.innerHTML = makeSpinnerSvg('h-3 w-3 text-rose-600') + ' <span>Removing…</span>';
    }

    document.querySelectorAll('.roster-remove-btn').forEach(function(b) {
        b.disabled = true;
    });

    if (unassignStaffIdEl && unassignForm) {
        unassignStaffIdEl.value = staffId;
        unassignForm.submit();
    }
}

if (rosterSearch) {
    rosterSearch.addEventListener('input', function(e) {
        var q = e.target.value.toLowerCase().trim();
        var filtered = currentRosterStaffData.filter(function(m) {
            return (m.name || '').toLowerCase().includes(q) || (m.email || '').toLowerCase().includes(q);
        });
        renderRosterItems(filtered);
    });
}

document.querySelectorAll('.js-close-roster-modal').forEach(function(b) {
    b.addEventListener('click', function() {
        if (!isSubmittingAction) rosterModal.close();
    });
});

if (rosterModal) {
    rosterModal.addEventListener('click', function(e) {
        if (isSubmittingAction) return;
        var r = rosterModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            rosterModal.close();
        }
    });

    rosterModal.addEventListener('cancel', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
        }
    });
}

// ── Quick Assign Modal ───────────────────────────────────────────────────
var quickModal     = document.getElementById('quick-assign-modal');
var quickForm      = document.getElementById('quick-assign-form');
var quickSubmitBtn = document.getElementById('quick-modal-submit-btn');
var quickCancelBtn = document.getElementById('quick-modal-cancel-btn');
var quickCloseBtn  = document.getElementById('quick-modal-close-btn');
var quickLoader    = document.getElementById('quick-modal-loader');

function openQuickAssignModal() {
    if (isSubmittingAction) return;
    if (quickLoader) quickLoader.classList.add('hidden');
    if (quickSubmitBtn) {
        quickSubmitBtn.disabled = false;
        quickSubmitBtn.classList.remove('btn-loading');
        quickSubmitBtn.innerHTML = 'Assign Staff'; // CHANGED: plain text, as in the markup (was icon plus span)
    }
    if (quickCancelBtn) quickCancelBtn.disabled = false;
    if (quickCloseBtn) quickCloseBtn.disabled = false;
    if (quickModal) quickModal.showModal();
}

if (quickForm) {
    quickForm.addEventListener('submit', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
            return;
        }
        isSubmittingAction = true;

        if (quickLoader) quickLoader.classList.remove('hidden');
        if (quickSubmitBtn) {
            quickSubmitBtn.disabled = true;
            quickSubmitBtn.classList.add('btn-loading');
            quickSubmitBtn.innerHTML = makeSpinnerSvg('h-3.5 w-3.5 text-white') + ' <span>Assigning Staff…</span>';
        }
        if (quickCancelBtn) quickCancelBtn.disabled = true;
        if (quickCloseBtn) quickCloseBtn.disabled = true;
    });
}

document.querySelectorAll('.js-close-quick-modal').forEach(function(b) {
    b.addEventListener('click', function() {
        if (!isSubmittingAction) quickModal.close();
    });
});

if (quickModal) {
    quickModal.addEventListener('click', function(e) {
        if (isSubmittingAction) return;
        var r = quickModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            quickModal.close();
        }
    });

    quickModal.addEventListener('cancel', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
        }
    });
}

// ── Delete Modal ─────────────────────────────────────────────────────────
var deleteModal     = document.getElementById('delete-confirm-modal');
var stallNameEl     = document.getElementById('delete-stall-name');
var confirmDelBtn   = document.getElementById('confirm-delete-btn');
var deleteCancelBtn = document.getElementById('delete-modal-cancel-btn');
var deleteCloseBtn  = document.getElementById('delete-modal-close-btn'); // ADDED
var deleteLoader    = document.getElementById('delete-modal-loader');
var deleteForm      = document.getElementById('delete-stall-form');

// CHANGED: points the one shared delete form at the stall, and hides the evaluation-records sentence when hasEvaluations is 0.
function openDeleteModal(stallId, stallName, hasEvaluations) {
    if (isSubmittingAction) return;
    deleteForm.action = '/admin/stall/' + stallId;
    stallNameEl.textContent = stallName;
    document.getElementById('delete-stall-evals-note').classList.toggle('hidden', !hasEvaluations);

    if (deleteLoader) deleteLoader.classList.add('hidden');
    if (confirmDelBtn) {
        confirmDelBtn.disabled = false;
        confirmDelBtn.classList.remove('btn-loading');
        confirmDelBtn.innerHTML = 'Delete Stall'; // CHANGED: plain text, as in the markup (was icon plus "Yes, Delete")
    }
    if (deleteCancelBtn) deleteCancelBtn.disabled = false;
    if (deleteCloseBtn) deleteCloseBtn.disabled = false; // ADDED: the dialog's new X button

    deleteModal.showModal();
}

document.querySelectorAll('.js-close-delete-modal').forEach(function(b) {
    b.addEventListener('click', function() {
        if (!isSubmittingAction) deleteModal.close();
    });
});

if (deleteModal) {
    deleteModal.addEventListener('click', function(e) {
        if (isSubmittingAction) return;
        var r = deleteModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            deleteModal.close();
        }
    });

    deleteModal.addEventListener('cancel', function(e) {
        if (isSubmittingAction) {
            e.preventDefault();
        }
    });
}

if (confirmDelBtn) {
    confirmDelBtn.addEventListener('click', function() {
        // CHANGED: the confirm button is only reachable after openDeleteModal() set the form action, so only the busy check remains.
        if (isSubmittingAction) return;
        isSubmittingAction = true;

        if (deleteLoader) deleteLoader.classList.remove('hidden');
        confirmDelBtn.disabled = true;
        confirmDelBtn.classList.add('btn-loading');
        confirmDelBtn.innerHTML = makeSpinnerSvg('h-3.5 w-3.5 text-white') + ' <span>Deleting Stall…</span>';
        if (deleteCancelBtn) deleteCancelBtn.disabled = true;
        if (deleteCloseBtn) deleteCloseBtn.disabled = true; // ADDED: the dialog's new X button

        deleteForm.submit();
    });
}

// ADDED: Chrome closes a dialog on a second Esc even when cancel was prevented, so the key itself is blocked while a request runs.
document.addEventListener('keydown', function(e) {
    if (isSubmittingAction && e.key === 'Escape') e.preventDefault();
});
</script>
@endsection
@endsection
