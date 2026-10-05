<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Ahp;
use App\Services\Saw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

// CHANGED: role checks moved out of each method into the role middleware on this controller's routes (routes/web.php).
class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Dashboard Counts
        $studentCount = DB::table('users')
            ->where('role', 'student')
            ->count();

        $stallCount = DB::table('stalls')->count();

        $evaluationCount = DB::table('stall_evaluations')->count();

        // Average rating per stall (Ranked by overall composite score)
        $results = DB::table('stall_evaluations')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stalls.id as stall_id',
                'stalls.name',
                DB::raw('COUNT(stall_evaluations.id) as eval_count'),
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price'),
                DB::raw('(AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4 as overall_score')
            )
            ->groupBy('stalls.id', 'stalls.name')
            ->orderByDesc('overall_score')
            ->get();

        // DSS ranking: order by SAW score, attach SAW and AHP scores
        $ahp = new Ahp;
        $ahpConsistency = $ahp->consistency();
        $weights = $ahpConsistency['weights'];

        $results = (new Saw)->rank($results, $weights);
        $results = $ahp->attachScores($results, $weights);

        // Top Ranked Stall (DSS Benchmark Winner)
        $topStall = $results->first() ?? null;

        // Campus-Wide Aggregate Criteria Health
        $campusHealth = DB::table('stall_evaluations')
            ->selectRaw('
                AVG(cleanliness) as avg_cleanliness,
                AVG(service) as avg_service,
                AVG(taste) as avg_taste,
                AVG(price) as avg_price,
                (AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4 as avg_overall
            ')
            ->first();

        // Stalls Needing Attention (< 3.0 in any criterion or overall)
        $attentionStalls = $results->filter(function($stall) {
            return (float)$stall->overall_score < 3.0 || (float)$stall->cleanliness < 3.0 || (float)$stall->service < 3.0;
        })->values();

        // CHANGED: trend query moved to Controller::activityTrend(), shared with the staff dashboard.
        [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'activityPeriodLabel' => $activityPeriodLabel,
            'activityTotalCount' => $activityTotalCount,
        ] = $this->activityTrend($request);

        // Evaluations per stall (for Pie Chart - derived from $results in-memory to eliminate redundant query)
        $pieChartData = $results->map(function ($row) {
            return (object) [
                'name'  => $row->name,
                'count' => (int) $row->eval_count,
            ];
        });

        // Recent 5 evaluations
        // CHANGED: evaluations are anonymous; the users join and student name are no longer loaded.
        $recentEvaluations = DB::table('stall_evaluations')
            ->join('stalls', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select(
                'stall_evaluations.id',
                'stall_evaluations.cleanliness',
                'stall_evaluations.service',
                'stall_evaluations.taste',
                'stall_evaluations.price',
                'stall_evaluations.created_at',
                'stalls.name as stall_name'
            )
            ->orderBy('stall_evaluations.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'studentCount',
            'stallCount',
            'evaluationCount',
            'results',
            'topStall',
            'campusHealth',
            'attentionStalls',
            'trendDates',
            'trendCounts',
            'availableYears',
            'selectedYear',
            'selectedMonth',
            'activityPeriodLabel',
            'activityTotalCount',
            'pieChartData',
            'recentEvaluations',
            'ahpConsistency'
        ));
    }

    // ADDED: printable report. Same SAW and AHP ranking as the dashboard, limited to the
    // period and stalls the admin picks. Period boundaries use Philippine time, while
    // created_at is stored in UTC.
    public const REPORT_SECTIONS = [
        'ranking'    => 'Stall ranking',
        'attention'  => 'Stalls needing attention',
        'chart'      => 'SAW score chart',
        'prepared'   => 'Prepared by',
    ];

    public const CRITERION_LABELS = [
        'food_quality'    => 'Food Quality',
        'service_quality' => 'Service Quality',
        'price'           => 'Price',
        'cleanliness'     => 'Cleanliness',
    ];

    public function report(Request $request)
    {
        $input = $request->validate([
            'period'          => 'nullable|in:all,month,year,custom',
            'from'            => 'nullable|required_if:period,custom|date',
            'to'              => 'nullable|required_if:period,custom|date|after_or_equal:from',
            'stalls'          => 'nullable|array',
            'stalls.*'        => 'integer',
            'stall_filter'    => 'nullable|boolean',
            'sections'        => 'nullable|array',
            'sections.*'      => 'in:' . implode(',', array_keys(self::REPORT_SECTIONS)),
            'section_filter'  => 'nullable|boolean',
        ]);

        $tz = 'Asia/Manila';
        $now = now($tz);
        $period = $input['period'] ?? 'all';
        $from = null;
        $to = null;

        if ($period === 'month') {
            $from = $now->copy()->startOfMonth();
            $to = $now->copy()->endOfMonth();
            $periodLabel = $from->format('F Y');
        } elseif ($period === 'year') {
            $from = $now->copy()->startOfYear();
            $to = $now->copy()->endOfYear();
            $periodLabel = 'January – December ' . $from->format('Y');
        } elseif ($period === 'custom') {
            $from = \Carbon\Carbon::parse($input['from'], $tz)->startOfDay();
            $to = \Carbon\Carbon::parse($input['to'], $tz)->endOfDay();
            $periodLabel = $from->format('M j, Y') . ' – ' . $to->format('M j, Y');
        } else {
            $periodLabel = 'All evaluations up to ' . $now->format('M j, Y');
        }

        $fromUtc = $from?->copy()->utc();
        $toUtc = $to?->copy()->utc();

        $allStalls = DB::table('stalls')->orderBy('name')->get(['id', 'name']);

        $selectedIds = $request->boolean('stall_filter')
            ? collect($input['stalls'] ?? [])->map(fn ($id) => (int) $id)
                ->intersect($allStalls->pluck('id'))->values()->all()
            : $allStalls->pluck('id')->all();

        $sections = $request->boolean('section_filter')
            ? array_values($input['sections'] ?? [])
            : array_keys(self::REPORT_SECTIONS);

        $results = DB::table('stalls')
            ->leftJoin('stall_evaluations', function ($join) use ($fromUtc, $toUtc) {
                $join->on('stalls.id', '=', 'stall_evaluations.stall_id');
                if ($fromUtc) $join->where('stall_evaluations.created_at', '>=', $fromUtc);
                if ($toUtc) $join->where('stall_evaluations.created_at', '<=', $toUtc);
            })
            ->whereIn('stalls.id', $selectedIds)
            ->select(
                'stalls.id as stall_id',
                'stalls.name',
                DB::raw('COUNT(stall_evaluations.id) as eval_count'),
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price'),
                DB::raw('(AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4 as overall_score')
            )
            ->groupBy('stalls.id', 'stalls.name')
            ->get();

        $ahp = new Ahp;
        $ahpConsistency = $ahp->consistency();
        $weights = $ahpConsistency['weights'];

        $results = (new Saw)->rank($results, $weights);
        $results = $ahp->attachScores($results, $weights);

        $rated = $results->filter(fn ($row) => (int) $row->eval_count > 0)->values();
        $unrated = $results->filter(fn ($row) => (int) $row->eval_count === 0)->sortBy('name')->values();

        $totals = DB::table('stall_evaluations')
            ->whereIn('stall_id', $selectedIds)
            ->when($fromUtc, fn ($q) => $q->where('created_at', '>=', $fromUtc))
            ->when($toUtc, fn ($q) => $q->where('created_at', '<=', $toUtc))
            ->selectRaw('
                COUNT(*) as evaluations,
                COUNT(DISTINCT student_id) as respondents,
                AVG(taste) as food_quality,
                AVG(service) as service_quality,
                AVG(price) as price,
                AVG(cleanliness) as cleanliness,
                MIN(created_at) as first_at,
                MAX(created_at) as last_at
            ')
            ->first();

        // A stall needs attention when its overall mean or any single criterion mean is below 3.00.
        $attention = $rated->map(function ($row) {
            $means = [];
            foreach (Saw::COLUMNS as $criterion => $column) {
                $means[$criterion] = (float) $row->$column;
            }
            asort($means);

            $row->weakest = array_key_first($means);
            $row->below = array_keys(array_filter($means, fn ($m) => $m < 3.0));

            return $row;
        })->filter(fn ($row) => (float) $row->overall_score < 3.0 || $row->below !== [])->values();

        $preparedBy = [
            'name'  => Auth::user()->name,
            'title' => ucfirst(Auth::user()->role === 'admin' ? 'administrator' : Auth::user()->role),
        ];

        return view('admin.report', [
            'period'          => $period,
            'from'            => $from,
            'to'              => $to,
            'periodLabel'     => $periodLabel,
            'generatedAt'     => $now,
            'allStalls'       => $allStalls,
            'selectedIds'     => $selectedIds,
            'sections'        => $sections,
            'sectionOptions'  => self::REPORT_SECTIONS,
            'criterionLabels' => self::CRITERION_LABELS,
            'columns'         => Saw::COLUMNS,
            'ahpConsistency'  => $ahpConsistency,
            'rated'           => $rated,
            'unrated'         => $unrated,
            'totals'          => $totals,
            'attention'       => $attention,
            'preparedBy'      => $preparedBy,
            // CHANGED: removed 'autoPrint'. The page now prints from the live preview, so print=1 is never sent.
        ]);
    }

    public function stalls()
    {
        $stalls = DB::table('stalls')
            ->orderBy('name')
            ->get();

        $staffUsers = DB::table('users')
            ->leftJoin('stalls', 'users.stall_id', '=', 'stalls.id')
            ->where('users.role', 'staff')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.stall_id',
                'stalls.name as current_stall_name'
            )
            ->orderBy('users.name')
            ->get();

        $unassignedStaff = $staffUsers->whereNull('stall_id')->values();
        $stallStaffMap = $staffUsers->whereNotNull('stall_id')->groupBy('stall_id');

        $results = DB::table('stall_evaluations')
            ->select(
                'stall_id',
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price')
            )
            ->groupBy('stall_id')
            ->get()
            ->keyBy('stall_id');

        return view('admin.stalls', compact('stalls', 'staffUsers', 'unassignedStaff', 'stallStaffMap', 'results'));
    }

    public function evaluations(Request $request)
    {
        // CHANGED: evaluations are anonymous. The users join, student_id and student name are no longer
        // loaded, because each row is also written into the page as JSON for the details modal.
        $query = DB::table('stall_evaluations')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stall_evaluations.id',
                'stall_evaluations.stall_id',
                'stall_evaluations.cleanliness',
                'stall_evaluations.service',
                'stall_evaluations.taste',
                'stall_evaluations.price',
                'stall_evaluations.comment',
                'stall_evaluations.created_at',
                'stalls.name as stall_name'
            );

        if ($request->filled('q')) {
            $q = '%' . trim($request->q) . '%';
            // CHANGED: search no longer matches student names.
            $query->where(function($sub) use ($q) {
                $sub->where('stalls.name', 'like', $q)
                    ->orWhere('stall_evaluations.comment', 'like', $q);
            });
        }

        if ($request->filled('stall_id')) {
            $query->where('stall_evaluations.stall_id', $request->stall_id);
        }

        $sortBy = $request->get('sort', 'latest');
        if ($sortBy === 'oldest') {
            $query->orderBy('stall_evaluations.created_at', 'asc');
        } elseif ($sortBy === 'rating_high') {
            $query->orderByRaw('(stall_evaluations.cleanliness + stall_evaluations.service + stall_evaluations.taste + stall_evaluations.price) DESC');
        } elseif ($sortBy === 'rating_low') {
            $query->orderByRaw('(stall_evaluations.cleanliness + stall_evaluations.service + stall_evaluations.taste + stall_evaluations.price) ASC');
        } else {
            $query->latest('stall_evaluations.created_at');
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $evaluations = $query->paginate($perPage)->withQueryString();
        $stalls = DB::table('stalls')->select('id', 'name')->orderBy('name')->get();

        return view('admin.evaluations', compact('evaluations', 'stalls'));
    }

    public function students(Request $request)
    {
        // CHANGED: codes are upper-case and matched against UPPER(TRIM(course)) below, the same way the Students view assigns department badges. PostgreSQL compares text case-sensitively, so a course saved as "bsit" or "BS Crim" got a badge but was left out when filtering by its department. Added "BS CRIM", which the view already counted as CCJE; dropped the mixed-case duplicates.
        $deptCourseMap = [
            'CCSICT' => ['BSIT', 'BSCS', 'BSIS', 'ACT', 'MIT'],
            'CHM'    => ['BSHM', 'BSTM', 'HRM'],
            'CBA'    => ['BSBA', 'BSA', 'BSMA', 'BSENTREP'],
            'CED'    => ['BSED', 'BEED', 'BPED', 'BTLED'],
            'CCJE'   => ['BSCRIM', 'BS CRIM', 'BSLE'],
            'CAS'    => ['BA COMM', 'BS PSYCH', 'BS BIO', 'BACOMM', 'BSPSYCH'],
        ];

        $query = DB::table('users')
            ->leftJoin('stall_evaluations', 'stall_evaluations.student_id', '=', 'users.id')
            ->where('users.role', 'student')
            ->select(
                'users.id',
                'users.name',
                'users.student_number',
                'users.course',
                'users.year_level',
                'users.created_at',
                DB::raw('COUNT(stall_evaluations.id) as evaluations_count'),
                DB::raw('MAX(stall_evaluations.created_at) as last_evaluation_at')
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.student_number',
                'users.course',
                'users.year_level',
                'users.created_at'
            );

        // Search filter
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('users.name', 'like', "%{$q}%")
                  ->orWhere('users.student_number', 'like', "%{$q}%");
            });
        }

        // Department filter
        $selectedDept = $request->get('department');
        if ($selectedDept && isset($deptCourseMap[$selectedDept])) {
            // CHANGED: case- and space-insensitive match (see $deptCourseMap).
            $codes = $deptCourseMap[$selectedDept];
            $query->whereRaw('UPPER(TRIM(users.course)) IN (' . implode(',', array_fill(0, count($codes), '?')) . ')', $codes);
        }

        // Course filter
        if ($request->filled('course')) {
            // CHANGED: case- and space-insensitive, so "BSIT" also finds "bsit".
            $query->whereRaw('UPPER(TRIM(users.course)) = ?', [strtoupper(trim($request->course))]);
        }

        // Year level filter
        if ($request->filled('year_level')) {
            // CHANGED: case-insensitive. Sign-up saves "1st year" but the profile form saved "1st Year", so on PostgreSQL the filter missed every student who had edited their profile.
            $query->whereRaw('LOWER(users.year_level) = ?', [strtolower($request->year_level)]);
        }

        // Sort order
        $sortBy = $request->get('sort', 'latest');
        if ($sortBy === 'name_asc') {
            $query->orderBy('users.name', 'asc');
        } elseif ($sortBy === 'evaluations_desc') {
            $query->orderByDesc('evaluations_count');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('users.created_at', 'asc');
        } else {
            $query->orderByDesc('users.created_at');
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $students = $query->paginate($perPage)->withQueryString();

        // Department counts for filter dropdown options (single grouped query
        // instead of one COUNT query per department)
        $courseCounts = DB::table('users')
            ->where('role', 'student')
            ->whereNotNull('course')
            ->select('course', DB::raw('COUNT(*) as cnt'))
            ->groupBy('course')
            ->pluck('cnt', 'course');

        // CHANGED: counts are grouped by UPPER(TRIM(course)) so the numbers in the department dropdown match what the filter returns.
        $upperCourseCounts = [];
        foreach ($courseCounts as $course => $cnt) {
            $key = strtoupper(trim((string) $course));
            $upperCourseCounts[$key] = ($upperCourseCounts[$key] ?? 0) + $cnt;
        }

        $departmentStats = [];
        foreach ($deptCourseMap as $code => $courses) {
            $departmentStats[$code] = collect($courses)->sum(fn ($c) => $upperCourseCounts[$c] ?? 0);
        }

        $departments = [
            ['code' => 'CCSICT', 'name' => 'Computing Studies (CCSICT)', 'courses' => ['BSIT', 'BSCS', 'BSIS', 'ACT']],
            ['code' => 'CHM',    'name' => 'Hospitality Management (CHM)', 'courses' => ['BSHM', 'BSTM', 'HRM']],
            ['code' => 'CBA',    'name' => 'Business & Accountancy (CBA)', 'courses' => ['BSBA', 'BSA', 'BSMA', 'BSENTREP']],
            ['code' => 'CED',    'name' => 'Teacher Education (CED)', 'courses' => ['BSED', 'BEED', 'BPED', 'BTLED']],
            ['code' => 'CCJE',   'name' => 'Criminal Justice (CCJE)', 'courses' => ['BSCRIM', 'BSCrim', 'BSLE']],
            ['code' => 'CAS',    'name' => 'Arts & Sciences (CAS)', 'courses' => ['BA Comm', 'BS Psych', 'BS Bio']],
        ];

        // Available distinct courses derived from $courseCounts (eliminates redundant DB query)
        $dbCourses = $courseCounts->keys()->filter(fn ($c) => trim((string)$c) !== '')->values()->toArray();
        $courseOptions = array_values(array_unique(array_merge(['BSIT', 'BSCS', 'BSHM', 'BSBA', 'BSED', 'BEED', 'BSCRIM'], $dbCourses)));

        $yearOptions = ['1st year', '2nd year', '3rd year', '4th year'];

        return view('admin.students', compact(
            'students',
            'departmentStats',
            'departments',
            'courseOptions',
            'yearOptions',
            'selectedDept'
        ));
    }

    // Add Stall
    public function addStall(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
            'description' => 'nullable|string|max:500',
        ]);

        $stallId = DB::table('stalls')->insertGetId([
            'name' => trim($request->name),
            'description' => $request->filled('description') ? trim($request->description) : null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign multiple selected staff members
        if ($request->has('staff_ids') && is_array($request->staff_ids)) {
            DB::table('users')
                ->whereIn('id', $request->staff_ids)
                ->where('role', 'staff')
                ->update(['stall_id' => $stallId, 'updated_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stall added successfully!');
    }

    // Edit Stall
    public function editStall(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        DB::table('stalls')->where('id', $id)->update([
            'name' => trim($request->name),
            'description' => $request->filled('description') ? trim($request->description) : null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            'updated_at' => now(),
        ]);

        // Sync staff assignments:
        // 1. Unassign all staff currently attached to this stall
        DB::table('users')->where('stall_id', $id)->update(['stall_id' => null, 'updated_at' => now()]);

        // 2. Assign the newly selected staff members
        if ($request->has('staff_ids') && is_array($request->staff_ids)) {
            DB::table('users')
                ->whereIn('id', $request->staff_ids)
                ->where('role', 'staff')
                ->update(['stall_id' => $id, 'updated_at' => now()]);
        }

        return redirect()->back()->with('success', 'Stall updated successfully!');
    }

    // Delete Stall
    public function deleteStall($id)
    {
        DB::table('stalls')
            ->where('id',$id)
            ->delete();

        return back()->with('success','Stall deleted.');
    }

    // Quick Assign Staff
    public function assignStaff(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'stall_id' => 'required|exists:stalls,id',
        ]);

        $stall = DB::table('stalls')->where('id', $request->stall_id)->first();

        DB::table('users')->where('id', $request->staff_id)->where('role', 'staff')->update([
            'stall_id' => $request->stall_id,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Staff member assigned to ' . ($stall ? $stall->name : 'stall') . ' successfully!');
    }

    // Unassign Staff
    public function unassignStaff(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        DB::table('users')->where('id', $request->staff_id)->where('role', 'staff')->update([
            'stall_id' => null,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Staff member unassigned from stall successfully.');
    }

    // ─── Internal User Management ──────────────────────────────────────

    public function users(Request $request)
    {
        // Total system counts for stat cards & filter pill badges
        // CHANGED: include clinic accounts and count them.
        $stats = DB::table('users')
            ->whereIn('role', ['admin', 'staff', 'clinic'])
            ->selectRaw("
                COUNT(*) as total_count,
                COUNT(CASE WHEN role = 'admin' THEN 1 END) as admin_count,
                COUNT(CASE WHEN role = 'clinic' THEN 1 END) as clinic_count,
                COUNT(CASE WHEN role = 'staff' THEN 1 END) as staff_count,
                COUNT(CASE WHEN role = 'staff' AND stall_id IS NOT NULL THEN 1 END) as assigned_staff_count,
                COUNT(CASE WHEN role = 'staff' AND stall_id IS NULL THEN 1 END) as unassigned_staff_count
            ")
            ->first();

        $query = DB::table('users')
            ->leftJoin('stalls', 'stalls.id', '=', 'users.stall_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.stall_id',
                'users.created_at',
                'stalls.name as stall_name'
            )
            // CHANGED: include clinic accounts.
            ->whereIn('users.role', ['admin', 'staff', 'clinic']);

        if ($request->filled('q')) {
            $q = '%' . strtolower(trim($request->q)) . '%';
            $query->where(function ($sub) use ($q) {
                $sub->whereRaw('LOWER(users.name) LIKE ?', [$q])
                    ->orWhereRaw('LOWER(users.email) LIKE ?', [$q])
                    ->orWhereRaw('LOWER(stalls.name) LIKE ?', [$q]);
            });
        }

        $role = $request->get('role', $request->get('role_filter', 'all'));
        if ($role === 'admin') {
            $query->where('users.role', 'admin');
        // ADDED: clinic filter pill.
        } elseif ($role === 'clinic') {
            $query->where('users.role', 'clinic');
        } elseif ($role === 'staff') {
            $query->where('users.role', 'staff');
        } elseif ($role === 'unassigned') {
            $query->where('users.role', 'staff')->whereNull('users.stall_id');
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $users = $query->orderByDesc('users.created_at')->paginate($perPage)->withQueryString();

        $stalls = DB::table('stalls')
            ->select('id', 'name', 'is_active')
            ->orderBy('name')
            ->get();

        return view('admin.users', compact('users', 'stalls', 'stats'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            // CHANGED: admins can create clinic accounts.
            'role'     => 'required|in:admin,staff,clinic',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'stall_id' => 'nullable|exists:stalls,id',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        User::create([
            'role'              => $request->role,
            'name'              => trim($request->name),
            'email'             => trim($request->email),
            'stall_id'          => $request->role === 'staff' ? $request->stall_id : null,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // CHANGED: label for clinic accounts.
        $label = ['admin' => 'Administrator', 'staff' => 'Staff', 'clinic' => 'Clinic'][$request->role];
        if ($request->wantsJson()) {
            session()->flash('success', "{$label} account created successfully!");
            return response()->json([
                'success' => true,
                'message' => "{$label} account created successfully!",
            ]);
        }
        return redirect()->back()->with('success', "{$label} account created successfully!");
    }

    public function updateUser(Request $request, $id)
    {
        $target = User::findOrFail($id);

        // CHANGED: clinic accounts are managed here too.
        if (!in_array($target->role, ['admin', 'staff', 'clinic'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid operation for this user type.'], 422);
            }
            return redirect()->back()->with('error', 'Invalid operation for this user type.');
        }

        $rules = [
            // CHANGED: admins can assign the clinic role.
            'role'     => 'required|in:admin,staff,clinic',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $target->id,
            'stall_id' => 'nullable|exists:stalls,id',
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ];
        }

        $request->validate($rules);

        // Safeguard: Do not allow logged-in admin to demote themselves if they are the only admin
        if ($target->id === Auth::id() && $request->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You cannot remove your own administrator privileges.',
                    'errors' => [
                        'role' => ['You cannot remove your own administrator privileges.']
                    ]
                ], 422);
            }
            return redirect()->back()->with('error', 'You cannot remove your own administrator privileges.');
        }

        $target->name = trim($request->name);
        $target->email = trim($request->email);
        $target->role = $request->role;
        $target->stall_id = $request->role === 'staff' ? $request->stall_id : null;

        if ($request->filled('password')) {
            $target->password = Hash::make($request->password);
        }

        $target->save();

        if ($request->wantsJson()) {
            session()->flash('success', "Account for {$target->name} updated successfully.");
            return response()->json([
                'success' => true,
                'message' => "Account for {$target->name} updated successfully.",
            ]);
        }
        return redirect()->back()->with('success', "Account for {$target->name} updated successfully.");
    }

    public function deleteUser(Request $request, $id)
    {
        $target = User::findOrFail($id);

        // Prevent self-deletion
        if ($target->id === Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'You cannot delete your own account.'], 422);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Safeguard: Prevent deleting the last remaining admin
        if ($target->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Cannot delete the last remaining administrator account in the system.'], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete the last remaining administrator account in the system.');
            }
        }

        // Only allow deleting admin/staff from this page (students managed elsewhere)
        // CHANGED: clinic accounts can be deleted from this page too.
        if (!in_array($target->role, ['admin', 'staff', 'clinic'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid operation.'], 422);
            }
            return redirect()->back()->with('error', 'Invalid operation.');
        }

        $targetName = $target->name;
        $target->delete();

        if ($request->wantsJson()) {
            session()->flash('success', "{$targetName}'s account has been deleted.");
            return response()->json([
                'success' => true,
                'message' => "{$targetName}'s account has been deleted.",
            ]);
        }
        return redirect()->back()->with('success', "{$targetName}'s account has been deleted.");
    }
}