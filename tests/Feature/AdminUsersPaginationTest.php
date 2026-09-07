<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Current SuperAdmin',
        'email' => 'superadmin@example.com',
        'password' => bcrypt('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    User::create([
        'name' => 'Second Administrator',
        'email' => 'admin2@example.com',
        'password' => bcrypt('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $stallId = DB::table('stalls')->insertGetId([
        'name' => 'Snack Hub',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    User::create([
        'name' => 'Assigned Vendor',
        'email' => 'staff1@example.com',
        'password' => bcrypt('StaffPassword123!'),
        'role' => 'staff',
        'stall_id' => $stallId,
        'email_verified_at' => now(),
    ]);

    User::create([
        'name' => 'Unassigned Staff',
        'email' => 'staff2@example.com',
        'password' => bcrypt('StaffPassword123!'),
        'role' => 'staff',
        'stall_id' => null,
        'email_verified_at' => now(),
    ]);
});

test('admin can access users directory with pagination and system stats', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users'));

    $response->assertStatus(200);
    $response->assertSee('Staff &amp; Administrators', false);
    $response->assertSee('Total Accounts');
    $response->assertSee('Showing');
    $response->assertSee('Per Page');
    $response->assertSee('Second Administrator');
    $response->assertSee('Assigned Vendor');
    $response->assertSee('Unassigned Staff');
});

test('admin can filter users by role and search query with pagination', function () {
    // Filter Admins
    $responseAdmin = $this->actingAs($this->admin)->get(route('admin.users', ['role' => 'admin']));
    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('Second Administrator');
    $responseAdmin->assertDontSee('Assigned Vendor');

    // Filter Staff
    $responseStaff = $this->actingAs($this->admin)->get(route('admin.users', ['role' => 'staff']));
    $responseStaff->assertStatus(200);
    $responseStaff->assertSee('Assigned Vendor');
    $responseStaff->assertSee('Unassigned Staff');
    $responseStaff->assertDontSee('Second Administrator');

    // Filter Unassigned
    $responseUnassigned = $this->actingAs($this->admin)->get(route('admin.users', ['role' => 'unassigned']));
    $responseUnassigned->assertStatus(200);
    $responseUnassigned->assertSee('Unassigned Staff');
    $responseUnassigned->assertDontSee('Assigned Vendor');

    // Search query
    $responseSearch = $this->actingAs($this->admin)->get(route('admin.users', ['q' => 'Snack Hub']));
    $responseSearch->assertStatus(200);
    $responseSearch->assertSee('Assigned Vendor');
    $responseSearch->assertDontSee('Unassigned Staff');
});

test('users page renders unified modal-sharp dialogs with shrunken width and no redundant subtitles', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users'));

    $response->assertStatus(200);

    // Verify modal-sharp and shrunken max-w-[400px] classes are present
    $response->assertSee('modal-sharp max-w-[400px]', false);

    // Verify clean titles
    $response->assertSee('Create Account');
    $response->assertSee('Edit Account');
    $response->assertSee('Delete Account');

    // Verify removed verbose subtitles
    $response->assertDontSee('Add a new administrator or canteen staff member');
    $response->assertDontSee('Update account details, role, or password');

    // Verify segmented control and hidden input for role
    $response->assertSee('create-role-admin-btn');
    $response->assertSee('create-role-staff-btn');
    $response->assertSee('id="create-role-input"', false);
});

test('admin can create new user through modal endpoint', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.users.create'), [
        'role' => 'staff',
        'name' => 'Jane Barista',
        'email' => 'jane@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('users', [
        'email' => 'jane@example.com',
        'name' => 'Jane Barista',
        'role' => 'staff',
    ]);
});

test('users page contains inline error slots and button spinner containers for modals', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users'));

    $response->assertStatus(200);

    // Verify inline error elements
    $response->assertSee('data-error-for="name"', false);
    $response->assertSee('data-error-for="email"', false);
    $response->assertSee('data-error-for="password"', false);
    $response->assertSee('data-error-for="password_confirmation"', false);
    $response->assertSee('data-error-for="role"', false);
    $response->assertSee('data-error-for="stall_id"', false);
    $response->assertSee('data-general-error', false);

    // Verify submit button targets
    $response->assertSee('id="create-user-submit-btn"', false);
    $response->assertSee('id="edit-user-submit-btn"', false);
    $response->assertSee('id="delete-user-submit-btn"', false);
});

test('ajax user creation returns 422 json with field errors on invalid input', function () {
    $response = $this->actingAs($this->admin)->postJson(route('admin.users.create'), [
        'role' => 'admin',
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('ajax user creation returns 200 json with success flash on valid input', function () {
    $response = $this->actingAs($this->admin)->postJson(route('admin.users.create'), [
        'role' => 'staff',
        'name' => 'Ajax Staff',
        'email' => 'ajaxstaff@example.com',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('users', ['email' => 'ajaxstaff@example.com']);
});

test('ajax user creation returns 422 with password confirmation mismatch error', function () {
    $response = $this->actingAs($this->admin)->postJson(route('admin.users.create'), [
        'role' => 'admin',
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'ValidPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
    // Laravel's confirmed rule produces a message containing "confirmation does not match"
    $errors = $response->json('errors.password');
    $this->assertTrue(collect($errors)->contains(fn ($msg) => str_contains(strtolower($msg), 'match') || str_contains(strtolower($msg), 'confirm')));
});

test('ajax user creation returns 422 with password rule violations when confirmation matches', function () {
    $response = $this->actingAs($this->admin)->postJson(route('admin.users.create'), [
        'role' => 'admin',
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
    $errors = $response->json('errors.password');
    // Error does NOT contain "match" or "confirmation does not match" - it is a rule violation (e.g. length/characters)
    $this->assertFalse(collect($errors)->contains(fn ($msg) => str_contains(strtolower($msg), 'match')));
});

test('users page includes resetCreateModal logic and confirmation error routing', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users'));

    $response->assertStatus(200);
    $response->assertSee('resetCreateModal', false);
    $response->assertSee('createModal.addEventListener(\'close\'', false);
    $response->assertSee('data-error-for="password_confirmation"', false);
    $response->assertSee('data-error-for="password"', false);
});

test('ajax user update disallows self demotion with 422 json error', function () {
    $response = $this->actingAs($this->admin)->putJson(route('admin.users.update', $this->admin->id), [
        'role' => 'staff',
        'name' => $this->admin->name,
        'email' => $this->admin->email,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['role']);
});

test('users page includes enter key modal navigation configuration', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users'));

    $response->assertStatus(200);
    $response->assertSee('wireEnterKeyNavigation', false);
    $response->assertSee('create_name', false);
    $response->assertSee('create_email', false);
    $response->assertSee('create_password', false);
    $response->assertSee('create_password_confirmation', false);
    $response->assertSee('create-user-submit-btn', false);
});

