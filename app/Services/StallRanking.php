<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * DSS ranking for food stalls, restored from the original StaffController (commit 14afc19).
 *
 * Each row must carry per-criterion averages (cleanliness, service, taste, price).
 * Rows with no evaluations (null averages) score 0.
 */
class StallRanking
{
    /**
     * Criterion weights. The original code read these from the `criteria` table,
     * which was never seeded, so it always fell back to these equal weights.
     */
    public const CRITERIA = [
        'cleanliness' => ['weight' => 0.25, 'is_benefit' => true],
        'service'     => ['weight' => 0.25, 'is_benefit' => true],
        'taste'       => ['weight' => 0.25, 'is_benefit' => true],
        'price'       => ['weight' => 0.25, 'is_benefit' => true],
    ];

    /**
     * Attach `saw_score` and `ahp_score` (both 0..1) to each row and sort by SAW, best first.
     */
    public static function rank(Collection $rows): Collection
    {
        $totalWeight = array_sum(array_column(self::CRITERIA, 'weight')) ?: 1;

        // Max value per criterion across the stalls being ranked
        $max = array_fill_keys(array_keys(self::CRITERIA), 0.0);
        foreach ($rows as $row) {
            foreach ($max as $field => $value) {
                $max[$field] = max($value, (float) $row->$field);
            }
        }

        foreach ($rows as $row) {
            if ($row->cleanliness === null) {
                $row->saw_score = 0.0;
                $row->ahp_score = 0.0;
                continue;
            }

            $saw = 0.0;
            $ahp = 1.0;

            foreach (self::CRITERIA as $field => $c) {
                $norm = $max[$field] ? (float) $row->$field / $max[$field] : 0.0;

                if (!$c['is_benefit']) {
                    $norm = 1 - $norm;
                }

                $weight = $c['weight'] / $totalWeight;

                // SAW: weighted sum of normalized ratings
                $saw += $norm * $weight;

                // "AHP" as in the original code: weighted product of normalized ratings
                $ahp *= pow(max($norm, 0.000001), $weight);
            }

            $row->saw_score = $saw;
            $row->ahp_score = $ahp;
        }

        return $rows->sortByDesc('saw_score')->values();
    }
}
