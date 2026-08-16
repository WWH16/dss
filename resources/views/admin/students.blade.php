@extends('layouts.dashboard')
@section('title', 'Manage Students | Admin — DSS')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Manage Students</h1>
            <p class="text-sm font-medium text-neutral-500 mt-0.5">Directory of all registered student accounts and evaluator profiles.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-50 border border-brand-200/60 rounded text-xs font-bold text-brand-800 uppercase tracking-wider tabular-nums">
            {{ $students->count() }} Total Evaluators
        </span>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-neutral-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('admin.students') }}" class="flex flex-col sm:flex-row gap-2.5">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, or student number…"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600/20">
            </div>
            <select name="course" class="px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600">
                <option value="">All Courses</option>
                @foreach($courseOptions as $c)
                    <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            <select name="year_level" class="px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm font-medium focus:outline-none focus:border-brand-600">
                <option value="">All Year Levels</option>
                @foreach($yearOptions as $y)
                    <option value="{{ $y }}" {{ request('year_level') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary text-sm py-2 px-4 font-bold flex items-center justify-center gap-1.5 shrink-0">
                <ion-icon name="search-outline" class="text-sm leading-none"></ion-icon>
                Filter
            </button>
            @if(request('q') || request('course') || request('year_level'))
                <a href="{{ route('admin.students') }}" class="btn btn-ghost text-sm py-2 px-3 font-semibold flex items-center justify-center gap-1 shrink-0 border border-neutral-200">Clear</a>
            @endif
        </form>
    </div>

    {{-- Students Table --}}
    <div class="bg-white rounded-lg border border-neutral-200/80 shadow-xs overflow-hidden">
        @if($students->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-14 h-14 rounded-md bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400">
                    <ion-icon name="people-outline" class="text-3xl text-neutral-400"></ion-icon>
                </div>
                <p class="text-base font-bold text-neutral-900 mb-1">No students found</p>
                <p class="text-xs text-neutral-500 max-w-sm">Try adjusting your search criteria or clear the filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/75 text-[10px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-200">
                            <th class="px-5 py-3">Student Name</th>
                            <th class="px-5 py-3">Student Number</th>
                            <th class="px-5 py-3">Course</th>
                            <th class="px-5 py-3">Year Level</th>
                            <th class="px-5 py-3">Email Address</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($students as $student)
                            <tr class="hover:bg-neutral-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-neutral-900 flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-md bg-brand-50 border border-brand-200/70 text-brand-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </span>
                                    <span>{{ $student->name }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-neutral-700 font-mono text-xs font-semibold tabular-nums">{{ $student->student_number ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-neutral-700 font-medium">{{ $student->course ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-neutral-700 font-medium">{{ $student->year_level ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-neutral-500 text-xs font-mono break-all">{{ $student->email }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button type="button"
                                        onclick='openDetailsModal(@json($student))'
                                        class="text-brand-700 hover:text-brand-800 text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 transition-colors bg-white px-2.5 py-1.5 rounded border border-brand-200 hover:bg-brand-50">
                                        <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($students as $student)
                        <div class="p-4 flex items-center justify-between gap-3 hover:bg-neutral-50/60 transition-colors">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $student->name }}</h3>
                                <p class="text-xs text-neutral-500 mt-0.5">{{ $student->course ?: '—' }} • {{ $student->year_level ?: '—' }}</p>
                            </div>
                            <button type="button"
                                onclick='openDetailsModal(@json($student))'
                                class="shrink-0 text-brand-700 hover:text-brand-800 text-[11px] font-bold uppercase tracking-wide inline-flex items-center gap-1 bg-white px-2.5 py-1.5 rounded border border-brand-200">
                                <ion-icon name="eye-outline" class="text-sm leading-none"></ion-icon>
                                View
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ── Student Details Modal ────────────────────────────────────────── --}}
<dialog id="details-modal" class="confirm-modal">
    <div class="flex items-start gap-3.5 mb-4">
        <div class="flex-shrink-0 w-9 h-9 rounded-md bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-700">
            <ion-icon name="id-card-outline" class="text-xl text-brand-700"></ion-icon>
        </div>
        <div class="w-full">
            <h3 id="details-name" class="text-sm font-bold text-neutral-900 leading-tight mb-3"></h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-neutral-100"><dt class="text-neutral-500 font-semibold uppercase tracking-wide">Student Number</dt><dd id="details-student-number" class="font-mono font-bold text-neutral-900"></dd></div>
                <div class="flex justify-between py-1 border-b border-neutral-100"><dt class="text-neutral-500 font-semibold uppercase tracking-wide">Course</dt><dd id="details-course" class="font-bold text-neutral-900"></dd></div>
                <div class="flex justify-between py-1 border-b border-neutral-100"><dt class="text-neutral-500 font-semibold uppercase tracking-wide">Year Level</dt><dd id="details-year" class="font-bold text-neutral-900"></dd></div>
                <div class="flex justify-between py-1"><dt class="text-neutral-500 font-semibold uppercase tracking-wide">Email</dt><dd id="details-email" class="font-mono font-bold text-neutral-900 break-all text-right"></dd></div>
            </dl>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
        <button type="button" class="btn btn-ghost btn-sm js-close-details-modal">Close</button>
    </div>
</dialog>

@section('scripts')
<script>
var detailsModal = document.getElementById('details-modal');

function openDetailsModal(student) {
    document.getElementById('details-name').textContent = student.name;
    document.getElementById('details-student-number').textContent = student.student_number || '—';
    document.getElementById('details-course').textContent = student.course || '—';
    document.getElementById('details-year').textContent = student.year_level || '—';
    document.getElementById('details-email').textContent = student.email;
    detailsModal.showModal();
}

document.querySelectorAll('.js-close-details-modal').forEach(function(b) {
    b.addEventListener('click', function() { detailsModal.close(); });
});

detailsModal.addEventListener('click', function(e) {
    var r = detailsModal.getBoundingClientRect();
    if (e.clientY < r.top || e.clientY > r.bottom || e.clientX < r.left || e.clientX > r.right) {
        detailsModal.close();
    }
});
</script>
@endsection
@endsection
