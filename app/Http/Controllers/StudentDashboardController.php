<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        // Student profile
        $profile = $user;

        // Food stalls (selecting essential columns)
        $stalls = DB::table('stalls')
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        // Student's own evaluations
        $myStudentEvals = DB::table('stall_evaluations')
            ->join('stalls', 'stall_evaluations.stall_id', '=', 'stalls.id')
            ->where('stall_evaluations.student_id', $user->id)
            ->select(
                'stall_evaluations.id',
                'stall_evaluations.stall_id',
                'stall_evaluations.cleanliness',
                'stall_evaluations.service',
                'stall_evaluations.taste',
                'stall_evaluations.price',
                'stall_evaluations.created_at',
                'stalls.name as stall_name'
            )
            ->orderByDesc('stall_evaluations.created_at')
            ->get();

        // Map evaluations by stall ID for fast status badge lookup
        $evaluatedStallsMap = $myStudentEvals->groupBy('stall_id')->map(function ($evals) {
            $latest = $evals->first();
            $avg = ($latest->cleanliness + $latest->service + $latest->taste + $latest->price) / 4;
            return [
                'eval_count' => $evals->count(),
                'latest_avg' => round($avg, 1),
                'latest_date' => $latest->created_at,
            ];
        });

        // Summary Stats
        $totalStallsCount = $stalls->count();
        $uniqueEvaluatedCount = $evaluatedStallsMap->count();
        $coveragePct = $totalStallsCount > 0 ? round(($uniqueEvaluatedCount / $totalStallsCount) * 100) : 0;
        $totalEvalsCount = $myStudentEvals->count();

        $overallAvgGiven = $totalEvalsCount > 0
            ? round($myStudentEvals->sum(function ($e) {
                return ($e->cleanliness + $e->service + $e->taste + $e->price) / 4;
            }) / $totalEvalsCount, 2)
            : 0;

        // Top Ranked Stall on Campus (DSS Composite Benchmark)
        $topCampusStall = DB::table('stalls')
            ->leftJoin('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select(
                'stalls.id',
                'stalls.name',
                'stalls.description',
                DB::raw('COUNT(stall_evaluations.id) as eval_count'),
                DB::raw('COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall_score')
            )
            ->groupBy('stalls.id', 'stalls.name', 'stalls.description')
            ->havingRaw('COUNT(stall_evaluations.id) > 0')
            ->orderByDesc('overall_score')
            ->first();

        return view('student.dashboard', compact(
            'profile',
            'stalls',
            'myStudentEvals',
            'evaluatedStallsMap',
            'totalStallsCount',
            'uniqueEvaluatedCount',
            'coveragePct',
            'totalEvalsCount',
            'overallAvgGiven',
            'topCampusStall'
        ));
    }

    public function profile()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $profile = $user;

        // Single aggregated SQL query for profile metrics
        $stats = DB::table('stall_evaluations')
            ->where('student_id', $user->id)
            ->selectRaw('COUNT(*) as total_evals, COUNT(DISTINCT stall_id) as unique_stalls, AVG((cleanliness + service + taste + price) / 4) as avg_given')
            ->first();

        $totalEvals = $stats->total_evals ?? 0;
        $uniqueStalls = $stats->unique_stalls ?? 0;
        $avgGiven = $stats->avg_given ? (float)$stats->avg_given : 0;

        return view('student.profile', compact(
            'profile',
            'totalEvals',
            'uniqueStalls',
            'avgGiven'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'course' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'name' => trim($request->name),
            'course' => $request->course ? trim($request->course) : null,
            'year_level' => $request->year_level ? trim($request->year_level) : null,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Profile information updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password provided is incorrect.']);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $query = DB::table('stall_evaluations')
            ->join('stalls', 'stall_evaluations.stall_id', '=', 'stalls.id')
            ->where('stall_evaluations.student_id', $user->id)
            ->select(
                'stall_evaluations.id',
                'stall_evaluations.student_id',
                'stall_evaluations.stall_id',
                'stall_evaluations.cleanliness',
                'stall_evaluations.service',
                'stall_evaluations.taste',
                'stall_evaluations.price',
                'stall_evaluations.comment',
                'stall_evaluations.created_at',
                'stalls.name as stall_name',
                'stalls.description as stall_description'
            );

        if ($request->filled('q')) {
            $q = '%' . trim($request->q) . '%';
            $query->where(function ($w) use ($q) {
                $w->where('stalls.name', 'like', $q)
                  ->orWhere('stall_evaluations.comment', 'like', $q);
            });
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('stall_evaluations.created_at', 'asc');
        } elseif ($sort === 'rating_high') {
            $query->orderByRaw('(cleanliness + service + taste + price) DESC');
        } elseif ($sort === 'rating_low') {
            $query->orderByRaw('(cleanliness + service + taste + price) ASC');
        } else {
            $query->orderByDesc('stall_evaluations.created_at');
        }

        $myStudentEvals = $query->paginate(12)->withQueryString();
        $totalEvaluations = $myStudentEvals->total();

        return view('student.history', compact('myStudentEvals', 'totalEvaluations'));
    }
}