<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Ahp;
use App\Services\Saw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

// CHANGED: role checks moved out of each method into the role middleware on this controller's routes (routes/web.php).
class StaffController extends Controller
{
    /**
     * Staff Dashboard: Scoped strictly to the staff member's assigned food stall.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Look up assigned food stall for this staff member (via users.stall_id)
        // CHANGED: find() returns null for an unassigned (null) stall_id, so the ternary is gone.
        $stall = DB::table('stalls')->find($user->stall_id);

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

        // 2. Metrics for the assigned stall ONLY (consolidated into 1 single aggregate query)
        $metrics = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->selectRaw('
                COUNT(*) as total_evaluations,
                COUNT(DISTINCT student_id) as unique_students,
                AVG(cleanliness) as cleanliness,
                AVG(service) as service,
                AVG(taste) as taste,
                AVG(price) as price,
                COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall
            ')
            ->first();

        $totalEvaluations = $metrics->total_evaluations ?? 0;
        $uniqueStudents = $metrics->unique_students ?? 0;
        $averages = $metrics;

        // 3. Compute this stall's current campus rank without exposing details of other stalls
        $rankedStalls = DB::table('stalls')
            ->leftJoin('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
            ->select(
                'stalls.id',
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price'),
                DB::raw('COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall_score')
            )
            ->groupBy('stalls.id')
            ->orderByDesc('overall_score')
            ->get();
        $weights = (new Ahp)->weights();
        $rankedStalls = (new Saw)->rank($rankedStalls, $weights);
        $rankedStalls = (new Ahp)->attachScores($rankedStalls, $weights);

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

        // CHANGED: trend query moved to Controller::activityTrend(), shared with the admin dashboard.
        [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'activityPeriodLabel' => $activityPeriodLabel,
            'activityTotalCount' => $activityTotalCount,
        ] = $this->activityTrend($request, $stall->id);

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
        $user = Auth::user();

        // Check if this staff member has an assigned stall
        // CHANGED: find() returns null for an unassigned (null) stall_id, so the if-block is gone.
        $myStall = DB::table('stalls')->find($user->stall_id);
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
        $weights = (new Ahp)->weights();
        $standings = (new Saw)->rank($standings, $weights);
        $standings = (new Ahp)->attachScores($standings, $weights);

        return view('staff.standings', [
            'standings' => $standings,
            'myStall' => $myStall,
            'user' => $user,
        ]);
    }

    // ADDED: the assigned stall's QR code, so staff can print it or share it with students.
    public function qr()
    {
        $user = Auth::user();
        $stall = DB::table('stalls')->find($user->stall_id);

        if (! $stall) {
            return redirect()->route('staff.dashboard')->with('error', 'Access restricted. You must be assigned to a food stall by an Administrator to view its QR code.');
        }

        return view('staff.qr', $this->stallQrData($stall));
    }

    /**
     * Staff Profile Page
     */
    public function profile()
    {
        $user = Auth::user();
        // CHANGED: find() returns null for an unassigned (null) stall_id, so the ternary is gone.
        $stall = DB::table('stalls')->find($user->stall_id);

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
        $user = Auth::user();

        $request->validate([
            // CHANGED: Laravel's current_password rule replaces the manual Hash::check() block.
            'current_password' => 'required|current_password',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ], [
            'current_password.current_password' => 'The current password you provided is incorrect.',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}