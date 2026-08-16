@extends('layouts.dashboard')
@section('title', 'Manage Stalls | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-200/80">
        <div>
            <h1 class="text-xl font-bold text-neutral-900 tracking-tight">Manage Stalls</h1>
            <p class="text-xs text-neutral-500 mt-0.5">Add, rename, and monitor canteen food vendors across campus.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200/80">
                <ion-icon name="storefront-outline" class="text-sm" aria-hidden="true"></ion-icon>
                {{ $stalls->count() }} {{ Str::plural('Stall', $stalls->count()) }} Total
            </span>
        </div>
    </div>

    {{-- ── 2. Flash Messages ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md text-xs font-semibold flex items-center gap-2" role="alert">
            <ion-icon name="checkmark-circle" class="text-base text-emerald-600" aria-hidden="true"></ion-icon>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-xs font-semibold flex items-center gap-2" role="alert">
            <ion-icon name="alert-circle" class="text-base text-red-600" aria-hidden="true"></ion-icon>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── 3. Main 2-Column Sharp Layout ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column: Creation Form & Guidelines (4 cols) --}}
        <div class="lg:col-span-4 space-y-5">
            
            {{-- Add Stall Card --}}
            <div class="bg-white rounded-lg border border-neutral-200/80 p-5 shadow-2xs">
                <div class="mb-3.5 pb-2.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900 tracking-tight">Add New Stall</h2>
                    <p class="text-[11px] text-neutral-500 mt-0.5">Register a vendor to enable student evaluations.</p>
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
                    <span>Admin Guidelines</span>
                </div>
                <ul class="space-y-1.5 text-neutral-600 text-[11px] font-medium leading-relaxed">
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Use clear, unique names for easy recognition by students.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Renaming a stall preserves all historical evaluation data.</span>
                    </li>
                    <li class="flex items-start gap-1.5">
                        <span class="text-neutral-400 mt-0.5">•</span>
                        <span>Deleting a stall permanently removes its evaluation records.</span>
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
                        <p class="text-[11px] text-neutral-500 mt-0.5">Manage existing vendor accounts and ratings</p>
                    </div>

                    {{-- Sharp Search Input --}}
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
                        @endphp
                        <div class="stall-item flex flex-col sm:flex-row sm:items-center justify-between py-3 px-5 gap-3 hover:bg-neutral-50/70 transition-colors" data-stall-name="{{ strtolower($stall->name) }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-200/70 text-brand-800 flex items-center justify-center shrink-0">
                                    <ion-icon name="storefront-outline" class="text-base text-brand-800"></ion-icon>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-neutral-900 truncate block">{{ $stall->name }}</span>
                                    <div class="flex items-center gap-2 text-[11px] text-neutral-500">
                                        @if($avgRating !== null)
                                            <span class="inline-flex items-center gap-0.5 font-bold tabular-nums {{ $avgRating >= 4 ? 'text-brand-700' : ($avgRating >= 3 ? 'text-amber-700' : 'text-red-600') }}">
                                                <ion-icon name="star" class="text-amber-500 text-xs inline-block"></ion-icon>
                                                {{ number_format($avgRating, 2) }} / 5.00
                                            </span>
                                        @else
                                            <span class="text-neutral-400">No ratings yet</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                {{-- Edit Button --}}
                                <button type="button"
                                    onclick="openEditModal({{ $stall->id }}, '{{ addslashes($stall->name) }}')"
                                    aria-label="Edit {{ $stall->name }}"
                                    class="text-neutral-700 hover:text-brand-800 text-xs font-semibold inline-flex items-center gap-1 transition-colors bg-white hover:bg-neutral-50 px-2.5 py-1.5 rounded-md border border-neutral-200 hover:border-brand-300 shadow-2xs">
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
                                    class="text-red-600 hover:text-red-700 text-xs font-semibold inline-flex items-center gap-1 transition-colors bg-white hover:bg-red-50 px-2.5 py-1.5 rounded-md border border-neutral-200 hover:border-red-300 shadow-2xs">
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

{{-- ── 4. Edit Stall Modal ─────────────────────────────────────────────── --}}
<dialog id="edit-modal" class="confirm-modal" aria-labelledby="edit-modal-title">
    <form id="edit-form" method="POST">
        @csrf
        @method('PUT')
        <div class="flex items-start gap-3.5 mb-4">
            <div class="flex-shrink-0 w-9 h-9 rounded-md bg-brand-50 border border-brand-200/80 flex items-center justify-center text-brand-800">
                <ion-icon name="create-outline" class="text-lg text-brand-800" aria-hidden="true"></ion-icon>
            </div>
            <div class="w-full">
                <h3 id="edit-modal-title" class="text-sm font-bold text-neutral-900 leading-tight mb-1.5">Edit Stall Name</h3>
                <label for="edit-stall-name-input" class="sr-only">Stall Name</label>
                <input type="text" id="edit-stall-name-input" name="name"
                    class="w-full px-3 py-2 bg-white border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700 transition-colors"
                    required>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-edit-modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold rounded-md flex items-center gap-1 shadow-2xs">
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
        <button type="button" class="btn btn-ghost btn-sm text-xs rounded-md js-close-delete-modal">Cancel</button>
        <button type="button" id="confirm-delete-btn"
            class="btn btn-sm text-xs font-semibold text-white bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 rounded-md flex items-center gap-1 shadow-2xs transition-colors">
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
var editModal     = document.getElementById('edit-modal');
var editForm      = document.getElementById('edit-form');
var editNameInput = document.getElementById('edit-stall-name-input');

function openEditModal(stallId, stallName) {
    editForm.action = '/admin/stall/' + stallId;
    editNameInput.value = stallName;
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
