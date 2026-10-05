<?php

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('password changed email names the student, shows Philippine time, and has no password', function () {
    $mail = new PasswordChangedMail('Juan Dela Cruz', Carbon::parse('2026-10-05 00:30:00', 'UTC'));

    $mail->assertHasSubject('Your ISU Canteen Evaluation System password was changed');
    $mail->assertSeeInHtml('Juan Dela Cruz');
    $mail->assertSeeInHtml('Oct 5, 2026, 8:30 AM');
    $mail->assertSeeInHtml('contact the canteen system administrator');
    $mail->assertDontSeeInHtml('NewPass1!');
});

function studentWithPassword(string $password = 'OldPass1!'): User
{
    return User::create([
        'name' => 'Juan Dela Cruz',
        'email' => 'juan_cyn@isu.edu.ph',
        'password' => bcrypt($password),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
}

test('changing the password emails the student', function () {
    Mail::fake();
    $student = studentWithPassword();

    $this->actingAs($student)->post(route('student.profile.password'), [
        'current_password' => 'OldPass1!',
        'password' => 'NewPass1!',
        'password_confirmation' => 'NewPass1!',
        'email' => 'attacker@example.com',
    ])->assertSessionHas('success', 'Password updated successfully!');

    Mail::assertSent(PasswordChangedMail::class, function (PasswordChangedMail $mail) {
        return $mail->hasTo('juan_cyn@isu.edu.ph')
            && ! $mail->hasTo('attacker@example.com')
            && $mail->studentName === 'Juan Dela Cruz';
    });
    Mail::assertSentCount(1);
});

test('no email when the current password is wrong or the new one is invalid', function () {
    Mail::fake();
    $student = studentWithPassword();

    $this->actingAs($student)->post(route('student.profile.password'), [
        'current_password' => 'wrong',
        'password' => 'NewPass1!',
        'password_confirmation' => 'NewPass1!',
    ])->assertSessionHasErrors('current_password');

    $this->actingAs($student)->post(route('student.profile.password'), [
        'current_password' => 'OldPass1!',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');

    Mail::assertNothingSent();
});

test('a mail failure does not undo or block the password change', function () {
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP down'));
    $student = studentWithPassword();

    $this->actingAs($student)->post(route('student.profile.password'), [
        'current_password' => 'OldPass1!',
        'password' => 'NewPass1!',
        'password_confirmation' => 'NewPass1!',
    ])->assertSessionHas('success', 'Password updated successfully!');

    expect(Hash::check('NewPass1!', $student->fresh()->password))->toBeTrue();
});
