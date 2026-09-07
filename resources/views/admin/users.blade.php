@extends('layouts.dashboard')
@section('title', 'Staff & Admin Management | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── Flash Messages ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-lg text-emerald-600 shrink-0"></ion-icon>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 transition-colors p-1" aria-label="Dismiss">
                <ion-icon name="close-outline" class="text-base"></ion-icon>
            </button>
        </div>
    @endif
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
                Manage administrator and canteen staff accounts. Students create their own accounts on the register page.
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
        $adminCount = $users->where('role', 'admin')->count();
        $staffCount = $users->where('role', 'staff')->count();
        $assignedStaffCount = $users->where('role', 'staff')->whereNotNull('stall_id')->count();
        $unassignedStaffCount = $users->where('role', 'staff')->whereNull('stall_id')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Users Card --}}
        <div class="bg-white rounded-xl border border-neutral-200/80 p-4 sm:p-5 shadow-2xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">Total Accounts</span>
                <div class="w-8 h-8 rounded-lg bg-neutral-100 text-neutral-700 flex items-center justify-center">
                    <ion-icon name="people" class="text-base"></ion-icon>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-display text-neutral-900">{{ $users->count() }}</span>
                <span class="text-xs text-neutral-500 font-medium">registered</span>
            </div>
            <p class="text-[11px] text-neutral-400 mt-1">Canteen staff and administrators</p>
        </div>

        {{-- Administrators Card --}}
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
            <p class="text-[11px] text-neutral-400 mt-1">Full access to stalls, evaluations, and settings</p>
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
            <p class="text-[11px] text-neutral-400 mt-1">{{ $assignedStaffCount }} assigned to food stalls</p>
        </div>
    </div>

    {{-- ── 3. Search & Filter Bar ───────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs p-4 sm:p-5 space-y-3">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            {{-- Instant Search Input --}}
            <div class="flex-1 relative">
                <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                <input type="text" id="user-search-input" placeholder="Search accounts by name, email, or stall…"
                    class="w-full pl-9 pr-9 py-2 bg-neutral-50/70 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition-all">
                <button type="button" id="user-search-clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 hidden cursor-pointer" aria-label="Clear search">
                    <ion-icon name="close-circle" class="text-base"></ion-icon>
                </button>
            </div>

            {{-- Role Filter Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 shrink-0 text-xs">
                <button type="button" onclick="filterByRole('all')" id="filter-btn-all"
                    class="px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer bg-neutral-900 text-white shadow-2xs">
                    All ({{ $users->count() }})
                </button>
                <button type="button" onclick="filterByRole('admin')" id="filter-btn-admin"
                    class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-neutral-100 text-neutral-600 hover:bg-neutral-200">
                    Admins ({{ $adminCount }})
                </button>
                <button type="button" onclick="filterByRole('staff')" id="filter-btn-staff"
                    class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-neutral-100 text-neutral-600 hover:bg-neutral-200">
                    Staff ({{ $staffCount }})
                </button>
                @if($unassignedStaffCount > 0)
                    <button type="button" onclick="filterByRole('unassigned')" id="filter-btn-unassigned"
                        class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 flex items-center gap-1">
                        <ion-icon name="alert-circle" class="text-xs text-amber-600"></ion-icon>
                        <span>Unassigned ({{ $unassignedStaffCount }})</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Active Result Count Indicator --}}
        <div class="flex items-center justify-between text-[11px] text-neutral-500 pt-2 border-t border-neutral-100">
            <span id="results-count-text">Showing {{ $users->count() }} of {{ $users->count() }} accounts</span>
            <span class="text-neutral-400">Click on any account row to view quick actions</span>
        </div>
    </div>

    {{-- ── 4. Main Account Directory ───────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/80 shadow-2xs overflow-hidden">
        @if($users->isEmpty())
            <div class="p-16 text-center">
                <div class="w-14 h-14 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto mb-3">
                    <ion-icon name="people-outline" class="text-2xl"></ion-icon>
                </div>
                <h3 class="text-sm font-bold text-neutral-800">No staff or admin accounts found</h3>
                <p class="text-xs text-neutral-400 mt-1 max-w-sm mx-auto">Create your first administrator or staff account using the button above.</p>
                <button type="button" onclick="openCreateUserModal()" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-lg mt-4 inline-flex items-center gap-1.5">
                    <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                    <span>Create Account</span>
                </button>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
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
                                        <span class="text-neutral-400 text-xs font-medium flex items-center gap-1">
                                            <ion-icon name="lock-closed-outline" class="text-xs text-neutral-300"></ion-icon>
                                            <span>All Campus Stalls</span>
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
                                    <span class="block text-[10px] text-neutral-400 font-normal">
                                        {{ \Carbon\Carbon::parse($u->created_at)->diffForHumans() }}
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
                                            <span class="inline-flex items-center px-2.5 py-1.5 text-xs text-neutral-400 italic">
                                                Protected
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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
                            <span class="text-[11px] text-neutral-400 font-semibold uppercase">Assignment:</span>
                            @if($isAdmin)
                                <span class="font-medium text-neutral-500">All Stalls</span>
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

            {{-- No Results from Search Filter --}}
            <div id="no-search-results" class="hidden p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto mb-2">
                    <ion-icon name="search-outline" class="text-xl"></ion-icon>
                </div>
                <p class="text-sm font-bold text-neutral-700">No matching accounts found</p>
                <p class="text-xs text-neutral-400 mt-0.5">Try searching with a different name, email, or role filter.</p>
                <button type="button" onclick="resetFilters()" class="btn btn-ghost btn-sm text-xs mt-3 border border-neutral-200">
                    Reset Filter
                </button>
            </div>
        @endif
    </div>

</div>

{{-- ── 5. CREATE ACCOUNT MODAL ─────────────────────────────────────────── --}}
<dialog id="create-user-modal" class="confirm-modal max-w-md w-full relative p-0 overflow-hidden" aria-labelledby="create-modal-title">
    <div id="create-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="p-6">
        <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center">
                    <ion-icon name="person-add-outline" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h3 id="create-modal-title" class="text-base font-bold font-display text-neutral-900 leading-tight">Create New Account</h3>
                    <p class="text-[11px] text-neutral-500">Add an administrator or canteen stall staff member</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="text-neutral-400 hover:text-neutral-600 transition-colors p-1" aria-label="Close">
                <ion-icon name="close-outline" class="text-xl leading-none"></ion-icon>
            </button>
        </div>

        <form id="create-user-form" method="POST" action="{{ route('admin.users.create') }}" class="space-y-4 mt-4">
            @csrf

            {{-- Role Selection Radio Cards --}}
            <div>
                <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1.5">
                    Account Role <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label class="relative flex flex-col p-3 border rounded-lg cursor-pointer transition-all role-card" id="create-role-admin-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-neutral-900">Administrator</span>
                            <input type="radio" name="role" value="admin" checked onchange="toggleCreateRole('admin')" class="accent-brand-600">
                        </div>
                        <p class="text-[10px] text-neutral-500 leading-tight">Full access to algorithms, stalls, scores, and users.</p>
                    </label>

                    <label class="relative flex flex-col p-3 border rounded-lg cursor-pointer transition-all role-card" id="create-role-staff-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-neutral-900">Canteen Staff</span>
                            <input type="radio" name="role" value="staff" onchange="toggleCreateRole('staff')" class="accent-brand-600">
                        </div>
                        <p class="text-[10px] text-neutral-500 leading-tight">Views feedback and rankings for their assigned food stall.</p>
                    </label>
                </div>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="create-stall-container" class="hidden p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5 transition-all">
                <label for="create_stall_id" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider">
                    Assign to Canteen Stall <span class="text-neutral-400 font-normal">(Optional)</span>
                </label>
                <select id="create_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-600">
                    <option value="">-- Assign Later (Unassigned) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-neutral-500">You can also reassign or attach stall staff later under Manage Stalls.</p>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="create_name" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="create_name" name="name" placeholder="e.g. Maria Santos" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
            </div>

            {{-- Email Address --}}
            <div>
                <label for="create_email" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="create_email" name="email" placeholder="e.g. maria@isu.edu.ph" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
            </div>

            {{-- Password Fields --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="create_password" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="create_password" name="password" placeholder="••••••••" required
                            class="w-full pl-3 pr-8 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                        <button type="button" onclick="togglePasswordVisibility('create_password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer">
                            <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="create_password_confirmation" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Confirm <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="create_password_confirmation" name="password_confirmation" placeholder="••••••••" required
                            class="w-full pl-3 pr-8 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                        <button type="button" onclick="togglePasswordVisibility('create_password_confirmation', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600 cursor-pointer">
                            <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-[10px] text-neutral-400 leading-tight">Must contain at least 8 characters, mixed case letters, numbers, and symbols.</p>

            {{-- Form Buttons --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
                <button type="button" onclick="closeCreateUserModal()" class="btn btn-ghost btn-sm text-xs font-semibold px-3 py-2 rounded-lg text-neutral-600 hover:text-neutral-900 border border-neutral-200">
                    Cancel
                </button>
                <button type="submit" id="create-user-submit-btn" class="btn btn-primary btn-sm text-xs font-bold px-4 py-2 rounded-lg flex items-center gap-1.5 shadow-2xs cursor-pointer">
                    <ion-icon name="person-add" class="text-sm"></ion-icon>
                    <span>Create Account</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 6. EDIT ACCOUNT MODAL ───────────────────────────────────────────── --}}
<dialog id="edit-user-modal" class="confirm-modal max-w-md w-full relative p-0 overflow-hidden" aria-labelledby="edit-modal-title">
    <div id="edit-modal-loader" class="modal-loading-bar hidden">
        <div class="modal-loading-bar-inner"></div>
    </div>

    <div class="p-6">
        <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-neutral-100 text-neutral-700 flex items-center justify-center">
                    <ion-icon name="create-outline" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h3 id="edit-modal-title" class="text-base font-bold font-display text-neutral-900 leading-tight">Edit Account</h3>
                    <p class="text-[11px] text-neutral-500">Update account details, role, or credentials</p>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-neutral-400 hover:text-neutral-600 transition-colors p-1" aria-label="Close">
                <ion-icon name="close-outline" class="text-xl leading-none"></ion-icon>
            </button>
        </div>

        <form id="edit-user-form" method="POST" action="" class="space-y-4 mt-4">
            @csrf
            @method('PUT')

            {{-- Role Selection --}}
            <div>
                <label for="edit_role" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="edit_role" name="role" onchange="toggleEditRole(this.value)"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600">
                    <option value="admin">Administrator</option>
                    <option value="staff">Canteen Staff</option>
                </select>
                <p id="edit-self-warning" class="hidden text-[10px] text-amber-700 mt-1 font-medium flex items-center gap-1">
                    <ion-icon name="information-circle" class="text-xs"></ion-icon>
                    You are editing your own logged-in account. You cannot remove your administrator privileges.
                </p>
            </div>

            {{-- Dynamic Stall Assignment Selector (for staff) --}}
            <div id="edit-stall-container" class="hidden p-3 bg-neutral-50 border border-neutral-200/80 rounded-lg space-y-1.5">
                <label for="edit_stall_id" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider">
                    Assigned Canteen Stall
                </label>
                <select id="edit_stall_id" name="stall_id"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-600">
                    <option value="">-- Unassigned (No Stall) --</option>
                    @foreach($stalls as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Full Name --}}
            <div>
                <label for="edit_name" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit_name" name="name" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600">
            </div>

            {{-- Email Address --}}
            <div>
                <label for="edit_email" class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="edit_email" name="email" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600">
            </div>

            {{-- Optional Password Reset Section --}}
            <div class="pt-2 border-t border-neutral-100">
                <button type="button" onclick="toggleEditPasswordSection()" class="text-xs font-bold text-brand-700 hover:text-brand-900 flex items-center gap-1 cursor-pointer">
                    <ion-icon name="key-outline" class="text-sm"></ion-icon>
                    <span id="edit-pwd-toggle-text">Change Account Password</span>
                </button>

                <div id="edit-password-fields" class="hidden space-y-3 mt-3 p-3 bg-neutral-50 rounded-lg border border-neutral-200">
                    <p class="text-[10px] text-neutral-500">Leave blank to keep existing password.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_password" class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">New Password</label>
                            <input type="password" id="edit_password" name="password" placeholder="••••••••"
                                class="w-full px-2.5 py-1.5 bg-white border border-neutral-300 rounded text-xs font-medium focus:outline-none focus:border-brand-600">
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Confirm Password</label>
                            <input type="password" id="edit_password_confirmation" name="password_confirmation" placeholder="••••••••"
                                class="w-full px-2.5 py-1.5 bg-white border border-neutral-300 rounded text-xs font-medium focus:outline-none focus:border-brand-600">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Buttons --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
                <button type="button" onclick="closeEditUserModal()" class="btn btn-ghost btn-sm text-xs font-semibold px-3 py-2 rounded-lg text-neutral-600 hover:text-neutral-900 border border-neutral-200">
                    Cancel
                </button>
                <button type="submit" id="edit-user-submit-btn" class="btn btn-primary btn-sm text-xs font-bold px-4 py-2 rounded-lg flex items-center gap-1.5 shadow-2xs cursor-pointer">
                    <ion-icon name="save-outline" class="text-sm"></ion-icon>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 7. DELETE CONFIRMATION MODAL ───────────────────────────────────── --}}
<dialog id="delete-user-modal" class="confirm-modal max-w-sm w-full relative p-0 overflow-hidden" aria-labelledby="delete-modal-title">
    <div class="p-6">
        <div class="flex items-start gap-3.5 mb-3">
            <div class="w-10 h-10 rounded-full bg-red-50 border border-red-200 text-red-600 flex items-center justify-center shrink-0">
                <ion-icon name="trash-outline" class="text-xl"></ion-icon>
            </div>
            <div>
                <h3 id="delete-modal-title" class="text-base font-bold font-display text-neutral-900 leading-tight">Delete Account</h3>
                <p class="text-xs text-neutral-500 mt-1 leading-relaxed">
                    Are you sure you want to delete <strong id="delete-modal-name" class="text-neutral-900"></strong>'s account?
                </p>
            </div>
        </div>

        <div class="p-3 bg-red-50/70 border border-red-200/80 rounded-lg text-[11px] text-red-800 leading-relaxed mb-4">
            This will permanently revoke all access permissions for this user. This action cannot be undone.
        </div>

        <form id="delete-user-form" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                <button type="button" onclick="closeDeleteUserModal()" class="btn btn-ghost btn-sm text-xs font-semibold px-3 py-2 rounded-lg text-neutral-600 hover:text-neutral-900 border border-neutral-200">
                    Cancel
                </button>
                <button type="submit" class="btn btn-sm text-xs font-bold px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white border border-red-600 shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="trash" class="text-sm"></ion-icon>
                    <span>Delete Account</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- ── 8. Client-Side Polish & Filtering Script ───────────────────────── --}}
<script>
    // ── Instant Real-time Filter & Search ─────────────────────────────────
    var currentRoleFilter = 'all';
    var userRows = document.querySelectorAll('.user-row');
    var searchInput = document.getElementById('user-search-input');
    var clearSearchBtn = document.getElementById('user-search-clear');
    var noResultsDiv = document.getElementById('no-search-results');
    var countText = document.getElementById('results-count-text');
    var totalUsersCount = {{ $users->count() }};

    function applyUserFilters() {
        var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        var visibleCount = 0;

        if (clearSearchBtn) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        userRows.forEach(function(row) {
            var name = row.getAttribute('data-name') || '';
            var email = row.getAttribute('data-email') || '';
            var role = row.getAttribute('data-role') || '';
            var stall = row.getAttribute('data-stall') || '';
            var isAssigned = row.getAttribute('data-assigned') === '1';

            var matchesSearch = query === '' || name.includes(query) || email.includes(query) || stall.includes(query);
            var matchesRole = true;

            if (currentRoleFilter === 'admin') {
                matchesRole = role === 'admin';
            } else if (currentRoleFilter === 'staff') {
                matchesRole = role === 'staff';
            } else if (currentRoleFilter === 'unassigned') {
                matchesRole = role === 'staff' && !isAssigned;
            }

            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultsDiv) {
            noResultsDiv.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        if (countText) {
            countText.textContent = 'Showing ' + visibleCount + ' of ' + totalUsersCount + ' accounts';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyUserFilters);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            applyUserFilters();
            searchInput.focus();
        });
    }

    function filterByRole(role) {
        currentRoleFilter = role;

        var allButtons = [
            document.getElementById('filter-btn-all'),
            document.getElementById('filter-btn-admin'),
            document.getElementById('filter-btn-staff'),
            document.getElementById('filter-btn-unassigned')
        ];

        allButtons.forEach(function(btn) {
            if (!btn) return;
            btn.className = "px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-neutral-100 text-neutral-600 hover:bg-neutral-200";
        });

        var activeBtn = document.getElementById('filter-btn-' + role);
        if (activeBtn) {
            if (role === 'unassigned') {
                activeBtn.className = "px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer bg-amber-500 text-white shadow-2xs flex items-center gap-1";
            } else {
                activeBtn.className = "px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer bg-neutral-900 text-white shadow-2xs";
            }
        }

        applyUserFilters();
    }

    function resetFilters() {
        if (searchInput) searchInput.value = '';
        filterByRole('all');
    }

    // ── Create Modal Controls ─────────────────────────────────────────────
    var createModal = document.getElementById('create-user-modal');

    function openCreateUserModal() {
        if (!createModal) return;
        toggleCreateRole('admin');
        createModal.showModal();
    }

    function closeCreateUserModal() {
        if (createModal) createModal.close();
    }

    function toggleCreateRole(role) {
        var stallContainer = document.getElementById('create-stall-container');
        var adminCard = document.getElementById('create-role-admin-card');
        var staffCard = document.getElementById('create-role-staff-card');

        if (role === 'staff') {
            if (stallContainer) stallContainer.classList.remove('hidden');
            if (staffCard) staffCard.className = "relative flex flex-col p-3 border-2 border-brand-600 bg-brand-50/20 rounded-lg cursor-pointer transition-all role-card";
            if (adminCard) adminCard.className = "relative flex flex-col p-3 border border-neutral-200 rounded-lg cursor-pointer transition-all role-card";
        } else {
            if (stallContainer) stallContainer.classList.add('hidden');
            if (adminCard) adminCard.className = "relative flex flex-col p-3 border-2 border-brand-600 bg-brand-50/20 rounded-lg cursor-pointer transition-all role-card";
            if (staffCard) staffCard.className = "relative flex flex-col p-3 border border-neutral-200 rounded-lg cursor-pointer transition-all role-card";
        }
    }

    // ── Edit Modal Controls ───────────────────────────────────────────────
    var editModal = document.getElementById('edit-user-modal');
    var editForm = document.getElementById('edit-user-form');

    function openEditUserModal(id, name, email, role, stallId, isSelf) {
        if (!editModal || !editForm) return;

        editForm.action = "/admin/users/" + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;

        var stallSelect = document.getElementById('edit_stall_id');
        if (stallSelect) stallSelect.value = stallId ? stallId : '';

        var selfWarning = document.getElementById('edit-self-warning');
        var roleSelect = document.getElementById('edit_role');
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
        var pwdInput = document.getElementById('edit_password');
        var pwdConfirm = document.getElementById('edit_password_confirmation');
        if (pwdInput) pwdInput.value = '';
        if (pwdConfirm) pwdConfirm.value = '';

        editModal.showModal();
    }

    function closeEditUserModal() {
        if (editModal) editModal.close();
    }

    function toggleEditRole(role) {
        var stallContainer = document.getElementById('edit-stall-container');
        if (role === 'staff') {
            if (stallContainer) stallContainer.classList.remove('hidden');
        } else {
            if (stallContainer) stallContainer.classList.add('hidden');
        }
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

    // ── Delete Modal Controls ─────────────────────────────────────────────
    var deleteModal = document.getElementById('delete-user-modal');
    var deleteForm = document.getElementById('delete-user-form');
    var deleteName = document.getElementById('delete-modal-name');

    function openDeleteUserModal(id, name, role) {
        if (!deleteModal || !deleteForm) return;
        deleteForm.action = "/admin/users/" + id;
        if (deleteName) deleteName.textContent = name + ' (' + (role === 'admin' ? 'Administrator' : 'Staff') + ')';
        deleteModal.showModal();
    }

    function closeDeleteUserModal() {
        if (deleteModal) deleteModal.close();
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
