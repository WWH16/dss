<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
