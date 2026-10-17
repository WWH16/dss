<?php

use App\Models\User;
use App\Services\Ahp;
use App\Services\NeedsImprovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function improvementMeans(float $taste, float $service, float $price, float $cleanliness): object
{
    return (object) compact('taste', 'service', 'price', 'cleanliness');
}

test('flags only the criteria below 3.00', function () {
    $flags = NeedsImprovement::flags(improvementMeans(4, 4, 2.4, 3), (new Ahp)->weights());

    expect($flags)->toBe(['price' => 2.4]);
});

test('orders flags lowest first, ties going to the higher AHP weight', function () {
    $flags = NeedsImprovement::flags(improvementMeans(2.5, 2.5, 1.8, 2.5), (new Ahp)->weights());

    expect(array_keys($flags))->toBe(['price', 'food_quality', 'cleanliness', 'service_quality']);
});

test('compares the score as shown, rounded to 2 decimals', function () {
    $weights = (new Ahp)->weights();

    expect(NeedsImprovement::flags(improvementMeans(2.996, 4, 4, 4), $weights))->toBe([])
        ->and(NeedsImprovement::flags(improvementMeans(2.994, 4, 4, 4), $weights))->toBe(['food_quality' => 2.99]);
});

// Creates a stall, its staff user, and one evaluation per entry of $ratings from a different student each.
// Each entry is [taste, service, price, cleanliness] and an optional created_at.
function staffWithEvaluations(array $ratings): User
{
    $stallId = DB::table('stalls')->insertGetId([
        'name' => 'Improvement Stall',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach ($ratings as $i => $rating) {
        $student = User::create([
            'name' => "Student {$i}",
            'email' => "student{$i}@example.com",
            'password' => bcrypt('StudentPassword123!'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $at = $rating[4] ?? now();

        DB::table('stall_evaluations')->insert([
            'student_id' => $student->id,
            'stall_id' => $stallId,
            'taste' => $rating[0],
            'service' => $rating[1],
            'price' => $rating[2],
            'cleanliness' => $rating[3],
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    return User::createWithProfile([
        'name' => 'Staff',
        'email' => 'staff@example.com',
        'password' => bcrypt('StaffPassword123!'),
        'role' => 'staff',
        'email_verified_at' => now(),
    ], ['stall_id' => $stallId]);
}

test('the staff dashboard shows the recommendation for a low criterion only', function () {
    $staff = staffWithEvaluations(array_fill(0, 5, [4, 4, 2, 4]));

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeText('Needs Improvement')
        ->assertSeeText(NeedsImprovement::ADVICE['price'])
        ->assertDontSeeText(NeedsImprovement::ADVICE['food_quality'])
        ->assertDontSeeText(NeedsImprovement::ADVICE['service_quality'])
        ->assertDontSeeText(NeedsImprovement::ADVICE['cleanliness'])
        ->assertDontSeeText('Provisional');
});

test('the staff dashboard lists several low criteria lowest first, under the card names', function () {
    $staff = staffWithEvaluations(array_fill(0, 5, [2.8, 2.2, 4, 3.5]));

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeTextInOrder([
            'Service', '2.20★', NeedsImprovement::ADVICE['service_quality'],
            'Taste', '2.80★', NeedsImprovement::ADVICE['food_quality'],
        ])
        ->assertDontSeeText('Service Quality')
        ->assertDontSeeText('Food Quality')
        ->assertDontSeeText('Needs improvement in all areas')
        ->assertDontSeeText(NeedsImprovement::ADVICE['price'])
        ->assertDontSeeText(NeedsImprovement::ADVICE['cleanliness']);
});

test('the staff dashboard says when every criterion meets the standard', function () {
    $staff = staffWithEvaluations(array_fill(0, 5, [4, 3, 4, 5]));

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeText('All criteria meet the 3.00 standard')
        ->assertSeeText('Meets Standard')
        ->assertDontSeeText('Needs Improvement')
        ->assertDontSeeText(NeedsImprovement::ADVICE['service_quality']);
});

test('the staff dashboard marks flags from fewer than 5 evaluations as provisional', function () {
    $staff = staffWithEvaluations(array_fill(0, 2, [2, 2, 2, 2]));

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeText('Provisional')
        ->assertSeeText('Needs improvement in all areas')
        ->assertSeeText(NeedsImprovement::ADVICE['cleanliness']);
});

test('this month\'s evaluations decide the flags when there are any', function () {
    $lastMonth = now('Asia/Manila')->startOfMonth()->subDay()->utc();
    $staff = staffWithEvaluations([
        [1, 1, 1, 1, $lastMonth],
        [1, 1, 1, 1, $lastMonth],
        [1, 1, 1, 1, $lastMonth],
        [4, 4, 4, 4],
    ]);

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeText('All criteria meet the 3.00 standard')
        ->assertDontSeeText(NeedsImprovement::ADVICE['food_quality']);
});

test('all-time evaluations decide the flags when this month has none', function () {
    $lastMonth = now('Asia/Manila')->startOfMonth()->subDay()->utc();
    $staff = staffWithEvaluations([[2, 4, 4, 4, $lastMonth]]);

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSeeText('from 1 evaluation (all-time; none yet this month)')
        ->assertSeeText(NeedsImprovement::ADVICE['food_quality']);
});
