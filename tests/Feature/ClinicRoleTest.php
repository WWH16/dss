<?php

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function adminUser(): User
{
    return User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function clinicUser(): User
{
    return User::create([
        'name' => 'Clinic Nurse',
        'email' => 'clinic@example.com',
        'password' => bcrypt('password'),
        'role' => 'clinic',
        'email_verified_at' => now(),
    ]);
}

test('admin seeder creates only the admin account', function () {
    $this->seed(AdminSeeder::class);

    expect(User::pluck('email')->all())->toBe(['admin@gmail.com']);
    expect(User::where('email', 'admin@gmail.com')->value('role'))->toBe('admin');
});

test('login page offers clinic role and preselects it', function () {
    $response = $this->get('/login?role=clinic');

    $response->assertStatus(200);
    $response->assertSee('<option value="clinic" selected>Clinic</option>', false);
});

test('register form never offers the clinic role, even from role=clinic', function () {
    $response = $this->get('/login?role=clinic');

    $register = str($response->getContent())->after('id="register_role"')->before('</select>');
    expect((string) $register)->not->toContain('value="clinic"');
    expect((string) $register)->toContain('<option value="student" selected>Student</option>');
});

test('clinic user signs in and lands on the admin dashboard', function () {
    clinicUser();

    $response = $this->post('/login', [
        'role' => 'clinic',
        'email' => 'clinic@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/admin/dashboard');
    $this->assertAuthenticated();
});

test('clinic user cannot sign in with the admin role picked', function () {
    clinicUser();

    $response = $this->from('/login')->post('/login', [
        'role' => 'admin',
        'email' => 'clinic@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'Invalid credentials');
    $this->assertGuest();
});

test('registering with the clinic role is rejected', function () {
    $response = $this->from('/login')->post('/register', [
        'role' => 'clinic',
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasErrors('role');
    expect(User::where('email', 'sneaky@example.com')->exists())->toBeFalse();
});

function seedStall(): int
{
    return DB::table('stalls')->insertGetId([
        'name' => 'Snack Hub',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

test('clinic user can open the monitoring pages', function (string $route) {
    seedStall();

    $this->actingAs(clinicUser())->get(route($route))->assertStatus(200);
})->with(['admin.dashboard', 'admin.evaluations', 'admin.report']);

test('clinic user is bounced from admin-only pages', function (string $route) {
    $this->actingAs(clinicUser())->get(route($route))->assertRedirect('/login');
})->with(['admin.stalls', 'admin.students', 'admin.users']);

test('clinic user cannot change stalls or accounts', function () {
    $stallId = seedStall();
    $clinic = clinicUser();

    $this->actingAs($clinic)->post(route('admin.stall.add'), ['name' => 'New'])->assertRedirect('/login');
    $this->actingAs($clinic)->delete(route('admin.stall.delete', $stallId))->assertRedirect('/login');
    $this->actingAs($clinic)->post(route('admin.users.create'), [
        'role' => 'admin', 'name' => 'X', 'email' => 'x@example.com',
        'password' => 'Password1!', 'password_confirmation' => 'Password1!',
    ])->assertRedirect('/login');

    expect(DB::table('stalls')->where('id', $stallId)->exists())->toBeTrue();
    expect(DB::table('stalls')->count())->toBe(1);
    expect(User::where('email', 'x@example.com')->exists())->toBeFalse();
});

test('student cannot delete a stall', function () {
    $stallId = seedStall();
    $student = User::create([
        'name' => 'Student', 'email' => 'student@example.com', 'password' => bcrypt('password'),
        'role' => 'student', 'email_verified_at' => now(),
    ]);

    $this->actingAs($student)->delete(route('admin.stall.delete', $stallId))->assertRedirect('/login');

    expect(DB::table('stalls')->where('id', $stallId)->exists())->toBeTrue();
});

test('admin can still delete a stall', function () {
    $stallId = seedStall();

    $this->actingAs(adminUser())->delete(route('admin.stall.delete', $stallId));

    expect(DB::table('stalls')->where('id', $stallId)->exists())->toBeFalse();
});

test('clinic dashboard hides admin-only links and shows clinic sidebar', function () {
    seedStall();

    $response = $this->actingAs(clinicUser())->get(route('admin.dashboard'));

    $response->assertDontSee(route('admin.stalls'), false);
    $response->assertDontSee(route('admin.students'), false);
    $response->assertDontSee(route('admin.users'), false);
    $response->assertSee(route('admin.evaluations'), false);
    $response->assertSee(route('admin.report'), false);
});

test('admin dashboard still links to admin-only pages', function () {
    seedStall();

    $response = $this->actingAs(adminUser())->get(route('admin.dashboard'));

    $response->assertSee(route('admin.stalls'), false);
    $response->assertSee(route('admin.students'), false);
    $response->assertSee(route('admin.users'), false);
});

test('admin creates a clinic account', function () {
    $this->actingAs(adminUser())->post(route('admin.users.create'), [
        'role' => 'clinic',
        'name' => 'Clinic Nurse',
        'email' => 'nurse@example.com',
        'stall_id' => seedStall(),
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasNoErrors();

    $nurse = User::where('email', 'nurse@example.com')->first();
    expect($nurse->role)->toBe('clinic');
    expect($nurse->stall_id)->toBeNull();
});

test('admin users page lists and filters clinic accounts', function () {
    $admin = adminUser();
    clinicUser();

    $this->actingAs($admin)->get(route('admin.users'))
        ->assertSee('Clinic Nurse')
        ->assertSee('Clinic (1)');

    $this->actingAs($admin)->get(route('admin.users', ['role' => 'clinic']))
        ->assertSee('Clinic Nurse')
        ->assertDontSee('admin@example.com');
});

test('admin changes a clinic account to staff with a stall, and back to clinic', function () {
    $admin = adminUser();
    $clinic = clinicUser();
    $stallId = seedStall();

    $this->actingAs($admin)->put(route('admin.users.update', $clinic->id), [
        'role' => 'staff', 'name' => $clinic->name, 'email' => $clinic->email, 'stall_id' => $stallId,
    ])->assertSessionHasNoErrors();
    expect($clinic->fresh()->only('role', 'stall_id'))->toBe(['role' => 'staff', 'stall_id' => $stallId]);

    $this->actingAs($admin)->put(route('admin.users.update', $clinic->id), [
        'role' => 'clinic', 'name' => $clinic->name, 'email' => $clinic->email, 'stall_id' => $stallId,
    ])->assertSessionHasNoErrors();
    expect($clinic->fresh()->only('role', 'stall_id'))->toBe(['role' => 'clinic', 'stall_id' => null]);
});

test('admin deletes a clinic account', function () {
    $admin = adminUser();
    $clinic = clinicUser();

    $this->actingAs($admin)->delete(route('admin.users.delete', $clinic->id))
        ->assertSessionHas('success');

    expect(User::find($clinic->id))->toBeNull();
});
