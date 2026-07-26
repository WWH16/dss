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

        return view('admin.dashboard', compact(
            'studentCount',
            'stallCount',
            'evaluationCount',
            'stalls',
            'evaluations',
            'results',
            'trendDates',
            'trendCounts'
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

    public function evaluations()
    {
        if (!Auth::check() || Auth::user()->role != 'admin') return redirect('/login');

        $evaluations = DB::table('stall_evaluations')
            ->join('users','users.id','=','stall_evaluations.student_id')
            ->join('stalls','stalls.id','=','stall_evaluations.stall_id')
            ->select(
                'stall_evaluations.*',
                'users.name as student_name',
                'stalls.name as stall_name'
            )
            ->latest('stall_evaluations.created_at')
            ->get();

        return view('admin.evaluations', compact('evaluations'));
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