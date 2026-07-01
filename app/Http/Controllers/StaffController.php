<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role != 'staff') {
            return redirect('/login');
        }

        $user = Auth::user();

        // PROFILE (FIXED)
        $profile = DB::table('users')
            ->where('id', $user->id)
            ->first();

        // Food stalls
        $stalls = DB::table('stalls')
            ->orderBy('name')
            ->get();

        // Criteria
        $criteria = DB::table('criteria')->get();

        if ($criteria->isEmpty()) {
            $criteria = collect([
                (object)['name'=>'cleanliness','weight'=>0.25,'is_benefit'=>1],
                (object)['name'=>'service','weight'=>0.25,'is_benefit'=>1],
                (object)['name'=>'taste','weight'=>0.25,'is_benefit'=>1],
                (object)['name'=>'price','weight'=>0.25,'is_benefit'=>1],
            ]);
        }

        // Average ratings
        $ratings = DB::table('stall_evaluations')
            ->select(
                'stall_id',
                DB::raw('AVG(cleanliness) as cleanliness'),
                DB::raw('AVG(service) as service'),
                DB::raw('AVG(taste) as taste'),
                DB::raw('AVG(price) as price')
            )
            ->groupBy('stall_id')
            ->get();

        // Max values
        $max = [
            'cleanliness'=>0,
            'service'=>0,
            'taste'=>0,
            'price'=>0
        ];

        foreach ($ratings as $row) {
            foreach ($max as $key => $value) {
                $max[$key] = max($value, (float)$row->$key);
            }
        }

        $totalWeight = $criteria->sum('weight');
        if ($totalWeight == 0) $totalWeight = 1;

        /*
        | SAW
        */
        $results = [];

        foreach ($ratings as $row) {
            $score = 0;

            foreach ($criteria as $c) {
                $field = strtolower($c->name);

                if ($max[$field] == 0) continue;

                $norm = $row->$field / $max[$field];

                if (!$c->is_benefit) {
                    $norm = 1 - $norm;
                }

                $score += $norm * ($c->weight / $totalWeight);
            }

            $results[] = [
                'stall_id' => $row->stall_id,
                'score' => $score
            ];
        }

        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        /*
        | AHP
        */
        $ahpResults = [];

        foreach ($ratings as $row) {
            $product = 1;

            foreach ($criteria as $c) {
                $field = strtolower($c->name);

                $norm = $max[$field]
                    ? $row->$field / $max[$field]
                    : 0;

                if (!$c->is_benefit) {
                    $norm = 1 - $norm;
                }

                $norm = max($norm, 0.000001);

                $product *= pow($norm, $c->weight / $totalWeight);
            }

            $ahpResults[] = [
                'stall_id' => $row->stall_id,
                'score' => $product
            ];
        }

        usort($ahpResults, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        /*
        | IMPROVEMENTS (placeholder so view won’t break)
        */
        $improvements = $results;

        $evaluations = DB::table('stall_evaluations')->get();

        return view('staff.staff_dashboard', [
            'profile' => $profile,
            'stalls' => $stalls,
            'ratings' => $ratings,
            'results' => $results,
            'ahpResults' => $ahpResults,
            'improvements' => $improvements,
            'evaluations' => $evaluations,
        ]);
    }
}