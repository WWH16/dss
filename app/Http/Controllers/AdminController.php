<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role != 'admin') {
            return redirect('/login');
        }

        // Dashboard Counts
        $studentCount = DB::table('users')
            ->where('role', 'student')
            ->count();

        $stallCount = DB::table('stalls')->count();

        $evaluationCount = DB::table('stall_evaluations')->count();

        // All stalls
        $stalls = DB::table('stalls')
            ->orderBy('name')
            ->get();

        // Evaluation Results
        $evaluations = DB::table('stall_evaluations')
            ->join('users','users.id','=','stall_evaluations.student_id')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stall_evaluations.*',
                'users.name as student_name',
                'stalls.name as stall_name'
            )
            ->latest()
            ->get();

        // Average rating per stall
        $results = DB::table('stall_evaluations')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stalls.name',
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price')
            )
            ->groupBy('stalls.name')
            ->get();

        // Daily evaluation trend (last 30 days)
        $evalTrend = DB::table('stall_evaluations')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build full 30-day range filled with 0 for missing days
        $trendDates = [];
        $trendCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->format('M d');
            $trendCounts[] = isset($evalTrend[$d]) ? (int) $evalTrend[$d]->count : 0;
        }

        // Evaluations per stall (for Pie Chart)
        $pieChartData = DB::table('stall_evaluations')
            ->join('stalls', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select('stalls.name', DB::raw('COUNT(*) as count'))
            ->groupBy('stalls.name')
            ->get();

        // Recent 5 evaluations
        $recentEvaluations = DB::table('stall_evaluations')
            ->join('users', 'users.id', '=', 'stall_evaluations.student_id')
            ->join('stalls', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select('stall_evaluations.*', 'users.name as student_name', 'stalls.name as stall_name')
            ->orderBy('stall_evaluations.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'studentCount',
            'stallCount',
            'evaluationCount',
            'stalls',
            'evaluations',
            'results',
            'trendDates',
            'trendCounts',
            'pieChartData',
            'recentEvaluations'
        ));
    }

    public function stalls()
    {
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $stalls = DB::table('stalls')->orderBy('name')->get();

        $results = DB::table('stall_evaluations')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stalls.name',
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price')
            )
            ->groupBy('stalls.name')
            ->get();

        return view('admin.stalls', compact('stalls', 'results'));
    }

    public function evaluations(Request $request)
    {
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $query = DB::table('stall_evaluations')
            ->join('users','users.id','=','stall_evaluations.student_id')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stall_evaluations.id',
                'stall_evaluations.stall_id',
                'stall_evaluations.student_id',
                'stall_evaluations.cleanliness',
                'stall_evaluations.service',
                'stall_evaluations.taste',
                'stall_evaluations.price',
                'stall_evaluations.comment',
                'stall_evaluations.created_at',
                'users.name as student_name',
                'stalls.name as stall_name'
            );

        if ($request->filled('q')) {
            $q = '%' . trim($request->q) . '%';
            $query->where(function($sub) use ($q) {
                $sub->where('users.name', 'like', $q)
                    ->orWhere('stalls.name', 'like', $q)
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
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $deptCourseMap = [
            'CCSICT' => ['BSIT', 'BSCS', 'BSIS', 'ACT', 'MIT'],
            'CHM'    => ['BSHM', 'BSTM', 'HRM'],
            'CBA'    => ['BSBA', 'BSA', 'BSMA', 'BSEntrep', 'BSENTREP'],
            'CED'    => ['BSED', 'BEED', 'BPED', 'BTLED'],
            'CCJE'   => ['BSCRIM', 'BSCrim', 'BSLE'],
            'CAS'    => ['BA Comm', 'BS Psych', 'BS Bio', 'BACOMM', 'BSPSYCH'],
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
            $query->whereIn('users.course', $deptCourseMap[$selectedDept]);
        }

        // Course filter
        if ($request->filled('course')) {
            $query->where('users.course', $request->course);
        }

        // Year level filter
        if ($request->filled('year_level')) {
            $query->where('users.year_level', $request->year_level);
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

        // Department counts for filter dropdown options
        $departmentStats = [];
        foreach ($deptCourseMap as $code => $courses) {
            $departmentStats[$code] = DB::table('users')
                ->where('role', 'student')
                ->whereIn('course', $courses)
                ->count();
        }

        $departments = [
            ['code' => 'CCSICT', 'name' => 'Computing Studies (CCSICT)', 'courses' => ['BSIT', 'BSCS', 'BSIS', 'ACT']],
            ['code' => 'CHM',    'name' => 'Hospitality Management (CHM)', 'courses' => ['BSHM', 'BSTM', 'HRM']],
            ['code' => 'CBA',    'name' => 'Business & Accountancy (CBA)', 'courses' => ['BSBA', 'BSA', 'BSMA', 'BSENTREP']],
            ['code' => 'CED',    'name' => 'Teacher Education (CED)', 'courses' => ['BSED', 'BEED', 'BPED', 'BTLED']],
            ['code' => 'CCJE',   'name' => 'Criminal Justice (CCJE)', 'courses' => ['BSCRIM', 'BSCrim', 'BSLE']],
            ['code' => 'CAS',    'name' => 'Arts & Sciences (CAS)', 'courses' => ['BA Comm', 'BS Psych', 'BS Bio']],
        ];

        // Available distinct courses from DB or standard list
        $dbCourses = DB::table('users')
            ->where('role', 'student')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course')
            ->toArray();
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
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        DB::table('stalls')->insert([
            'name' => trim($request->name),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Stall added successfully!');
    }

    // Edit Stall
    public function editStall(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        DB::table('stalls')->where('id', $id)->update([
            'name' => trim($request->name),
            'updated_at' => now(),
        ]);

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
}