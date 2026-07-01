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

        return view('student.dashboard', compact(
            'profile',
            'stalls',
            'myStudentEvals'
        ));
    }
}