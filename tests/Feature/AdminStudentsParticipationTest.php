<?php

use App\Models\Program;
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
        // CHANGED: program, year level and student number are saved on the students table.
        $student = User::createWithProfile([
            'name' => $name,
            'email' => "student{$i}@example.com",
            'password' => bcrypt('StudentPassword123!'),
            'role' => 'student',
            'email_verified_at' => now(),
        ], [
            'program_id' => Program::where('code', 'BSIT')->value('id'),
            'year_level' => '4th year',
            'student_number' => "21-000{$i}",
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

// ADDED: the list's total and the open stall count come from the participation query, so the page
// runs it and the page query only.
test('students page totals come from the participation query', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    $all = $this->actingAs($this->admin)->get(route('admin.students'));

    expect($all->viewData('students')->total())->toBe(4)
        ->and($all->viewData('openStalls'))->toBe(1)
        ->and(array_filter(DB::getQueryLog(), fn ($q) => str_contains($q['query'], 'stall_evaluations') || str_contains($q['query'], 'stalls')))->toHaveCount(2);
    expect($this->actingAs($this->admin)->get(route('admin.students', ['status' => 'evaluated']))->viewData('students')->total())->toBe(1);
    expect($this->actingAs($this->admin)->get(route('admin.students', ['status' => 'pending']))->viewData('students')->total())->toBe(3);
});

test('students page shows this month\'s stalls out of the open stalls', function () {
    // A second open stall and a closed one; Ana's evaluation of the closed stall does not count.
    $ana = User::where('name', 'Ana This Month')->first();
    $openId = DB::table('stalls')->insertGetId(['name' => 'Rice Bowl', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    $closedId = DB::table('stalls')->insertGetId(['name' => 'Old Grill', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()]);
    DB::table('stall_evaluations')->insert([
        'student_id' => $ana->id, 'stall_id' => $closedId,
        'cleanliness' => 4, 'service' => 4, 'taste' => 4, 'price' => 4,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->admin)->get(route('admin.students'))
        ->assertOk()
        ->assertSee('<strong class="font-semibold text-neutral-900">1</strong> of 2 stalls', false);

    DB::table('stall_evaluations')->insert([
        'student_id' => $ana->id, 'stall_id' => $openId,
        'cleanliness' => 4, 'service' => 4, 'taste' => 4, 'price' => 4,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->admin)->get(route('admin.students'))
        ->assertOk()
        ->assertSeeText('All 2 stalls')
        ->assertSeeText('1 of 4');
});

test('students page filters by program and shows the track name', function () {
    $bsit = Program::firstWhere('code', 'BSIT');
    $bscs = Program::firstWhere('code', 'BSCS');
    $make = fn ($name, $p, $t, $n) => User::createWithProfile(
        ['name' => $name, 'email' => "{$n}@x.com", 'password' => 'Password1!', 'role' => 'student'],
        ['student_number' => $n, 'program_id' => $p->id, 'track_id' => $p->tracks->firstWhere('code', $t)->id],
    );
    $make('Eve Cruz', $bsit, 'WMAD', '22-1');
    $make('Finn Reyes', $bscs, 'DM', '22-2');

    $this->actingAs($this->admin)->get(route('admin.students', ['program' => 'BSIT']))
        ->assertSee('Eve Cruz')->assertDontSee('Finn Reyes')
        ->assertSee('BSIT-WMAD')->assertSee('title="Web and Mobile Application Development"', false);
});
