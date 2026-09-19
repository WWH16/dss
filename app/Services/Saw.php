<?php

namespace App\Services;

use Illuminate\Support\Collection;

// Client's SawService, reworked for this codebase.
// Lines marked CHANGED or ADDED differ from theirs. Unmarked lines are theirs.
//
// CHANGED: class renamed from SawService to Saw, to match the file name.
class Saw
{
    // ADDED: every statement is answered on a 1-5 scale.
    public const SCALE_MAX = 5.0;

    // ADDED: criterion => stall_evaluations column. The table predates these names.
    public const COLUMNS = [
        'food_quality' => 'taste',
        'service_quality' => 'service',
        'price' => 'price',
        'cleanliness' => 'cleanliness',
    ];

    public function calculate(array $alternatives, array $weights): array
    {
        // CHANGED: list moved to criteria() so rank() and Ahp share one copy.
        $criteria = self::criteria();

        $normalized = [];

        // Normalize
        foreach ($alternatives as $index => $alternative) {

            foreach ($criteria as $criterion) {

                // Benefit criteria
                // CHANGED: price moved here out of a cost branch. The column holds a
                // 1-5 rating, not an amount, so a higher value is the better result.
                if (
                    $criterion === 'food_quality' ||
                    $criterion === 'service_quality' ||
                    $criterion === 'cleanliness' ||
                    $criterion === 'price'
                ) {
                    // CHANGED: divides by SCALE_MAX, not by the highest observed rating,
                    // so adding or removing a stall cannot reorder the others. The
                    // client's $max and $min block became unused and was removed.
                    $normalized[$index][$criterion] =
                        $alternative[$criterion] /
                        self::SCALE_MAX;
                }
            }
        }

        // Calculate final SAW score
        $results = [];

        foreach ($alternatives as $index => $alternative) {

            $score = 0;

            foreach ($criteria as $criterion) {

                $score +=
                    $normalized[$index][$criterion]
                    * $weights[$criterion];
            }

            $results[$index] = [
                'id' => $alternative['id'],
                // CHANGED: the staff standings query selects no name column.
                'name' => $alternative['name'] ?? null,
                // CHANGED: was round($score, 4). Rounding before usort() made stalls
                // differing at the 5th decimal tie, and the views already format to 4.
                'score' => $score,
                'normalized' => $normalized[$index]
            ];
        }

        // Rank from highest to lowest
        // CHANGED: falls back to name then id, so tied stalls no longer depend on the
        // order the database happened to return them in.
        usort($results, function ($a, $b) {
            return [$b['score'], $a['name'], $a['id']]
               <=> [$a['score'], $b['name'], $b['id']];
        });

        foreach ($results as $rank => &$result) {
            $result['rank'] = $rank + 1;
        }

        return $results;
    }

    // ADDED: the list calculate() declared inline, shared with Ahp.
    public static function criteria(): array
    {
        return array_keys(self::COLUMNS);
    }

    // ADDED: adapter for the controllers, which hold database rows rather than the
    // arrays calculate() expects. Attaches saw_score and sorts best first. A stall with
    // no evaluations has null averages and scores 0.
    public function rank(Collection $rows, array $weights): Collection
    {
        $alternatives = [];

        foreach ($rows as $key => $row) {
            if ($this->hasNoEvaluations($row)) {
                $row->saw_score = 0.0;
                continue;
            }

            $alternative = ['id' => $key, 'name' => data_get($row, 'name')];

            foreach (self::COLUMNS as $criterion => $column) {
                $alternative[$criterion] = (float) $row->$column;
            }

            $alternatives[] = $alternative;
        }

        foreach ($this->calculate($alternatives, $weights) as $result) {
            $rows[$result['id']]->saw_score = $result['score'];
        }

        // Same rule as calculate(). PHP's sort is stable, so anything still tied keeps
        // the order the query returned.
        return $rows->sort(function ($a, $b) {
            return [$b->saw_score, data_get($a, 'name')]
               <=> [$a->saw_score, data_get($b, 'name')];
        })->values();
    }

    private function hasNoEvaluations(object $row): bool
    {
        foreach (self::COLUMNS as $column) {
            if ($row->$column !== null) {
                return false;
            }
        }

        return true;
    }
}
