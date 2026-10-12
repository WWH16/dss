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

    $stallId = DB::table('stalls')->insertGetId([
        'name' => 'Snack Hub',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Evaluation time per student: this month, none, none, last month (Philippine calendar).
    $lastMonth = now('Asia/Manila')->startOfMonth()->subDay()->utc();
    $students = [
        'Ana This Month' => now(),
        'Ben Never' => null,
        'Cora Never' => null,
        'Dan Last Month' => $lastMonth,
    ];

    foreach (array_keys($students) as $i => $name) {
        $student = User::create([
            'name' => $name,
            'email' => "student{$i}@example.com",
            'password' => bcrypt('StudentPassword123!'),
            'role' => 'student',
            'course' => 'BSIT',
            'year_level' => '4th year',
            'student_number' => "21-000{$i}",
            'email_verified_at' => now(),
        ]);

        if ($at = $students[$name]) {
            DB::table('stall_evaluations')->insert([
                'student_id' => $student->id,
                'stall_id' => $stallId,
                'cleanliness' => 4,
                'service' => 4,
                'taste' => 4,
                'price' => 4,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }
});

test('students page summarises this month\'s participation', function () {
    $this->actingAs($this->admin)->get(route('admin.students'))
        ->assertOk()
        ->assertSeeText('1 of 4')
        ->assertSeeText('students has evaluated in ' . now('Asia/Manila')->format('F'))
        ->assertDontSeeText('Last submitted');
});

test('students page filters by this month\'s participation', function () {
    $this->actingAs($this->admin)->get(route('admin.students', ['status' => 'pending']))
        ->assertOk()
        ->assertSeeText('Ben Never')
        ->assertSeeText('Cora Never')
        ->assertSeeText('Dan Last Month')
        ->assertDontSeeText('Ana This Month')
        ->assertSeeText('1 of 4');

    $this->actingAs($this->admin)->get(route('admin.students', ['status' => 'evaluated', 'sort' => 'evaluations_asc']))
        ->assertOk()
        ->assertSeeText('Ana This Month')
        ->assertDontSeeText('Dan Last Month')
        ->assertDontSeeText('Ben Never');
});
