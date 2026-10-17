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

        // CHANGED: the stall is saved on the staff table.
        $staff ??= User::createWithProfile([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => bcrypt('StaffPassword123!'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ], ['stall_id' => $stallId]);
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

test('the This Month card counts only this month\'s students', function () {
    $staff = staffWithRatedStalls([4]);
    $other = User::create([
        'name' => 'Other Student',
        'email' => 'other@example.com',
        'password' => bcrypt('StudentPassword123!'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
    // Last day of last month in Philippine time.
    $lastMonth = now('Asia/Manila')->startOfMonth()->subDay()->utc();
    DB::table('stall_evaluations')->insert([
        'student_id' => $other->id,
        'stall_id' => $staff->staff->stall_id,
        'cleanliness' => 3, 'service' => 3, 'taste' => 3, 'price' => 3,
        'created_at' => $lastMonth,
        'updated_at' => $lastMonth,
    ]);

    $this->actingAs($staff)->get(route('staff.dashboard'))
        ->assertOk()
        ->assertViewHas('monthStudents', 1)
        ->assertSeeText('Student rated you in ' . now('Asia/Manila')->format('F'));
});

test('the activity chart counts days in Philippine time', function () {
    $staff = staffWithRatedStalls([4]);
    // 7 AM on October 1 in Manila is still September 30 in UTC.
    DB::table('stall_evaluations')->insert([
        'student_id' => User::where('role', 'student')->value('id'),
        'stall_id' => $staff->staff->stall_id,
        'cleanliness' => 3, 'service' => 3, 'taste' => 3, 'price' => 3,
        'created_at' => '2025-09-30 23:00:00',
        'updated_at' => '2025-09-30 23:00:00',
    ]);
    $october = $this->actingAs($staff)->get(route('staff.dashboard', ['activity_month' => 10, 'activity_year' => 2025]))->assertOk();
    expect(array_combine($october->viewData('trendDates'), $october->viewData('trendCounts'))['Oct 01'])->toBe(1);

    $september = $this->actingAs($staff)->get(route('staff.dashboard', ['activity_month' => 9, 'activity_year' => 2025]))->assertOk();
    expect(array_combine($september->viewData('trendDates'), $september->viewData('trendCounts'))['Sep 30'])->toBe(0);
});

test('the evaluations list reuses the stall total unless searching', function () {
    $staff = staffWithRatedStalls([4]);

    DB::enableQueryLog();
    $all = $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
    $countQueries = collect(DB::getQueryLog())->filter(fn ($q) => str_contains($q['query'], 'count(*) as aggregate'));
    expect($all->viewData('evaluations')->total())->toBe(1)
        ->and($countQueries)->toBeEmpty();

    $none = $this->actingAs($staff)->get(route('staff.dashboard', ['q' => 'no such comment']))->assertOk();
    expect($none->viewData('evaluations')->total())->toBe(0);
});
