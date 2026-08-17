<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        // Student profile
        $profile = DB::table('users')
            ->where('id', $user->id)
            ->first();

        // Food stalls
        $stalls = DB::table('stalls')
            ->orderBy('name')
            ->get();

        // Student's own evaluations
        $myStudentEvals = DB::table('stall_evaluations')
            ->join('stalls', 'stall_evaluations.stall_id', '=', 'stalls.id')
            ->where('stall_evaluations.student_id', $user->id)
            ->select(
                'stall_evaluations.*',
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
                DB::raw('(AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4 as overall_score')
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

        $profile = DB::table('users')
            ->where('id', $user->id)
            ->first();

        return view('student.profile', compact('profile'));
    }

    public function history()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return redirect('/login');
        }

        $myStudentEvals = DB::table('stall_evaluations')
            ->join('stalls', 'stall_evaluations.stall_id', '=', 'stalls.id')
            ->where('stall_evaluations.student_id', $user->id)
            ->select(
                'stall_evaluations.*',
                'stalls.name as stall_name'
            )
            ->orderByDesc('stall_evaluations.created_at')
            ->get();

        return view('student.history', compact('myStudentEvals'));
    }
}