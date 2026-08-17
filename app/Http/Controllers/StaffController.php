<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Staff Dashboard: Scoped strictly to the staff member's assigned food stall.
     */
    public function dashboard(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();

        // 1. Look up assigned food stall for this staff member (via users.stall_id or legacy stalls.staff_id)
        $stall = null;
        if ($user->stall_id) {
            $stall = DB::table('stalls')->where('id', $user->stall_id)->first();
        }
        if (!$stall) {
            $stall = DB::table('stalls')->where('staff_id', $user->id)->first();
        }

        // If not assigned to any stall, render the unassigned state with zero stall data
        if (!$stall) {
            return view('staff.staff_dashboard', [
                'hasStall' => false,
                'stall' => null,
                'user' => $user,
                'totalEvaluations' => 0,
                'uniqueStudents' => 0,
                'averages' => null,
                'evaluations' => collect(),
                'stallRank' => null,
                'totalStalls' => DB::table('stalls')->count(),
            ]);
        }

        // 2. Metrics for the assigned stall ONLY
        $totalEvaluations = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->count();

        $uniqueStudents = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->distinct('student_id')
            ->count('student_id');

        $averages = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->selectRaw('
                AVG(cleanliness) as cleanliness,
                AVG(service) as service,
                AVG(taste) as taste,
                AVG(price) as price,
                COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall
            ')
            ->first();

        // 3. Compute this stall's current campus rank without exposing details of other stalls
        $rankedStalls = DB::table('stalls')
            ->leftJoin('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select(
                'stalls.id',
                DB::raw('COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall_score')
            )
            ->groupBy('stalls.id')
            ->orderByDesc('overall_score')
            ->get();

        $stallRank = null;
        $totalStalls = $rankedStalls->count();
        foreach ($rankedStalls as $idx => $r) {
            if ($r->id == $stall->id) {
                $stallRank = $idx + 1;
                break;
            }
        }

        // 4. Compute campus-wide benchmark criteria averages for comparison
        $campusCriteria = DB::table('stall_evaluations')
            ->selectRaw('
                AVG(cleanliness) as cleanliness,
                AVG(service) as service,
                AVG(taste) as taste,
                AVG(price) as price,
                COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall
            ')
            ->first();

        // 5. Rating breakdown distribution (1 to 5 stars) for this stall
        $ratingDistribution = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 5 THEN 1 ELSE 0 END), 0) as stars_5,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 4 THEN 1 ELSE 0 END), 0) as stars_4,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 3 THEN 1 ELSE 0 END), 0) as stars_3,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 2 THEN 1 ELSE 0 END), 0) as stars_2,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 1 THEN 1 ELSE 0 END), 0) as stars_1
            ')
            ->first();

        // 6. Evaluation Activity Timeline trend with Month & Year filtering
        $availableYears = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [(int)date('Y')];
        }
        if (!in_array((int)date('Y'), $availableYears)) {
            array_unshift($availableYears, (int)date('Y'));
        }

        $selectedYear = (int)$request->get('activity_year', $availableYears[0] ?? date('Y'));
        $selectedMonth = $request->get('activity_month', '30_days');

        $trendDates = [];
        $trendCounts = [];

        if ($selectedMonth === 'all') {
            // Full Year: Monthly aggregations (Jan - Dec)
            $evalTrend = DB::table('stall_evaluations')
                ->where('stall_id', $stall->id)
                ->selectRaw('MONTH(created_at) as m, COUNT(*) as count')
                ->whereYear('created_at', $selectedYear)
                ->groupByRaw('MONTH(created_at)')
                ->get()
                ->keyBy('m');

            for ($m = 1; $m <= 12; $m++) {
                $trendDates[] = date('M', mktime(0, 0, 0, $m, 1));
                $trendCounts[] = isset($evalTrend[$m]) ? (int)$evalTrend[$m]->count : 0;
            }
            $activityPeriodLabel = "Full Year {$selectedYear}";
        } elseif (is_numeric($selectedMonth) && (int)$selectedMonth >= 1 && (int)$selectedMonth <= 12) {
            // Specific Month: Day-by-Day (Day 1 to Days in Month)
            $m = (int)$selectedMonth;
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $selectedYear);

            $evalTrend = DB::table('stall_evaluations')
                ->where('stall_id', $stall->id)
                ->selectRaw('DAY(created_at) as d, COUNT(*) as count')
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $m)
                ->groupByRaw('DAY(created_at)')
                ->get()
                ->keyBy('d');

            $monthShort = date('M', mktime(0, 0, 0, $m, 1));
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $trendDates[] = sprintf('%s %02d', $monthShort, $d);
                $trendCounts[] = isset($evalTrend[$d]) ? (int)$evalTrend[$d]->count : 0;
            }
            $activityPeriodLabel = date('F Y', mktime(0, 0, 0, $m, 1, $selectedYear));
        } else {
            // Default: Rolling Last 30 Days
            $selectedMonth = '30_days';
            $evalTrend = DB::table('stall_evaluations')
                ->where('stall_id', $stall->id)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            for ($i = 29; $i >= 0; $i--) {
                $d = now()->subDays($i)->format('Y-m-d');
                $trendDates[] = now()->subDays($i)->format('M d');
                $trendCounts[] = isset($evalTrend[$d]) ? (int) $evalTrend[$d]->count : 0;
            }
            $activityPeriodLabel = 'Last 30 Days';
        }
        $activityTotalCount = array_sum($trendCounts);

        // 7. Paginated evaluations list for this stall (STRICT PRIVACY: zero student names or IDs)
        $evaluationsQuery = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->select(
                'id',
                'cleanliness',
                'service',
                'taste',
                'price',
                'comment',
                'created_at'
            );

        if ($request->filled('q')) {
            $q = '%' . trim($request->q) . '%';
            $evaluationsQuery->where('comment', 'like', $q);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $evaluationsQuery->orderBy('created_at', 'asc');
        } elseif ($sort === 'rating_high') {
            $evaluationsQuery->orderByRaw('(cleanliness + service + taste + price) DESC');
        } elseif ($sort === 'rating_low') {
            $evaluationsQuery->orderByRaw('(cleanliness + service + taste + price) ASC');
        } else {
            $evaluationsQuery->orderBy('created_at', 'desc');
        }

        $evaluations = $evaluationsQuery->paginate(10)->withQueryString();

        return view('staff.staff_dashboard', [
            'hasStall' => true,
            'stall' => $stall,
            'user' => $user,
            'totalEvaluations' => $totalEvaluations,
            'uniqueStudents' => $uniqueStudents,
            'averages' => $averages,
            'campusCriteria' => $campusCriteria,
            'ratingDistribution' => $ratingDistribution,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'activityPeriodLabel' => $activityPeriodLabel,
            'activityTotalCount' => $activityTotalCount,
            'evaluations' => $evaluations,
            'stallRank' => $stallRank,
            'totalStalls' => $totalStalls,
        ]);
    }

    /**
     * Other Stall Standings / Rankings:
     * Staff can view aggregate rankings across campus, but CANNOT view other stalls' individual feedback.
     */
    public function standings()
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();

        // Check if this staff member has an assigned stall
        $myStall = null;
        if ($user->stall_id) {
            $myStall = DB::table('stalls')->where('id', $user->stall_id)->first();
        }
        // Strict Security Guard: Unassigned staff MUST NOT view campus rankings or performance
        if (!$myStall) {
            return redirect()->route('staff.dashboard')->with('error', 'Access restricted. You must be assigned to a food stall by an Administrator to view campus standings.');
        }

        // Fetch aggregate standing data only (no student evaluations or comments)
        $standings = DB::table('stalls')
            ->leftJoin('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select(
                'stalls.id',
                'stalls.name',
                'stalls.is_active',
                DB::raw('COUNT(stall_evaluations.id) as eval_count'),
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price'),
                DB::raw('COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall_score')
            )
            ->groupBy('stalls.id', 'stalls.name', 'stalls.is_active')
            ->orderByDesc('overall_score')
            ->get();

        return view('staff.standings', [
            'standings' => $standings,
            'myStall' => $myStall,
            'user' => $user,
        ]);
    }

    /**
     * Staff Profile Page
     */
    public function profile()
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();
        $stall = null;
        if ($user->stall_id) {
            $stall = DB::table('stalls')->where('id', $user->stall_id)->first();
        }
        if (!$stall) {
            $stall = DB::table('stalls')->where('staff_id', $user->id)->first();
        }

        return view('staff.profile', [
            'profile' => $user,
            'stall' => $stall,
        ]);
    }

    /**
     * Update Staff Profile Details
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update Staff Password
     */
    public function updatePassword(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password you provided is incorrect.']);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}