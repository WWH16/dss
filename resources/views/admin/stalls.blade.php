@extends('layouts.dashboard')
@section('title', 'Manage Stalls | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Manage Stalls</h1>
        <p class="text-neutral-500 text-sm">Add, remove, and monitor food stalls in the canteen.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <span class="material-symbols-outlined text-lg leading-none">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl mx-auto">

        {{-- Add / Manage List --}}
        <div class="bg-white rounded-xl border border-neutral-200/60 p-6 shadow-sm">
            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider mb-5 pb-2 border-b border-neutral-100">Add New Stall</h2>

            <form action="{{ route('admin.stall.add') }}" method="POST" class="flex flex-col sm:flex-row gap-2 mb-6">
                @csrf
                <input type="text" name="name"
                    class="flex-1 px-3 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/15"
                    placeholder="e.g. Stall #1 - Food Hub" required>
                <button class="btn btn-primary text-sm py-2 px-4 font-semibold flex items-center justify-center gap-1 shrink-0 w-full sm:w-auto">
                    <span class="material-symbols-outlined text-sm leading-none">add</span>
                    Add
                </button>
            </form>

            <h2 class="font-bold text-neutral-800 text-sm uppercase tracking-wider mb-3 pb-2 border-b border-neutral-100">Current Stalls</h2>
            <div class="space-y-1">
                @forelse($stalls as $stall)
                    <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-neutral-50 transition-colors border border-transparent hover:border-neutral-100">
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">storefront</span>
                            </div>
                            <span class="text-sm font-bold text-neutral-900 truncate">{{ $stall->name }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button"
                                onclick="openEditModal({{ $stall->id }}, '{{ addslashes($stall->name) }}')"
                                class="relative text-brand-600 hover:text-brand-700 text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 transition-colors bg-white px-2 py-1.5 rounded border border-brand-100 hover:border-brand-200 hover:bg-brand-50 before:absolute before:inset-[-8px]">
                                <span class="material-symbols-outlined text-[14px] leading-none">edit</span>
                                <span class="hidden sm:inline">Edit</span>
                            </button>
                            <form id="delete-form-{{ $stall->id }}" action="{{ route('admin.stall.delete', $stall->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                onclick="openDeleteModal({{ $stall->id }}, '{{ addslashes($stall->name) }}')"
                                class="relative text-red-400 hover:text-red-600 text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 transition-colors bg-white px-2 py-1.5 rounded border border-red-100 hover:border-red-200 hover:bg-red-50 before:absolute before:inset-[-8px]">
                                <span class="material-symbols-outlined text-[14px] leading-none">delete</span>
                                <span class="hidden sm:inline">Delete</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400 text-center py-6">No stalls yet. Add one above.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- ── Edit Stall Modal ────────────────────────────────────────── --}}
<dialog id="edit-modal" class="confirm-modal">
    <form id="edit-form" method="POST">
        @csrf
        @method('PUT')
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                <span class="material-symbols-outlined" style="font-size: 1.4rem;">edit_square</span>
            </div>
            <div class="w-full">
                <h3 class="text-base font-bold text-neutral-900 leading-tight mb-2" style="font-family: var(--font-display);">Edit Stall Name</h3>
                <input type="text" id="edit-stall-name-input" name="name"
                    class="w-full px-3 py-2.5 bg-neutral-50 border border-neutral-200 rounded-lg text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/15"
                    required>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm js-close-edit-modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-sm leading-none">save</span>
                Save Changes
            </button>
        </div>
    </form>
</dialog>

{{-- ── Delete Confirmation Modal ────────────────────────────────────────── --}}
<dialog id="delete-confirm-modal" class="confirm-modal">
    <div class="flex items-start gap-4 mb-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600">
            <span class="material-symbols-outlined" style="font-size: 1.4rem;">delete_forever</span>
        </div>
        <div>
            <h3 class="text-base font-bold text-neutral-900 leading-tight mb-1" style="font-family: var(--font-display);">Delete Stall?</h3>
            <p class="text-neutral-500 text-xs leading-relaxed">
                You are about to permanently delete <strong id="delete-stall-name" class="text-neutral-800"></strong>.
                This will also remove all associated evaluations. This action cannot be undone.
            </p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
        <button type="button" class="btn btn-ghost btn-sm js-close-delete-modal">Cancel</button>
        <button type="button" id="confirm-delete-btn"
            class="btn btn-sm font-bold text-white flex items-center gap-1"
            style="background-color: oklch(0.58 0.23 28); border-color: oklch(0.58 0.23 28);">
            <span class="material-symbols-outlined text-sm leading-none">delete</span>
            Yes, Delete
        </button>
    </div>
</dialog>

@section('scripts')
<script>
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
    confirmDelBtn.innerHTML = '<span class="material-symbols-outlined text-sm leading-none">hourglass_empty</span> Deleting…';
    document.getElementById(pendingFormId).submit();
});
</script>
@endsection
@endsection
