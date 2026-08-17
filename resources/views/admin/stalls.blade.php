@extends('layouts.dashboard')
@section('title', 'Manage Stalls | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-200/80">
        <div>
            <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Manage Stalls &amp; Staff</h1>
            <p class="text-xs text-neutral-500 mt-0.5">Add, rename, assign staff accounts, and monitor canteen food vendors across campus.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80">
                <ion-icon name="storefront-outline" class="text-sm" aria-hidden="true"></ion-icon>
                {{ $stalls->count() }} {{ Str::plural('Stall', $stalls->count()) }} Total
            </span>
            @if($unassignedStaff->isNotEmpty())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300">
                    <ion-icon name="alert-circle" class="text-sm text-amber-600" aria-hidden="true"></ion-icon>
                    {{ $unassignedStaff->count() }} Unassigned Staff
                </span>
            @endif
        </div>
    </div>

    {{-- ── 2. Pending Staff Assignment Banner (Quick Onboarding) ───────────── --}}
    @if($unassignedStaff->isNotEmpty())
        <div class="p-4 bg-amber-50/90 border border-amber-200/90 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                    <ion-icon name="people" class="text-lg text-amber-700"></ion-icon>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-amber-950">
                        {{ $unassignedStaff->count() }} {{ Str::plural('Staff Account', $unassignedStaff->count()) }} Pending Assignment
                    </h3>
                    <p class="text-[11px] text-amber-800/90 mt-0.5">
                        These registered staff members cannot view their stall evaluations or rankings until assigned to an active stall.
                    </p>
                </div>
            </div>
            <button type="button" onclick="openQuickAssignModal()"
                class="btn btn-primary text-xs font-bold px-3 py-1.5 rounded-md self-start sm:self-auto shrink-0 shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                <span>Assign Staff</span>
            </button>
        </div>
    @endif

    {{-- ── 3. Main 2-Column Layout ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Creation Form & Guidelines (4 cols) --}}
        <div class="lg:col-span-4 space-y-5">
            
            {{-- Add Stall Card --}}
            <div class="bg-white rounded-lg border border-neutral-200/80 p-5 shadow-2xs">
                <div class="mb-3.5 pb-2.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Add New Stall</h2>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Register a vendor &amp; optionally assign staff members.</p>
                </div>

                <form action="{{ route('admin.stall.add') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label for="add-stall-name" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Stall Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="add-stall-name" name="name"
                            value="{{ old('name') }}"
                            class="w-full px-3 py-2 bg-white border @error('name') border-red-300 bg-red-50/20 @else border-neutral-300 @enderror rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors"
                            placeholder="e.g. Stall #1 — Food Hub" required>
                        @error('name')
                            <p class="text-xs text-red-600 font-medium flex items-center gap-1 mt-1">
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
                            <span id="add-staff-selected-count" class="text-[10px] font-bold text-brand-700">0 selected</span>
                        </div>

                        @if($staffUsers->isEmpty())
                            <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-md text-center">
                                <p class="text-[11px] text-neutral-500">No staff accounts registered yet.</p>
                            </div>
                        @else
                            {{-- Search in Add Form --}}
                            <div class="relative mb-1.5">
                                <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-xs pointer-events-none"></ion-icon>
                                <input type="text" id="add-staff-search" placeholder="Search staff…"
                                    class="w-full pl-7 pr-3 py-1 bg-white border border-neutral-300 rounded text-[11px] font-medium focus:outline-none focus:border-brand-700">
                            </div>

                            <div id="add-staff-list" class="max-h-36 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-300 rounded-md space-y-1 custom-scrollbar">
                                @foreach($staffUsers as $staff)
                                    <label class="add-staff-item flex items-center justify-between p-1.5 rounded hover:bg-white border border-transparent hover:border-neutral-200 transition-colors cursor-pointer text-xs"
                                        data-staff-name="{{ strtolower($staff->name) }}" data-staff-email="{{ strtolower($staff->email) }}">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}"
                                                class="add-staff-checkbox rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-3.5 w-3.5">
                                            <div class="min-w-0">
                                                <span class="font-bold text-neutral-900 truncate block text-[11px]">{{ $staff->name }}</span>
                                                <span class="text-[10px] text-neutral-400 truncate block font-mono">{{ $staff->email }}</span>
                                            </div>
                                        </div>
                                        @if(!$staff->stall_id)
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold shrink-0">Unassigned</span>
                                        @else
                                            <span class="px-1.5 py-0.2 rounded bg-neutral-100 text-neutral-500 border border-neutral-200 text-[9px] font-medium shrink-0 truncate max-w-[80px]" title="Currently in {{ $staff->current_stall_name }}">{{ $staff->current_stall_name }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-[10px] text-neutral-400 mt-1">Multiple staff accounts can manage the same food stall.</p>
                    </div>

                    <div>
                        <label for="add-stall-desc" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Description (Optional)
                        </label>
                        <textarea id="add-stall-desc" name="description" rows="2"
                            class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors"
                            placeholder="Short description of offerings or location">{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary text-xs py-2 px-4 font-semibold flex items-center justify-center gap-1.5 w-full rounded-md shadow-2xs cursor-pointer">
                        <ion-icon name="add-outline" class="text-base"></ion-icon>
                        Create Stall
                    </button>
                </form>
            </div>

            {{-- Guidelines Card --}}
            <div class="bg-white rounded-lg border border-neutral-200/80 p-4.5 shadow-2xs space-y-2.5 text-xs">
                <div class="flex items-center gap-1.5 text-neutral-800 font-bold text-[11px] uppercase tracking-wider">
                    <ion-icon name="information-circle-outline" class="text-base text-brand-700" aria-hidden="true"></ion-icon>
                    <span>Staff &amp; Stall Guidelines</span>
                </div>
                <ul class="space-y-1.5 text-neutral-600 text-[11px] font-medium leading-relaxed">
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>A single stall can have multiple staff members assigned to it.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Click any staff badge to view the full scrollable roster &amp; manage assignments.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Unassigned staff are blocked from viewing campus standings until assigned.</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Right Column: Stalls Directory (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white rounded-lg border border-neutral-200/80 shadow-2xs overflow-hidden">
                {{-- Header & Search Bar --}}
                <div class="px-5 py-4 border-b border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-neutral-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Active Stalls Directory</h2>
                        <p class="text-[11px] text-neutral-500 mt-0.5">Manage existing vendor accounts, staff assignments, and ratings</p>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-56">
                        <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none" aria-hidden="true"></ion-icon>
                        <input type="text" id="stall-search-input"
                            placeholder="Filter stalls…"
                            aria-label="Filter stalls by name"
                            class="w-full pl-8 pr-3 py-1.5 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
                    </div>
                </div>

                {{-- Stalls List --}}
                <div id="stalls-list-container" class="divide-y divide-neutral-100">
                    @forelse($stalls as $stall)
                        @php
                            $stallScore = $results->firstWhere('name', $stall->name);
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
                        <div class="stall-item flex flex-col sm:flex-row sm:items-center justify-between py-3.5 px-5 gap-3 hover:bg-neutral-50/70 transition-colors" data-stall-name="{{ strtolower($stall->name) }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200/70 text-brand-800 flex items-center justify-center shrink-0">
                                    <ion-icon name="storefront-outline" class="text-lg text-brand-800"></ion-icon>
                                </div>
                                <div class="min-w-0 space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-neutral-900 truncate">{{ $stall->name }}</span>
                                        @if($stall->is_active)
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                        @else
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.2 rounded bg-neutral-100 text-neutral-500 border border-neutral-200">Inactive</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 text-[11px] text-neutral-500 flex-wrap">
                                        {{-- Rating --}}
                                        @if($avgRating !== null)
                                            <span class="inline-flex items-center gap-0.5 font-bold tabular-nums {{ $avgRating >= 4 ? 'text-brand-700' : ($avgRating >= 3 ? 'text-amber-700' : 'text-red-600') }}">
                                                <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                                {{ number_format($avgRating, 2) }} / 5.00
                                            </span>
                                        @else
                                            <span class="text-neutral-400">No ratings yet</span>
                                        @endif

                                        <span class="text-neutral-300">•</span>

                                        {{-- Scalable Compact Staff Presentation with Interactive Roster --}}
                                        @if($staffCount === 0)
                                            <button type="button" onclick="openEditModal({{ $stall->id }}, '{{ addslashes($stall->name) }}', {{ $staffIdsJson }}, '{{ addslashes($stall->description ?? '') }}', {{ $stall->is_active ? 1 : 0 }})"
                                                class="text-[10px] text-neutral-400 hover:text-brand-700 font-medium inline-flex items-center gap-1 cursor-pointer transition-colors">
                                                <ion-icon name="person-add-outline" class="text-xs"></ion-icon>
                                                <span>Assign staff</span>
                                            </button>
                                        @elseif($staffCount === 1)
                                            <button type="button" onclick="openRosterModal({{ $stall->id }}, '{{ addslashes($stall->name) }}', {{ $staffRosterJson }})"
                                                class="inline-flex items-center gap-1 font-semibold text-brand-800 bg-brand-50 hover:bg-brand-100 px-2 py-0.5 rounded text-[10px] border border-brand-200/60 transition-colors cursor-pointer"
                                                title="Click to view staff roster for {{ $stall->name }}">
                                                <ion-icon name="person-outline" class="text-xs"></ion-icon>
                                                Staff: {{ $assignedStaffList->first()->name }}
                                            </button>
                                        @elseif($staffCount === 2)
                                            <button type="button" onclick="openRosterModal({{ $stall->id }}, '{{ addslashes($stall->name) }}', {{ $staffRosterJson }})"
                                                class="inline-flex items-center gap-1 font-semibold text-brand-800 bg-brand-50 hover:bg-brand-100 px-2 py-0.5 rounded text-[10px] border border-brand-200/60 transition-colors cursor-pointer"
                                                title="Click to view staff roster for {{ $stall->name }}">
                                                <ion-icon name="people-outline" class="text-xs"></ion-icon>
                                                Staff: {{ Str::limit($assignedStaffList[0]->name, 12) }}, {{ Str::limit($assignedStaffList[1]->name, 12) }}
                                            </button>
                                        @else
                                            <button type="button" onclick="openRosterModal({{ $stall->id }}, '{{ addslashes($stall->name) }}', {{ $staffRosterJson }})"
                                                class="inline-flex items-center gap-1 font-semibold text-brand-800 bg-brand-50 hover:bg-brand-100 px-2 py-0.5 rounded text-[10px] border border-brand-200/60 transition-colors cursor-pointer"
                                                title="Click to view all {{ $staffCount }} staff members">
                                                <ion-icon name="people-outline" class="text-xs"></ion-icon>
                                                Staff: {{ Str::limit($assignedStaffList[0]->name, 10) }}, {{ Str::limit($assignedStaffList[1]->name, 10) }}
                                                <span class="bg-brand-200/70 text-brand-900 px-1 rounded text-[9px] font-bold">+{{ $staffCount - 2 }} more</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                {{-- Edit Button --}}
                                <button type="button"
                                    onclick="openEditModal({{ $stall->id }}, '{{ addslashes($stall->name) }}', {{ $staffIdsJson }}, '{{ addslashes($stall->description ?? '') }}', {{ $stall->is_active ? 1 : 0 }})"
                                    aria-label="Edit {{ $stall->name }}"
                                    class="text-neutral-700 hover:text-brand-800 text-xs font-semibold inline-flex items-center gap-1 transition-colors bg-white hover:bg-neutral-50 px-2.5 py-1.5 rounded-md border border-neutral-200 hover:border-brand-300 shadow-2xs cursor-pointer">
                                    <ion-icon name="pencil-outline" class="text-sm"></ion-icon>
                                    <span>Edit</span>
                                </button>

                                {{-- Hidden Delete Form --}}
                                <form id="delete-form-{{ $stall->id }}" action="{{ route('admin.stall.delete', $stall->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                {{-- Delete Button --}}
                                <button type="button"
                                    onclick="openDeleteModal({{ $stall->id }}, '{{ addslashes($stall->name) }}')"
                                    aria-label="Delete {{ $stall->name }}"
                                    class="text-red-600 hover:text-red-700 text-xs font-semibold inline-flex items-center gap-1 transition-colors bg-white hover:bg-red-50 px-2.5 py-1.5 rounded-md border border-neutral-200 hover:border-red-300 shadow-2xs cursor-pointer">
                                    <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 px-6 text-center">
                            <div class="w-10 h-10 rounded-md flex items-center justify-center mx-auto mb-2.5 bg-neutral-100 text-neutral-400">
                                <ion-icon name="storefront-outline" class="text-2xl text-neutral-400 opacity-40"></ion-icon>
                            </div>
                            <p class="text-xs font-bold text-neutral-700 mb-0.5">No stalls registered yet</p>
                            <p class="text-[11px] text-neutral-500 max-w-xs mx-auto">Use the form on the left to add your first canteen vendor.</p>
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
<dialog id="edit-modal" class="confirm-modal max-w-lg" aria-labelledby="edit-modal-title">
    <form id="edit-form" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-800">
                    <ion-icon name="create-outline" class="text-lg text-brand-800" aria-hidden="true"></ion-icon>
                </div>
                <div>
                    <h3 id="edit-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Edit Food Stall</h3>
                    <p class="text-[11px] text-neutral-500">Update vendor details &amp; assigned staff members</p>
                </div>
            </div>
            <button type="button" class="text-neutral-400 hover:text-neutral-600 js-close-edit-modal p-1 cursor-pointer" aria-label="Close modal">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="space-y-3.5 text-left">
            <div>
                <label for="edit-stall-name-input" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Stall Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit-stall-name-input" name="name"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700"
                    required>
            </div>

            {{-- Searchable Staff Picker in Modal --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-[11px] font-semibold text-neutral-700 uppercase tracking-wider">
                        Assigned Staff Members
                    </label>
                    <span id="edit-staff-count" class="text-[10px] font-bold text-brand-700">0 selected</span>
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
                                class="w-full pl-7 pr-3 py-1.5 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
                        </div>

                        {{-- Filter Tabs --}}
                        <div class="flex items-center gap-1.5 text-[10px]">
                            <button type="button" id="tab-all-staff" onclick="filterModalStaff('all')" class="px-2 py-0.5 rounded font-bold bg-neutral-900 text-white transition-colors cursor-pointer">All ({{ $staffUsers->count() }})</button>
                            <button type="button" id="tab-unassigned-staff" onclick="filterModalStaff('unassigned')" class="px-2 py-0.5 rounded font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer">⭐ Unassigned ({{ $unassignedStaff->count() }})</button>
                            <button type="button" id="tab-selected-staff" onclick="filterModalStaff('selected')" class="px-2 py-0.5 rounded font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer">Selected Only</button>
                        </div>
                    </div>

                    {{-- Scrollable Staff Checkbox List --}}
                    <div id="edit-staff-list" class="max-h-48 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-300 rounded-md space-y-1 divide-y divide-neutral-200/40 custom-scrollbar">
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
                                    <div class="min-w-0">
                                        <span class="font-bold text-neutral-900 truncate block text-xs">{{ $staff->name }}</span>
                                        <span class="text-[11px] text-neutral-400 truncate block font-mono">{{ $staff->email }}</span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    @if(!$staff->stall_id)
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">Unassigned</span>
                                    @else
                                        <span id="staff-stall-tag-{{ $staff->id }}" class="px-2 py-0.5 rounded bg-neutral-100 text-neutral-600 border border-neutral-200 text-[10px] font-medium truncate max-w-[110px] inline-block" title="{{ $staff->current_stall_name }}">
                                            {{ $staff->current_stall_name }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                <p class="text-[10px] text-neutral-400 mt-1">Checking a staff member assigned elsewhere will reassign them to this stall.</p>
            </div>

            <div>
                <label for="edit-stall-desc-input" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Description
                </label>
                <textarea id="edit-stall-desc-input" name="description" rows="2"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700"></textarea>
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

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-edit-modal cursor-pointer">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold rounded-md flex items-center gap-1 shadow-2xs cursor-pointer">
                <ion-icon name="save-outline" class="text-sm leading-none" aria-hidden="true"></ion-icon>
                Save Changes
            </button>
        </div>
    </form>
</dialog>

{{-- ── 5. Dedicated Stall Staff Roster Modal (Scrollable at Scale) ───────── --}}
<dialog id="roster-modal" class="confirm-modal max-w-md" aria-labelledby="roster-modal-title">
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-800">
                    <ion-icon name="people-outline" class="text-lg text-brand-800" aria-hidden="true"></ion-icon>
                </div>
                <div>
                    <h3 id="roster-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Staff Roster</h3>
                    <p id="roster-modal-subtitle" class="text-[11px] text-neutral-500">Assigned members for this vendor</p>
                </div>
            </div>
            <button type="button" class="text-neutral-400 hover:text-neutral-600 js-close-roster-modal p-1 cursor-pointer" aria-label="Close roster">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        {{-- Filter within Roster --}}
        <div class="relative">
            <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-xs pointer-events-none"></ion-icon>
            <input type="text" id="roster-search-input" placeholder="Search members in roster…"
                class="w-full pl-7 pr-3 py-1.5 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
        </div>

        {{-- Scrollable List of Assigned Staff --}}
        <div id="roster-staff-list" class="max-h-60 overflow-y-auto p-1.5 bg-neutral-50 border border-neutral-300 rounded-md space-y-1.5 custom-scrollbar">
            <!-- Rendered dynamically by JavaScript -->
        </div>

        {{-- Hidden Form for Unassign Action --}}
        <form id="unassign-staff-form" action="{{ route('admin.staff.unassign') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="staff_id" id="unassign-staff-id">
        </form>

        <div class="flex items-center justify-between pt-3 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-roster-modal cursor-pointer">Close</button>
            <button type="button" id="roster-edit-stall-btn"
                class="btn btn-primary btn-sm text-xs font-semibold rounded-md flex items-center gap-1 shadow-2xs cursor-pointer">
                <ion-icon name="pencil-outline" class="text-sm"></ion-icon>
                <span>Edit All Assignments</span>
            </button>
        </div>
    </div>
</dialog>

{{-- ── 6. Quick Assign Staff Modal ───────────────────────────────────────── --}}
<dialog id="quick-assign-modal" class="confirm-modal" aria-labelledby="quick-assign-title">
    <form action="{{ route('admin.staff.assign') }}" method="POST" class="space-y-4">
        @csrf
        <div class="flex items-center gap-3 pb-3 border-b border-neutral-100">
            <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-800">
                <ion-icon name="person-add-outline" class="text-lg text-brand-800" aria-hidden="true"></ion-icon>
            </div>
            <div>
                <h3 id="quick-assign-title" class="text-sm font-bold text-neutral-900 leading-tight">Quick Assign Staff</h3>
                <p class="text-[11px] text-neutral-500">Link unassigned staff directly to a food stall</p>
            </div>
        </div>

        <div class="space-y-3 text-left">
            <div>
                <label for="quick-staff-id" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Staff Member <span class="text-red-500">*</span>
                </label>
                <select id="quick-staff-id" name="staff_id" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
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
                <label for="quick-stall-id" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Target Food Stall <span class="text-red-500">*</span>
                </label>
                <select id="quick-stall-id" name="stall_id" required
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
                    <option value="">-- Select Food Stall --</option>
                    @foreach($stalls as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-quick-modal cursor-pointer">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold rounded-md flex items-center gap-1 shadow-2xs cursor-pointer">
                <ion-icon name="checkmark-outline" class="text-sm leading-none"></ion-icon>
                Assign Staff
            </button>
        </div>
    </form>
</dialog>

{{-- ── 7. Delete Confirmation Modal ─────────────────────────────────────── --}}
<dialog id="delete-confirm-modal" class="confirm-modal" aria-labelledby="delete-modal-title">
    <div class="flex items-start gap-3.5 mb-4">
        <div class="flex-shrink-0 w-9 h-9 rounded-md bg-red-50 border border-red-200/80 flex items-center justify-center text-red-700">
            <ion-icon name="trash-outline" class="text-lg text-red-700" aria-hidden="true"></ion-icon>
        </div>
        <div>
            <h3 id="delete-modal-title" class="text-sm font-bold text-neutral-900 leading-tight mb-1">Delete Stall?</h3>
            <p class="text-neutral-500 text-xs leading-relaxed">
                Permanently delete <strong id="delete-stall-name" class="text-neutral-900 font-semibold"></strong>?
                This will remove all associated evaluation records and cannot be undone.
            </p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
        <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-delete-modal cursor-pointer">Cancel</button>
        <button type="button" id="confirm-delete-btn"
            class="btn btn-sm text-xs font-semibold text-white bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 rounded-md flex items-center gap-1 shadow-2xs transition-colors cursor-pointer">
            <ion-icon name="trash-outline" class="text-sm leading-none" aria-hidden="true"></ion-icon>
            Yes, Delete
        </button>
    </div>
</dialog>

@section('scripts')
<script>
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
                item.style.display = 'flex';
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

// ── Search & Counter in Add Stall Form ────────────────────────────────────
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

// ── Edit Modal & Scalable Staff Selector ─────────────────────────────────
var editModal       = document.getElementById('edit-modal');
var editForm        = document.getElementById('edit-form');
var editNameInput   = document.getElementById('edit-stall-name-input');
var editDescInput   = document.getElementById('edit-stall-desc-input');
var editActiveInput = document.getElementById('edit-stall-active-input');
var editStaffSearch = document.getElementById('edit-staff-search');
var currentFilterTab = 'all';

function openEditModal(stallId, stallName, staffIdsArray, desc, isActive) {
    editForm.action = '/admin/stall/' + stallId;
    editNameInput.value = stallName;
    editDescInput.value = desc || '';
    editActiveInput.checked = isActive === 1;

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
        btnAll.className = tab === 'all' ? 'px-2 py-0.5 rounded font-bold bg-neutral-900 text-white transition-colors cursor-pointer' : 'px-2 py-0.5 rounded font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
        btnUn.className = tab === 'unassigned' ? 'px-2 py-0.5 rounded font-bold bg-neutral-900 text-white transition-colors cursor-pointer' : 'px-2 py-0.5 rounded font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
        btnSel.className = tab === 'selected' ? 'px-2 py-0.5 rounded font-bold bg-neutral-900 text-white transition-colors cursor-pointer' : 'px-2 py-0.5 rounded font-semibold bg-neutral-100 text-neutral-600 hover:bg-neutral-200 transition-colors cursor-pointer';
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

document.querySelectorAll('.js-close-edit-modal').forEach(function(b) {
    b.addEventListener('click', function() { editModal.close(); });
});

editModal.addEventListener('click', function(e) {
    var r = editModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        editModal.close();
    }
});

// ── Stall Staff Roster Modal ─────────────────────────────────────────────
var rosterModal       = document.getElementById('roster-modal');
var rosterTitle       = document.getElementById('roster-modal-title');
var rosterSubtitle    = document.getElementById('roster-modal-subtitle');
var rosterList        = document.getElementById('roster-staff-list');
var rosterSearch      = document.getElementById('roster-search-input');
var rosterEditBtn     = document.getElementById('roster-edit-stall-btn');
var unassignForm      = document.getElementById('unassign-staff-form');
var unassignStaffIdEl = document.getElementById('unassign-staff-id');
var currentRosterStallId = null;
var currentRosterStallName = '';
var currentRosterStaffData = [];

function openRosterModal(stallId, stallName, staffMembersArray) {
    currentRosterStallId = stallId;
    currentRosterStallName = stallName;
    currentRosterStaffData = Array.isArray(staffMembersArray) ? staffMembersArray : [];

    rosterTitle.textContent = stallName + ' — Staff Roster';
    rosterSubtitle.textContent = currentRosterStaffData.length + ' assigned ' + (currentRosterStaffData.length === 1 ? 'member' : 'members');
    if (rosterSearch) rosterSearch.value = '';

    renderRosterItems(currentRosterStaffData);

    rosterEditBtn.onclick = function() {
        rosterModal.close();
        var staffIds = currentRosterStaffData.map(function(s) { return s.id; });
        openEditModal(stallId, stallName, staffIds, '', 1);
    };

    rosterModal.showModal();
}

function renderRosterItems(list) {
    if (!rosterList) return;
    rosterList.innerHTML = '';

    if (list.length === 0) {
        rosterList.innerHTML = '<div class="p-6 text-center text-neutral-400 text-xs font-medium">No staff members found matching criteria.</div>';
        return;
    }

    list.forEach(function(member) {
        var card = document.createElement('div');
        card.className = 'flex items-center justify-between p-2.5 bg-white rounded-md border border-neutral-200 shadow-2xs';
        card.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-brand-50 border border-brand-200 text-brand-800 flex items-center justify-center font-bold text-xs shrink-0">
                    ${(member.name || 'U').charAt(0).toUpperCase()}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-neutral-900 truncate">${member.name}</h4>
                    <p class="text-[10px] text-neutral-400 truncate font-mono">${member.email}</p>
                </div>
            </div>
            <button type="button" onclick="triggerUnassignStaff(${member.id}, '${escapeQuotes(member.name)}')"
                class="text-neutral-500 hover:text-red-700 bg-neutral-50 hover:bg-red-50 border border-neutral-200 hover:border-red-200 px-2 py-1 rounded text-[11px] font-semibold flex items-center gap-1 transition-colors cursor-pointer"
                title="Remove staff from this stall">
                <ion-icon name="close-circle-outline" class="text-xs"></ion-icon>
                <span>Remove</span>
            </button>
        `;
        rosterList.appendChild(card);
    });
}

function escapeQuotes(str) {
    return (str || '').replace(/'/g, "\\'");
}

function triggerUnassignStaff(staffId, staffName) {
    if (!confirm('Remove ' + staffName + ' from ' + currentRosterStallName + '? The staff account will become unassigned.')) {
        return;
    }
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
    b.addEventListener('click', function() { rosterModal.close(); });
});

if (rosterModal) {
    rosterModal.addEventListener('click', function(e) {
        var r = rosterModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            rosterModal.close();
        }
    });
}

// ── Quick Assign Modal ───────────────────────────────────────────────────
var quickModal = document.getElementById('quick-assign-modal');

function openQuickAssignModal() {
    if (quickModal) quickModal.showModal();
}

document.querySelectorAll('.js-close-quick-modal').forEach(function(b) {
    b.addEventListener('click', function() { quickModal.close(); });
});

if (quickModal) {
    quickModal.addEventListener('click', function(e) {
        var r = quickModal.getBoundingClientRect();
        if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
            quickModal.close();
        }
    });
}

// ── Delete Modal ─────────────────────────────────────────────────────────
var deleteModal    = document.getElementById('delete-confirm-modal');
var stallNameEl    = document.getElementById('delete-stall-name');
var confirmDelBtn  = document.getElementById('confirm-delete-btn');
var pendingFormId  = null;

function openDeleteModal(stallId, stallName) {
    pendingFormId = 'delete-form-' + stallId;
    stallNameEl.textContent = stallName;
    deleteModal.showModal();
}

document.querySelectorAll('.js-close-delete-modal').forEach(function(b) {
    b.addEventListener('click', function() { deleteModal.close(); });
});

deleteModal.addEventListener('click', function(e) {
    var r = deleteModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        deleteModal.close();
    }
});

confirmDelBtn.addEventListener('click', function() {
    if (!pendingFormId) return;
    confirmDelBtn.disabled = true;
    confirmDelBtn.innerHTML = '<ion-icon name="hourglass-outline" class="text-xs leading-none"></ion-icon> Deleting…';
    document.getElementById(pendingFormId).submit();
});
</script>
@endsection
@endsection
