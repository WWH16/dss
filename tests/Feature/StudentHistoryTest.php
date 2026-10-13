<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::create([
        'name' => 'History Student',
        'email' => 'history@example.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
});

function historyEvaluation(int $studentId, $at): void
{
    $stallId = DB::table('stalls')->insertGetId([
        'name' => 'Bukohan Corner', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);
    DB::table('stall_evaluations')->insert([
        'student_id' => $studentId, 'stall_id' => $stallId,
        'cleanliness' => 4, 'service' => 4, 'taste' => 4, 'price' => 4,
        'created_at' => $at, 'updated_at' => $at,
    ]);
}

// The dashboard and the history page read the same rows, so a stall on one is on the other.
test('an evaluation on the dashboard is also in the history', function () {
    historyEvaluation($this->student->id, now());

    $this->actingAs($this->student)->get(route('student.dashboard'))->assertSee('Bukohan Corner');
    $this->actingAs($this->student)->get(route('student.history'))
        ->assertOk()
        ->assertSee('Bukohan Corner');
});

test('with no evaluations the history shows one action, the monthly rule, and no search', function () {
    $content = $this->actingAs($this->student)->get(route('student.history'))
        ->assertOk()
        ->assertSee('No evaluations yet')
        // CHANGED: the rule is once per stall each month.
        ->assertSee('You can evaluate each stall once a month.')
        ->assertDontSee('name="q"', false)
        ->getContent();

    expect(substr_count($content, "Scan a stall's QR"))->toBe(1);
});

// CHANGED: other stalls stay open after an evaluation this month, so the scan action stays on the history.
test('an evaluation this month leaves the scan action on the history', function () {
    historyEvaluation($this->student->id, now());

    $this->actingAs($this->student)->get(route('student.history'))
        ->assertSee("Scan a stall's QR", false)
        ->assertDontSee('Next evaluation opens');
});
