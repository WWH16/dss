<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::create([
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    $this->activeStallId = DB::table('stalls')->insertGetId([
        'name' => 'Active Canteen',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->inactiveStallId = DB::table('stalls')->insertGetId([
        'name' => 'Closed Canteen',
        'is_active' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('inactive stalls do not appear in the student evaluation form dropdown', function () {
    $response = $this->actingAs($this->student)->get(route('student.evaluation'));

    $response->assertStatus(200);
    $response->assertSee('Active Canteen');
    $response->assertDontSee('Closed Canteen');
});

test('requesting an inactive stall by query parameter redirects with an error flash message', function () {
    $response = $this->actingAs($this->student)->get(route('student.evaluation', ['stall' => $this->inactiveStallId]));

    $response->assertRedirect(route('student.evaluation'));
    $response->assertSessionHas('error', 'Closed Canteen is currently closed for student evaluations.');
});

test('submitting an evaluation for an inactive stall is rejected and not saved', function () {
    $responses = [];
    for ($i = 1; $i <= 10; $i++) {
        $responses[$i] = 5;
    }

    $response = $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'stall_id' => $this->inactiveStallId,
        'responses' => $responses,
        'comment' => 'Should be rejected',
    ]);

    $response->assertSessionHasErrors(['stall_id']);

    $this->assertDatabaseMissing('stall_evaluations', [
        'stall_id' => $this->inactiveStallId,
        'student_id' => $this->student->id,
    ]);
});

test('submitting an evaluation for an active stall succeeds and is saved', function () {
    $responses = [];
    for ($i = 1; $i <= 10; $i++) {
        $responses[$i] = 4;
    }

    $response = $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'stall_id' => $this->activeStallId,
        'responses' => $responses,
        'comment' => 'Great food!',
    ]);

    $response->assertSessionHas('success', 'Evaluation submitted successfully!');

    $this->assertDatabaseHas('stall_evaluations', [
        'stall_id' => $this->activeStallId,
        'student_id' => $this->student->id,
        'comment' => 'Great food!',
    ]);
});

test('inactive stalls do not appear on the student dashboard', function () {
    $response = $this->actingAs($this->student)->get(route('student.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Active Canteen');
    $response->assertDontSee('Closed Canteen');
});
