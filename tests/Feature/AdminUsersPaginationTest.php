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
