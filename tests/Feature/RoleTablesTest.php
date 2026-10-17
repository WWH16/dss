<?php

use App\Models\Program;
use App\Models\Staff;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('creating a user adds the row for its role and no other', function (string $role) {
    $user = User::create(['name' => 'X', 'email' => "{$role}@example.com", 'password' => 'Password1!', 'role' => $role]);
    foreach (User::PROFILE_TABLES as $r => $table) {
        expect(DB::table($table)->where('user_id', $user->id)->exists())->toBe($r === $role);
    }
})->with(['student', 'staff', 'admin', 'clinic']);

test('changing the role moves the row to the new role table', function () {
    $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'Password1!', 'role' => 'staff']);
    $user->update(['role' => 'clinic']);
    expect(DB::table('staff')->where('user_id', $user->id)->exists())->toBeFalse()
        ->and(DB::table('clinics')->where('user_id', $user->id)->exists())->toBeTrue();
});

test('createWithProfile saves the student fields', function () {
    $bsit = Program::firstWhere('code', 'BSIT');
    $wmad = $bsit->tracks->firstWhere('code', 'WMAD');
    $user = User::createWithProfile(
        ['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Password1!', 'role' => 'student'],
        ['student_number' => '21-00001', 'program_id' => $bsit->id, 'track_id' => $wmad->id, 'year_level' => '1st year'],
    );
    expect($user->student->only('student_number', 'program_id', 'track_id'))
        ->toBe(['student_number' => '21-00001', 'program_id' => $bsit->id, 'track_id' => $wmad->id]);
});

test('the database rejects a track from another program', function () {
    $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'Password1!', 'role' => 'student']);
    $bscs = Program::where('code', 'BSCS')->value('id');
    $wmad = Track::where('code', 'WMAD')->value('id');
    expect(fn () => $user->student()->update(['program_id' => $bscs, 'track_id' => $wmad]))
        ->toThrow(QueryException::class);
});

test('an evaluation must belong to a student', function () {
    $admin = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'Password1!', 'role' => 'admin']);
    $stall = DB::table('stalls')->insertGetId(['name' => 'S1', 'is_active' => true]);
    expect(fn () => DB::table('stall_evaluations')->insert([
        'student_id' => $admin->id, 'stall_id' => $stall, 'cleanliness' => 5, 'service' => 5, 'taste' => 5, 'price' => 5,
    ]))->toThrow(QueryException::class);
});

test('deleting a user removes their sessions and reset token', function () {
    $user = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'Password1!', 'role' => 'staff']);
    DB::table('sessions')->insert(['id' => 's1', 'user_id' => $user->id, 'payload' => '', 'last_activity' => 0]);
    DB::table('password_reset_tokens')->insert(['email' => 'a@example.com', 'token' => 't']);
    $user->delete();
    expect(DB::table('sessions')->count())->toBe(0)
        ->and(DB::table('password_reset_tokens')->count())->toBe(0);
});

test('assigning a staff member saves the stall on the staff table', function () {
    $admin = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'Password1!', 'role' => 'admin']);
    $staff = User::create(['name' => 'S', 'email' => 's@example.com', 'password' => 'Password1!', 'role' => 'staff']);
    $stall = DB::table('stalls')->insertGetId(['name' => 'S1', 'is_active' => true]);
    $this->actingAs($admin)->post(route('admin.staff.assign'), ['staff_id' => $staff->id, 'stall_id' => $stall]);
    expect(Staff::find($staff->id)->stall_id)->toBe($stall);
});

test('deleting a stall leaves its staff unassigned', function () {
    $stall = DB::table('stalls')->insertGetId(['name' => 'S1', 'is_active' => true]);
    $staff = User::createWithProfile(['name' => 'S', 'email' => 's@example.com', 'password' => 'Password1!', 'role' => 'staff'], ['stall_id' => $stall]);
    DB::table('stalls')->delete($stall);
    expect(Staff::find($staff->id))->not->toBeNull()
        ->and(Staff::find($staff->id)->stall_id)->toBeNull();
});
