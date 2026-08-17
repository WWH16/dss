@extends('layouts.dashboard')
@section('title', 'Manage Stalls | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-200/80">
        <div>
            <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Manage Stalls</h1>
            <p class="text-xs text-neutral-500 mt-0.5">Add, rename, assign staff accounts, and monitor canteen food vendors across campus.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80">
                <ion-icon name="storefront-outline" class="text-sm" aria-hidden="true"></ion-icon>
                {{ $stalls->count() }} {{ Str::plural('Stall', $stalls->count()) }} Total
            </span>
        </div>
    </div>

    {{-- ── 2. Main 2-Column Layout ────────────────────────────────────────── --}}
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

                    <div>
                        <label class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                            Assign Staff Members (Multiple allowed)
                        </label>
                        @if($staffUsers->isEmpty())
                            <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-md text-center">
                                <p class="text-[11px] text-neutral-500">No staff accounts registered yet.</p>
                            </div>
                        @else
                            <div class="max-h-32 overflow-y-auto p-2 bg-neutral-50 border border-neutral-300 rounded-md space-y-1.5 divide-y divide-neutral-200/50">
                                @foreach($staffUsers as $staff)
                                    <label class="flex items-center gap-2 text-xs text-neutral-800 cursor-pointer pt-1 first:pt-0">
                                        <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}"
                                            class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-3.5 w-3.5">
                                        <span class="font-medium truncate">{{ $staff->name }}</span>
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

                    <button type="submit" class="btn btn-primary text-xs py-2 px-4 font-semibold flex items-center justify-center gap-1.5 w-full rounded-md shadow-2xs">
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
                        <span>All assigned staff see detailed evaluations for their stall.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Student names are strictly protected and hidden across all staff dashboards.</span>
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
                            $staffIdsJson = json_encode($assignedStaffList->pluck('id')->toArray());
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

                                        {{-- Assigned Staff Count/List --}}
                                        @if($assignedStaffList->isNotEmpty())
                                            <span class="inline-flex items-center gap-1 font-semibold text-brand-800 bg-brand-50 px-2 py-0.5 rounded text-[10px] border border-brand-200/60" title="{{ $assignedStaffList->pluck('name')->join(', ') }}">
                                                <ion-icon name="people-outline" class="text-xs"></ion-icon>
                                                Staff: {{ $assignedStaffList->pluck('name')->join(', ') }} ({{ $assignedStaffList->count() }})
                                            </span>
                                        @else
                                            <span class="text-[10px] text-neutral-400 font-medium">No staff assigned</span>
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

{{-- ── 4. Edit Stall Modal (Multi-Staff Assignment) ─────────────────────── --}}
<dialog id="edit-modal" class="confirm-modal" aria-labelledby="edit-modal-title">
    <form id="edit-form" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="flex items-center gap-3 pb-3 border-b border-neutral-100">
            <div class="w-9 h-9 rounded-md bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-800">
                <ion-icon name="create-outline" class="text-lg text-brand-800" aria-hidden="true"></ion-icon>
            </div>
            <div>
                <h3 id="edit-modal-title" class="text-sm font-bold text-neutral-900 leading-tight">Edit Food Stall</h3>
                <p class="text-[11px] text-neutral-500">Update vendor name, staff assignments, and status</p>
            </div>
        </div>

        <div class="space-y-3 text-left">
            <div>
                <label for="edit-stall-name-input" class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Stall Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit-stall-name-input" name="name"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700"
                    required>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-neutral-700 mb-1 uppercase tracking-wider">
                    Assigned Staff Members
                </label>
                @if($staffUsers->isEmpty())
                    <div class="p-2.5 bg-neutral-50 border border-neutral-200 rounded-md text-center">
                        <p class="text-[11px] text-neutral-500">No staff accounts registered yet.</p>
                    </div>
                @else
                    <div class="max-h-36 overflow-y-auto p-2 bg-neutral-50 border border-neutral-300 rounded-md space-y-1.5 divide-y divide-neutral-200/50">
                        @foreach($staffUsers as $staff)
                            <label class="flex items-center gap-2 text-xs text-neutral-800 cursor-pointer pt-1 first:pt-0">
                                <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" id="edit-staff-{{ $staff->id }}"
                                    class="edit-staff-checkbox rounded border-neutral-300 text-brand-600 focus:ring-brand-500 h-3.5 w-3.5">
                                <span class="font-medium truncate">{{ $staff->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <p class="text-[10px] text-neutral-400 mt-1">Select one or more staff accounts to assign to this stall.</p>
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

{{-- ── 5. Delete Confirmation Modal ─────────────────────────────────────── --}}
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
// ── Quick Filter Stalls ───────────────────────────────────────────────────
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

// ── Edit Modal ───────────────────────────────────────────────────────────
var editModal       = document.getElementById('edit-modal');
var editForm        = document.getElementById('edit-form');
var editNameInput   = document.getElementById('edit-stall-name-input');
var editDescInput   = document.getElementById('edit-stall-desc-input');
var editActiveInput = document.getElementById('edit-stall-active-input');

function openEditModal(stallId, stallName, staffIdsArray, desc, isActive) {
    editForm.action = '/admin/stall/' + stallId;
    editNameInput.value = stallName;
    editDescInput.value = desc || '';
    editActiveInput.checked = isActive === 1;

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

    editModal.showModal();
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
