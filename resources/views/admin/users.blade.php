@extends('layouts.dashboard')
@section('title', 'Staff & Admin Management | Admin — DSS')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">

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

    {{-- ── 1. Page Header & Primary Actions ────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-neutral-200/80">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2.5">
                <ion-icon name="shield-checkmark-outline" class="text-brand-700 text-2xl"></ion-icon>
                <span>Staff &amp; Administrators</span>
            </h1>
            <p class="text-xs text-neutral-500 mt-0.5 max-w-2xl">
                Directory and access management for canteen staff and system administrators.
            </p>
        </div>
        <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0">
            <button type="button" onclick="openCreateUserModal()"
                class="btn btn-primary text-xs font-bold px-4 py-2 rounded-lg shadow-2xs flex items-center gap-1.5 cursor-pointer transition-all">
                <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                <span>New Account</span>
            </button>
        </div>
    </div>

    {{-- ── 2. Stat Metric Cards ────────────────────────────────────────── --}}
    @php
        $totalCount = $stats->total_count ?? $users->total();
        $adminCount = $stats->admin_count ?? 0;
        $staffCount = $stats->staff_count ?? 0;
        $assignedStaffCount = $stats->assigned_staff_count ?? 0;
        $unassignedStaffCount = $stats->unassigned_staff_count ?? 0;
        $currentRole = request('role', 'all');
        $hasFilters = request()->filled('q') || ($currentRole !== 'all') || (request()->filled('per_page') && request('per_page') != 10);
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Accounts Card --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Total Accounts</span>
                <div class="w-8 h-8 rounded-lg bg-neutral-100 text-neutral-700 flex items-center justify-center">
                    <ion-icon name="id-card" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-display text-neutral-900">{{ $totalCount }}</span>
                <span class="text-xs text-neutral-500 font-medium">accounts</span>
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">{{ $adminCount }} administrators · {{ $staffCount }} canteen staff</p>
        </div>

        {{-- System Administrators Card --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-brand-800 uppercase tracking-wider">System Administrators</span>
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 border border-brand-200/80 flex items-center justify-center">
                    <ion-icon name="shield-checkmark" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-display text-neutral-900">{{ $adminCount }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 border border-brand-200 text-[10px]">
                    Full Access
                </span>
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">Full management of stalls, evaluations, and settings</p>
        </div>

        {{-- Canteen Staff Card --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Canteen Staff</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 border border-sky-200/80 flex items-center justify-center">
                    <ion-icon name="storefront" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-display text-neutral-900">{{ $staffCount }}</span>
                @if($unassignedStaffCount > 0)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] flex items-center gap-1">
                        <ion-icon name="alert-circle" class="text-xs"></ion-icon>
                        {{ $unassignedStaffCount }} Unassigned
                    </span>
                @else
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">
                        All Assigned
                    </span>
                @endif
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">{{ $assignedStaffCount }} assigned to canteen stalls</p>
        </div>
    </div>

    {{-- ── 3. Search & Filter Bar ───────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs p-4 sm:p-5">
        <form id="user-filter-form" method="GET" action="{{ route('admin.users') }}" class="space-y-3">
            @if(request('role') && request('role') !== 'all')
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                {{-- Search Input --}}
                <div class="flex-1 relative">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input type="text" name="q" id="user-search-input" value="{{ request('q') }}" placeholder="Search by name, email, or stall…"
                        class="w-full pl-9 pr-9 py-2 bg-neutral-50/70 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-all">
                    @if(request('q'))
                        <a href="{{ route('admin.users', array_filter(['role' => $currentRole !== 'all' ? $currentRole : null, 'per_page' => request('per_page')])) }}"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer" aria-label="Clear search">
                            <ion-icon name="close-circle" class="text-base"></ion-icon>
                        </a>
                    @endif
                </div>

                {{-- Role Filter Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 shrink-0 text-xs">
                    <a href="{{ route('admin.users', array_filter(['q' => request('q'), 'per_page' => request('per_page')])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer {{ $currentRole === 'all' ? 'font-bold bg-neutral-900 text-white shadow-2xs' : 'font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                        All ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.users', array_filter(['role' => 'admin', 'q' => request('q'), 'per_page' => request('per_page')])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer {{ $currentRole === 'admin' ? 'font-bold bg-neutral-900 text-white shadow-2xs' : 'font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                        Admins ({{ $adminCount }})
                    </a>
                    <a href="{{ route('admin.users', array_filter(['role' => 'staff', 'q' => request('q'), 'per_page' => request('per_page')])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer {{ $currentRole === 'staff' ? 'font-bold bg-neutral-900 text-white shadow-2xs' : 'font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                        Staff ({{ $staffCount }})
                    </a>
                    <a href="{{ route('admin.users', array_filter(['role' => 'unassigned', 'q' => request('q'), 'per_page' => request('per_page')])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer {{ $currentRole === 'unassigned' ? 'font-bold bg-amber-500 text-white shadow-2xs' : ($unassignedStaffCount > 0 ? 'font-semibold bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100' : 'font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200') }} flex items-center gap-1">
                        @if($unassignedStaffCount > 0 && $currentRole !== 'unassigned')
                            <ion-icon name="alert-circle" class="text-xs text-amber-600"></ion-icon>
                        @endif
                        <span>Unassigned ({{ $unassignedStaffCount }})</span>
                    </a>
                </div>
            </div>

            {{-- Active Filter Chips (Only rendered when filters are active) --}}
            @if($hasFilters)
                <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-neutral-100 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-neutral-400 font-medium text-[11px]">Active filters:</span>
                        @if(request('q'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 text-[11px] font-semibold">
                                Search: "{{ request('q') }}"
                            </span>
                        @endif
                        @if($currentRole !== 'all')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-50 border border-brand-200 text-brand-800 text-[11px] font-semibold">
                                Role: {{ ucfirst($currentRole) }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold">
                            {{ $users->total() }} {{ Str::plural('result', $users->total()) }}
                        </span>
                    </div>

                    <a href="{{ route('admin.users') }}" class="text-neutral-500 hover:text-neutral-900 font-semibold text-xs inline-flex items-center gap-1">
                        <ion-icon name="close-circle-outline" class="text-sm"></ion-icon>
                        Clear filters
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- ── 4. Main Account Directory ───────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
        @if($users->isEmpty())
            <div class="p-16 text-center">
                <div class="w-14 h-14 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto mb-3">
                    <ion-icon name="people-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-800">
                    {{ $hasFilters ? 'No accounts found' : 'No staff or admin accounts yet' }}
                </h3>
                <p class="text-xs text-neutral-400 mt-1 max-w-sm mx-auto">
                    {{ $hasFilters ? 'No accounts matched your search or role filters. Try adjusting your search query.' : 'Get started by creating your first administrator or staff account.' }}
                </p>
                @if($hasFilters)
                    <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm text-xs font-bold px-4 py-2 rounded-lg mt-4 inline-flex items-center gap-1.5 shadow-2xs">
                        <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
                        <span>Reset Filters</span>
                    </a>
                @else
                    <button type="button" onclick="openCreateUserModal()" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-lg mt-4 inline-flex items-center gap-1.5 shadow-2xs">
                        <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                        <span>Create Account</span>
                    </button>
                @endif
            </div>
        @else
            <div id="users-directory-container" class="overflow-x-auto">
                {{-- Desktop Table View --}}
                <table id="users-desktop-table" class="w-full text-left border-collapse min-w-[760px] hidden md:table">
                    <thead>
                        <tr class="border-b border-neutral-200/70 bg-neutral-50/75">
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Account</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Role</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Stall Assignment</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Created</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-neutral-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100/80" id="users-table-body">
                        @foreach($users as $u)
                            @php
                                $isCurrentUser = $u->id === Auth::id();
                                $isAdmin = $u->role === 'admin';
                                $isStaff = $u->role === 'staff';
                                $isAssigned = !empty($u->stall_id);
                            @endphp
                            <tr class="user-row hover:bg-neutral-50/70 transition-colors {{ $isCurrentUser ? 'bg-brand-50/20' : '' }}"
                                data-name="{{ strtolower($u->name) }}"
                                data-email="{{ strtolower($u->email) }}"
                                data-role="{{ $u->role }}"
                                data-stall="{{ strtolower($u->stall_name ?? 'unassigned') }}"
                                data-assigned="{{ $isAssigned ? '1' : '0' }}">

                                {{-- User Column --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full {{ $isAdmin ? 'bg-brand-100 text-brand-800 border border-brand-200/70' : 'bg-sky-100 text-sky-800 border border-sky-200/70' }} flex items-center justify-center font-bold text-xs shrink-0 font-display">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-neutral-900 truncate flex items-center gap-1.5">
                                                <span>{{ $u->name }}</span>
                                                @if($isCurrentUser)
                                                    <span class="text-[9px] font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200/80">YOU</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-neutral-500">
                                                <span class="truncate font-mono">{{ $u->email }}</span>
                                                <button type="button" onclick="copyToClipboard('{{ $u->email }}', this)" class="text-neutral-400 hover:text-neutral-700 transition-colors cursor-pointer" title="Copy email">
                                                    <ion-icon name="copy-outline" class="text-xs leading-none"></ion-icon>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role Column --}}
                                <td class="px-5 py-3.5">
                                    @if($isAdmin)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-brand-50 text-brand-800 border border-brand-200/80">
                                            <ion-icon name="shield-checkmark" class="text-xs text-brand-600"></ion-icon>
                                            <span>Administrator</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200/80">
                                            <ion-icon name="person" class="text-xs text-sky-600"></ion-icon>
                                            <span>Canteen Staff</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Stall Assignment Column --}}
                                <td class="px-5 py-3.5">
                                    @if($isAdmin)
                                        <span class="text-neutral-400 text-xs font-medium flex items-center gap-1.5">
                                            <ion-icon name="globe-outline" class="text-xs text-neutral-400"></ion-icon>
                                            <span>System-wide</span>
                                        </span>
                                    @elseif($isAssigned)
                                        <div class="flex items-center gap-1.5 text-xs font-semibold text-neutral-800">
                                            <ion-icon name="storefront-outline" class="text-brand-700 text-sm shrink-0"></ion-icon>
                                            <span class="truncate max-w-[200px]" title="{{ $u->stall_name }}">{{ $u->stall_name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                            <ion-icon name="alert-circle" class="text-xs text-amber-600"></ion-icon>
                                            <span>Unassigned</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Created Date Column --}}
                                <td class="px-5 py-3.5">
                                    <span class="text-xs text-neutral-600 font-medium">
                                        {{ \Carbon\Carbon::parse($u->created_at)->format('M d, Y') }}
                                    </span>
                                </td>

                                {{-- Actions Column --}}
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                            onclick="openEditUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}', {{ $u->stall_id ?? 'null' }}, {{ $isCurrentUser ? 'true' : 'false' }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-semibold text-neutral-700 hover:text-neutral-900 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 transition-all cursor-pointer">
                                            <ion-icon name="create-outline" class="text-sm text-neutral-500"></ion-icon>
                                            <span>Edit</span>
                                        </button>

                                        @if(!$isCurrentUser)
                                            <button type="button"
                                                onclick="openDeleteUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->role }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50/50 hover:bg-red-50 border border-red-200/70 transition-all cursor-pointer">
                                                <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                                <span>Delete</span>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-neutral-100 text-neutral-500">
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
                <div class="md:hidden divide-y divide-neutral-100" id="users-mobile-list">
                @foreach($users as $u)
                    @php
                        $isCurrentUser = $u->id === Auth::id();
                        $isAdmin = $u->role === 'admin';
                        $isStaff = $u->role === 'staff';
                        $isAssigned = !empty($u->stall_id);
                    @endphp
                    <div class="user-row p-4 space-y-3 {{ $isCurrentUser ? 'bg-brand-50/20' : '' }}"
                        data-name="{{ strtolower($u->name) }}"
                        data-email="{{ strtolower($u->email) }}"
                        data-role="{{ $u->role }}"
                        data-stall="{{ strtolower($u->stall_name ?? 'unassigned') }}"
                        data-assigned="{{ $isAssigned ? '1' : '0' }}">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $isAdmin ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-sky-100 text-sky-800 border border-sky-200' }} flex items-center justify-center font-bold text-sm shrink-0 font-display">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-neutral-900 flex items-center gap-1.5">
                                        <span>{{ $u->name }}</span>
                                        @if($isCurrentUser)
                                            <span class="text-[9px] font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200">YOU</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-neutral-500 font-mono mt-0.5">{{ $u->email }}</div>
                                </div>
                            </div>
                            <div>
                                @if($isAdmin)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-800 border border-brand-200">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                        Staff
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Stall Details for Mobile --}}
                        <div class="flex items-center justify-between text-xs pt-2 border-t border-neutral-100 text-neutral-600">
                            <span class="text-[11px] text-neutral-400 font-semibold">Assignment:</span>
                            @if($isAdmin)
                                <span class="font-medium text-neutral-500">System-wide</span>
                            @elseif($isAssigned)
                                <span class="font-bold text-neutral-800 flex items-center gap-1 truncate max-w-[180px]">
                                    <ion-icon name="storefront" class="text-brand-700 text-xs"></ion-icon>
                                    {{ $u->stall_name }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                    Unassigned
                                </span>
                            @endif
                        </div>

                        {{-- Actions for Mobile --}}
                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button"
                                onclick="openEditUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}', {{ $u->stall_id ?? 'null' }}, {{ $isCurrentUser ? 'true' : 'false' }})"
                                class="btn btn-ghost text-xs font-semibold px-3 py-1.5 rounded border border-neutral-200 text-neutral-700">
                                Edit
                            </button>
                            @if(!$isCurrentUser)
                                <button type="button"
                                    onclick="openDeleteUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->role }}')"
                                    class="text-xs font-semibold px-3 py-1.5 rounded bg-red-50 text-red-600 border border-red-200">
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            <div class="px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                    <span>
                        Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $users->total() }}</strong> accounts
                    </span>

                    {{-- Per Page Selector --}}
                    <div class="flex items-center gap-1.5 border-l border-neutral-200 pl-3">
                        <label for="per_page_select" class="text-[11px] font-bold text-neutral-400 uppercase">Per Page</label>
                        <select id="per_page_select" onchange="window.location.href = this.value" class="bg-white border border-neutral-200 rounded px-2 py-1 text-xs font-semibold focus:outline-none focus:border-brand-600 cursor-pointer">
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
                        <span class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="hidden sm:flex items-center gap-1">
                        @if($users->hasPages())
                            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                @if($page == $users->currentPage())
                                    <span class="w-8 h-8 rounded-lg bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <span class="w-8 h-8 rounded-lg bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                1
                            </span>
                        @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </a>
                    @else
                        <span class="px-2.5 py-1.5 rounded-lg border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
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
<dialog id="create-user-modal" class="confirm-modal modal-sharp max-w-[400px] w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60" aria-labelledby="create-modal-title">
    <div id="create-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    {{-- Modal Header --}}
    <div class="px-5 py-3.5 bg-neutral-50/80 border-b border-neutral-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200/80 text-brand-700 flex items-center justify-center shrink-0">
                <ion-icon name="person-add-outline" class="text-base"></ion-icon>
            </div>
            <h3 id="create-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Create Account</h3>
        </div>
        <button type="button" onclick="closeCreateUserModal()" class="text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-lg"></ion-icon>
        </button>
    </div>

    <form id="create-user-form" method="POST" action="{{ route('admin.users.create') }}">
        @csrf

        <div class="p-5 space-y-3.5">
            {{-- General Error Box --}}
            <div data-general-error class="general-error-box hidden p-2.5 bg-rose-50 border border-rose-200/80 rounded-lg text-xs text-rose-800 flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
                <span class="leading-tight font-medium"></span>
            </div>

            {{-- Role Selection Segmented Control --}}
            <div>
                <label class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Account Role <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 p-1 bg-neutral-100/90 rounded-lg border border-neutral-200/80 gap-1 text-xs">
                    <button type="button" id="create-role-admin-btn" onclick="toggleCreateRole('admin')"
                        class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-bold text-xs transition-all shadow-xs bg-white text-brand-900 border border-neutral-200/80 cursor-pointer">
                        <ion-icon name="shield-checkmark-outline" class="text-sm"></ion-icon>
                        <span>Administrator</span>
                    </button>
                    <button type="button" id="create-role-staff-btn" onclick="toggleCreateRole('staff')"
                        class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 transition-all cursor-pointer">
                        <ion-icon name="restaurant-outline" class="text-sm"></ion-icon>
                        <span>Canteen Staff</span>
                    </button>
                </div>
                <input type="hidden" name="role" id="create-role-input" value="admin">
                <p data-error-for="role" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="create-stall-container" class="hidden space-y-1.5">
                <label for="create_stall_id" class="block text-xs font-bold text-neutral-700">
                    Assigned Stall <span class="text-neutral-400 font-normal">(Optional)</span>
                </label>
                <select id="create_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="">-- Unassigned (Assign Later) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <p data-error-for="stall_id" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="create_name" class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="create_name" name="name" placeholder="e.g. Maria Santos" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p data-error-for="name" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Email Address --}}
            <div>
                <label for="create_email" class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="create_email" name="email" placeholder="e.g. maria@isu.edu.ph" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p data-error-for="email" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Password Fields --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label for="create_password" class="block text-xs font-bold text-neutral-700 mb-1.5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="create_password" name="password" placeholder="••••••••" required
                            class="w-full pl-3 pr-8 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('create_password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer" aria-label="Toggle password visibility">
                            <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                        </button>
                    </div>
                    <p data-error-for="password" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                        <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                        <span></span>
                    </p>
                </div>
                <div>
                    <label for="create_password_confirmation" class="block text-xs font-bold text-neutral-700 mb-1.5">
                        Confirm <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="create_password_confirmation" name="password_confirmation" placeholder="••••••••" required
                            class="w-full pl-3 pr-8 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('create_password_confirmation', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer" aria-label="Toggle password visibility">
                            <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                        </button>
                    </div>
                    <p data-error-for="password_confirmation" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                        <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                        <span></span>
                    </p>
                </div>
            </div>
            <p class="text-[10px] text-neutral-400 -mt-1">Min. 8 characters with letters, numbers & symbols.</p>
        </div>

        {{-- Form Footer Actions --}}
        <div class="px-5 py-3.5 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeCreateUserModal()" class="px-3.5 py-2 text-xs font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="create-user-submit-btn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                <ion-icon name="person-add" class="text-sm"></ion-icon>
                <span>Create Account</span>
            </button>
        </div>
    </form>
</dialog>

{{-- ── 6. EDIT ACCOUNT MODAL ───────────────────────────────────────────── --}}
<dialog id="edit-user-modal" class="confirm-modal modal-sharp max-w-[400px] w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60" aria-labelledby="edit-modal-title">
    <div id="edit-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    {{-- Modal Header --}}
    <div class="px-5 py-3.5 bg-neutral-50/80 border-b border-neutral-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-neutral-100 border border-neutral-200/80 text-neutral-700 flex items-center justify-center shrink-0">
                <ion-icon name="create-outline" class="text-base"></ion-icon>
            </div>
            <h3 id="edit-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Edit Account</h3>
        </div>
        <button type="button" onclick="closeEditUserModal()" class="text-neutral-400 hover:text-neutral-700 p-1.5 rounded-lg transition-colors cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-lg"></ion-icon>
        </button>
    </div>

    <form id="edit-user-form" method="POST" action="">
        @csrf
        @method('PUT')

        <div class="p-5 space-y-3.5">
            {{-- General Error Box --}}
            <div data-general-error class="general-error-box hidden p-2.5 bg-rose-50 border border-rose-200/80 rounded-lg text-xs text-rose-800 flex items-center gap-2">
                <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
                <span class="leading-tight font-medium"></span>
            </div>

            {{-- Role Selection --}}
            <div>
                <label for="edit_role" class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="edit_role" name="role" onchange="toggleEditRole(this.value)"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="admin">Administrator</option>
                    <option value="staff">Canteen Staff</option>
                </select>
                <p data-error-for="role" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
                <div id="edit-self-warning" class="hidden mt-2 p-2 bg-amber-50 border border-amber-200/80 rounded-lg text-[11px] text-amber-800 flex items-center gap-1.5">
                    <ion-icon name="information-circle" class="text-sm shrink-0 text-amber-600"></ion-icon>
                    <span>You are editing your own account. Role cannot be changed.</span>
                </div>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="edit-stall-container" class="hidden space-y-1.5">
                <label for="edit_stall_id" class="block text-xs font-bold text-neutral-700">
                    Assigned Canteen Stall
                </label>
                <select id="edit_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                    <option value="">-- Unassigned (No Stall) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <p data-error-for="stall_id" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="edit_name" class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p data-error-for="name" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Email Address --}}
            <div>
                <label for="edit_email" class="block text-xs font-bold text-neutral-700 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="edit_email" name="email" required
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-900 focus:outline-none focus:border-brand-700 focus:bg-white transition-colors">
                <p data-error-for="email" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                    <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                    <span></span>
                </p>
            </div>

            {{-- Optional Password Reset Section --}}
            <div class="pt-2 border-t border-neutral-100">
                <button type="button" onclick="toggleEditPasswordSection()" class="text-xs font-bold text-brand-700 hover:text-brand-900 flex items-center gap-1 cursor-pointer">
                    <ion-icon name="key-outline" class="text-sm"></ion-icon>
                    <span id="edit-pwd-toggle-text">Change Account Password</span>
                </button>

                <div id="edit-password-fields" class="hidden space-y-2.5 mt-2.5 p-3 bg-neutral-50 rounded-lg border border-neutral-200/80">
                    <p class="text-[10px] text-neutral-400">Leave blank to keep existing password.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label for="edit_password" class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">New Password</label>
                            <div class="relative">
                                <input type="password" id="edit_password" name="password" placeholder="••••••••"
                                    class="w-full pl-2.5 pr-7 py-1.5 bg-white border border-neutral-200 rounded text-xs font-medium focus:outline-none focus:border-brand-700">
                                <button type="button" onclick="togglePasswordVisibility('edit_password', this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer">
                                    <ion-icon name="eye-outline" class="text-xs leading-none"></ion-icon>
                                </button>
                            </div>
                            <p data-error-for="password" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                                <span></span>
                            </p>
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Confirm</label>
                            <div class="relative">
                                <input type="password" id="edit_password_confirmation" name="password_confirmation" placeholder="••••••••"
                                    class="w-full pl-2.5 pr-7 py-1.5 bg-white border border-neutral-200 rounded text-xs font-medium focus:outline-none focus:border-brand-700">
                                <button type="button" onclick="togglePasswordVisibility('edit_password_confirmation', this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer">
                                    <ion-icon name="eye-outline" class="text-xs leading-none"></ion-icon>
                                </button>
                            </div>
                            <p data-error-for="password_confirmation" class="field-error hidden text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <ion-icon name="alert-circle-outline" class="text-xs shrink-0"></ion-icon>
                                <span></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Footer Actions --}}
        <div class="px-5 py-3.5 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeEditUserModal()" class="px-3.5 py-2 text-xs font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="edit-user-submit-btn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                <ion-icon name="save-outline" class="text-sm"></ion-icon>
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</dialog>

{{-- ── 7. DELETE CONFIRMATION MODAL ───────────────────────────────────── --}}
<dialog id="delete-user-modal" class="confirm-modal modal-sharp max-w-[380px] w-full rounded-xl p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60" aria-labelledby="delete-modal-title">
    <div class="p-5">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-full bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0">
                <ion-icon name="trash-outline" class="text-xl"></ion-icon>
            </div>
            <div>
                <h3 id="delete-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Delete Account</h3>
                <p class="text-xs text-neutral-500 mt-1 leading-relaxed">
                    Are you sure you want to delete <strong id="delete-modal-name" class="text-neutral-900"></strong>?
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 bg-rose-50/70 border border-rose-200/70 rounded-lg text-xs text-rose-800 leading-relaxed">
            This will permanently delete the account and revoke all access. This action cannot be undone.
        </div>

        {{-- General Error Box for Delete --}}
        <div data-general-error class="general-error-box hidden mt-3 p-2.5 bg-rose-100/90 border border-rose-300 rounded-lg text-xs text-rose-900 flex items-center gap-2">
            <ion-icon name="alert-circle" class="text-base shrink-0 text-rose-600"></ion-icon>
            <span class="leading-tight font-medium"></span>
        </div>
    </div>

    <form id="delete-user-form" method="POST" action="">
        @csrf
        @method('DELETE')

        <div class="px-5 py-3.5 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeDeleteUserModal()" class="px-3.5 py-2 text-xs font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="delete-user-submit-btn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs cursor-pointer">
                <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                <span>Delete Account</span>
            </button>
        </div>
    </form>
</dialog>

{{-- ── 8. Client-Side Polish & Filtering Script ───────────────────────── --}}
<script>
    // ── Search & Filter Form Helpers ─────────────────────────────────────
    var searchInput = document.getElementById('user-search-input');
    var filterForm = document.getElementById('user-filter-form');
    var searchTimeout = null;

    if (searchInput && filterForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                filterForm.submit();
            }, 450);
        });
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
            submitBtn.innerHTML = '<ion-icon name="person-add" class="text-sm"></ion-icon><span>Create Account</span>';
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

    function toggleCreateRole(role) {
        var stallContainer = document.getElementById('create-stall-container');
        var adminBtn = document.getElementById('create-role-admin-btn');
        var staffBtn = document.getElementById('create-role-staff-btn');
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

        if (role === 'staff') {
            if (stallContainer) stallContainer.classList.remove('hidden');
            if (staffBtn) {
                staffBtn.className = "flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-bold text-xs transition-all shadow-xs bg-white text-brand-900 border border-neutral-200/80 cursor-pointer";
            }
            if (adminBtn) {
                adminBtn.className = "flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 transition-all cursor-pointer";
            }
        } else {
            if (stallContainer) stallContainer.classList.add('hidden');
            if (adminBtn) {
                adminBtn.className = "flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-bold text-xs transition-all shadow-xs bg-white text-brand-900 border border-neutral-200/80 cursor-pointer";
            }
            if (staffBtn) {
                staffBtn.className = "flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-md font-medium text-xs text-neutral-600 hover:text-neutral-900 transition-all cursor-pointer";
            }
        }
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

    function openEditUserModal(id, name, email, role, stallId, isSelf) {
        if (!editModal || !editForm) return;

        clearFormErrors(editForm);
        isEditingSelf = isSelf;

        editForm.action = "/admin/users/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
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
        if (pwdToggleText) pwdToggleText.textContent = 'Change Account Password';
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
            toggleText.textContent = 'Hide Password Fields';
        } else {
            pwdFields.classList.add('hidden');
            toggleText.textContent = 'Change Account Password';
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
        if (deleteName) deleteName.textContent = name + ' (' + (role === 'admin' ? 'Administrator' : 'Staff') + ')';
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
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-xs text-emerald-600 leading-none"></ion-icon>';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
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
