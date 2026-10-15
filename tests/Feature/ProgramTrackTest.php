<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('profile update saves a track offered by the course and rejects one that is not', function () {
    $student = User::create([
        'name' => 'Juan Dela Cruz',
        'email' => 'juan_cyn@isu.edu.ph',
        'password' => bcrypt('StudentPassword123!'),
        'role' => 'student',
        'course' => 'BSIT',
        'track' => 'WMAD',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($student)
        ->post(route('student.profile.update'), ['name' => 'Juan Dela Cruz', 'course' => 'BSCS', 'track' => 'WMAD'])
        ->assertSessionHasErrors(['track' => 'The selected specialization track is invalid.']);
    expect($student->fresh()->only('course', 'track'))->toBe(['course' => 'BSIT', 'track' => 'WMAD']);

    $this->actingAs($student)
        ->post(route('student.profile.update'), ['name' => 'Juan Dela Cruz', 'course' => 'BSEMC', 'track' => 'GD'])
        ->assertSessionHasNoErrors();
    expect($student->fresh()->only('course', 'track'))->toBe(['course' => 'BSEMC', 'track' => 'GD']);
});

test('a student signs up with a program and track, verifies, logs in and submits an evaluation', function () {
    \Illuminate\Support\Facades\Mail::fake();
    $stallId = \Illuminate\Support\Facades\DB::table('stalls')->insertGetId([
        'name' => 'Snack Hub', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);
    $form = [
        'role' => 'student',
        'name' => 'Maria Santos',
        'email' => 'msantos_cyn@isu.edu.ph',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'course' => 'BSCS',
        'year_level' => '2nd year',
        'student_number' => '24-00001',
    ];

    // A track from another program, or none, is rejected.
    $this->post('/register', $form + ['track' => 'WMAD'])->assertSessionHasErrors('track');
    $this->post('/register', $form)->assertSessionHasErrors(['track' => 'Please select your specialization track.']);

    $this->post('/register', $form + ['track' => 'DM'])->assertSessionHasNoErrors();
    $otp = \App\Models\EmailVerification::where('email', $form['email'])->value('otp');
    $this->post(route('otp.verify'), ['email' => $form['email'], 'otp' => $otp])->assertRedirect('/login');

    $student = User::where('email', $form['email'])->first();
    expect($student->only('course', 'track'))->toBe(['course' => 'BSCS', 'track' => 'DM']);

    $this->post('/login', ['role' => 'student', 'student_number' => '24-00001', 'password' => 'Password1!'])
        ->assertRedirect('/student/dashboard');
    $this->assertAuthenticatedAs($student);

    $this->get(\Illuminate\Support\Facades\URL::signedRoute('student.evaluation', ['stall' => $stallId], null, false))->assertOk();
    $this->post(route('student.evaluation.store'), ['responses' => array_fill_keys(range(1, 10), 5)])
        ->assertSessionHas('success', 'Evaluation submitted successfully!');
    $this->assertDatabaseHas('stall_evaluations', ['stall_id' => $stallId, 'student_id' => $student->id]);

    // The admin Students page shows the program and track.
    $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('x'), 'role' => 'admin', 'email_verified_at' => now()]);
    $this->actingAs($admin)->get(route('admin.students'))->assertSee('BSCS-DM')->assertSee('title="Data Mining"', false);
});
