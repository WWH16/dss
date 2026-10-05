<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('login failure displays invalid credentials alert', function () {
    $response = $this->from('/login')->post('/login', [
        'active_tab' => 'login',
        'role' => 'student',
        'student_number' => '99-99999',
        'password' => 'WrongPassword123!',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'Invalid credentials');

    $followResponse = $this->followRedirects($response);
    $followResponse->assertStatus(200);
    $followResponse->assertSee('Invalid credentials');
});

test('login validation failure preserves login tab and displays field errors', function () {
    $response = $this->from('/login')->post('/login', [
        'active_tab' => 'login',
        'role' => 'invalid-role',
        'password' => '',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['role', 'password']);

    $followResponse = $this->followRedirects($response);
    $followResponse->assertStatus(200);
    $followResponse->assertSee('The selected role is invalid.');
});

test('signup validation failure preserves register tab and displays inline errors', function () {
    $response = $this->from('/login')->post('/register', [
        'active_tab' => 'register',
        'role' => 'student',
        'name' => '',
        'email' => '',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['name', 'email', 'password']);

    $followResponse = $this->followRedirects($response);
    $followResponse->assertStatus(200);
    $followResponse->assertSee('id="register-form-block" style=""', false);
    $followResponse->assertSee('Please enter your full name.');
    $followResponse->assertSee('Please enter your email address.');
    $followResponse->assertSee('The password confirmation does not match.');
});

test('recaptcha error displays in red alert banner on auth page', function () {
    $errorBag = new \Illuminate\Support\ViewErrorBag();
    $bag = new \Illuminate\Support\MessageBag([
        'g_recaptcha_response' => ['reCAPTCHA security verification failed. Please refresh and try again.'],
    ]);
    $errorBag->put('default', $bag);

    $response = $this->withSession(['errors' => $errorBag])->get('/login');
    $response->assertSee('reCAPTCHA security verification failed. Please refresh and try again.');
});

test('password change rejects a wrong current password and accepts the right one', function () {
    $student = User::create([
        'name' => 'Student', 'email' => 'student@example.com', 'password' => bcrypt('OldPass1!'),
        'role' => 'student', 'email_verified_at' => now(),
    ]);
    $payload = ['password' => 'NewPass1!', 'password_confirmation' => 'NewPass1!'];

    $this->actingAs($student)->from(route('student.profile'))
        ->post(route('student.profile.password'), ['current_password' => 'wrong'] + $payload)
        ->assertSessionHasErrors(['current_password' => 'The current password provided is incorrect.']);

    $this->actingAs($student)
        ->post(route('student.profile.password'), ['current_password' => 'OldPass1!'] + $payload)
        ->assertSessionHas('success');

    expect(Hash::check('NewPass1!', $student->fresh()->password))->toBeTrue();
});
