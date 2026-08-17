@extends('layouts.dashboard')
@section('title', 'Student Accounts | Admin — DSS')

@php
    function getStudentDeptInfo($course) {
        $c = strtoupper(trim($course ?? ''));
        if (in_array($c, ['BSIT', 'BSCS', 'BSIS', 'ACT', 'MIT'])) {
            return [
                'code' => 'CCSICT',
                'name' => 'Computing Studies (CCSICT)',
                'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200/70',
                'dot' => 'bg-emerald-500'
            ];
        }
        if (in_array($c, ['BSHM', 'BSTM', 'HRM'])) {
            return [
                'code' => 'CHM',
                'name' => 'Hospitality Mgt. (CHM)',
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200/70',
                'dot' => 'bg-amber-500'
            ];
        }
        if (in_array($c, ['BSBA', 'BSA', 'BSMA', 'BSENTREP', 'BSEntrep'])) {
            return [
                'code' => 'CBA',
                'name' => 'Business & Acctg. (CBA)',
                'badge' => 'bg-blue-50 text-blue-800 border-blue-200/70',
                'dot' => 'bg-blue-500'
            ];
        }
        if (in_array($c, ['BSED', 'BEED', 'BPED', 'BTLED'])) {
            return [
                'code' => 'CED',
                'name' => 'Teacher Education (CED)',
                'badge' => 'bg-purple-50 text-purple-800 border-purple-200/70',
                'dot' => 'bg-purple-500'
            ];
        }
        if (in_array($c, ['BSCRIM', 'BS CRIM', 'BSLE'])) {
            return [
                'code' => 'CCJE',
                'name' => 'Criminal Justice (CCJE)',
                'badge' => 'bg-rose-50 text-rose-800 border-rose-200/70',
                'dot' => 'bg-rose-500'
            ];
        }
        if (in_array($c, ['BA COMM', 'BS PSYCH', 'BS BIO', 'BACOMM', 'BSPSYCH'])) {
            return [
                'code' => 'CAS',
                'name' => 'Arts & Sciences (CAS)',
                'badge' => 'bg-teal-50 text-teal-800 border-teal-200/70',
                'dot' => 'bg-teal-500'
            ];
        }
        return [
            'code' => 'GEN',
            'name' => $c ?: 'General Department',
            'badge' => 'bg-neutral-100 text-neutral-700 border-neutral-200/70',
            'dot' => 'bg-neutral-400'
        ];
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- ── 1. Page Header & Key Metrics Strip ───────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-neutral-200/70">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2.5">
                <ion-icon name="people-outline" class="text-brand-700 text-2xl"></ion-icon>
                Student Accounts & Evaluators
            </h1>
            <p class="text-xs sm:text-sm font-medium text-neutral-500 mt-0.5">
                Directory, evaluator activity records, and academic department distributions across campus.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 border border-brand-200/70 rounded-[4px] text-xs font-bold text-brand-800 uppercase tracking-wider tabular-nums">
                <ion-icon name="person-circle-outline" class="text-sm text-brand-700"></ion-icon>
                {{ $totalStudents }} Registered Students
            </span>
        </div>
    </div>



    @php
        $activeFilterCount = 0;
        if (request('department')) $activeFilterCount++;
        if (request('course')) $activeFilterCount++;
        if (request('year_level')) $activeFilterCount++;
        if (request('sort') && request('sort') !== 'latest') $activeFilterCount++;
        $hasFilters = $activeFilterCount > 0 || request('q');
    @endphp

    {{-- ── 2. Search & Filter Bar with Show/Hide Toggle ─────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-2xs p-4 sm:p-5">
        <form id="student-filter-form" method="GET" action="{{ route('admin.students') }}" class="space-y-4">
            
            {{-- Search & Toggle Row --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                {{-- Search Bar --}}
                <div class="flex-1 relative">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, student number, or email…"
                        class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                </div>

                {{-- Action Controls --}}
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    {{-- Toggle Filters Button --}}
                    <button type="button" id="toggle-filter-btn" onclick="toggleFilterDrawer()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all border cursor-pointer {{ $activeFilterCount > 0 ? 'bg-brand-50 text-brand-800 border-brand-300 shadow-2xs' : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50' }}">
                        <ion-icon name="options-outline" class="text-base text-brand-700"></ion-icon>
                        <span id="filter-toggle-label">{{ $activeFilterCount > 0 ? 'Hide Filters' : 'Show Filters' }}</span>
                        @if($activeFilterCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-brand-600 text-white font-bold tabular-nums">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Clear Filters Button --}}
                    @if($hasFilters)
                        <a href="{{ route('admin.students') }}" class="btn btn-ghost text-xs sm:text-sm py-2 px-3 font-semibold flex items-center gap-1 border border-neutral-200 rounded-lg text-neutral-600 hover:text-neutral-900" title="Reset all filters">
                            <ion-icon name="close-circle-outline" class="text-sm text-neutral-400"></ion-icon>
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            {{-- Collapsible Filter Drawer --}}
            <div id="filter-drawer" class="{{ $activeFilterCount > 0 ? '' : 'hidden' }} pt-3 border-t border-neutral-100/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Department Dropdown --}}
                    <div>
                        <label for="filter_department" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Department / College</label>
                        <select id="filter_department" name="department" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Academic Departments</option>
                            @foreach($departments as $dept)
                                @php $deptCount = $departmentStats[$dept['code']] ?? 0; @endphp
                                <option value="{{ $dept['code'] }}" {{ request('department') == $dept['code'] ? 'selected' : '' }}>
                                    {{ $dept['name'] }} ({{ $deptCount }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course / Program Select --}}
                    <div>
                        <label for="filter_course" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Program / Degree</label>
                        <select id="filter_course" name="course" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Programs / Courses</option>
                            @foreach($courseOptions as $c)
                                <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Year Level Select --}}
                    <div>
                        <label for="filter_year" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Year Level</label>
                        <select id="filter_year" name="year_level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Year Levels</option>
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ request('year_level') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Select --}}
                    <div>
                        <label for="filter_sort" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Sort Order</label>
                        <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Accounts</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A to Z)</option>
                            <option value="evaluations_desc" {{ request('sort') == 'evaluations_desc' ? 'selected' : '' }}>Most Active Evaluators</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Accounts</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results Count & Active Filters Indicator --}}
            <div class="flex items-center justify-between text-xs text-neutral-500 pt-1">
                <div>
                    Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->count() }}</strong> of <span class="tabular-nums">{{ $totalStudents }}</span> student accounts
                </div>
                @if(request('department') || request('course') || request('year_level'))
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @if(request('department'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-50 border border-brand-200/60 text-brand-800 text-[10px] font-bold">
                                Dept: {{ request('department') }}
                            </span>
                        @endif
                        @if(request('course'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 border border-neutral-200 text-neutral-800 text-[10px] font-bold">
                                Course: {{ request('course') }}
                            </span>
                        @endif
                        @if(request('year_level'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 border border-neutral-200 text-neutral-800 text-[10px] font-bold">
                                Year: {{ request('year_level') }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </form>
    </div>

    {{-- ── 4. Students Accounts Data Table ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        @if($students->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-xl bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3 text-neutral-400">
                    <ion-icon name="people-outline" class="text-3xl text-neutral-400"></ion-icon>
                </div>
                <p class="text-base font-bold text-neutral-900 mb-1">No student accounts found</p>
                <p class="text-xs text-neutral-500 max-w-sm">
                    No student accounts matched your query or filter parameters. Try clearing or expanding your search filters.
                </p>
                <a href="{{ route('admin.students') }}" class="btn btn-primary btn-sm mt-4 font-bold inline-flex items-center gap-1">
                    <ion-icon name="refresh-outline"></ion-icon>
                    Reset Filters
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[860px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/80 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-200/80">
                            <th class="px-5 py-3.5">Student Evaluator</th>
                            <th class="px-5 py-3.5">Student ID</th>
                            <th class="px-5 py-3.5">Department</th>
                            <th class="px-5 py-3.5">Program & Year</th>
                            <th class="px-5 py-3.5">Evaluations Submitted</th>
                            <th class="px-5 py-3.5">Registered</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($students as $student)
                            @php
                                $dept = getStudentDeptInfo($student->course);
                                $hasEvaluations = $student->evaluations_count > 0;
                            @endphp
                            <tr class="hover:bg-neutral-50/70 transition-colors">
                                {{-- Name & Avatar --}}
                                <td class="px-5 py-3.5 font-bold text-neutral-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200/70 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-neutral-900 leading-tight truncate">{{ $student->name }}</p>
                                            <p class="text-[11px] text-neutral-500 font-mono mt-0.5">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Student ID --}}
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs font-semibold px-2 py-1 bg-neutral-100 text-neutral-800 rounded border border-neutral-200/60 tabular-nums">
                                        {{ $student->student_number ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- Department --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $dept['badge'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dept['dot'] }}"></span>
                                        {{ $dept['code'] }}
                                    </span>
                                </td>

                                {{-- Course & Year --}}
                                <td class="px-5 py-3.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-neutral-900">{{ $student->course ?: 'Not set' }}</span>
                                        @if($student->year_level)
                                            <span class="text-neutral-400 mx-1">•</span>
                                            <span class="text-neutral-600 font-medium">{{ $student->year_level }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Evaluation Count --}}
                                <td class="px-5 py-3.5">
                                    @if($hasEvaluations)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60 tabular-nums">
                                            <ion-icon name="checkmark-circle" class="text-sm text-emerald-600"></ion-icon>
                                            {{ $student->evaluations_count }} {{ Str::plural('Evaluation', $student->evaluations_count) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-neutral-100 text-neutral-500 border border-neutral-200/60">
                                            0 Submitted
                                        </span>
                                    @endif
                                </td>

                                {{-- Registered Date --}}
                                <td class="px-5 py-3.5 text-xs text-neutral-500 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($student->created_at)->format('M d, Y') }}
                                </td>

                                {{-- Action --}}
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <button type="button"
                                        onclick='openDetailsModal(@json($student), @json($dept))'
                                        class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 transition-all bg-white px-3 py-1.5 rounded-lg border border-brand-200/80 hover:bg-brand-50 shadow-2xs">
                                        <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($students as $student)
                        @php
                            $dept = getStudentDeptInfo($student->course);
                            $hasEvaluations = $student->evaluations_count > 0;
                        @endphp
                        <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50/70 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $student->name }}</h3>
                                        <p class="text-[11px] text-neutral-500 font-mono truncate">{{ $student->email }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $dept['badge'] }} shrink-0">
                                    {{ $dept['code'] }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs bg-neutral-50/70 p-2.5 rounded-lg border border-neutral-100">
                                <div>
                                    <span class="block text-[10px] font-bold text-neutral-400 uppercase">Student Number</span>
                                    <span class="font-mono font-semibold text-neutral-900">{{ $student->student_number ?: 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-neutral-400 uppercase">Program / Year</span>
                                    <span class="font-medium text-neutral-900">{{ $student->course ?: '—' }} ({{ $student->year_level ?: '—' }})</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <div class="text-xs">
                                    @if($hasEvaluations)
                                        <span class="text-emerald-700 font-bold flex items-center gap-1">
                                            <ion-icon name="checkmark-circle" class="text-sm"></ion-icon>
                                            {{ $student->evaluations_count }} evaluations
                                        </span>
                                    @else
                                        <span class="text-neutral-400 font-medium">0 evaluations</span>
                                    @endif
                                </div>
                                <button type="button"
                                    onclick='openDetailsModal(@json($student), @json($dept))'
                                    class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 bg-white px-3 py-1.5 rounded-lg border border-brand-200 shadow-2xs">
                                    <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                                    Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ── 5. Student Account Details Modal ──────────────────────────────── --}}
<dialog id="details-modal" class="confirm-modal max-w-lg w-full rounded-2xl p-0 overflow-hidden shadow-2xl backdrop:bg-neutral-950/40">
    <div class="bg-neutral-900 text-white p-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div id="modal-avatar" class="w-10 h-10 rounded-xl bg-brand-600 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-sm">
            </div>
            <div>
                <h3 id="details-name" class="text-base font-bold text-white leading-tight"></h3>
                <p id="details-email" class="text-xs text-neutral-300 font-mono mt-0.5"></p>
            </div>
        </div>
        <button type="button" class="js-close-details-modal text-neutral-400 hover:text-white p-1 transition-colors" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-2xl"></ion-icon>
        </button>
    </div>

    <div class="p-6 space-y-5 bg-white">
        <div>
            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2.5">Academic & Identity Information</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-neutral-50 p-4 rounded-xl border border-neutral-100 text-xs">
                <div>
                    <span class="block text-neutral-400 font-bold uppercase text-[10px]">Student Number</span>
                    <span id="details-student-number" class="font-mono font-bold text-neutral-900 text-sm"></span>
                </div>
                <div>
                    <span class="block text-neutral-400 font-bold uppercase text-[10px]">Department</span>
                    <span id="details-dept-name" class="font-bold text-neutral-900 text-sm"></span>
                </div>
                <div>
                    <span class="block text-neutral-400 font-bold uppercase text-[10px]">Course / Program</span>
                    <span id="details-course" class="font-bold text-neutral-900 text-sm"></span>
                </div>
                <div>
                    <span class="block text-neutral-400 font-bold uppercase text-[10px]">Year Level</span>
                    <span id="details-year" class="font-bold text-neutral-900 text-sm"></span>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-2.5">Evaluator Activity & History</h4>
            <div class="bg-neutral-50 p-4 rounded-xl border border-neutral-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center">
                        <ion-icon name="receipt-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-900">Total Evaluations Logged</p>
                        <p class="text-[11px] text-neutral-500" id="details-last-active"></p>
                    </div>
                </div>
                <span id="details-eval-count" class="text-base font-extrabold text-brand-800 bg-brand-50 px-3 py-1 rounded-full border border-brand-200 tabular-nums"></span>
            </div>
        </div>

        <div class="text-[11px] text-neutral-400 flex items-center justify-between pt-2 border-t border-neutral-100">
            <span>Account Type: <strong class="text-neutral-700">Student Client</strong></span>
            <span>Registered: <strong id="details-created-at" class="text-neutral-700"></strong></span>
        </div>
    </div>

    <div class="flex items-center justify-end p-4 bg-neutral-50 border-t border-neutral-100">
        <button type="button" class="btn btn-primary btn-sm px-4 py-2 font-bold js-close-details-modal rounded-lg">
            Done
        </button>
    </div>
</dialog>

@section('scripts')
<script>
var detailsModal = document.getElementById('details-modal');

function toggleFilterDrawer() {
    var drawer = document.getElementById('filter-drawer');
    var label = document.getElementById('filter-toggle-label');
    if (!drawer) return;

    var isHidden = drawer.classList.contains('hidden');
    if (isHidden) {
        drawer.classList.remove('hidden');
        if (label) label.textContent = 'Hide Filters';
    } else {
        drawer.classList.add('hidden');
        if (label) label.textContent = 'Show Filters';
    }
}

function openDetailsModal(student, dept) {
    document.getElementById('details-name').textContent = student.name;
    document.getElementById('details-email').textContent = student.email;
    document.getElementById('modal-avatar').textContent = (student.name || 'S').charAt(0).toUpperCase();

    document.getElementById('details-student-number').textContent = student.student_number || 'Not Set';
    document.getElementById('details-dept-name').textContent = (dept && dept.name) ? dept.name : (student.course || 'General');
    document.getElementById('details-course').textContent = student.course || 'Not Set';
    document.getElementById('details-year').textContent = student.year_level || 'Not Set';

    var evalCount = student.evaluations_count || 0;
    document.getElementById('details-eval-count').textContent = evalCount + ' Submitted';

    if (student.last_evaluation_at) {
        var d = new Date(student.last_evaluation_at);
        document.getElementById('details-last-active').textContent = 'Last active: ' + d.toLocaleDateString();
    } else {
        document.getElementById('details-last-active').textContent = 'No evaluation submissions yet';
    }

    if (student.created_at) {
        var created = new Date(student.created_at);
        document.getElementById('details-created-at').textContent = created.toLocaleDateString();
    } else {
        document.getElementById('details-created-at').textContent = '—';
    }

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
