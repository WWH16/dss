<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Rates one stall per score, all four criteria equal, and returns the staff user of the first stall.
function staffWithRatedStalls(array $scores): User
{
    $student = User::create([
        'name' => 'Student',
        'email' => 'student@example.com',
        'password' => bcrypt('StudentPassword123!'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    foreach ($scores as $i => $score) {
        $stallId = DB::table('stalls')->insertGetId([
            'name' => "Stall {$i}",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('stall_evaluations')->insert([
            'student_id' => $student->id,
            'stall_id' => $stallId,
            'cleanliness' => $score,
            'service' => $score,
            'taste' => $score,
            'price' => $score,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staff ??= User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => bcrypt('StaffPassword123!'),
            'role' => 'staff',
            'stall_id' => $stallId,
            'email_verified_at' => now(),
        ]);
    }

    return $staff;
}

test('the campus benchmark leaves out the viewing stall', function () {
    $this->actingAs(staffWithRatedStalls([5, 2, 3, 4]))->get(route('staff.dashboard'))
        ->assertOk()
        ->assertViewHas('campusCriteria', fn ($campus) => $campus->stalls === 3 && abs($campus->overall - 3.0) < 0.001)
        ->assertSee('Avg of other stalls')
        ->assertSee('#1 on Campus');
});

test('the campus benchmark needs at least 2 other rated stalls', function () {
    $this->actingAs(staffWithRatedStalls([5, 2]))->get(route('staff.dashboard'))
        ->assertOk()
        ->assertViewHas('campusCriteria', null)
        ->assertSee('Not enough campus data yet')
        ->assertDontSee('vs others')
        ->assertDontSee('on Campus');
});
