@extends('layouts.dashboard')
{{-- CHANGED: replaced the em-dash in the title with a hyphen; "Student Accounts" became "Students", the one name the sidebar and top bar use. --}}
@section('title', 'Students | Admin - DSS')
{{-- CHANGED: added header_title so the top bar reads "Students" (the sidebar label) instead of the layout default "Dashboard". --}}
@section('header_title', 'Students')

{{-- CHANGED: removed getStudentDeptInfo() and every department (CCSICT, CHM, CBA, CED, CCJE, CAS). The system serves one college, so the Department filter, column and details row are gone. --}}

@section('content')
{{-- CHANGED: was max-w-7xl mx-auto, which centred the page and left dead space beside the sidebar; same fix as the overview and stalls pages. --}}
<div class="max-w-[1600px] space-y-6">

    {{-- CHANGED: the visible "Student Evaluators" heading and its description repeated the top bar under a second name; the h1 stays for screen readers and reads "Students", as on the evaluations page. --}}
    <h1 class="sr-only">Students</h1>

    {{-- CHANGED: the filter state is read here for the inline toolbar; the "Show Filters" drawer, its count badge and the active-filter chips are gone. --}}
    @php
        $sort = request('sort') ?: 'latest';
        $status = request('status');
        $narrowed = request('q') || request('course') || request('year_level');
        $hasFilters = $narrowed || $status || $sort !== 'latest';
        $sortUrl = fn ($s) => request()->fullUrlWithQuery(['sort' => $s, 'page' => null]);
        $statusUrl = fn ($s) => request()->fullUrlWithQuery(['status' => $s, 'page' => null]);
        $total = (int) $participation->total;
        $evaluated = (int) $participation->evaluated;
        $month = now('Asia/Manila')->format('F');
        // CHANGED: the Philippine-time registration date is set once per student here and travels to the details dialog on the student object.
        $students->getCollection()->each(function ($s) use ($openStalls, $month) {
            // CHANGED: the track's full name from User::PROGRAMS, falling back to the stored code.
            $s->track_name = \App\Models\User::PROGRAMS[$s->course][$s->track] ?? $s->track;
            $s->registered_at = \Carbon\Carbon::parse($s->created_at, 'UTC')->timezone('Asia/Manila');
            $s->registered_label = $s->registered_at->format('M d, Y');
            // CHANGED: this month's stalls and the dialog's status line are set here once, so the table and the dialog share one rule.
            $done = (int) $s->stalls_this_month;
            $s->month_complete = $done > 0 && $done >= $openStalls;
            $s->month_line = ($done === 0 ? 'Not yet evaluated' : ($s->month_complete ? "All {$done} " . Str::plural('stall', $done) : "{$done} of {$openStalls} stalls") . ' evaluated') . " in {$month}";
        });
        // CHANGED: this month's progress across the open stalls, shared by the table and the mobile cards. A student rates each stall once a month, so "Evaluated" became "X of N stalls"; only a student who has rated every open stall gets the emerald badge. "Not yet" stays plain neutral-600 text.
        $monthBadge = function ($s) use ($openStalls) {
            $done = (int) $s->stalls_this_month;
            if ($done === 0) {
                return '<span class="text-xs text-neutral-600 whitespace-nowrap">Not yet</span>';
            }
            if ($s->month_complete) {
                return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 whitespace-nowrap"><ion-icon name="checkmark-circle" class="text-sm text-emerald-600" aria-hidden="true"></ion-icon>All ' . $done . ' ' . Str::plural('stall', $done) . '</span>';
            }
            return '<span class="text-xs text-neutral-600 whitespace-nowrap tabular-nums"><strong class="font-semibold text-neutral-900">' . $done . '</strong> of ' . $openStalls . ' stalls</span>';
        };
    @endphp

    {{-- CHANGED: search, filters, the participation summary and the table share one card, as on the evaluations page. --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        <div class="p-4 sm:px-5 border-b border-neutral-200/70 space-y-3">
            <form method="GET" action="{{ route('admin.students') }}" class="flex flex-col lg:flex-row lg:items-center gap-2.5">
                @foreach(['per_page', 'status'] as $keep)
                    @if(request($keep))
                        <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">
                    @endif
                @endforeach

                {{-- CHANGED: placeholder no longer says "or email"; the controller searches name and student number only, so typing an email found nothing. --}}
                <div class="flex-1 relative">
                    <label for="filter_q" class="sr-only">Search</label>
                    <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-base pointer-events-none"></ion-icon>
                    <input id="filter_q" type="search" name="q" data-live-search value="{{ request('q') }}" placeholder="Search by name or student number…"
                        class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15">
                </div>

                <div class="grid grid-cols-2 lg:flex gap-2.5">
                    <div>
                        <label for="filter_course" class="sr-only">Program</label>
                        <select id="filter_course" name="course" onchange="this.form.submit()" class="w-full lg:w-40 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 cursor-pointer {{ request('course') ? 'border-brand-300 bg-brand-50 text-brand-900 font-semibold' : 'text-neutral-700' }}">
                            <option value="">All programs</option>
                            @foreach($courseOptions as $c)
                                <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_year" class="sr-only">Year level</label>
                        <select id="filter_year" name="year_level" onchange="this.form.submit()" class="w-full lg:w-40 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 cursor-pointer {{ request('year_level') ? 'border-brand-300 bg-brand-50 text-brand-900 font-semibold' : 'text-neutral-700' }}">
                            <option value="">All year levels</option>
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ request('year_level') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_sort" class="sr-only">Sort</label>
                        <select id="filter_sort" name="sort" onchange="this.form.submit()" class="w-full lg:w-48 px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-sm focus:outline-none focus:border-brand-600 cursor-pointer {{ $sort !== 'latest' ? 'border-brand-300 bg-brand-50 text-brand-900 font-semibold' : 'text-neutral-700' }}">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest accounts</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest accounts</option>
                            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A to Z</option>
                            <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z to A</option>
                            <option value="evaluations_desc" {{ $sort === 'evaluations_desc' ? 'selected' : '' }}>Most evaluations</option>
                            <option value="evaluations_asc" {{ $sort === 'evaluations_asc' ? 'selected' : '' }}>Fewest evaluations</option>
                        </select>
                    </div>
                </div>

                @if($hasFilters)
                    <a href="{{ route('admin.students') }}" class="self-start lg:self-auto inline-flex items-center gap-1 px-3 py-2 rounded-md text-sm font-semibold text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50">
                        <ion-icon name="close-outline" class="text-base"></ion-icon>
                        Clear
                    </a>
                @endif
            </form>

            {{-- CHANGED: one-line participation summary and an All / Evaluated / Not yet filter, so finding who has not evaluated needs no scanning. Both cover the current month; "Evaluated" means at least one stall this month, since a student rates each stall once a month. Counts follow the search and filters above. --}}
            @if($total > 0)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <p class="text-sm text-neutral-600">
                        <strong class="font-semibold text-neutral-900 tabular-nums">{{ $evaluated }} of {{ $total }}</strong>
                        {{ $narrowed ? 'matching ' : '' }}{{ Str::plural('student', $total) }} {{ $evaluated === 1 ? 'has' : 'have' }} evaluated in {{ $month }}
                        <span class="tabular-nums">({{ round($evaluated / $total * 100) }}%)</span>
                    </p>
                    <nav aria-label="Participation" class="self-start sm:self-auto inline-flex p-0.5 bg-neutral-100 rounded-md text-xs">
                        @foreach(['' => ['All', $total], 'evaluated' => ['Evaluated', $evaluated], 'pending' => ['Not yet', $total - $evaluated]] as $key => [$label, $count])
                            @php $active = $status === ($key ?: null); @endphp
                            <a href="{{ $statusUrl($key ?: null) }}" @if($active) aria-current="page" @endif
                                class="px-3 py-1.5 rounded inline-flex items-center gap-1.5 font-semibold transition-colors {{ $active ? 'bg-white text-neutral-900 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900' }}">
                                {{ $label }}
                                <span class="tabular-nums font-medium text-neutral-500">{{ $count }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif
        </div>

        @if($students->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                {{-- CHANGED: icon neutral-400 on neutral-50 to brand-700 on brand-50, like the other admin empty states. --}}
                <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center mb-3 text-brand-700">
                    <ion-icon name="people-outline" class="text-3xl text-brand-700"></ion-icon>
                </div>
                {{-- CHANGED: the message and "Reset Filters" button now depend on $hasFilters. Before, a site with no students yet was told its filters matched nothing and offered a reset button that did nothing. "Student accounts" became "students". --}}
                @if($hasFilters)
                    <p class="text-base font-bold text-neutral-900 mb-1">No students found</p>
                    <p class="text-xs text-neutral-500 max-w-sm">
                        No students matched your search or filters. Try clearing or expanding your filters.
                    </p>
                    <a href="{{ route('admin.students') }}" class="btn btn-primary btn-sm mt-4 font-bold inline-flex items-center gap-1 rounded-md">
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
                {{-- CHANGED: "Student Evaluator" header became "Name"; the Department column is removed. "Evaluations Submitted" split in two: "This month" carries the status badge, "Evaluations" the all-time count as a plain number; Evaluations and Registered sort through the sort parameter. Column widths are set so names no longer take the spare room; they add up to 100% so no empty strip is left after Registered. --}}
                <table class="w-full text-left border-collapse min-w-[760px] hidden md:table">
                    <thead>
                        <tr class="bg-neutral-50/80 text-[11px] text-neutral-500 font-bold uppercase tracking-wider border-b border-neutral-200/80">
                            {{-- CHANGED: Name sorts from the header like Evaluations and Registered. Idle sort icons are dimmed so only the active column's arrow stands out; they brighten on hover. --}}
                            <th class="px-5 py-3 w-[29%]" aria-sort="{{ $sort === 'name_asc' ? 'ascending' : ($sort === 'name_desc' ? 'descending' : 'none') }}">
                                <a href="{{ $sortUrl($sort === 'name_asc' ? 'name_desc' : 'name_asc') }}" class="group inline-flex items-center gap-1 uppercase hover:text-neutral-900 {{ str_starts_with($sort, 'name') ? 'text-neutral-900' : '' }}">
                                    Name
                                    <ion-icon name="{{ $sort === 'name_asc' ? 'arrow-up-outline' : ($sort === 'name_desc' ? 'arrow-down-outline' : 'swap-vertical-outline') }}" class="text-xs {{ str_starts_with($sort, 'name') ? '' : 'opacity-50 group-hover:opacity-100' }}" aria-hidden="true"></ion-icon>
                                </a>
                            </th>
                            <th class="px-4 py-3 w-[15%]">Student ID</th>
                            <th class="px-4 py-3 w-[20%]">Program & Year</th>
                            <th class="px-4 py-3 w-[18%]">This month</th>
                            <th class="px-4 py-3 w-[10%] text-right" aria-sort="{{ $sort === 'evaluations_asc' ? 'ascending' : ($sort === 'evaluations_desc' ? 'descending' : 'none') }}">
                                <a href="{{ $sortUrl($sort === 'evaluations_desc' ? 'evaluations_asc' : 'evaluations_desc') }}" class="group inline-flex items-center gap-1 uppercase hover:text-neutral-900 {{ str_starts_with($sort, 'evaluations') ? 'text-neutral-900' : '' }}">
                                    Evaluations
                                    <ion-icon name="{{ $sort === 'evaluations_asc' ? 'arrow-up-outline' : ($sort === 'evaluations_desc' ? 'arrow-down-outline' : 'swap-vertical-outline') }}" class="text-xs {{ str_starts_with($sort, 'evaluations') ? '' : 'opacity-50 group-hover:opacity-100' }}" aria-hidden="true"></ion-icon>
                                </a>
                            </th>
                            <th class="pl-8 pr-5 py-3 w-[8%]" aria-sort="{{ $sort === 'oldest' ? 'ascending' : ($sort === 'latest' ? 'descending' : 'none') }}">
                                <a href="{{ $sortUrl($sort === 'latest' ? 'oldest' : 'latest') }}" class="group inline-flex items-center gap-1 uppercase hover:text-neutral-900 {{ in_array($sort, ['latest', 'oldest']) ? 'text-neutral-900' : '' }}">
                                    Registered
                                    <ion-icon name="{{ $sort === 'oldest' ? 'arrow-up-outline' : ($sort === 'latest' ? 'arrow-down-outline' : 'swap-vertical-outline') }}" class="text-xs {{ in_array($sort, ['latest', 'oldest']) ? '' : 'opacity-50 group-hover:opacity-100' }}" aria-hidden="true"></ion-icon>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($students as $student)
                            {{-- CHANGED: the whole row opens the details dialog; the name button is the keyboard target and its click bubbles here. --}}
                            <tr class="hover:bg-neutral-50 transition-colors cursor-pointer" onclick='openDetailsModal(@json($student))'>
                                {{-- CHANGED: initial avatar is a neutral circle instead of a green square, so it no longer reads as a status badge next to the green evaluated badge. --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-600 text-xs font-semibold flex items-center justify-center shrink-0" aria-hidden="true">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </span>
                                        {{-- CHANGED: the name is a button in place of the removed eye button, so keyboard and screen reader users can still open the dialog. --}}
                                        <button type="button" class="font-medium text-neutral-900 leading-tight text-left rounded-sm cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">{{ $student->name }}</button>
                                    </div>
                                </td>

                                {{-- CHANGED: student ID is plain 13px text instead of a chip and no longer monospace; the department cell is removed. --}}
                                <td class="px-4 py-3 text-[13px] text-neutral-700 tabular-nums whitespace-nowrap">{{ $student->student_number ?: 'Not set' }}</td>

                                <td class="px-4 py-3 text-[13px] whitespace-nowrap">
                                    {{-- CHANGED: the track code follows the course (e.g. BSIT-WMAD); hovering shows its full name. --}}
                                    <span class="font-medium text-neutral-900" title="{{ $student->track_name }}">{{ $student->course ?: 'Not set' }}{{ $student->track ? '-' . $student->track : '' }}</span>
                                    @if($student->year_level)
                                        <span class="text-neutral-500 mx-1" aria-hidden="true">·</span>
                                        <span class="text-neutral-600">{{ $student->year_level }}</span>
                                    @endif
                                </td>

                                {{-- CHANGED: this month's stalls ($monthBadge). --}}
                                <td class="px-4 py-3">{!! $monthBadge($student) !!}</td>

                                {{-- CHANGED: all-time count as a plain number; at most one per stall each month, so it counts every stall rating the student has made. --}}
                                <td class="px-4 py-3 text-right text-[13px] text-neutral-700 tabular-nums">{{ $student->evaluations_count }}</td>

                                {{-- CHANGED: shows the registration date in Philippine time instead of the raw UTC date. --}}
                                <td class="pl-8 pr-5 py-3 text-[13px] text-neutral-600 whitespace-nowrap tabular-nums">
                                    <time datetime="{{ $student->registered_at->toIso8601String() }}">{{ $student->registered_label }}</time>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile Card View -->
                {{-- CHANGED: one row per student: neutral avatar, name with ID and program underneath, status on the right. The whole card opens the details dialog; the eye button is gone and the name button is the keyboard target. The labelled grey box and the department are gone. --}}
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($students as $student)
                        {{-- CHANGED: a trailing chevron marks the row as tappable, as on the users page; active:bg gives touch feedback, since hover never fires on a phone. The chevron is decorative; the name button stays the keyboard target. --}}
                        <div class="p-4 pr-3 flex items-center gap-3 hover:bg-neutral-50 active:bg-neutral-100 transition-colors cursor-pointer" onclick='openDetailsModal(@json($student))'>
                            <span class="w-8 h-8 rounded-full bg-neutral-100 text-neutral-600 text-xs font-semibold flex items-center justify-center shrink-0" aria-hidden="true">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-semibold text-neutral-900 leading-tight truncate"><button type="button" class="text-left rounded-sm cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">{{ $student->name }}</button></h3>
                                <p class="text-xs text-neutral-600 mt-0.5 truncate tabular-nums">
                                    {{ $student->student_number ?: 'No ID' }} · {{ $student->course ?: 'Not set' }}{{ $student->track ? '-' . $student->track : '' }}{{ $student->year_level ? ' · ' . $student->year_level : '' }}
                                </p>
                            </div>
                            <div class="shrink-0">{!! $monthBadge($student) !!}</div>
                            <ion-icon name="chevron-forward-outline" class="text-lg text-neutral-400 shrink-0" aria-hidden="true"></ion-icon>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Pagination Footer Bar ───────────────────────────────── --}}
            <div class="px-5 py-4 bg-neutral-50/70 border-t border-neutral-200/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-neutral-500 font-medium order-2 sm:order-1">
                    {{-- CHANGED: "accounts" became "students". --}}
                    <span>
                        Showing <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900 font-bold tabular-nums">{{ $students->total() }}</strong> students
                    </span>

                    {{-- Per Page Selector --}}
                    {{-- CHANGED: "PER PAGE" uppercase bold became sentence-case "Per page", as on the evaluations page. --}}
                    <div class="flex items-center gap-1.5 border-l border-neutral-200 pl-3">
                        <label for="per_page_select" class="text-xs text-neutral-500">Per page</label>
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

{{-- ── 5. Student Account Details Modal ────── --}}
{{-- CHANGED: rebuilt in the evaluations dialog's layout, keeping this dialog's colours. Same width and shadow (plain confirm-modal, no modal-sharp) but no border: confirm-modal's pale 1px border showed as a light rim around the dark green header. The dark green header band holds the avatar, name and student number; a participation panel sits on top like the evaluations average panel; details are label/value rows instead of an uppercase-labelled grid; the "Account Role" line (always "Student") is gone and Registered joins the rows; the Done button is the small one. --}}
<dialog id="details-modal" class="confirm-modal p-0! border-0!" aria-labelledby="details-name">
    <div class="bg-brand-900 text-white px-6 py-4 flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <div id="modal-avatar" class="w-9 h-9 rounded-md bg-brand-800 border border-brand-700/80 text-white font-bold text-sm flex items-center justify-center shrink-0" aria-hidden="true"></div>
            <div class="min-w-0">
                <h3 id="details-name" class="text-sm font-bold text-white leading-tight truncate"></h3>
                <p id="details-student-number" class="text-[11px] text-brand-200 tabular-nums"></p>
            </div>
        </div>
        <button type="button" class="js-close-details-modal text-white/70 hover:text-white p-1 cursor-pointer" aria-label="Close">
            <ion-icon name="close-outline" class="text-xl"></ion-icon>
        </button>
    </div>

    <div class="px-6 pb-6">
        {{-- CHANGED: the participation panel takes the evaluated badge's emerald when the student has evaluated this month and the old neutral-50 box colour when not; the count is the big number, in the old count chip's brand-900. The last-submitted date stays out: matched against the evaluations page it identified who wrote an anonymous evaluation. --}}
        <div id="details-month-panel" class="mt-5 rounded-lg px-4 py-3.5 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-neutral-900">Evaluations submitted</p>
                <p id="details-this-month" class="text-[11px] font-semibold mt-0.5"></p>
            </div>
            <span id="details-eval-count" class="text-3xl leading-none font-bold text-brand-900 tabular-nums"></span>
        </div>

        <dl class="mt-2 divide-y divide-neutral-100 text-sm">
            {{-- CHANGED: added the Track row; removed the Department row. --}}
            @foreach(['course' => 'Program', 'track' => 'Track', 'year' => 'Year level', 'created-at' => 'Registered'] as $id => $label)
                <div class="flex items-center justify-between gap-4 py-2.5">
                    <dt class="text-neutral-600">{{ $label }}</dt>
                    <dd id="details-{{ $id }}" class="font-medium text-neutral-900 text-right"></dd>
                </div>
            @endforeach
        </dl>

        <div class="flex justify-end pt-4 mt-4 border-t border-neutral-100">
            <button type="button" class="btn btn-primary btn-sm text-xs font-semibold rounded-md js-close-details-modal cursor-pointer">Done</button>
        </div>
    </div>
</dialog>

@section('scripts')
<script>
var detailsModal = document.getElementById('details-modal');

// CHANGED: takes only the student; the Philippine-time registration date arrives on it (registered_label), set by the server. The department line is removed. toggleFilterDrawer() is gone with the drawer.
function openDetailsModal(student) {
    document.getElementById('details-name').textContent = student.name;
    // CHANGED: the student number moved from a monospace "ID:" badge and a grid cell into the header subtitle.
    document.getElementById('details-student-number').textContent = student.student_number || 'No student number';
    document.getElementById('modal-avatar').textContent = (student.name || 'S').charAt(0).toUpperCase();

    document.getElementById('details-course').textContent = student.course || 'Not Set';
    // CHANGED: the track's full name (track_name, set by the server).
    document.getElementById('details-track').textContent = student.track_name || 'Not Set';
    document.getElementById('details-year').textContent = student.year_level || 'Not Set';
    document.getElementById('details-created-at').textContent = student.registered_label;

    // CHANGED: the count is a bare number in the panel; the status line gives this month's stalls, as in the This month column. The panel turns emerald only once every open stall is rated, matching the table badge. Only the colour classes are toggled, so the base classes live in the markup alone.
    var complete = student.month_complete;
    document.getElementById('details-eval-count').textContent = student.evaluations_count;
    var panel = document.getElementById('details-month-panel').classList;
    panel.toggle('bg-emerald-50', complete);
    panel.toggle('bg-neutral-50', !complete);
    var monthLine = document.getElementById('details-this-month');
    monthLine.textContent = student.month_line;
    monthLine.classList.toggle('text-emerald-800', complete);
    monthLine.classList.toggle('text-neutral-600', !complete);

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
