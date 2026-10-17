<?php

use App\Models\Program;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// CHANGED: program and track are IDs on the students table instead of codes on users.
test('profile update saves a track offered by the program and rejects one that is not', function () {
    $code = fn (string $program, string $track) => [
        'program_id' => $p = Program::where('code', $program)->value('id'),
        'track_id' => Track::where('program_id', $p)->where('code', $track)->value('id'),
    ];
    $student = User::createWithProfile([
        'name' => 'Juan Dela Cruz',
        'email' => 'juan_cyn@isu.edu.ph',
        'password' => bcrypt('StudentPassword123!'),
        'role' => 'student',
        'email_verified_at' => now(),
    ], $code('BSIT', 'WMAD'));

    $this->actingAs($student)
        ->post(route('student.profile.update'), ['name' => 'Juan Dela Cruz', 'program_id' => $code('BSCS', 'DM')['program_id'], 'track_id' => $code('BSIT', 'WMAD')['track_id']])
        ->assertSessionHasErrors(['track_id' => 'The selected specialization track is invalid.']);
    expect($student->student()->first()->only('program_id', 'track_id'))->toBe($code('BSIT', 'WMAD'));

    $this->actingAs($student)
        ->post(route('student.profile.update'), ['name' => 'Juan Dela Cruz'] + $code('BSEMC', 'GD'))
        ->assertSessionHasNoErrors();
    expect($student->student()->first()->only('program_id', 'track_id'))->toBe($code('BSEMC', 'GD'));

    $this->actingAs($student)->get(route('student.profile'))
        ->assertSee('BSEMC')->assertSee('Game Development');
});

test('a student signs up with a program and track, verifies, logs in and submits an evaluation', function () {
    \Illuminate\Support\Facades\Mail::fake();
    $stallId = \Illuminate\Support\Facades\DB::table('stalls')->insertGetId([
        'name' => 'Snack Hub', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);
    // CHANGED: the form posts program_id and track_id (IDs from the programs and tracks tables).
    $bscs = Program::firstWhere('code', 'BSCS');
    $dm = $bscs->tracks->firstWhere('code', 'DM')->id;
    $form = [
        'role' => 'student',
        'name' => 'Maria Santos',
        'email' => 'msantos_cyn@isu.edu.ph',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'program_id' => $bscs->id,
        'year_level' => '2nd year',
        'student_number' => '24-00001',
    ];

    // A track from another program, or none, is rejected.
    $this->post('/register', $form + ['track_id' => Track::where('code', 'WMAD')->value('id')])->assertSessionHasErrors('track_id');
    $this->post('/register', $form)->assertSessionHasErrors(['track_id' => 'Please select your specialization track.']);

    $this->post('/register', $form + ['track_id' => $dm])->assertSessionHasNoErrors();
    $otp = \App\Models\EmailVerification::where('email', $form['email'])->value('otp');
    $this->post(route('otp.verify'), ['email' => $form['email'], 'otp' => $otp])->assertRedirect('/login');

    $student = User::where('email', $form['email'])->first();
    // CHANGED: program, track and student number are saved on the students table.
    expect($student->student->only('program_id', 'track_id', 'student_number'))
        ->toBe(['program_id' => $bscs->id, 'track_id' => $dm, 'student_number' => '24-00001']);

    $this->post('/login', ['role' => 'student', 'student_number' => '24-00001', 'password' => 'Password1!'])
        ->assertRedirect('/student/dashboard');
    $this->assertAuthenticatedAs($student);

    $this->get(\Illuminate\Support\Facades\URL::signedRoute('student.evaluation', ['stall' => $stallId], null, false))->assertOk();
    $this->post(route('student.evaluation.store'), ['responses' => array_fill_keys(range(1, 10), 5)])
        ->assertSessionHas('success', 'Evaluation submitted successfully!');
    $this->assertDatabaseHas('stall_evaluations', ['stall_id' => $stallId, 'student_id' => $student->id]);
    // CHANGED: the admin Students page check moved to AdminStudentsParticipationTest.
});

test('program and track must be single numeric IDs at signup and in the profile form', function () {
    $bsit = Program::firstWhere('code', 'BSIT');
    $form = [
        'role' => 'student', 'name' => 'Maria Santos', 'email' => 'msantos_cyn@isu.edu.ph',
        'password' => 'Password1!', 'password_confirmation' => 'Password1!',
        'year_level' => '2nd year', 'student_number' => '24-00002',
        'track_id' => $bsit->tracks->first()->id,
    ];
    $this->post('/register', $form + ['program_id' => [$bsit->id, $bsit->id]])->assertSessionHasErrors('program_id');
    $this->post('/register', $form + ['program_id' => 'abc'])->assertSessionHasErrors('program_id');

    $student = User::create(['name' => 'Juan', 'email' => 'juan_cyn@isu.edu.ph', 'password' => bcrypt('x'), 'role' => 'student', 'email_verified_at' => now()]);
    $this->actingAs($student)
        ->post(route('student.profile.update'), ['name' => 'Juan', 'program_id' => [$bsit->id], 'track_id' => [$bsit->tracks->first()->id]])
        ->assertSessionHasErrors(['program_id', 'track_id']);
});
