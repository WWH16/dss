<?php

use App\Mail\OtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('otp email generates correct verification url with configured app url', function () {
    config(['app.url' => 'https://isu-dss.vercel.app']);

    $mail = new OtpMail('123456', 'Juan Dela Cruz', 'juan@isu.edu.ph');
    $html = $mail->render();

    expect($html)->toContain('https://isu-dss.vercel.app/verify-otp?email=juan%40isu.edu.ph');
    expect($html)->not->toContain('localhost');
});

test('otp verification succeeds when email is submitted in request even if session is lost', function () {
    $email = 'juan_cyn@isu.edu.ph';
    $otp = '654321';

    EmailVerification::create([
        'email'      => $email,
        'otp'        => $otp,
        'payload'    => [
            'name'           => 'Juan Dela Cruz',
            'email'          => $email,
            'password'       => bcrypt('password123'),
            'role'           => 'student',
            'course'         => 'BSIT',
            'year_level'     => '3rd Year',
            'student_number' => '2023-0001',
            'stall_id'       => null,
        ],
        'expires_at' => now()->addMinutes(15),
    ]);

    // Simulate request coming from email link on external device (no session pre-existing)
    $response = $this->post(route('otp.verify'), [
        'email' => $email,
        'otp'   => $otp,
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => $email,
        'role'  => 'student',
    ]);
});
