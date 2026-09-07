@extends('layouts.dashboard')
@section('title', 'User Management | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── Flash Messages ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <ion-icon name="checkmark-circle" class="text-lg text-emerald-600 shrink-0"></ion-icon>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200/80 text-red-800 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <ion-icon name="alert-circle" class="text-lg text-red-600 shrink-0"></ion-icon>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── 1. Page Header ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-200/80">
        <div>
            <h1 class="text-xl font-bold text-neutral-900 tracking-tight">User Management</h1>
            <p class="text-xs text-neutral-500 mt-0.5">Create and manage administrator and staff accounts. Students self-register via the public portal.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            @php
                $adminCount = $users->where('role', 'admin')->count();
                $staffCount = $users->where('role', 'staff')->count();
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80">
                <ion-icon name="shield-outline" class="text-sm" aria-hidden="true"></ion-icon>
                {{ $adminCount }} {{ Str::plural('Admin', $adminCount) }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-neutral-100 text-neutral-700 border border-neutral-200/80">
                <ion-icon name="person-outline" class="text-sm" aria-hidden="true"></ion-icon>
                {{ $staffCount }} {{ Str::plural('Staff', $staffCount) }}
            </span>
        </div>
    </div>

    {{-- ── 2. Main Layout: Form + Table ────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Create Account Form (4 cols) --}}
        <div class="lg:col-span-4 space-y-5">
            <div class="bg-white rounded-lg border border-neutral-200/80 p-5 shadow-2xs">
                <div class="mb-3.5 pb-2.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Create Account</h2>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Add a new admin or staff member to the system.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.create') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label for="create_role" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select id="create_role" name="role"
                            class="w-full px-3 py-2 bg-white border @error('role') border-red-300 bg-red-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors">
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        @error('role')
                            <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                <ion-icon name="alert-circle" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="create_name" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="create_name" name="name" value="{{ old('name') }}" placeholder="e.g. Juan Dela Cruz"
                            class="w-full px-3 py-2 bg-white border @error('name') border-red-300 bg-red-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors" required>
                        @error('name')
                            <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                <ion-icon name="alert-circle" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="create_email" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="create_email" name="email" value="{{ old('email') }}" placeholder="e.g. admin@isu.edu.ph"
                            class="w-full px-3 py-2 bg-white border @error('email') border-red-300 bg-red-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors" required>
                        @error('email')
                            <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                <ion-icon name="alert-circle" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="create_password" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="create_password" name="password" placeholder="••••••••"
                            class="w-full px-3 py-2 bg-white border @error('password') border-red-300 bg-red-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors" required>
                        @error('password')
                            <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
                                <ion-icon name="alert-circle" class="text-xs leading-none" aria-hidden="true"></ion-icon>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-[10px] text-neutral-400 mt-1">Min 8 chars, mixed case, number, symbol.</p>
                    </div>

                    <div>
                        <label for="create_password_confirmation" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="create_password_confirmation" name="password_confirmation" placeholder="••••••••"
                            class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors" required>
                    </div>

                    <button type="submit"
                        class="btn btn-primary w-full py-2.5 font-bold text-xs rounded-md border-0 cursor-pointer flex items-center justify-center gap-2">
                        <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                        Create Account
                    </button>
                </form>
            </div>

            {{-- Security Note --}}
            <div class="bg-amber-50/60 border border-amber-200/70 rounded-lg p-4">
                <div class="flex items-start gap-2.5">
                    <ion-icon name="lock-closed" class="text-amber-700 text-base mt-0.5 shrink-0"></ion-icon>
                    <div>
                        <p class="text-[11px] font-bold text-amber-900">Security Note</p>
                        <p class="text-[11px] text-amber-800/80 mt-0.5 leading-relaxed">
                            Admin and staff accounts can only be created from this page by authenticated administrators. Public registration is limited to student and staff roles only.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: User Table (8 cols) --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-lg border border-neutral-200/80 shadow-2xs overflow-hidden">

                {{-- Table Header / Search --}}
                <div class="p-4 sm:p-5 border-b border-neutral-100">
                    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="flex-1 relative">
                            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm pointer-events-none"></ion-icon>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email…"
                                class="w-full pl-8 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <select name="role_filter" onchange="this.form.submit()"
                                class="px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium focus:outline-none focus:border-brand-600">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role_filter') == 'admin' ? 'selected' : '' }}>Admins Only</option>
                                <option value="staff" {{ request('role_filter') == 'staff' ? 'selected' : '' }}>Staff Only</option>
                            </select>
                            @if(request('q') || request('role_filter'))
                                <a href="{{ route('admin.users') }}" class="px-3 py-2 border border-neutral-200 rounded-lg text-xs font-semibold text-neutral-600 hover:text-neutral-900 transition-colors" title="Clear filters">
                                    <ion-icon name="close-circle-outline" class="text-sm"></ion-icon>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Users Table --}}
                @if($users->isEmpty())
                    <div class="p-12 text-center">
                        <ion-icon name="people-outline" class="text-4xl text-neutral-300 mb-3"></ion-icon>
                        <p class="text-sm font-bold text-neutral-500">No accounts found</p>
                        <p class="text-xs text-neutral-400 mt-1">Create an admin or staff account using the form on the left.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-neutral-100 bg-neutral-50/60">
                                    <th class="px-4 sm:px-5 py-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider">User</th>
                                    <th class="px-4 sm:px-5 py-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 sm:px-5 py-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider hidden sm:table-cell">Created</th>
                                    <th class="px-4 sm:px-5 py-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100/80">
                                @foreach($users as $u)
                                    @php
                                        $isCurrentUser = $u->id === Auth::id();
                                        $roleBadge = $u->role === 'admin'
                                            ? 'bg-brand-50 text-brand-800 border-brand-200/70'
                                            : 'bg-neutral-100 text-neutral-700 border-neutral-200/70';
                                        $roleIcon = $u->role === 'admin' ? 'shield' : 'person';
                                    @endphp
                                    <tr class="hover:bg-neutral-50/50 transition-colors {{ $isCurrentUser ? 'bg-brand-50/30' : '' }}">
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-xs border border-brand-200/60 shrink-0">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-xs font-bold text-neutral-900 truncate flex items-center gap-1.5">
                                                        {{ $u->name }}
                                                        @if($isCurrentUser)
                                                            <span class="text-[9px] font-bold text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200/60">YOU</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[11px] text-neutral-500 truncate">{{ $u->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold border {{ $roleBadge }}">
                                                <ion-icon name="{{ $roleIcon }}" class="text-xs"></ion-icon>
                                                {{ ucfirst($u->role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5 hidden sm:table-cell">
                                            <span class="text-[11px] text-neutral-500 font-medium">
                                                {{ \Carbon\Carbon::parse($u->created_at)->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-5 py-3.5 text-right">
                                            @if(!$isCurrentUser)
                                                <form method="POST" action="{{ route('admin.users.delete', $u->id) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete {{ addslashes($u->name) }}\'s account? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-[11px] font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 transition-all cursor-pointer bg-transparent">
                                                        <ion-icon name="trash-outline" class="text-xs"></ion-icon>
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[11px] text-neutral-400 font-medium">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
