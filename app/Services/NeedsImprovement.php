<?php

namespace App\Services;

// ADDED: the client's "Needs Improvement" recommendations. Reads Saw::COLUMNS and the AHP weights; changes neither.
class NeedsImprovement
{
    public const THRESHOLD = 3.0;

    // Below this many evaluations the flags are shown as provisional.
    public const MIN_EVALUATIONS = 5;

    // The names the staff dashboard's criterion cards use.
    public const LABELS = [
        'food_quality' => 'Taste',
        'service_quality' => 'Service',
        'price' => 'Price',
        'cleanliness' => 'Cleanliness',
    ];

    public const ADVICE = [
        'food_quality' => 'The stall needs to ensure food freshness, proper preparation, and safe food handling.',
        'service_quality' => 'The stall should improve service efficiency, staff responsiveness, and customer assistance.',
        'price' => 'The stall should offer reasonable and affordable prices that match the food quality and serving size.',
        'cleanliness' => 'The stall should maintain clean food preparation areas, utensils, and proper personal hygiene.',
    ];

    // Criterion => mean, for each criterion whose mean rounded to 2 decimals (as shown on screen) is below 3.00.
    // Lowest first; on a tie the criterion with the higher AHP weight comes first.
    public static function flags(object $means, array $weights): array
    {
        $flags = [];

        foreach (Saw::COLUMNS as $criterion => $column) {
            $score = round((float) $means->$column, 2);

            if ($score < self::THRESHOLD) {
                $flags[$criterion] = $score;
            }
        }

        uksort($flags, fn ($a, $b) => [$flags[$a], $weights[$b]] <=> [$flags[$b], $weights[$a]]);

        return $flags;
    }
}
