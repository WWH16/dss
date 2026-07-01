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

        return view('admin.dashboard', compact(
            'studentCount',
            'stallCount',
            'evaluationCount',
            'stalls',
            'evaluations',
            'results'
        ));
    }

    // Add Stall
    public function addStall(Request $request)
    {
        $request->validate([
            'name'=>'required',
            
        ]);

        DB::table('stalls')->insert([
            'name'=>$request->name,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        return back()->with('success','Stall added successfully.');
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