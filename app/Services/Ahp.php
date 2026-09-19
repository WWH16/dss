<?php

namespace App\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;

// Client's AhpService, reworked for this codebase.
// Lines marked CHANGED or ADDED differ from theirs. Unmarked lines are theirs.
//
// CHANGED: class renamed from AhpService to Ahp, to match the file name.
class Ahp
{
    // ADDED: pairwise comparisons, in Saw::criteria() order. No Saaty judgments were
    // supplied, so each cell is the ratio of how many questionnaire statements cover
    // each criterion (3 food, 2 service, 2 price, 3 cleanliness), which is consistent
    // by construction and gives weights of 0.30, 0.20, 0.20, 0.30. Replace with the
    // client's own comparisons when they provide them.
    public const PAIRWISE = [
        [1,     3 / 2, 3 / 2, 1    ],
        [2 / 3, 1,     1,     2 / 3],
        [2 / 3, 1,     1,     2 / 3],
        [1,     3 / 2, 3 / 2, 1    ],
    ];

    // ADDED: hoisted out of calculateWeights() so guardMatrix() can check the size.
    private const RI = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49
    ];

    // CHANGED: added the optional $criteria parameter. Saw reads weights by name, but
    // this returned them by position, so feeding one into the other threw
    // "Undefined array key".
    public function calculateWeights(array $matrix, array $criteria = []): array
    {
        $n = count($matrix);

        // ADDED: rejects the inputs that divided by zero below.
        $this->guardMatrix($matrix, $n);

        if ($criteria !== [] && count($criteria) !== $n) {
            throw new InvalidArgumentException(
                'AHP expects one criterion name per matrix row, got '.count($criteria)." for {$n} rows."
            );
        }

        // Step 1: Sum each column
        $columnSums = array_fill(0, $n, 0);

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $columnSums[$j] += $matrix[$i][$j];
            }
        }

        // Step 2: Normalize the matrix
        $normalized = [];

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalized[$i][$j] =
                    $matrix[$i][$j] / $columnSums[$j];
            }
        }

        // Step 3: Calculate priority/weight
        $weights = [];

        for ($i = 0; $i < $n; $i++) {
            $weights[$i] =
                array_sum($normalized[$i]) / $n;
        }

        // Step 4: Calculate lambda max
        $weightedSum = [];

        for ($i = 0; $i < $n; $i++) {
            $sum = 0;

            for ($j = 0; $j < $n; $j++) {
                $sum += $matrix[$i][$j] * $weights[$j];
            }

            $weightedSum[$i] = $sum;
        }

        $lambdaValues = [];

        for ($i = 0; $i < $n; $i++) {
            $lambdaValues[$i] =
                $weightedSum[$i] / $weights[$i];
        }

        $lambdaMax = array_sum($lambdaValues) / $n;

        // Step 5: Consistency Index
        // CHANGED: guarded $n === 1, which divided by ($n - 1) = 0.
        $CI = $n > 1
            ? ($lambdaMax - $n) / ($n - 1)
            : 0.0;

        // Random Index
        // CHANGED: was $RI[$n] ?? 1.49, which returned a wrong ratio for 11+ criteria
        // instead of failing. guardMatrix() rejects those sizes first.
        $randomIndex = self::RI[$n];

        // Step 6: Consistency Ratio
        $CR = $randomIndex == 0
            ? 0.0 // CHANGED: was int 0, so the return type varied with $n.
            : $CI / $randomIndex;

        return [
            // CHANGED: keyed by criterion name when names were supplied.
            'weights' => $criteria === []
                ? $weights
                : array_combine($criteria, $weights),
            'lambda_max' => $lambdaMax,
            'CI' => $CI,
            'CR' => $CR,
            'consistent' => $CR <= 0.10
        ];
    }

    // ADDED: the configured matrix, solved and checked. The client's code returned CR
    // but nothing read it, so an inconsistent matrix produced weights silently.
    public function consistency(): array
    {
        $result = $this->calculateWeights(self::PAIRWISE, Saw::criteria());

        if (! $result['consistent']) {
            throw new InvalidArgumentException(sprintf(
                'AHP pairwise matrix is inconsistent: CR is %.4f, above the 0.10 threshold. Revise Ahp::PAIRWISE.',
                $result['CR']
            ));
        }

        return $result;
    }

    // ADDED: the weights the controllers hand to Saw::rank().
    public function weights(): array
    {
        return $this->consistency()['weights'];
    }

    // ADDED: attaches ahp_score, using AHP's distributive synthesis. Each rating is
    // divided by the SUM of every rated stall's rating on that criterion, so the
    // priorities on a criterion add up to 1, and so do the final scores. That sum
    // normalization is what makes this column differ from saw_score, which divides by
    // the scale maximum. The old StallRanking class stored a weighted product here and
    // called it AHP; that is the Weighted Product Model, so the numbers have changed.
    public function attachScores(Collection $rows, array $weights): Collection
    {
        $rated = [];

        foreach ($rows as $row) {
            if ($this->hasNoRatings($row)) {
                $row->ahp_score = 0.0;
                continue;
            }

            $rated[] = $row;
        }

        $columnSums = [];

        foreach (Saw::COLUMNS as $criterion => $column) {
            $columnSums[$criterion] = 0.0;

            foreach ($rated as $row) {
                $columnSums[$criterion] += (float) $row->$column;
            }
        }

        foreach ($rated as $row) {
            $score = 0.0;

            foreach (Saw::COLUMNS as $criterion => $column) {
                $priority = $columnSums[$criterion] > 0
                    ? (float) $row->$column / $columnSums[$criterion]
                    : 0.0;

                $score += $priority * $weights[$criterion];
            }

            $row->ahp_score = $score;
        }

        return $rows;
    }

    private function hasNoRatings(object $row): bool
    {
        foreach (Saw::COLUMNS as $column) {
            if ($row->$column !== null) {
                return false;
            }
        }

        return true;
    }

    // ADDED: a zero or negative entry made Step 2 divide by a zero column sum, or
    // Step 4 divide by a zero weight.
    private function guardMatrix(array $matrix, int $n): void
    {
        if ($n === 0) {
            throw new InvalidArgumentException('AHP needs at least one criterion.');
        }

        if (! isset(self::RI[$n])) {
            throw new InvalidArgumentException(
                "AHP has no random index for {$n} criteria; the published table stops at 10."
            );
        }

        foreach ($matrix as $i => $row) {
            if (count($row) !== $n) {
                throw new InvalidArgumentException("AHP matrix row {$i} has ".count($row)." entries, expected {$n}.");
            }

            foreach ($row as $j => $value) {
                if (! is_numeric($value) || $value <= 0) {
                    throw new InvalidArgumentException("AHP matrix entry [{$i}][{$j}] must be greater than zero.");
                }
            }
        }
    }
}
