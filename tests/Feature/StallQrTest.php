<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function qrStall(bool $active = true): int
{
    return DB::table('stalls')->insertGetId([
        'name' => $active ? 'Snack Hub' : 'Closed Corner',
        'is_active' => $active,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function qrUser(string $role): User
{
    return User::create([
        'name' => ucfirst($role) . ' User',
        'email' => "{$role}@example.com",
        'student_number' => $role === 'student' ? '21-00001' : null,
        'password' => bcrypt('password'),
        'role' => $role,
        'email_verified_at' => now(),
    ]);
}

// The link the admin QR page encodes for a stall.
function qrLink($test, int $stallId): string
{
    $url = null;
    $test->actingAs(qrUser('admin'))->get(route('admin.stall.qr', $stallId))
        ->assertViewHas('url', function ($value) use (&$url) {
            $url = $value;
            return true;
        });
    auth()->logout();

    return $url;
}

test('admin gets a QR page whose link is the signed evaluation link for that stall', function () {
    $stallId = qrStall();

    $response = $this->actingAs(qrUser('admin'))->get(route('admin.stall.qr', $stallId))
        ->assertOk()
        ->assertSee('Snack Hub')
        ->assertSee('data:image/svg+xml;base64,', false)
        ->assertSee('Download PNG')
        ->assertSee('snack-hub-qr.svg', false)
        ->assertDontSee('currently closed');

    $url = $response->viewData('url');
    expect($url)->toStartWith(url('/student/evaluation?stall=' . $stallId . '&signature='));
    $response->assertSee(e($url), false);
});

test('a signed-out student who scans lands on that stall form after signing in', function () {
    $stallId = qrStall();
    $scanned = qrLink($this, $stallId);
    qrUser('student');

    $this->get($scanned)->assertRedirect(route('login'));

    $login = $this->post('/login', [
        'role' => 'student',
        'student_number' => '21-00001',
        'password' => 'password',
    ])->assertRedirectContains('/student/evaluation?');

    // Laravel stores the URL with its query string re-ordered; the signature must still validate.
    $this->get($login->headers->get('Location'))
        ->assertOk()
        ->assertSee('You are evaluating')
        ->assertSee('Snack Hub');
});

test('a non-student signing in after a scanned link goes to their own dashboard', function (string $role, string $home) {
    $stallId = qrStall();
    $scanned = qrLink($this, $stallId);
    if ($role !== 'admin') {
        qrUser($role);
    }

    $this->get($scanned);

    $this->post('/login', [
        'role' => $role,
        'email' => "{$role}@example.com",
        'password' => 'password',
    ])->assertRedirect($home);
})->with([
    ['admin', '/admin/dashboard'],
    ['clinic', '/admin/dashboard'],
    ['staff', '/staff/dashboard'],
]);

// ADDED: regression test for the 500 "Route [verification.notice] not defined" a scanned link threw
// for any signed-in account whose email_verified_at is null (every staff and admin self-registration).
test('an account with an unverified email that scans the link is redirected, not a 500', function () {
    $scanned = qrLink($this, qrStall());

    $user = qrUser('staff');
    $user->forceFill(['email_verified_at' => null])->save();

    $this->actingAs($user)->get($scanned)->assertRedirect('/login');
});

test('a student signing in normally still goes to the dashboard', function () {
    qrUser('student');

    $this->post('/login', [
        'role' => 'student',
        'student_number' => '21-00001',
        'password' => 'password',
    ])->assertRedirect('/student/dashboard');
});

test('QR page for a closed stall warns that scanning shows closed', function () {
    $stallId = qrStall(active: false);

    $this->actingAs(qrUser('admin'))->get(route('admin.stall.qr', $stallId))
        ->assertOk()
        ->assertSee('currently closed');
});

test('QR page for a missing stall is a 404', function () {
    $this->actingAs(qrUser('admin'))->get(route('admin.stall.qr', 999))->assertNotFound();
});

test('only admins can open QR pages', function (string $role) {
    $stallId = qrStall();

    $this->actingAs(qrUser($role))->get(route('admin.stall.qr', $stallId))->assertRedirect('/login');
})->with(['clinic', 'staff', 'student']);

test('stalls page links each stall to its QR page', function () {
    $stallId = qrStall();

    $this->actingAs(qrUser('admin'))->get(route('admin.stalls'))
        ->assertSee(route('admin.stall.qr', $stallId), false);
});

function assignedStaff(?int $stallId): User
{
    $staff = qrUser('staff');
    $staff->forceFill(['stall_id' => $stallId])->save();

    return $staff;
}

test('assigned staff see their stall QR code with the same signed link as the admin page', function () {
    $stallId = qrStall();
    $adminUrl = qrLink($this, $stallId);

    $this->actingAs(assignedStaff($stallId))->get(route('staff.qr'))
        ->assertOk()
        ->assertViewHas('url', $adminUrl)
        ->assertSee('Snack Hub')
        ->assertSee('data:image/svg+xml;base64,', false)
        ->assertSee('Download PNG')
        ->assertSee('snack-hub-qr.svg', false);
});

test('staff sidebar shows the QR Code tab only when assigned to a stall', function () {
    $stallId = qrStall();

    $this->actingAs(assignedStaff($stallId))->get(route('staff.dashboard'))
        ->assertSee(route('staff.qr'), false);

    $unassigned = User::create([
        'name' => 'Loose Staff', 'email' => 'loose@example.com', 'password' => bcrypt('password'),
        'role' => 'staff', 'email_verified_at' => now(),
    ]);
    $this->actingAs($unassigned)->get(route('staff.dashboard'))
        ->assertDontSee(route('staff.qr'), false);
});

test('unassigned staff are sent back to the dashboard from the QR tab', function () {
    $this->actingAs(assignedStaff(null))->get(route('staff.qr'))
        ->assertRedirect(route('staff.dashboard'))
        ->assertSessionHas('error');
});

test('only staff can open the staff QR tab', function (string $role) {
    $this->actingAs(qrUser($role))->get(route('staff.qr'))->assertRedirect('/login');
})->with(['admin', 'clinic', 'student']);
