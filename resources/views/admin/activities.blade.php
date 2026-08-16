@extends('layouts.dashboard')
@section('title', 'Evaluation Activities | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Evaluation Activities</h1>
            <p class="text-neutral-500 text-sm mt-0.5">Schedule evaluation periods and designate participating canteen stalls.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-50 border border-brand-200/60 rounded text-xs font-bold text-brand-800 uppercase tracking-wider tabular-nums">
            {{ $activities->count() }} Total Activities
        </span>
    </div>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200/70 text-emerald-800 rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <ion-icon name="checkmark-circle" class="text-lg leading-none text-emerald-600"></ion-icon>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 bg-red-50 border border-red-200/70 text-red-800 rounded-md text-xs font-bold flex items-start gap-2">
            <ion-icon name="alert-circle" class="text-lg leading-none text-red-600"></ion-icon>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Create Activity --}}
        <div class="bg-white rounded-lg border border-neutral-200/80 p-5 shadow-xs lg:col-span-1 h-fit">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-100">
                <ion-icon name="calendar-outline" class="text-brand-600 text-lg"></ion-icon>
                <h2 class="font-bold text-neutral-900 text-sm tracking-tight uppercase">Create Activity</h2>
            </div>

            <form action="{{ route('admin.activities.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Activity Name</label>
                    <input type="text" name="name" required placeholder="e.g. 1st Semester Evaluation"
                        class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">End Date</label>
                        <input type="date" name="end_date" required
                            class="w-full px-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Included Stalls</label>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto border border-neutral-200 rounded-md p-2.5 bg-neutral-50/50">
                        @forelse($stalls as $stall)
                            <label class="flex items-center gap-2 text-xs font-medium text-neutral-700 cursor-pointer p-1 rounded hover:bg-neutral-100/70">
                                <input type="checkbox" name="stall_ids[]" value="{{ $stall->id }}" class="rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
                                <span>{{ $stall->name }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-neutral-400">No stalls available. Add stalls first.</p>
                        @endforelse
                    </div>
                </div>
                <button class="btn btn-primary text-sm py-2 px-4 font-bold w-full flex items-center justify-center gap-1.5">
                    <ion-icon name="add-outline" class="text-base"></ion-icon>
                    Create Activity
                </button>
            </form>
        </div>

        {{-- Activity List --}}
        <div class="lg:col-span-2 space-y-3">
            @forelse($activities as $activity)
                @php
                    $statusStyles = [
                        'ongoing' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'upcoming' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'ended' => 'bg-neutral-100 text-neutral-600 border-neutral-200',
                        'inactive' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                @endphp
                <div class="bg-white rounded-lg border border-neutral-200/80 p-5 shadow-xs">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-bold text-neutral-900 text-sm">{{ $activity->name }}</h3>
                            <p class="text-xs text-neutral-500 mt-0.5 tabular-nums font-mono">
                                {{ $activity->start_date->format('M d, Y') }} — {{ $activity->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border shrink-0 {{ $statusStyles[$activity->status] }}">
                            {{ $activity->status }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @forelse($activity->stalls as $s)
                            <span class="text-[11px] font-semibold bg-neutral-50 text-neutral-700 border border-neutral-200 px-2 py-0.5 rounded">{{ $s->name }}</span>
                        @empty
                            <span class="text-[11px] text-neutral-400">No stalls included.</span>
                        @endforelse
                    </div>

                    <div class="flex items-center gap-2 pt-3 border-t border-neutral-100">
                        <button type="button"
                            onclick='openEditActivityModal(@json($activity))'
                            class="text-brand-700 hover:text-brand-800 text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 bg-white px-2.5 py-1.5 rounded border border-brand-200 hover:bg-brand-50">
                            <ion-icon name="pencil-outline" class="text-sm"></ion-icon>
                            Edit
                        </button>
                        <form action="{{ route('admin.activities.toggle', $activity->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 bg-white px-2.5 py-1.5 rounded border {{ $activity->is_active ? 'text-red-600 border-red-200 hover:bg-red-50' : 'text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                                <ion-icon name="{{ $activity->is_active ? 'toggle' : 'toggle-outline' }}" class="text-base"></ion-icon>
                                {{ $activity->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg border border-neutral-200/80 p-12 text-center shadow-xs">
                    <div class="w-14 h-14 rounded-md bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400 mx-auto">
                        <ion-icon name="calendar-outline" class="text-3xl text-neutral-400"></ion-icon>
                    </div>
                    <p class="text-base font-bold text-neutral-900 mb-1">No evaluation activities yet</p>
                    <p class="text-xs text-neutral-500">Create a schedule using the form on the left.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Edit Activity Modal ────────────────────────────────────────── --}}
<dialog id="edit-activity-modal" class="confirm-modal">
    <form id="edit-activity-form" method="POST">
        @csrf
        @method('PUT')
        <div class="flex items-start gap-3.5 mb-4">
            <div class="flex-shrink-0 w-9 h-9 rounded-md bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-700">
                <ion-icon name="create-outline" class="text-xl text-brand-700"></ion-icon>
            </div>
            <div class="w-full space-y-3">
                <h3 class="text-sm font-bold text-neutral-900 leading-tight">Edit Evaluation Activity</h3>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Activity Name</label>
                    <input type="text" id="edit-activity-name" name="name" required
                        class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Start Date</label>
                        <input type="date" id="edit-activity-start" name="start_date" required
                            class="w-full px-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1.5">End Date</label>
                        <input type="date" id="edit-activity-end" name="end_date" required
                            class="w-full px-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1.5">Included Stalls</label>
                    <div id="edit-activity-stalls" class="space-y-1.5 max-h-40 overflow-y-auto border border-neutral-200 rounded-md p-2.5 bg-neutral-50/50">
                        @foreach($stalls as $stall)
                            <label class="flex items-center gap-2 text-xs font-medium text-neutral-700 cursor-pointer p-1 rounded hover:bg-neutral-100/70">
                                <input type="checkbox" name="stall_ids[]" value="{{ $stall->id }}" class="edit-activity-stall-checkbox rounded border-neutral-300 text-brand-600 focus:ring-brand-600/30">
                                <span>{{ $stall->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
            <button type="button" class="btn btn-ghost btn-sm js-close-edit-activity-modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm font-bold flex items-center gap-1">
                <ion-icon name="save-outline" class="text-sm leading-none"></ion-icon>
                Save Changes
            </button>
        </div>
    </form>
</dialog>

@section('scripts')
<script>
var editActivityModal = document.getElementById('edit-activity-modal');
var editActivityForm  = document.getElementById('edit-activity-form');

function openEditActivityModal(activity) {
    editActivityForm.action = '/admin/activities/' + activity.id;
    document.getElementById('edit-activity-name').value = activity.name;
    document.getElementById('edit-activity-start').value = activity.start_date.substring(0, 10);
    document.getElementById('edit-activity-end').value = activity.end_date.substring(0, 10);

    var includedIds = (activity.stalls || []).map(function (s) { return String(s.id); });
    document.querySelectorAll('.edit-activity-stall-checkbox').forEach(function (cb) {
        cb.checked = includedIds.indexOf(cb.value) !== -1;
    });

    editActivityModal.showModal();
}

document.querySelectorAll('.js-close-edit-activity-modal').forEach(function(b) {
    b.addEventListener('click', function() { editActivityModal.close(); });
});

editActivityModal.addEventListener('click', function(e) {
    var r = editActivityModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        editActivityModal.close();
    }
});
</script>
@endsection
@endsection
