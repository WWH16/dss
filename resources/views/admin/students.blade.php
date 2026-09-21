@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen. --}}
@section('title', 'Student Accounts | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Students" (the sidebar label) instead of the layout default "Dashboard". --}}
@section('header_title', 'Students')

@php
    // CHANGED: wrapped in function_exists(). A plain function declaration in a view is fatal ("Cannot redeclare") the second time the view renders in the same PHP process. Also removed the per-department 'badge' and 'dot' colours (emerald, amber, blue, purple, rose, teal); every department badge now uses one neutral style, so green stays the only accent and colour keeps meaning score status.
    if (!function_exists('getStudentDeptInfo')) {
    function getStudentDeptInfo($course) {
        $c = strtoupper(trim($course ?? ''));
        if (in_array($c, ['BSIT', 'BSCS', 'BSIS', 'ACT', 'MIT'])) {
            return [
                'code' => 'CCSICT',
                'name' => 'Computing Studies (CCSICT)',
            ];
        }
        if (in_array($c, ['BSHM', 'BSTM', 'HRM'])) {
            return [
                'code' => 'CHM',
                'name' => 'Hospitality Mgt. (CHM)',
            ];
        }
        if (in_array($c, ['BSBA', 'BSA', 'BSMA', 'BSENTREP', 'BSEntrep'])) {
            return [
                'code' => 'CBA',
                'name' => 'Business & Acctg. (CBA)',
            ];
        }
        if (in_array($c, ['BSED', 'BEED', 'BPED', 'BTLED'])) {
            return [
                'code' => 'CED',
                'name' => 'Teacher Education (CED)',
            ];
        }
        if (in_array($c, ['BSCRIM', 'BS CRIM', 'BSLE'])) {
            return [
                'code' => 'CCJE',
                'name' => 'Criminal Justice (CCJE)',
            ];
        }
        if (in_array($c, ['BA COMM', 'BS PSYCH', 'BS BIO', 'BACOMM', 'BSPSYCH'])) {
            return [
                'code' => 'CAS',
                'name' => 'Arts & Sciences (CAS)',
            ];
        }
        return [
            'code' => 'GEN',
            'name' => $c ?: 'General Department',
        ];
    }
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- ── 1. Page Header ─────────────────────────────────────────────── --}}
    <div class="pb-4 border-b border-neutral-200/80">
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight flex items-center gap-2.5">
            <ion-icon name="people-outline" class="text-brand-700 text-2xl"></ion-icon>
            <span>Student Evaluators</span>
        </h1>
        <p class="text-xs text-neutral-500 mt-0.5 max-w-2xl">
            Directory and survey participation records for registered student evaluators across campus.
        </p>
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
                {{-- CHANGED: placeholder no longer says "or email"; the controller searches name and student number only, so typing an email found nothing. Field rounded-lg to rounded-md. --}}
                <div class="flex-1 relative">
                    <ion-icon name="search-outline" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or student number…"
                        class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                </div>

                {{-- Action Controls --}}
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    {{-- Toggle Filters Button --}}
                    {{-- CHANGED: rounded-lg to rounded-md; filter count badge raised from 10px to 11px. --}}
                    <button type="button" id="toggle-filter-btn" onclick="toggleFilterDrawer()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-md text-xs sm:text-sm font-bold transition-all border cursor-pointer {{ $activeFilterCount > 0 ? 'bg-brand-50 text-brand-800 border-brand-300 shadow-2xs' : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50' }}">
                        <ion-icon name="options-outline" class="text-base text-brand-700"></ion-icon>
                        <span id="filter-toggle-label">{{ $activeFilterCount > 0 ? 'Hide Filters' : 'Show Filters' }}</span>
                        @if($activeFilterCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[11px] bg-brand-600 text-white font-bold tabular-nums">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Clear Filters Button --}}
                    {{-- CHANGED: rounded-lg to rounded-md; icon neutral-400 to neutral-500. Now the only "clear" control; the duplicate "Clear filters" link below was removed. --}}
                    @if($hasFilters)
                        <a href="{{ route('admin.students') }}" class="btn btn-ghost text-xs sm:text-sm py-2 px-3 font-semibold flex items-center gap-1 border border-neutral-200 rounded-md text-neutral-600 hover:text-neutral-900" title="Reset all filters">
                            <ion-icon name="close-circle-outline" class="text-sm text-neutral-500"></ion-icon>
                            Clear
                        </a>
                    @endif
                </div>
            </div>

            {{-- Collapsible Filter Drawer --}}
            {{-- CHANGED: the four filter selects rounded-lg to rounded-md (controls are md). --}}
            <div id="filter-drawer" class="{{ $activeFilterCount > 0 ? '' : 'hidden' }} pt-3 border-t border-neutral-100/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Department Dropdown --}}
                    <div>
                        <label for="filter_department" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Department / College</label>
                        <select id="filter_department" name="department" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
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
                        <select id="filter_course" name="course" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Programs / Courses</option>
                            @foreach($courseOptions as $c)
                                <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Year Level Select --}}
                    <div>
                        <label for="filter_year" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Year Level</label>
                        <select id="filter_year" name="year_level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="">All Year Levels</option>
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ request('year_level') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Select --}}
                    <div>
                        <label for="filter_sort" class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">Sort Order</label>
                        <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-600">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Accounts</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A to Z)</option>
                            <option value="evaluations_desc" {{ request('sort') == 'evaluations_desc' ? 'selected' : '' }}>Most Active Evaluators</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Accounts</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filter Chips (Only shown when filters are applied) --}}
                @if($hasFilters)
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-neutral-100 text-xs">
                        <div class="flex flex-wrap items-center gap-1.5">
                            {{-- CHANGED: neutral-400 to neutral-500 for contrast. --}}
                            <span class="text-neutral-500 font-medium">Active filters:</span>
                            @if(request('q'))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 text-[11px] font-semibold">
                                    Search: "{{ request('q') }}"
                                </span>
                            @endif
                            @if(request('department'))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-brand-50 border border-brand-200 text-brand-800 text-[11px] font-semibold">
                                    Dept: {{ request('department') }}
                                </span>
                            @endif
                            @if(request('course'))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 text-[11px] font-semibold">
                                    Course: {{ request('course') }}
                                </span>
                            @endif
                            @if(request('year_level'))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 text-[11px] font-semibold">
                                    Year: {{ request('year_level') }}
                                </span>
                            @endif
                            @if(request('sort') && request('sort') !== 'latest')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-800 text-[11px] font-semibold">
                                    Sort: {{ request('sort') === 'name_asc' ? 'Name (A-Z)' : (request('sort') === 'evaluations_desc' ? 'Most Evaluations' : 'Oldest') }}
                                </span>
                            @endif
                            {{-- CHANGED: result count recoloured from emerald to neutral; emerald is kept for positive status only. --}}
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-neutral-100 border border-neutral-200 text-neutral-800 text-[11px] font-bold tabular-nums">
                                {{ $students->total() }} results
                            </span>
                        </div>

                        {{-- CHANGED: removed the "Clear filters" link; it repeated the "Clear" button in the toolbar. --}}
                    </div>
                @endif
            </div>

        </form>
    </div>

    {{-- ── 4. Students Accounts Data Table ──────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        @if($students->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                {{-- CHANGED: icon neutral-400 on neutral-50 to brand-700 on brand-50, like the other admin empty states. --}}
                <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-3 text-brand-700">
                    <ion-icon name="people-outline" class="text-3xl text-brand-700"></ion-icon>
                </div>
                {{-- CHANGED: the message and "Reset Filters" button now depend on $hasFilters. Before, a site with no students yet was told its filters matched nothing and offered a reset button that did nothing. --}}
                @if($hasFilters)
                    <p class="text-base font-bold text-neutral-900 mb-1">No student accounts found</p>
                    <p class="text-xs text-neutral-500 max-w-sm">
                        No student accounts matched your query or filter parameters. Try clearing or expanding your search filters.
                    </p>
                    <a href="{{ route('admin.students') }}" class="btn btn-primary btn-sm mt-4 font-bold inline-flex items-center gap-1">
                        <ion-icon name="refresh-outline"></ion-icon>
                        Reset Filters
                    </a>
                @else
                    <p class="text-base font-bold text-neutral-900 mb-1">No students registered yet</p>
                    <p class="text-xs text-neutral-500 max-w-sm">
                        Student accounts will appear here once students sign up.
                    </p>
                @endif
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
                                // CHANGED: dates are stored in UTC; format them in Philippine time on the server (as the printable report does) and pass them to the modal as strings.
                                $registeredAt = \Carbon\Carbon::parse($student->created_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y');
                                $lastEvalAt = $student->last_evaluation_at ? \Carbon\Carbon::parse($student->last_evaluation_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y') : null;
                                $modalDates = ['registered' => $registeredAt, 'last_eval' => $lastEvalAt];
                            @endphp
                            <tr class="hover:bg-neutral-50/70 transition-colors">
                                {{-- Name & Avatar --}}
                                {{-- CHANGED: avatar rounded-lg to rounded-md, matching the evaluations page. --}}
                                <td class="px-5 py-3.5 font-bold text-neutral-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-200/70 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-neutral-900 leading-tight truncate">{{ $student->name }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Student ID --}}
                                <td class="px-5 py-3.5">
                                    {{-- CHANGED: rounded to rounded-md. --}}
                                    <span class="font-mono text-xs font-semibold px-2 py-1 bg-neutral-100 text-neutral-800 rounded-md border border-neutral-200/60 tabular-nums">
                                        {{ $student->student_number ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- Department --}}
                                {{-- CHANGED: one neutral badge style for every department instead of a colour per department; the decorative coloured dot was removed; rounded-full to rounded-md. --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border bg-neutral-100 text-neutral-800 border-neutral-200/70">
                                        {{ $dept['code'] }}
                                    </span>
                                </td>

                                {{-- Course & Year --}}
                                <td class="px-5 py-3.5">
                                    <div class="text-xs">
                                        <span class="font-bold text-neutral-900">{{ $student->course ?: 'Not set' }}</span>
                                        @if($student->year_level)
                                            {{-- CHANGED: separator neutral-400 to neutral-500. --}}
                                            <span class="text-neutral-500 mx-1">•</span>
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
                                {{-- CHANGED: shows $registeredAt (Philippine time) instead of the raw UTC date. --}}
                                <td class="px-5 py-3.5 text-xs text-neutral-500 font-medium whitespace-nowrap">
                                    {{ $registeredAt }}
                                </td>

                                {{-- Action --}}
                                {{-- CHANGED: passes $modalDates so the modal shows server-formatted dates; button rounded-lg to rounded-md. --}}
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <button type="button"
                                        onclick='openDetailsModal(@json($student), @json($dept), @json($modalDates))'
                                        class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 transition-all bg-white px-3 py-1.5 rounded-md border border-brand-200/80 hover:bg-brand-50 shadow-2xs">
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
                            // CHANGED: same Philippine-time dates as the desktop table.
                            $registeredAt = \Carbon\Carbon::parse($student->created_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y');
                            $lastEvalAt = $student->last_evaluation_at ? \Carbon\Carbon::parse($student->last_evaluation_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y') : null;
                            $modalDates = ['registered' => $registeredAt, 'last_eval' => $lastEvalAt];
                        @endphp
                        <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50/70 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                {{-- CHANGED: avatar rounded-lg to rounded-md. --}}
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-md bg-brand-50 border border-brand-200 text-brand-800 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-neutral-900 leading-tight truncate">{{ $student->name }}</h3>
                                    </div>
                                </div>
                                {{-- CHANGED: neutral department badge (no per-department colour), 10px to 11px, rounded-full to rounded-md. --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border bg-neutral-100 text-neutral-800 border-neutral-200/70 shrink-0">
                                    {{ $dept['code'] }}
                                </span>
                            </div>

                            {{-- CHANGED: labels raised from 10px neutral-400 to 11px neutral-500; em-dash placeholders for a missing course or year replaced with "Not set"; box rounded-lg to rounded-md. --}}
                            <div class="grid grid-cols-2 gap-2 text-xs bg-neutral-50/70 p-2.5 rounded-md border border-neutral-100">
                                <div>
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Student Number</span>
                                    <span class="font-mono font-semibold text-neutral-900">{{ $student->student_number ?: 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Program / Year</span>
                                    <span class="font-medium text-neutral-900">{{ $student->course ?: 'Not set' }} ({{ $student->year_level ?: 'Not set' }})</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                {{-- CHANGED: count uses Str::plural (it printed "1 evaluations"); the zero state darkened from neutral-400 to neutral-500. --}}
                                <div class="text-xs">
                                    @if($hasEvaluations)
                                        <span class="text-emerald-700 font-bold flex items-center gap-1">
                                            <ion-icon name="checkmark-circle" class="text-sm"></ion-icon>
                                            {{ $student->evaluations_count }} {{ Str::plural('evaluation', $student->evaluations_count) }}
                                        </span>
                                    @else
                                        <span class="text-neutral-500 font-medium">0 evaluations</span>
                                    @endif
                                </div>
                                {{-- CHANGED: "Details" renamed "View" to match the desktop table; passes $modalDates; rounded-lg to rounded-md. --}}
                                <button type="button"
                                    onclick='openDetailsModal(@json($student), @json($dept), @json($modalDates))'
                                    class="text-brand-700 hover:text-brand-800 text-xs font-bold inline-flex items-center gap-1 bg-white px-3 py-1.5 rounded-md border border-brand-200 shadow-2xs">
                                    <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                                    View
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            <div class="px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                    <span>
                        Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->total() }}</strong> accounts
                    </span>

                    {{-- Per Page Selector --}}
                    <div class="flex items-center gap-1.5 border-l border-neutral-200 pl-3">
                        {{-- CHANGED: label neutral-400 to neutral-500; select rounded to rounded-md. --}}
                        <label for="per_page_select" class="text-[11px] font-bold text-neutral-500 uppercase">Per Page</label>
                        <select id="per_page_select" onchange="window.location.href = this.value" class="bg-white border border-neutral-200 rounded-md px-2 py-1 text-xs font-semibold focus:outline-none focus:border-brand-600 cursor-pointer">
                            @foreach([10, 25, 50] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $students->perPage() == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Pagination Controls --}}
                {{-- CHANGED: Previous, Next and page-number buttons rounded-lg to rounded-md (controls are md). --}}
                <div class="flex items-center gap-1 order-1 sm:order-2">
                    {{-- Previous Page Link --}}
                    @if($students->onFirstPage())
                        <span class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </span>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            <ion-icon name="chevron-back-outline" class="text-xs"></ion-icon>
                            Previous
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="hidden sm:flex items-center gap-1">
                        @if($students->hasPages())
                            @foreach($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                                @if($page == $students->currentPage())
                                    <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold text-xs flex items-center justify-center transition-colors">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <span class="w-8 h-8 rounded-md bg-brand-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                1
                            </span>
                        @endif
                    </div>

                    {{-- Next Page Link --}}
                    @if($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-white hover:bg-neutral-50 text-neutral-700 text-xs font-semibold transition-colors inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </a>
                    @else
                        <span class="px-2.5 py-1.5 rounded-md border border-neutral-200 bg-neutral-100 text-neutral-300 text-xs font-semibold cursor-not-allowed inline-flex items-center gap-1">
                            Next
                            <ion-icon name="chevron-forward-outline" class="text-xs"></ion-icon>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ── 5. Student Account Details Modal (Refined Balanced Radius) ────── --}}
<dialog id="details-modal" class="confirm-modal modal-sharp max-w-lg w-full rounded-lg p-0 overflow-hidden shadow-2xl border-0 outline-none bg-white backdrop:bg-neutral-950/60">
    {{-- Header --}}
    <div class="bg-brand-900 text-white px-5 py-4 flex items-center justify-between border-b border-brand-950">
        <div class="flex items-center gap-3">
            <div id="modal-avatar" class="w-10 h-10 rounded-md bg-brand-800 border border-brand-700/80 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-xs">
            </div>
            <div>
                <h3 id="details-name" class="text-sm font-bold text-white leading-tight tracking-tight"></h3>
                <p id="details-student-number-badge" class="text-[11px] text-brand-200 font-mono mt-0.5"></p>
            </div>
        </div>
        <button type="button" class="js-close-details-modal text-white/70 hover:text-white hover:bg-brand-800 p-1.5 rounded-md transition-colors cursor-pointer" aria-label="Close modal">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    {{-- Body --}}
    <div class="p-5 sm:p-6 space-y-4 bg-white text-xs">
        {{-- Section 1: Academic & Identity Profile --}}
        {{-- CHANGED (modal): section labels raised from 10px to 11px and field labels from 9px to 11px, all neutral-400 to neutral-500. --}}
        <div>
            <span class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-2">
                Academic & Identity Profile
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-neutral-200 border border-neutral-200 rounded-md overflow-hidden">
                <div class="bg-white p-3 space-y-0.5">
                    <span class="block text-neutral-500 font-bold uppercase text-[11px] tracking-wider">Student ID Number</span>
                    <span id="details-student-number" class="font-mono font-bold text-neutral-900 text-xs"></span>
                </div>
                <div class="bg-white p-3 space-y-0.5">
                    <span class="block text-neutral-500 font-bold uppercase text-[11px] tracking-wider">Department / College</span>
                    <span id="details-dept-name" class="font-bold text-neutral-900 text-xs"></span>
                </div>
                <div class="bg-white p-3 space-y-0.5">
                    <span class="block text-neutral-500 font-bold uppercase text-[11px] tracking-wider">Course / Degree</span>
                    <span id="details-course" class="font-semibold text-neutral-900 text-xs"></span>
                </div>
                <div class="bg-white p-3 space-y-0.5">
                    <span class="block text-neutral-500 font-bold uppercase text-[11px] tracking-wider">Year Level</span>
                    <span id="details-year" class="font-semibold text-neutral-900 text-xs"></span>
                </div>
            </div>
        </div>

        {{-- Section 2: Participation & Evaluation Record --}}
        <div>
            <span class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-2">
                Participation & Evaluation Record
            </span>
            <div class="p-3.5 bg-neutral-50/80 border border-neutral-200 rounded-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                        <ion-icon name="receipt-outline" class="text-base"></ion-icon>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-neutral-900">Total Evaluations Submitted</p>
                        <p class="text-[11px] text-neutral-500 font-medium" id="details-last-submitted"></p>
                    </div>
                </div>
                <span id="details-eval-count" class="text-xs font-bold font-mono text-brand-900 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200 tabular-nums"></span>
            </div>
        </div>

        {{-- Section 3: Account Metadata --}}
        {{-- CHANGED: footer text neutral-400 to neutral-500. --}}
        <div class="pt-3 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-500">
            <span>Account Role: <strong class="text-neutral-700 font-semibold">Student Evaluator</strong></span>
            <span>Registered: <strong id="details-created-at" class="text-neutral-700 font-semibold"></strong></span>
        </div>
    </div>

    {{-- Action Footer --}}
    <div class="flex items-center justify-end px-5 py-3.5 bg-neutral-50 border-t border-neutral-200">
        <button type="button" class="btn btn-primary text-xs px-4 py-2 font-bold rounded-md js-close-details-modal cursor-pointer">
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

// CHANGED: takes a third argument with the registered and last-evaluation dates already formatted in Philippine time by the server.
function openDetailsModal(student, dept, dates) {
    dates = dates || {};
    document.getElementById('details-name').textContent = student.name;
    document.getElementById('details-student-number-badge').textContent = 'ID: ' + (student.student_number || 'N/A');
    document.getElementById('modal-avatar').textContent = (student.name || 'S').charAt(0).toUpperCase();

    document.getElementById('details-student-number').textContent = student.student_number || 'Not Set';
    document.getElementById('details-dept-name').textContent = (dept && dept.name) ? dept.name : (student.course || 'General');
    document.getElementById('details-course').textContent = student.course || 'Not Set';
    document.getElementById('details-year').textContent = student.year_level || 'Not Set';

    var evalCount = student.evaluations_count || 0;
    document.getElementById('details-eval-count').textContent = evalCount + ' Submitted';

    // CHANGED: uses the server-formatted dates. new Date() on the raw "YYYY-MM-DD HH:MM:SS" value read UTC as local time, and Safari cannot parse that format at all ("Invalid Date"). The missing-date fallback is "Not set" instead of an em-dash.
    document.getElementById('details-last-submitted').textContent = dates.last_eval ? 'Last submitted: ' + dates.last_eval : 'No submissions yet';
    document.getElementById('details-created-at').textContent = dates.registered || 'Not set';

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
