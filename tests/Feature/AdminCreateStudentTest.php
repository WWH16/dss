<?php

use App\Models\Program;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an admin creates a verified student with any email', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    $bsit = Program::firstWhere('code', 'BSIT');
    $form = [
        'name' => 'Maria Santos',
        'email' => 'maria.santos@gmail.com',
        'student_number' => '24-00002',
        'program_id' => $bsit->id,
        'track_id' => $bsit->tracks->firstWhere('code', 'WMAD')->id,
        'year_level' => '2nd year',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ];

    $this->actingAs($admin)
        ->post(route('admin.students.create'), ['track_id' => Track::where('code', 'DM')->value('id')] + $form)
        ->assertSessionHasErrors('track_id');

    $this->actingAs($admin)
        ->post(route('admin.students.create'), $form)
        ->assertRedirect(route('admin.students'))
        ->assertSessionHasNoErrors();

    $student = User::firstWhere('email', 'maria.santos@gmail.com');
    expect($student->role)->toBe('student')
        ->and($student->email_verified_at)->not->toBeNull()
        ->and($student->student->only('student_number', 'program_id', 'year_level'))
        ->toBe(['student_number' => '24-00002', 'program_id' => $bsit->id, 'year_level' => '2nd year']);

    $this->actingAs($admin)->get(route('admin.students'))->assertSee('Maria Santos')->assertSee('Add Student');

    $this->actingAs($student)
        ->post(route('admin.students.create'), ['email' => 'other@gmail.com', 'student_number' => '24-00003'] + $form)
        ->assertRedirect();
    expect(User::where('email', 'other@gmail.com')->exists())->toBeFalse();
});
