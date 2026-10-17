<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Ahp;
use App\Services\Saw;
use App\Services\NeedsImprovement; // ADDED
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

        // 1. Look up assigned food stall for this staff member
        // CHANGED: via staff.stall_id (was users.stall_id).
        // CHANGED: find() returns null for an unassigned (null) stall_id, so the ternary is gone.
        $stall = DB::table('stalls')->find($user->staff?->stall_id); // CHANGED: the stall assignment is on the staff table.

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
        // CHANGED: also counts the students who rated this stall this calendar month (Philippine time). A student rates a stall once a month, so this is also the month's evaluation count. created_at is stored in UTC, so the month start is converted.
        // CHANGED: also carries the star breakdown (was step 5's own query) and the first evaluation's time for the
        // chart's year list, saving two round trips to the database.
        $metrics = DB::table('stall_evaluations')
            ->where('stall_id', $stall->id)
            ->selectRaw('
                COUNT(*) as total_evaluations,
                MIN(created_at) as first_at,
                COUNT(DISTINCT student_id) as unique_students,
                COUNT(DISTINCT CASE WHEN created_at >= ? THEN student_id END) as month_students,
                AVG(CASE WHEN created_at >= ? THEN cleanliness END) as month_cleanliness,
                AVG(CASE WHEN created_at >= ? THEN service END) as month_service,
                AVG(CASE WHEN created_at >= ? THEN taste END) as month_taste,
                AVG(CASE WHEN created_at >= ? THEN price END) as month_price,
                AVG(cleanliness) as cleanliness,
                AVG(service) as service,
                AVG(taste) as taste,
                AVG(price) as price,
                COALESCE((AVG(cleanliness) + AVG(service) + AVG(taste) + AVG(price)) / 4, 0) as overall,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 5 THEN 1 ELSE 0 END), 0) as stars_5,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 4 THEN 1 ELSE 0 END), 0) as stars_4,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 3 THEN 1 ELSE 0 END), 0) as stars_3,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 2 THEN 1 ELSE 0 END), 0) as stars_2,
                COALESCE(SUM(CASE WHEN ROUND((cleanliness + service + taste + price)/4) = 1 THEN 1 ELSE 0 END), 0) as stars_1
            ', array_fill(0, 5, now('Asia/Manila')->startOfMonth()->utc())) // CHANGED: 5 bindings, for the month means added above.
            ->first();

        $totalEvaluations = $metrics->total_evaluations ?? 0;
        $uniqueStudents = $metrics->unique_students ?? 0;
        $averages = $metrics;

        // 3. Compute this stall's current campus rank without exposing details of other stalls
        // CHANGED: join instead of leftJoin, so stalls with no evaluations take no rank and are not counted in totalStalls.
        $rankedStalls = DB::table('stalls')
            ->join('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
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

        // ADDED: the client's Needs Improvement rule, on this month's means, or all-time ones when the month has no
        // evaluations yet. A student rates a stall once a month, so month_students is the month's evaluation count.
        $inMonth = $metrics->month_students > 0;
        $basis = (object) collect(Saw::COLUMNS)
            ->mapWithKeys(fn ($column) => [$column => $metrics->{($inMonth ? 'month_' : '') . $column}])
            ->all();
        $basisCount = (int) ($inMonth ? $metrics->month_students : $totalEvaluations);
        $improvement = [
            'flags' => NeedsImprovement::flags($basis, $weights),
            'inMonth' => $inMonth,
            'count' => $basisCount,
            'provisional' => $basisCount < NeedsImprovement::MIN_EVALUATIONS,
        ];

        $stallRank = null;
        $totalStalls = $rankedStalls->count();
        foreach ($rankedStalls as $idx => $r) {
            if ($r->id == $stall->id) {
                $stallRank = $idx + 1;
                break;
            }
        }

        // 4. Compute campus-wide benchmark criteria averages for comparison
        // CHANGED: the benchmark is the mean of the OTHER rated stalls, taken from $rankedStalls instead of a query
        // over every evaluation, which included this stall and compared it with itself. With fewer than 2 other
        // rated stalls it is null and the view says there is not enough campus data yet.
        $otherStalls = $rankedStalls->where('id', '!=', $stall->id);
        $campusCriteria = $otherStalls->count() >= 2 ? (object) [
            'cleanliness' => $otherStalls->avg('cleanliness'),
            'service' => $otherStalls->avg('service'),
            'taste' => $otherStalls->avg('taste'),
            'price' => $otherStalls->avg('price'),
            'overall' => $otherStalls->avg('overall_score'),
            'stalls' => $otherStalls->count(),
        ] : null;

        // 5. Rating breakdown distribution (1 to 5 stars) for this stall
        // CHANGED: comes from $metrics; passed to the view as ratingDistribution.

        // CHANGED: trend query moved to Controller::activityTrend(), shared with the admin dashboard.
        [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'activityPeriodLabel' => $activityPeriodLabel,
            'activityTotalCount' => $activityTotalCount,
        ] = $this->activityTrend($request, $stall->id, $metrics->first_at);

        // CHANGED: the paginated evaluations query moved to stallEvaluations(), shared with standings().
        $evaluations = $this->stallEvaluations($request, $stall->id, (int) $totalEvaluations);

        return view('staff.staff_dashboard', [
            'hasStall' => true,
            'stall' => $stall,
            'user' => $user,
            'totalEvaluations' => $totalEvaluations,
            'uniqueStudents' => $uniqueStudents,
            // ADDED: for the This Month card.
            'monthStudents' => $metrics->month_students,
            'averages' => $averages,
            'campusCriteria' => $campusCriteria,
            // ADDED: for the Needs Improvement panel.
            'improvement' => $improvement,
            'ratingDistribution' => $metrics,
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

    // 7. Paginated evaluations list for this stall (STRICT PRIVACY: zero student names or IDs)
    // CHANGED: takes the stall's evaluation count when the caller already has it, so an unsearched list skips its count query.
    private function stallEvaluations(Request $request, $stallId, ?int $total = null)
    {
        $evaluationsQuery = DB::table('stall_evaluations')
            ->where('stall_id', $stallId)
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

        return $evaluationsQuery->paginate(10, ['*'], 'page', null, $request->filled('q') ? null : $total)->withQueryString();
    }

    /**
     * Other Stall Standings / Rankings:
     * Staff can view aggregate rankings across campus, but CANNOT view other stalls' individual feedback.
     */
    public function standings(Request $request)
    {
        $user = Auth::user();

        // Check if this staff member has an assigned stall
        // CHANGED: find() returns null for an unassigned (null) stall_id, so the if-block is gone.
        $myStall = DB::table('stalls')->find($user->staff?->stall_id); // CHANGED: the stall assignment is on the staff table.
        // Strict Security Guard: Unassigned staff MUST NOT view campus rankings or performance
        if (!$myStall) {
            return redirect()->route('staff.dashboard')->with('error', 'Access restricted. You must be assigned to a food stall by an Administrator to view campus standings.');
        }

        // Fetch aggregate standing data only (no student evaluations or comments)
        // CHANGED: join instead of leftJoin, so stalls with no evaluations take no rank or podium spot.
        $standings = DB::table('stalls')
            ->join('stall_evaluations', 'stalls.id', '=', 'stall_evaluations.stall_id')
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
            // ADDED: this stall's own evaluations, which replace the all-stalls leaderboard table.
            // CHANGED: the stall's eval_count from $standings is the list's total, which saves a count query (0 when it has no evaluations, so it is not ranked).
            'evaluations' => $this->stallEvaluations($request, $myStall->id, (int) ($standings->firstWhere('id', $myStall->id)?->eval_count ?? 0)),
        ]);
    }

    // ADDED: the assigned stall's QR code, so staff can print it or share it with students.
    public function qr()
    {
        $user = Auth::user();
        $stall = DB::table('stalls')->find($user->staff?->stall_id); // CHANGED: the stall assignment is on the staff table.

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
        $stall = DB::table('stalls')->find($user->staff?->stall_id); // CHANGED: the stall assignment is on the staff table.

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

        // CHANGED: trim() dropped; Laravel's global TrimStrings middleware already trims request input.
        DB::table('users')->where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
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
            // ADDED: a new token voids every "Remember me" cookie issued under the old password.
            'remember_token' => str()->random(60),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}