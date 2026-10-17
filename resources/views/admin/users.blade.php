@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Staff & Admin Management | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Staff & Admins" (the sidebar label) instead of the layout default "Dashboard". --}}
@section('header_title', 'Staff & Admins')
{{-- CHANGED (whole file): every rounded-lg and plain rounded became rounded-md (cards stay rounded-xl), every 9px and 10px text became 11px, grey helper and label text went from neutral-400 to neutral-500 for contrast, and every sky-* colour (staff avatars, badges and the staff stat icon) became the matching neutral-* shade, so brand green is the only accent. --}}
@section('content')
{{-- CHANGED: was max-w-7xl mx-auto, which centred the page and left dead space beside the sidebar; same fix as the overview and stalls pages. --}}
<div class="max-w-[1600px] space-y-6">

    {{-- ── Flash Messages (Success handled via layout toast) ────────────── --}}
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200/80 text-red-800 rounded-xl text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-lg text-red-600 shrink-0"></ion-icon>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 transition-colors p-1" aria-label="Dismiss">
                <ion-icon name="close-outline" class="text-base"></ion-icon>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200/80 text-red-800 rounded-xl text-xs font-semibold space-y-1 shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <ion-icon name="alert-circle" class="text-lg text-red-600 shrink-0"></ion-icon>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-red-700 ml-5 font-normal">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CHANGED: the visible "Staff & Administrators" heading and its description repeated the top bar under a second name; the h1 stays for screen readers and matches the top bar, as on the students and evaluations pages. New Account moved into the toolbar below. --}}
    <h1 class="sr-only">Staff &amp; Admins</h1>

    @php
        $currentRole = request('role', 'all');
        $hasFilters = request()->filled('q') || ($currentRole !== 'all') || (request()->filled('per_page') && request('per_page') != 10);
        // CHANGED: the role tabs come from one list instead of five copied links, and read their counts straight from $stats; the separate count variables (with fallbacks that never ran, since a COUNT query always returns a row) only fed the removed stat cards.
        $roleTabs = ['all' => ['All', $stats->total_count], 'admin' => ['Admins', $stats->admin_count], 'clinic' => ['Clinic', $stats->clinic_count], 'staff' => ['Staff', $stats->staff_count], 'unassigned' => ['Unassigned', $stats->unassigned_staff_count]];
    @endphp

    {{-- CHANGED: removed the three stat cards. Every count they showed (total, admins, staff, unassigned) is already on the role tabs, so the table now starts at the top of the page. --}}

    {{-- CHANGED: search, role tabs and the table share one card, as on the students and evaluations pages. The active-filter chips are gone: the tabs and the search box already show the filter, and the footer shows the count. --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
        {{-- CHANGED: the toolbar wraps instead of squeezing. Below 1280px the search box shares the first row with Clear and New Account, and the role tabs get a full-width row of their own that scrolls sideways to the card edge; from 1280px all of it sits on one row. Before, the tabs sat beside the search box from 640px up and crushed it, and on phones New Account took a row to itself. The form is display: contents so its fields join the toolbar's rows; it submits as before. --}}
        <div class="p-4 sm:px-5 border-b border-neutral-200/70 flex flex-wrap items-center gap-2.5">
            <form id="user-filter-form" method="GET" action="{{ route('admin.users') }}" class="contents">
                @if(request('role') && request('role') !== 'all')
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                {{-- Search Input --}}
                <div class="flex-1 relative min-w-0">
                    <label for="user-search-input" class="sr-only">Search accounts</label> {{-- ADDED --}}
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    {{-- CHANGED: 16px text on phones (was 14px everywhere), so iOS Safari no longer zooms the page when the box takes focus. --}}
                    <input type="text" name="q" id="user-search-input" data-live-search value="{{ request('q') }}" placeholder="Search by name, email, or stall…"
                        class="w-full pl-9 pr-9 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-all">
                    @if(request('q'))
                        <a href="{{ route('admin.users', array_filter(['role' => $currentRole !== 'all' ? $currentRole : null, 'per_page' => request('per_page')])) }}"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer" aria-label="Clear search">
                            <ion-icon name="close-circle" class="text-base"></ion-icon>
                        </a>
                    @endif
                </div>

                {{-- Role Filter Tabs --}}
                {{-- CHANGED: the filled pills became the segmented tabs of the students page (white on grey for the active tab, count beside the label). "Unassigned" keeps its amber text and alert icon while any staff wait for a stall. --}}
                {{-- CHANGED: was overflow-x-auto shrink-0 -mx-1 px-1 beside the search box; now its own row below 1280px (see the toolbar note), bleeding to the card edges so a cut-off tab shows the row scrolls. The tabs are 8px taller on phones. --}}
                <div class="order-last xl:order-none w-full xl:w-auto xl:shrink-0 overflow-x-auto -mx-4 px-4 sm:-mx-5 sm:px-5 xl:mx-0 xl:px-0 -my-1 py-1">
                    <nav aria-label="Filter by role" class="inline-flex p-0.5 bg-neutral-100 rounded-md text-xs whitespace-nowrap">
                        @foreach($roleTabs as $key => [$label, $count])
                            @php
                                $active = $currentRole === $key;
                                $warn = $key === 'unassigned' && $count > 0;
                            @endphp
                            <a href="{{ route('admin.users', array_filter(['role' => $key !== 'all' ? $key : null, 'q' => request('q'), 'per_page' => request('per_page')])) }}" @if($active) aria-current="page" @endif
                                class="px-3 py-2 sm:py-1.5 rounded inline-flex items-center gap-1.5 font-semibold transition-colors {{ $active ? 'bg-white text-neutral-900 shadow-2xs' : ($warn ? 'text-amber-800 hover:text-amber-950' : 'text-neutral-600 hover:text-neutral-900') }}">
                                @if($warn)
                                    <ion-icon name="alert-circle" class="text-sm text-amber-600" aria-hidden="true"></ion-icon>
                                @endif
                                {{ $label }}
                                <span class="tabular-nums font-medium {{ $warn && !$active ? 'text-amber-800' : 'text-neutral-500' }}">{{ $count }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>

                @if($hasFilters)
                    {{-- CHANGED: self-start sm:self-auto became self-stretch, so Clear matches the search box's height on the shared row. --}}
                    <a href="{{ route('admin.users') }}" class="self-stretch inline-flex items-center gap-1 px-3 py-2 rounded-md text-sm font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 shrink-0">
                        <ion-icon name="close-outline" class="text-base"></ion-icon>
                        Clear
                    </a>
                @endif
            </form>

            {{-- CHANGED: moved here from the removed page header. It now sits at the end of the search row on every width (was alone on its own row on phones), shows only its icon below 640px so the search box keeps its room, and stretches to the search box's height. --}}
            <button type="button" onclick="openCreateUserModal()" aria-label="New Account"
                class="self-stretch btn btn-primary text-xs font-bold px-4 py-2 rounded-md shadow-2xs flex items-center gap-1.5 cursor-pointer transition-all shrink-0">
                <ion-icon name="person-add-outline" class="text-lg sm:text-sm" aria-hidden="true"></ion-icon>
                <span class="hidden sm:inline">New Account</span>
            </button>
        </div>

        @if($users->isEmpty())
            <div class="p-16 text-center">
                {{-- CHANGED: empty-state icon neutral-400 on neutral-100 to brand-700 on brand-50, like the other admin empty states. --}}
                <div class="w-14 h-14 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center mx-auto mb-3">
                    <ion-icon name="people-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-800">
                    {{ $hasFilters ? 'No accounts found' : 'No staff or admin accounts yet' }}
                </h3>
                <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">
                    {{ $hasFilters ? 'No accounts matched your search or role filters. Try adjusting your search query.' : 'Get started by creating your first administrator or staff account.' }}
                </p>
                @if($hasFilters)
                    <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm text-xs font-bold px-4 py-2 rounded-md mt-4 inline-flex items-center gap-1.5 shadow-2xs">
                        <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
                        <span>Reset Filters</span>
                    </a>
                @else
                    <button type="button" onclick="openCreateUserModal()" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-md mt-4 inline-flex items-center gap-1.5 shadow-2xs">
                        <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                        <span>Create Account</span>
                    </button>
                @endif
            </div>
        @else
            <div id="users-directory-container" class="overflow-x-auto">
                {{-- Desktop Table View --}}
                {{-- CHANGED: columns have set widths so Role no longer takes spare room and Created sits next to the Edit button; "Stall Assignment" became "Stall"; the Actions header stays visible. --}}
                {{-- CHANGED: shown from 1024px instead of 768px, with min-w-[680px] instead of 760px. Between 769px and 1023px the sidebar leaves 480-735px, so the table always scrolled sideways; the account list covers that range now. --}}
                <table id="users-desktop-table" class="w-full text-left border-collapse min-w-[680px] hidden lg:table">
                    <thead>
                        <tr class="border-b border-neutral-200/70 bg-neutral-50/75">
                            <th class="px-5 py-3 w-[38%] text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Account</th>
                            <th class="px-4 py-3 w-[16%] text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 w-[26%] text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Stall</th>
                            {{-- CHANGED: Created shows from 1280px (hidden xl:table-cell), so the four main columns fit 1024px without scrolling. --}}
                            <th class="hidden xl:table-cell px-4 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Created</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100/80" id="users-table-body">
                        {{-- CHANGED: dropped the user-row class and data-* attributes from the rows; no script read them since search moved to the server. --}}
                        @foreach($users as $u)
                            @php
                                $isCurrentUser = $u->id === Auth::id();
                                $isAdmin = $u->role === 'admin';
                                $isClinic = $u->role === 'clinic'; // ADDED
                                $isAssigned = !empty($u->stall_id);
                            @endphp
                            <tr class="hover:bg-neutral-50/70 transition-colors {{ $isCurrentUser ? 'bg-brand-50/20' : '' }}">

                                {{-- User Column --}}
                                {{-- CHANGED: the initial avatar is neutral for every role, as on the students page; green avatars repeated the Administrator badge. The email is 13px plain text instead of 11px monospace, and the whole address is the copy button instead of a 12px icon beside it. --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-600 text-xs font-semibold flex items-center justify-center shrink-0" aria-hidden="true">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-neutral-900 flex items-center gap-1.5 min-w-0">
                                                <span class="truncate">{{ $u->name }}</span>
                                                @if($isCurrentUser)
                                                    <span class="shrink-0 text-[11px] font-semibold text-brand-800 bg-brand-50 px-1.5 py-0.5 rounded-md border border-brand-200/80">You</span>
                                                @endif
                                            </div>
                                            <button type="button" onclick="copyToClipboard({{ Js::from($u->email) }}, this)" title="Copy email"
                                                class="group max-w-full min-h-6 inline-flex items-center gap-1.5 text-[13px] text-neutral-600 hover:text-brand-800 cursor-pointer">
                                                <span class="truncate">{{ $u->email }}</span>
                                                <ion-icon name="copy-outline" class="text-sm shrink-0 text-neutral-400 group-hover:text-brand-700" aria-hidden="true"></ion-icon>
                                                <span class="sr-only" aria-live="polite">Copy email</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role Column --}}
                                {{-- CHANGED: only Administrator keeps a badge; Clinic and Canteen Staff are plain text, so the badge marks the accounts with full access. --}}
                                <td class="px-4 py-3">
                                    @if($isAdmin)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80 whitespace-nowrap">
                                            <ion-icon name="shield-checkmark" class="text-xs text-brand-600" aria-hidden="true"></ion-icon>
                                            <span>Administrator</span>
                                        </span>
                                    @else
                                        <span class="text-[13px] text-neutral-700 whitespace-nowrap">{{ $u->role === 'clinic' ? 'Clinic' : 'Canteen Staff' }}</span>
                                    @endif
                                </td>

                                {{-- Stall Assignment Column --}}
                                <td class="px-4 py-3">
                                    {{-- CHANGED: clinic accounts are system-wide like admins. --}}
                                    @if($isAdmin || $isClinic)
                                        <span class="text-neutral-500 text-[13px] flex items-center gap-1.5">
                                            <ion-icon name="globe-outline" class="text-sm text-neutral-400" aria-hidden="true"></ion-icon>
                                            <span>System-wide</span>
                                        </span>
                                    @elseif($isAssigned)
                                        <div class="flex items-center gap-1.5 text-[13px] font-medium text-neutral-800 min-w-0">
                                            <ion-icon name="storefront-outline" class="text-brand-700 text-sm shrink-0" aria-hidden="true"></ion-icon>
                                            <span class="truncate" title="{{ $u->stall_name }}">{{ $u->stall_name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-300">
                                            <ion-icon name="alert-circle" class="text-xs text-amber-600" aria-hidden="true"></ion-icon>
                                            <span>Unassigned</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Created Date Column --}}
                                {{-- CHANGED: created_at is stored in UTC; shown in Philippine time, as on the printable report. --}}
                                {{-- CHANGED: hidden below 1280px, as its header. --}}
                                <td class="hidden xl:table-cell px-4 py-3 text-[13px] text-neutral-600 whitespace-nowrap tabular-nums">
                                    {{ \Carbon\Carbon::parse($u->created_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y') }}
                                </td>

                                {{-- Actions Column --}}
                                {{-- CHANGED: Edit and Delete pass name and email through Js::from() instead of quoted addslashes() output, the same fix as the stalls page. --}}
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                            onclick="openEditUserModal({{ $u->id }}, {{ Js::from($u->name) }}, {{ Js::from($u->email) }}, '{{ $u->role }}', {{ $u->stall_id ?? 'null' }}, {{ $isCurrentUser ? 'true' : 'false' }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-semibold text-neutral-700 hover:text-neutral-900 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 transition-all cursor-pointer">
                                            <ion-icon name="create-outline" class="text-sm text-neutral-500"></ion-icon>
                                            <span>Edit</span>
                                        </button>

                                        @if(!$isCurrentUser)
                                            <button type="button"
                                                onclick="openDeleteUserModal({{ $u->id }}, {{ Js::from($u->name) }}, '{{ $u->role }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50/50 hover:bg-red-50 border border-red-200/70 transition-all cursor-pointer">
                                                <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                                <span>Delete</span>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-bold bg-neutral-100 text-neutral-500">
                                                Current User
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Mobile Card List View --}}
                {{-- CHANGED: one row per account, as on the students page: neutral avatar, name, the email copy button, then role and stall on one line, with Edit and Delete underneath. The labelled "Assignment:" strip is gone. --}}
                {{-- CHANGED: the list covers everything below 1024px (was below 768px), matching the table. --}}
                <div class="lg:hidden divide-y divide-neutral-100" id="users-mobile-list">
                @foreach($users as $u)
                    @php
                        $isCurrentUser = $u->id === Auth::id();
                        $isAdmin = $u->role === 'admin';
                        $isClinic = $u->role === 'clinic'; // ADDED
                        $isAssigned = !empty($u->stall_id);
                    @endphp
                    {{-- CHANGED: the whole row opens the Edit dialog (a tap on the email copy button still only copies), replacing the Edit and Delete buttons that crowded each row; Delete now lives in the Edit dialog. --}}
                    <div onclick="if (!event.target.closest('button')) this.querySelector('[data-row-edit]').click()"
                        class="p-4 sm:px-5 flex items-start gap-3 cursor-pointer active:bg-neutral-100/70 transition-colors {{ $isCurrentUser ? 'bg-brand-50/20' : '' }}">
                        <span class="w-9 h-9 rounded-full bg-neutral-100 text-neutral-600 text-sm font-semibold flex items-center justify-center shrink-0" aria-hidden="true">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-neutral-900 flex items-center gap-1.5 min-w-0">
                                <span class="truncate">{{ $u->name }}</span>
                                @if($isCurrentUser)
                                    <span class="shrink-0 text-[11px] font-semibold text-brand-800 bg-brand-50 px-1.5 py-0.5 rounded-md border border-brand-200">You</span>
                                @endif
                            </div>
                            <button type="button" onclick="copyToClipboard({{ Js::from($u->email) }}, this)" title="Copy email"
                                class="group max-w-full min-h-6 inline-flex items-center gap-1.5 text-[13px] text-neutral-600 hover:text-brand-800 cursor-pointer">
                                <span class="truncate">{{ $u->email }}</span>
                                <ion-icon name="copy-outline" class="text-sm shrink-0 text-neutral-400 group-hover:text-brand-700" aria-hidden="true"></ion-icon>
                                <span class="sr-only" aria-live="polite">Copy email</span>
                            </button>
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-neutral-600">
                                @if($isAdmin)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-semibold bg-brand-50 text-brand-800 border border-brand-200">
                                        <ion-icon name="shield-checkmark" class="text-xs text-brand-600" aria-hidden="true"></ion-icon>
                                        Administrator
                                    </span>
                                @else
                                    <span>{{ $u->role === 'clinic' ? 'Clinic' : 'Canteen Staff' }}</span>
                                @endif
                                <span class="text-neutral-400" aria-hidden="true">·</span>
                                {{-- CHANGED: clinic accounts are system-wide like admins. --}}
                                @if($isAdmin || $isClinic)
                                    <span class="text-neutral-500">System-wide</span>
                                @elseif($isAssigned)
                                    <span class="font-medium text-neutral-800 inline-flex items-center gap-1 min-w-0">
                                        <ion-icon name="storefront-outline" class="text-brand-700 text-sm shrink-0" aria-hidden="true"></ion-icon>
                                        <span class="truncate">{{ $u->stall_name }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-semibold bg-amber-50 text-amber-900 border border-amber-300">
                                        <ion-icon name="alert-circle" class="text-xs text-amber-600" aria-hidden="true"></ion-icon>
                                        Unassigned
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions for Mobile --}}
                        {{-- CHANGED: Js::from() for the name and email arguments, as in the desktop table. --}}
                        {{-- CHANGED: the Edit and Delete buttons became one 40px chevron button at the row's end. It is the keyboard and screen-reader route to the Edit dialog, and the button the row tap clicks. --}}
                        <button type="button" data-row-edit aria-label="Edit {{ $u->name }}"
                            onclick="openEditUserModal({{ $u->id }}, {{ Js::from($u->name) }}, {{ Js::from($u->email) }}, '{{ $u->role }}', {{ $u->stall_id ?? 'null' }}, {{ $isCurrentUser ? 'true' : 'false' }})"
                            class="self-center -mr-2 w-10 h-10 shrink-0 rounded-md inline-flex items-center justify-center text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 cursor-pointer">
                            <ion-icon name="chevron-forward-outline" class="text-lg" aria-hidden="true"></ion-icon>
                        </button>
                    </div>
                @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            {{-- CHANGED: the count and the per-page picker wrap onto two centred lines on phones (were one unbreakable line that ran past the card below about 400px), and the pager buttons are 40px tall below 640px. --}}
            <div class="px-4 sm:px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                    <span>
                        Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->total() }}</strong> accounts
                    </span>

                    {{-- Per Page Selector --}}
                    {{-- CHANGED: "PER PAGE" uppercase bold became sentence-case "Per page", as on the students and evaluations pages. --}}
                    {{-- CHANGED: the divider shows only from 640px, where the two sit on one line; the select is 16px on phones so iOS does not zoom on tap. --}}
                    <div class="flex items-center gap-1.5 sm:border-l sm:border-neutral-200 sm:pl-3">
                        <label for="per_page_select" class="text-xs text-neutral-500">Per page</label>
                        <select id="per_page_select" onchange="window.location.href = this.value" class="bg-white border border-neutral-200 rounded-md px-2 py-1 text-base sm:text-xs font-semibold focus:outline-none focus:border-brand-600 cursor-pointer">
                            @foreach([10, 25, 50] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $users->perPage() == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Pagination Controls --}}
                <div class="flex items-center gap-1 order-1 sm:order-2">
                    {{-- Previous Page Link --}}
                    @if($users->onFirstPage())
                        <span class="min-h-10 sm:min-h-0 px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="min-h-10 sm:min-h-0 px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="hidden sm:flex items-center gap-1">
                        @if($users->hasPages())
                            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                @if($page == $users->currentPage())
                                    <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                1
                            </span>
                        @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="min-h-10 sm:min-h-0 px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </a>
                    @else
                        <span class="min-h-10 sm:min-h-0 px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ── 5. CREATE ACCOUNT MODAL ─────────────────────────────────────────── --}}
{{-- CHANGED (all three dialogs): below 640px each dialog is a bottom sheet (sheet-on-phone, the student profile's pattern), with the green header (sheet-head) and the Cancel/submit row (sheet-actions) pinned while the fields scroll, so Create Account no longer scrolls out of reach on short phones. The forms drop their bottom padding there because sheet-actions brings its own, safe-area aware. Every input and select is 16px on phones (was 14px), so iOS Safari no longer zooms on focus. --}}
{{-- CHANGED (all three dialogs): rebuilt in the frame of the evaluations and students dialogs. Plain confirm-modal width and shadow with no padding or border (modal-sharp max-w-[400px] dropped), a dark green header band that runs to the edge with a tile, the title and a pale green subtitle, and the content padded below it. The grey footer bar became a hairline-topped row of small buttons inside the body, and inputs are 14px instead of 12px. Each field-error line is written on one line and the submit buttons are plain text; the markup the script reads (data-error-for, the inner span) is unchanged. --}}
<dialog id="create-user-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="create-modal-title">
    <div id="create-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="person-add-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <h3 id="create-modal-title" class="text-sm font-bold text-white leading-tight">Create Account</h3>
        </div>
        <button type="button" onclick="closeCreateUserModal()" class="text-white/70 hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <form id="create-user-form" method="POST" action="{{ route('admin.users.create') }}" class="px-6 pb-6 max-sm:pb-0">
        @csrf

        <div class="mt-5 space-y-4">
            {{-- General Error Box --}}
            <div data-general-error class="general-error-box hidden p-3 bg-rose-50 rounded-lg text-xs text-rose-800 flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
                <span class="leading-tight font-medium"></span>
            </div>

            {{-- Role Selection Segmented Control --}}
            {{-- CHANGED: the segmented control takes the role tabs' look (white on grey for the chosen role) and their short labels, Admin / Staff / Clinic, which fit three across in the narrower dialog. aria-pressed marks the chosen role. --}}
            <div>
                <p class="text-xs font-semibold text-neutral-900 mb-1.5">Account role <span class="text-rose-600">*</span></p>
                <div class="grid grid-cols-3 p-0.5 bg-neutral-100 rounded-md gap-0.5 text-xs">
                    <button type="button" id="create-role-admin-btn" onclick="toggleCreateRole('admin')" aria-pressed="true"
                        class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded font-semibold transition-colors cursor-pointer bg-white text-neutral-900 shadow-2xs">
                        <ion-icon name="shield-checkmark-outline" class="text-sm" aria-hidden="true"></ion-icon>
                        <span>Admin</span>
                    </button>
                    <button type="button" id="create-role-staff-btn" onclick="toggleCreateRole('staff')" aria-pressed="false"
                        class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded font-semibold transition-colors cursor-pointer text-neutral-600 hover:text-neutral-900">
                        <ion-icon name="restaurant-outline" class="text-sm" aria-hidden="true"></ion-icon>
                        <span>Staff</span>
                    </button>
                    {{-- ADDED: clinic role option. --}}
                    <button type="button" id="create-role-clinic-btn" onclick="toggleCreateRole('clinic')" aria-pressed="false"
                        class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded font-semibold transition-colors cursor-pointer text-neutral-600 hover:text-neutral-900">
                        <ion-icon name="medkit-outline" class="text-sm" aria-hidden="true"></ion-icon>
                        <span>Clinic</span>
                    </button>
                </div>
                <input type="hidden" name="role" id="create-role-input" value="admin">
                <p data-error-for="role" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="create-stall-container" class="hidden">
                <label for="create_stall_id" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Assigned stall <span class="text-neutral-500 font-normal">(optional)</span>
                </label>
                <select id="create_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">-- Unassigned (Assign Later) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <p data-error-for="stall_id" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="create_name" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Full name <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="create_name" name="name" placeholder="e.g. Maria Santos" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                <p data-error-for="name" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Email Address --}}
            <div>
                <label for="create_email" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Email address <span class="text-rose-600">*</span>
                </label>
                <input type="email" id="create_email" name="email" placeholder="e.g. maria@isu.edu.ph" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                <p data-error-for="email" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Password Fields --}}
            <div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_password" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                            Password <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="create_password" name="password" placeholder="••••••••" required
                                class="w-full pl-3 pr-9 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                            <button type="button" onclick="togglePasswordVisibility('create_password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-800 cursor-pointer" aria-label="Toggle password visibility">
                                <ion-icon name="eye-outline" class="text-base leading-none"></ion-icon>
                            </button>
                        </div>
                        <p data-error-for="password" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
                    </div>
                    <div>
                        <label for="create_password_confirmation" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                            Confirm <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="create_password_confirmation" name="password_confirmation" placeholder="••••••••" required
                                class="w-full pl-3 pr-9 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                            <button type="button" onclick="togglePasswordVisibility('create_password_confirmation', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-800 cursor-pointer" aria-label="Toggle password visibility">
                                <ion-icon name="eye-outline" class="text-base leading-none"></ion-icon>
                            </button>
                        </div>
                        <p data-error-for="password_confirmation" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
                    </div>
                </div>
                <p class="text-[11px] text-neutral-600 mt-1.5">Min. 8 characters with letters, numbers & symbols.</p>
            </div>
        </div>

        {{-- Form Footer Actions --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" onclick="closeCreateUserModal()" class="btn btn-ghost btn-sm text-xs rounded-md cursor-pointer">Cancel</button>
            <button type="submit" id="create-user-submit-btn" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Create Account</button>
        </div>
    </form>
</dialog>

{{-- ── 6. EDIT ACCOUNT MODAL ───────────────────────────────────────────── --}}
{{-- CHANGED: the header tile shows the account's initial, as in the students dialog, and the subtitle names the account being edited. --}}
<dialog id="edit-user-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="edit-modal-title">
    <div id="edit-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div id="edit-modal-avatar" class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 text-white font-bold text-sm flex items-center justify-center shrink-0" aria-hidden="true"></div>
            <div class="min-w-0">
                <h3 id="edit-modal-title" class="text-sm font-bold text-white leading-tight">Edit Account</h3>
                <p id="edit-modal-subtitle" class="text-[11px] text-brand-200 truncate"></p>
            </div>
        </div>
        <button type="button" onclick="closeEditUserModal()" class="text-white/70 hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <form id="edit-user-form" method="POST" action="" class="px-6 pb-6 max-sm:pb-0">
        @csrf
        @method('PUT')

        <div class="mt-5 space-y-4">
            {{-- General Error Box --}}
            <div data-general-error class="general-error-box hidden p-3 bg-rose-50 rounded-lg text-xs text-rose-800 flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
                <span class="leading-tight font-medium"></span>
            </div>

            {{-- Role Selection --}}
            <div>
                <label for="edit_role" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Role <span class="text-rose-600">*</span>
                </label>
                <select id="edit_role" name="role" onchange="toggleEditRole(this.value)"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors disabled:text-neutral-500 disabled:cursor-not-allowed">
                    <option value="admin">Administrator</option>
                    <option value="staff">Canteen Staff</option>
                    <option value="clinic">Clinic</option> {{-- ADDED --}}
                </select>
                <p data-error-for="role" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
                <div id="edit-self-warning" class="hidden mt-2 px-3 py-2 bg-amber-50 rounded-lg text-[11px] text-amber-900 flex items-center gap-1.5">
                    <ion-icon name="information-circle" class="text-sm shrink-0 text-amber-600"></ion-icon>
                    <span>You are editing your own account. Role cannot be changed.</span>
                </div>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="edit-stall-container" class="hidden">
                <label for="edit_stall_id" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Assigned canteen stall
                </label>
                <select id="edit_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                    <option value="">-- Unassigned (No Stall) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <p data-error-for="stall_id" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="edit_name" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Full name <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                <p data-error-for="name" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Email Address --}}
            <div>
                <label for="edit_email" class="block text-xs font-semibold text-neutral-900 mb-1.5">
                    Email address <span class="text-rose-600">*</span>
                </label>
                <input type="email" id="edit_email" name="email" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                <p data-error-for="email" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
            </div>

            {{-- Optional Password Reset Section --}}
            {{-- CHANGED: "Change Account Password" became "Reset password", the name admins look for; the field labels are sentence case instead of uppercase, and the fields sit in the evaluations dialog's tinted comment panel. The eye buttons got the aria-label the create dialog's already had. --}}
            <div>
                <button type="button" onclick="toggleEditPasswordSection()" class="text-xs font-semibold text-brand-700 hover:text-brand-900 flex items-center gap-1 cursor-pointer">
                    <ion-icon name="key-outline" class="text-sm" aria-hidden="true"></ion-icon>
                    <span id="edit-pwd-toggle-text">Reset password</span>
                </button>

                <div id="edit-password-fields" class="hidden mt-2.5 p-3.5 bg-surface-alt rounded-lg space-y-3">
                    <p class="text-[11px] text-neutral-600">Leave blank to keep existing password.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_password" class="block text-xs font-semibold text-neutral-900 mb-1.5">New password</label>
                            <div class="relative">
                                <input type="password" id="edit_password" name="password" placeholder="••••••••"
                                    class="w-full pl-3 pr-9 py-2 bg-white border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                                <button type="button" onclick="togglePasswordVisibility('edit_password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-800 cursor-pointer" aria-label="Toggle password visibility">
                                    <ion-icon name="eye-outline" class="text-base leading-none"></ion-icon>
                                </button>
                            </div>
                            <p data-error-for="password" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-xs font-semibold text-neutral-900 mb-1.5">Confirm</label>
                            <div class="relative">
                                <input type="password" id="edit_password_confirmation" name="password_confirmation" placeholder="••••••••"
                                    class="w-full pl-3 pr-9 py-2 bg-white border border-neutral-200 rounded-md text-base sm:text-sm text-neutral-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-colors">
                                <button type="button" onclick="togglePasswordVisibility('edit_password_confirmation', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-800 cursor-pointer" aria-label="Toggle password visibility">
                                    <ion-icon name="eye-outline" class="text-base leading-none"></ion-icon>
                                </button>
                            </div>
                            <p data-error-for="password_confirmation" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1"><ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon><span></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Footer Actions --}}
        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            {{-- ADDED: below 1024px the account list has no Delete button, so the Edit dialog offers it, on the left away from Save. Hidden when editing your own account, as in the table. --}}
            <button type="button" id="edit-delete-btn" onclick="deleteFromEditModal()" class="lg:hidden mr-auto inline-flex items-center gap-1.5 px-3.5 py-1.5 border border-transparent hover:border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-700 rounded-md transition-colors cursor-pointer">
                <ion-icon name="trash-outline" class="text-sm" aria-hidden="true"></ion-icon>
                Delete
            </button>
            <button type="button" onclick="closeEditUserModal()" class="btn btn-ghost btn-sm text-xs rounded-md cursor-pointer">Cancel</button>
            <button type="submit" id="edit-user-submit-btn" class="btn btn-primary btn-sm text-xs font-semibold rounded-md cursor-pointer">Save Changes</button>
        </div>
    </form>
</dialog>

{{-- ── 7. DELETE CONFIRMATION MODAL ───────────────────────────────────── --}}
{{-- CHANGED: the subtitle names the account and its role; the warning is a rose panel in the place of the evaluations dialog's average panel. The error box moved inside the form: showGeneralError(deleteForm) only searches the form, so a refused delete (such as the last administrator) never showed its message before. --}}
<dialog id="delete-user-modal" class="confirm-modal sheet-on-phone p-0! border-0!" aria-labelledby="delete-modal-title">
    <div class="sheet-head bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 flex items-center justify-center text-white shrink-0">
                <ion-icon name="trash-outline" class="text-lg" aria-hidden="true"></ion-icon>
            </div>
            <div class="min-w-0">
                <h3 id="delete-modal-title" class="text-sm font-bold text-white leading-tight">Delete Account</h3>
                <p id="delete-modal-name" class="text-[11px] text-brand-200 truncate"></p>
            </div>
        </div>
        <button type="button" onclick="closeDeleteUserModal()" class="text-white/70 hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <form id="delete-user-form" method="POST" action="" class="px-6 pb-6 max-sm:pb-0">
        @csrf
        @method('DELETE')

        <div class="mt-5 rounded-lg px-4 py-3.5 bg-rose-50">
            <p class="text-xs font-semibold text-rose-900">This action cannot be undone.</p>
            <p class="text-xs text-rose-800 mt-0.5 leading-relaxed">This will permanently delete the account and revoke all access.</p>
        </div>

        {{-- General Error Box for Delete --}}
        <div data-general-error class="general-error-box hidden mt-3 p-3 bg-rose-100 rounded-lg text-xs text-rose-900 flex items-center gap-2">
            <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
            <span class="leading-tight font-medium"></span>
        </div>

        <div class="sheet-actions max-sm:-mx-6 max-sm:px-6 flex items-center justify-end gap-2 pt-4 mt-5 border-t border-neutral-100">
            <button type="button" onclick="closeDeleteUserModal()" class="btn btn-ghost btn-sm text-xs rounded-md cursor-pointer">Cancel</button>
            <button type="submit" id="delete-user-submit-btn" class="btn btn-sm text-xs font-semibold rounded-md bg-rose-600 hover:bg-rose-700 text-white cursor-pointer">Delete Account</button>
        </div>
    </form>
</dialog>

{{-- ── 8. Client-Side Polish & Filtering Script ───────────────────────── --}}
<script>
    // ── Search & Filter Form Helpers ─────────────────────────────────────
    // CHANGED: the debounced auto-submit and caret restore moved to data-live-search in resources/js/app.js, shared with the other staff and admin searches.
    var searchInput = document.getElementById('user-search-input');

    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = "{{ route('admin.users') }}";
            }
        });
    }

    // ── Reusable Modal Helpers & Error Management ─────────────────────────
    var spinnerSvg = '<svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    function clearFormErrors(form) {
        if (!form) return;
        form.querySelectorAll('.field-error').forEach(function(el) {
            el.classList.add('hidden');
            var span = el.querySelector('span');
            if (span) span.textContent = '';
        });
        form.querySelectorAll('input, select').forEach(function(input) {
            input.classList.remove('border-rose-500', 'bg-rose-50/20', 'focus:border-rose-600');
            input.classList.add('border-neutral-200');
        });
        var generalBox = form.querySelector('[data-general-error]');
        if (generalBox) {
            generalBox.classList.add('hidden');
            var span = generalBox.querySelector('span');
            if (span) span.textContent = '';
        }
    }

    function showFieldError(form, fieldName, message) {
        if (!form) return;
        var inputEl = form.querySelector('[name="' + fieldName + '"]');
        var errorEl = form.querySelector('[data-error-for="' + fieldName + '"]');

        if (inputEl) {
            inputEl.classList.remove('border-neutral-200');
            inputEl.classList.add('border-rose-500', 'bg-rose-50/20', 'focus:border-rose-600');
        }

        if (errorEl) {
            errorEl.classList.remove('hidden');
            var span = errorEl.querySelector('span');
            if (span) span.textContent = message;
        }
    }

    function showGeneralError(form, message) {
        if (!form) return;
        var generalBox = form.querySelector('[data-general-error]');
        if (generalBox) {
            generalBox.classList.remove('hidden');
            var span = generalBox.querySelector('span');
            if (span) span.textContent = message;
        }
    }

    function applyFormValidationErrors(form, errors) {
        var hasFieldErrors = false;
        var pwdFields = form.querySelector('#edit-password-fields');

        // Extract and separate password errors:
        // Confirmation mismatch -> route to password_confirmation field
        // Password rule violations (length, letters, numbers, symbols, required) -> route to password field
        var rawPasswordMessages = errors.password ? [].concat(errors.password) : [];
        var confirmMessages = errors.password_confirmation ? [].concat(errors.password_confirmation) : [];
        var passwordRuleMessages = [];

        rawPasswordMessages.forEach(function(msg) {
            var lower = msg.toLowerCase();
            if (lower.includes('confirm') || lower.includes('match')) {
                confirmMessages.push(msg);
            } else {
                passwordRuleMessages.push(msg);
            }
        });

        for (var field in errors) {
            if (!errors.hasOwnProperty(field)) continue;
            if (field === 'password' || field === 'password_confirmation') continue;

            if (errors[field].length > 0) {
                showFieldError(form, field, errors[field][0]);
                hasFieldErrors = true;
            }
        }

        // Route password rule errors to password field
        if (passwordRuleMessages.length > 0) {
            if (pwdFields && pwdFields.classList.contains('hidden')) {
                toggleEditPasswordSection();
            }
            showFieldError(form, 'password', passwordRuleMessages[0]);
            hasFieldErrors = true;
        }

        // Route confirmation mismatch error to password_confirmation field
        if (confirmMessages.length > 0) {
            if (pwdFields && pwdFields.classList.contains('hidden')) {
                toggleEditPasswordSection();
            }
            showFieldError(form, 'password_confirmation', confirmMessages[0]);
            hasFieldErrors = true;
        }

        return hasFieldErrors;
    }

    // ── Create Modal Controls ─────────────────────────────────────────────
    var createModal = document.getElementById('create-user-modal');
    var createForm = document.getElementById('create-user-form');

    function resetCreateModal() {
        if (!createForm) return;
        createForm.reset();
        clearFormErrors(createForm);

        // Explicitly clear all text and selection inputs
        ['create_name', 'create_email', 'create_password', 'create_password_confirmation', 'create_stall_id'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.value = '';
                if (id === 'create_password' || id === 'create_password_confirmation') {
                    el.type = 'password';
                }
            }
        });

        // Reset eye toggle icons
        createForm.querySelectorAll('button[onclick*="togglePasswordVisibility"] ion-icon').forEach(function(icon) {
            icon.setAttribute('name', 'eye-outline');
        });

        // Reset role to default Administrator
        toggleCreateRole('admin');

        // Reset submit button and loader if in loading state
        var submitBtn = document.getElementById('create-user-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            submitBtn.innerHTML = 'Create Account'; // CHANGED: the submit button is plain text now (was icon plus span).
        }
        var loader = document.getElementById('create-modal-loader');
        if (loader) loader.classList.add('hidden');
    }

    function openCreateUserModal() {
        if (!createModal) return;
        resetCreateModal();
        createModal.showModal();
        setTimeout(function() {
            var firstInput = document.getElementById('create_name');
            if (firstInput) firstInput.focus();
        }, 50);
    }

    function closeCreateUserModal() {
        if (createModal) {
            resetCreateModal();
            createModal.close();
        }
    }

    if (createModal) {
        // Native close event (covers ESC key, backdrop click, or close() calls)
        createModal.addEventListener('close', function() {
            resetCreateModal();
        });
    }

    // CHANGED: handles three role buttons (admin, staff, clinic) instead of an admin/staff if-else.
    function toggleCreateRole(role) {
        var stallContainer = document.getElementById('create-stall-container');
        var roleInput = document.getElementById('create-role-input');

        if (roleInput) roleInput.value = role;

        // Clear role error if displayed
        if (createForm) {
            var roleErr = createForm.querySelector('[data-error-for="role"]');
            if (roleErr) {
                roleErr.classList.add('hidden');
                var span = roleErr.querySelector('span');
                if (span) span.textContent = '';
            }
        }

        if (stallContainer) stallContainer.classList.toggle('hidden', role !== 'staff');

        // CHANGED: only the colour classes and aria-pressed are toggled, so the base classes live in the markup alone (was: full activeClass/idleClass strings).
        ['admin', 'staff', 'clinic'].forEach(function (r) {
            var btn = document.getElementById('create-role-' + r + '-btn');
            if (!btn) return;
            var on = r === role;
            ['bg-white', 'text-neutral-900', 'shadow-2xs'].forEach(function (c) { btn.classList.toggle(c, on); });
            ['text-neutral-600', 'hover:text-neutral-900'].forEach(function (c) { btn.classList.toggle(c, !on); });
            btn.setAttribute('aria-pressed', on);
        });
    }

    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearFormErrors(createForm);

            var loader = document.getElementById('create-modal-loader');
            var submitBtn = document.getElementById('create-user-submit-btn');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (loader) loader.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = spinnerSvg + '<span>Creating...</span>';
            }

            var formData = new FormData(createForm);

            fetch(createForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                }).catch(function() {
                    return { ok: res.ok, status: res.status, data: {} };
                });
            })
            .then(function(result) {
                if (result.ok) {
                    window.location.reload();
                    return;
                }

                if (loader) loader.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }

                if (result.status === 422 && result.data && result.data.errors) {
                    var hasFieldErrors = applyFormValidationErrors(createForm, result.data.errors);
                    if (!hasFieldErrors && result.data.message) {
                        showGeneralError(createForm, result.data.message);
                    }
                } else {
                    var msg = (result.data && result.data.message) ? result.data.message : 'Unable to create account. Please check inputs.';
                    showGeneralError(createForm, msg);
                }
            })
            .catch(function(err) {
                if (loader) loader.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }
                showGeneralError(createForm, 'A network error occurred. Please try again.');
            });
        });
    }

    // ── Edit Modal Controls ───────────────────────────────────────────────
    var editModal = document.getElementById('edit-user-modal');
    var editForm = document.getElementById('edit-user-form');
    var roleSelect = document.getElementById('edit_role');
    var isEditingSelf = false;
    var editingUser = null; // ADDED: the account in the Edit dialog, for its Delete button.

    function openEditUserModal(id, name, email, role, stallId, isSelf) {
        if (!editModal || !editForm) return;

        clearFormErrors(editForm);
        isEditingSelf = isSelf;
        // ADDED: remember the account and hide Delete on your own account.
        editingUser = { id: id, name: name, role: role };
        var deleteBtn = document.getElementById('edit-delete-btn');
        if (deleteBtn) deleteBtn.classList.toggle('hidden', isSelf);

        editForm.action = "/admin/users/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        // ADDED: the header shows whose account this is.
        document.getElementById('edit-modal-avatar').textContent = (name || 'U').charAt(0).toUpperCase();
        document.getElementById('edit-modal-subtitle').textContent = name;
        if (roleSelect) roleSelect.value = role;

        var stallSelect = document.getElementById('edit_stall_id');
        if (stallSelect) stallSelect.value = stallId ? stallId : '';

        var selfWarning = document.getElementById('edit-self-warning');
        if (isSelf) {
            if (selfWarning) selfWarning.classList.remove('hidden');
            if (roleSelect) roleSelect.disabled = true;
        } else {
            if (selfWarning) selfWarning.classList.add('hidden');
            if (roleSelect) roleSelect.disabled = false;
        }

        toggleEditRole(role);

        // Reset password fields
        var pwdFields = document.getElementById('edit-password-fields');
        if (pwdFields) pwdFields.classList.add('hidden');
        var pwdToggleText = document.getElementById('edit-pwd-toggle-text');
        if (pwdToggleText) pwdToggleText.textContent = 'Reset password'; // CHANGED: was "Change Account Password"
        var pwdInput = document.getElementById('edit_password');
        var pwdConfirm = document.getElementById('edit_password_confirmation');
        if (pwdInput) {
            pwdInput.value = '';
            pwdInput.type = 'password';
        }
        if (pwdConfirm) {
            pwdConfirm.value = '';
            pwdConfirm.type = 'password';
        }
        editForm.querySelectorAll('button[onclick*="togglePasswordVisibility"] ion-icon').forEach(function(icon) {
            icon.setAttribute('name', 'eye-outline');
        });

        editModal.showModal();
        setTimeout(function() {
            var nameInput = document.getElementById('edit_name');
            if (nameInput) nameInput.focus();
        }, 50);
    }

    function closeEditUserModal() {
        if (editModal) {
            clearFormErrors(editForm);
            var pwdInput = document.getElementById('edit_password');
            var pwdConfirm = document.getElementById('edit_password_confirmation');
            if (pwdInput) pwdInput.value = '';
            if (pwdConfirm) pwdConfirm.value = '';
            var pwdFields = document.getElementById('edit-password-fields');
            if (pwdFields) pwdFields.classList.add('hidden');
            editModal.close();
        }
    }

    // ADDED: the Edit dialog's Delete button hands over to the delete confirmation.
    function deleteFromEditModal() {
        closeEditUserModal();
        openDeleteUserModal(editingUser.id, editingUser.name, editingUser.role);
    }

    function toggleEditRole(role) {
        var stallContainer = document.getElementById('edit-stall-container');
        if (role === 'staff') {
            if (stallContainer) stallContainer.classList.remove('hidden');
        } else {
            if (stallContainer) stallContainer.classList.add('hidden');
        }
    }

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearFormErrors(editForm);

            var wasDisabled = roleSelect && roleSelect.disabled;
            if (wasDisabled) {
                roleSelect.disabled = false;
            }

            var loader = document.getElementById('edit-modal-loader');
            var submitBtn = document.getElementById('edit-user-submit-btn');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (loader) loader.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = spinnerSvg + '<span>Saving...</span>';
            }

            var formData = new FormData(editForm);

            if (wasDisabled) {
                roleSelect.disabled = true;
            }

            fetch(editForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                }).catch(function() {
                    return { ok: res.ok, status: res.status, data: {} };
                });
            })
            .then(function(result) {
                if (result.ok) {
                    window.location.reload();
                    return;
                }

                if (loader) loader.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }

                if (result.status === 422 && result.data && result.data.errors) {
                    var hasFieldErrors = applyFormValidationErrors(editForm, result.data.errors);
                    if (!hasFieldErrors && result.data.message) {
                        showGeneralError(editForm, result.data.message);
                    }
                } else {
                    var msg = (result.data && result.data.message) ? result.data.message : 'Unable to update account.';
                    showGeneralError(editForm, msg);
                }
            })
            .catch(function(err) {
                if (loader) loader.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }
                showGeneralError(editForm, 'A network error occurred. Please try again.');
            });
        });
    }

    function toggleEditPasswordSection() {
        var pwdFields = document.getElementById('edit-password-fields');
        var toggleText = document.getElementById('edit-pwd-toggle-text');
        if (pwdFields.classList.contains('hidden')) {
            pwdFields.classList.remove('hidden');
            toggleText.textContent = 'Keep current password'; // CHANGED: was "Hide Password Fields"
        } else {
            pwdFields.classList.add('hidden');
            toggleText.textContent = 'Reset password'; // CHANGED: was "Change Account Password"
        }
    }

    // ── Real-time Input Error Clearing ────────────────────────────────────
    [createForm, editForm].forEach(function(form) {
        if (!form) return;
        form.querySelectorAll('input, select').forEach(function(field) {
            var clearHandler = function() {
                field.classList.remove('border-rose-500', 'bg-rose-50/20', 'focus:border-rose-600');
                field.classList.add('border-neutral-200');
                var errorEl = form.querySelector('[data-error-for="' + field.name + '"]');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                    var span = errorEl.querySelector('span');
                    if (span) span.textContent = '';
                }

                // If editing password, also clear any confirmation error reactively
                if (field.name === 'password') {
                    var confirmInput = form.querySelector('[name="password_confirmation"]');
                    var confirmErr = form.querySelector('[data-error-for="password_confirmation"]');
                    if (confirmInput) {
                        confirmInput.classList.remove('border-rose-500', 'bg-rose-50/20', 'focus:border-rose-600');
                        confirmInput.classList.add('border-neutral-200');
                    }
                    if (confirmErr) {
                        confirmErr.classList.add('hidden');
                        var cSpan = confirmErr.querySelector('span');
                        if (cSpan) cSpan.textContent = '';
                    }
                }
            };
            field.addEventListener('input', clearHandler);
            field.addEventListener('change', clearHandler);
        });
    });

    // ── Enter Key Navigation Between Modal Fields ─────────────────────────
    function wireEnterKeyNavigation(sequence) {
        sequence.forEach(function(item) {
            var currentEl = document.getElementById(item.from);
            if (!currentEl) return;
            currentEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
                    e.preventDefault();
                    var triggerAction = function(target) {
                        if (!target) return;
                        if (target.tagName === 'BUTTON' || target.type === 'submit') {
                            if (target.disabled) return;
                            if (target.form && typeof target.form.requestSubmit === 'function') {
                                target.form.requestSubmit(target);
                            } else {
                                target.click();
                            }
                        } else if (typeof target.focus === 'function') {
                            target.focus();
                        }
                    };

                    if (typeof item.to === 'function') {
                        triggerAction(item.to());
                    } else if (typeof item.to === 'string') {
                        triggerAction(document.getElementById(item.to));
                    }
                }
            });
        });
    }

    wireEnterKeyNavigation([
        { from: 'create_stall_id', to: 'create_name' },
        { from: 'create_name', to: 'create_email' },
        { from: 'create_email', to: 'create_password' },
        { from: 'create_password', to: 'create_password_confirmation' },
        { from: 'create_password_confirmation', to: 'create-user-submit-btn' },
    ]);

    wireEnterKeyNavigation([
        { from: 'edit_stall_id', to: 'edit_name' },
        { from: 'edit_name', to: 'edit_email' },
        {
            from: 'edit_email',
            to: function() {
                var pwdFields = document.getElementById('edit-password-fields');
                if (pwdFields && !pwdFields.classList.contains('hidden')) {
                    return document.getElementById('edit_password');
                }
                return document.getElementById('edit-user-submit-btn');
            }
        },
        { from: 'edit_password', to: 'edit_password_confirmation' },
        { from: 'edit_password_confirmation', to: 'edit-user-submit-btn' },
    ]);

    // ── Delete Modal Controls ─────────────────────────────────────────────
    var deleteModal = document.getElementById('delete-user-modal');
    var deleteForm = document.getElementById('delete-user-form');
    var deleteName = document.getElementById('delete-modal-name');

    function openDeleteUserModal(id, name, role) {
        if (!deleteModal || !deleteForm) return;
        clearFormErrors(deleteForm);
        deleteForm.action = "/admin/users/" + id;
        // CHANGED: label for clinic accounts; the name and role are the header subtitle, "Name · Role" (was "Name (Role)" in the sentence).
        if (deleteName) deleteName.textContent = name + ' · ' + ({ admin: 'Administrator', staff: 'Canteen Staff', clinic: 'Clinic' }[role] || role);
        deleteModal.showModal();
    }

    function closeDeleteUserModal() {
        if (deleteModal) {
            clearFormErrors(deleteForm);
            deleteModal.close();
        }
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearFormErrors(deleteForm);

            var submitBtn = document.getElementById('delete-user-submit-btn');
            var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = spinnerSvg + '<span>Deleting...</span>';
            }

            var formData = new FormData(deleteForm);

            fetch(deleteForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                }).catch(function() {
                    return { ok: res.ok, status: res.status, data: {} };
                });
            })
            .then(function(result) {
                if (result.ok) {
                    window.location.reload();
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }

                var msg = (result.data && result.data.message) ? result.data.message : 'Unable to delete account.';
                showGeneralError(deleteForm, msg);
            })
            .catch(function(err) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnHtml;
                }
                showGeneralError(deleteForm, 'A network error occurred. Please try again.');
            });
        });
    }

    // ── Password Visibility Toggle Helper ─────────────────────────────────
    function togglePasswordVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('ion-icon');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('name', 'eye-off-outline');
        } else {
            input.type = 'password';
            icon.setAttribute('name', 'eye-outline');
        }
    }

    // ── Copy to Clipboard Helper ──────────────────────────────────────────
    // CHANGED: the email itself is the button now, so the answer stays at the copy icon: it turns into a green check and the hidden "Copy email" label shows as a small green "Copied" beside it for 1.5s (no toast). The label is a live region, so screen readers hear it too. Swapping the whole button, as before, hid the address.
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('ion-icon'), label = btn.querySelector('[aria-live]');
            icon.setAttribute('name', 'checkmark-outline');
            icon.classList.add('text-emerald-600!');
            label.textContent = 'Copied';
            label.className = 'text-[11px] font-semibold text-emerald-700 shrink-0';
            setTimeout(function() {
                icon.setAttribute('name', 'copy-outline');
                icon.classList.remove('text-emerald-600!');
                label.textContent = 'Copy email';
                label.className = 'sr-only';
            }, 1500);
        });
    }

    // ── Backdrop Click to Close Modal Helper ──────────────────────────────
    [createModal, editModal, deleteModal].forEach(function(modal) {
        if (!modal) return;
        modal.addEventListener('click', function(e) {
            var rect = modal.getBoundingClientRect();
            var isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
                rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
            if (!isInDialog) {
                modal.close();
            }
        });
    });
</script>
@endsection
