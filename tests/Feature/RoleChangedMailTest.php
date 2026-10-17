<?php

use App\Mail\PasswordChangedMail;
use App\Mail\RoleChangedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('role changed email names the account, both roles, and Philippine time', function () {
    $mail = new RoleChangedMail('Maria Santos', 'Staff', 'Clinic', Carbon::parse('2026-10-05 00:30:00', 'UTC'));

    $mail->assertHasSubject('Your ISU Canteen Evaluation System role was changed');
    $mail->assertSeeInHtml('Maria Santos');
    $mail->assertSeeInOrderInHtml(['Staff', 'Clinic']);
    $mail->assertSeeInHtml('Oct 5, 2026, 8:30 AM');
    $mail->assertSeeInHtml('contact the canteen system administrator');
});

test('role changed email does not name a login option that does not exist', function () {
    $mail = new RoleChangedMail('Maria Santos', 'Staff', 'Administrator', now());

    $mail->assertSeeInHtml('choose your new role on the login page');
    $mail->assertDontSeeInHtml('choose Administrator');
});

function roleMailUser(string $role, string $email, string $name): User
{
    return User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt('password'),
        'role' => $role,
        'email_verified_at' => now(),
    ]);
}

test('changing the role emails the account', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'staff', 'name' => 'Maria Santos', 'email' => 'nurse@example.com', 'stall_id' => null,
    ])->assertSessionHasNoErrors();

    Mail::assertSent(RoleChangedMail::class, fn (RoleChangedMail $mail) => $mail->hasTo('nurse@example.com')
        && $mail->oldRoleLabel === 'Clinic'
        && $mail->newRoleLabel === 'Staff');
    Mail::assertSentCount(1);
});

test('no email when the role stays the same', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'clinic', 'name' => 'Maria S. Santos', 'email' => 'nurse@example.com',
        'password' => 'NewPass1!', 'password_confirmation' => 'NewPass1!',
    ])->assertSessionHasNoErrors();

    // CHANGED: the new password now sends PasswordChangedMail, so only the role email is checked.
    Mail::assertNotSent(RoleChangedMail::class);
});

test('an admin resetting the password emails the account without the password', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'clinic', 'name' => 'Maria Santos', 'email' => 'nurse@example.com',
        'password' => 'NewPass1!', 'password_confirmation' => 'NewPass1!',
    ])->assertSessionHasNoErrors();

    Mail::assertSent(PasswordChangedMail::class, fn (PasswordChangedMail $mail) => $mail->hasTo('nurse@example.com')
        && ! str_contains($mail->render(), 'NewPass1!'));
    Mail::assertSentCount(1);
});

test('no password email when the admin leaves the password blank', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'clinic', 'name' => 'Maria S. Santos', 'email' => 'nurse@example.com', 'password' => '',
    ])->assertSessionHasNoErrors();

    Mail::assertNothingSent();
});

test('no email when the admin demotes themselves or validation fails', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $admin->id), [
        'role' => 'staff', 'name' => 'Admin', 'email' => 'admin@example.com',
    ])->assertSessionHas('error');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'student', 'name' => 'Maria Santos', 'email' => 'nurse@example.com',
    ])->assertSessionHasErrors('role');

    Mail::assertNothingSent();
});

test('role and email changed together email the new address without the password', function () {
    Mail::fake();
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'admin', 'name' => 'Maria Santos', 'email' => 'maria@example.com',
        'password' => 'NewPass1!', 'password_confirmation' => 'NewPass1!',
    ])->assertSessionHasNoErrors();

    Mail::assertSent(RoleChangedMail::class, fn (RoleChangedMail $mail) => $mail->hasTo('maria@example.com')
        && ! $mail->hasTo('nurse@example.com')
        && ! str_contains($mail->render(), 'NewPass1!'));
});

test('a mail failure does not undo or block the role change', function () {
    $admin = roleMailUser('admin', 'admin@example.com', 'Admin');
    $target = roleMailUser('clinic', 'nurse@example.com', 'Maria Santos');
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP down'));

    $this->actingAs($admin)->put(route('admin.users.update', $target->id), [
        'role' => 'admin', 'name' => 'Maria Santos', 'email' => 'nurse@example.com',
    ])->assertSessionHas('success', 'Account for Maria Santos updated successfully.');

    expect($target->fresh()->role)->toBe('admin');
});
