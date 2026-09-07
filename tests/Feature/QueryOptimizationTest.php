<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    // Create several stalls
    for ($i = 1; $i <= 5; $i++) {
        $stallId = DB::table('stalls')->insertGetId([
            'name' => "Stall #{$i}",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staff = User::create([
            'name' => "Staff Member {$i}",
            'email' => "staff{$i}@example.com",
            'password' => bcrypt('StaffPassword123!'),
            'role' => 'staff',
            'stall_id' => $stallId,
            'email_verified_at' => now(),
        ]);

        if ($i === 1) {
            $this->staff = $staff;
            $this->stallId = $stallId;
        }

        // Seed evaluations
        DB::table('stall_evaluations')->insert([
            'student_id' => $this->admin->id,
            'stall_id' => $stallId,
            'cleanliness' => 4,
            'service' => 5,
            'taste' => 4,
            'price' => 4,
            'comment' => "Great food at stall {$i}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});

test('admin dashboard executes optimal query count with zero duplicate or dead queries', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $queries = DB::getQueryLog();

    // Verify pieChartData is computed in-memory without duplicate query
    $pieChartQueries = array_filter($queries, function ($q) {
        return str_contains($q['query'], 'COUNT(*) as count') && str_contains($q['query'], 'group by "stalls"."name"');
    });
    expect($pieChartQueries)->toBeEmpty();

    // Verify dead stalls query is removed
    $deadStallsQueries = array_filter($queries, function ($q) {
        return $q['query'] === 'select * from "stalls" order by "name" asc';
    });
    expect($deadStallsQueries)->toBeEmpty();
});

test('admin stalls page executes constant query count regardless of stall count (no N+1)', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $response = $this->actingAs($this->admin)->get(route('admin.stalls'));

    $response->assertStatus(200);
    $queriesBefore = count(DB::getQueryLog());

    // Add 10 more stalls with staff and evaluations
    for ($i = 6; $i <= 15; $i++) {
        $id = DB::table('stalls')->insertGetId([
            'name' => "Extra Stall #{$i}",
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('stall_evaluations')->insert([
            'student_id' => $this->admin->id,
            'stall_id' => $id,
            'cleanliness' => 4,
            'service' => 4,
            'taste' => 4,
            'price' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();

    $response2 = $this->actingAs($this->admin)->get(route('admin.stalls'));
    $response2->assertStatus(200);
    $queriesAfter = count(DB::getQueryLog());

    // Query count MUST be identical (constant O(1) query count, no N+1 query loop)
    expect($queriesAfter)->toBe($queriesBefore);
});

test('admin students page derives distinct courses without redundant DB query', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $response = $this->actingAs($this->admin)->get(route('admin.students'));

    $response->assertStatus(200);
    $queries = DB::getQueryLog();

    // Verify distinct course query was eliminated
    $distinctCourseQueries = array_filter($queries, function ($q) {
        return str_contains($q['query'], 'distinct "course"');
    });
    expect($distinctCourseQueries)->toBeEmpty();
});

test('staff dashboard consolidates stall evaluation metrics into single query', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $response = $this->actingAs($this->staff)->get(route('staff.dashboard'));

    $response->assertStatus(200);
    $queries = DB::getQueryLog();

    // Verify there are no multiple separate queries on stall_evaluations for count and averages
    $metricQueries = array_filter($queries, function ($q) {
        return str_contains($q['query'], 'from "stall_evaluations"')
            && str_contains($q['query'], 'where "stall_id" = ?')
            && str_contains($q['query'], 'COUNT(*) as total_evaluations');
    });
    expect(count($metricQueries))->toBe(1);
});
