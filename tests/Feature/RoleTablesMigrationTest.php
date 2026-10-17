<?php

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(DatabaseMigrations::class);

function rollBackRoleTables(): void
{
    $steps = DB::table('migrations')->where('migration', '>=', '2026_10_17_000001')->count();
    test()->artisan('migrate:rollback', ['--step' => $steps])->assertSuccessful();
}

test('existing users are copied into their role tables', function () {
    rollBackRoleTables();
    $stall = DB::table('stalls')->insertGetId(['name' => 'S1', 'is_active' => true]);
    $legacy = fn (array $a) => DB::table('users')->insertGetId($a + ['name' => 'X', 'password' => 'x']);
    $ana = $legacy(['email' => 'a@x.com', 'role' => 'student', 'course' => ' bsit ', 'track' => 'wmad', 'student_number' => '21-0001', 'year_level' => '2nd year']);
    $old = $legacy(['email' => 'b@x.com', 'role' => 'student', 'course' => 'BSHM', 'track' => 'X', 'student_number' => '']);
    $ben = $legacy(['email' => 'c@x.com', 'role' => 'staff', 'stall_id' => $stall]);
    $adm = $legacy(['email' => 'd@x.com', 'role' => 'admin']);
    $nurse = $legacy(['email' => 'e@x.com', 'role' => 'clinic']);

    $this->artisan('migrate')->assertSuccessful();

    $bsit = Program::firstWhere('code', 'BSIT');
    expect((array) DB::table('students')->where('user_id', $ana)->first(['program_id', 'track_id', 'student_number', 'year_level']))
        ->toBe(['program_id' => $bsit->id, 'track_id' => $bsit->tracks->firstWhere('code', 'WMAD')->id, 'student_number' => '21-0001', 'year_level' => '2nd year']);
    expect((array) DB::table('students')->where('user_id', $old)->first(['program_id', 'track_id', 'student_number']))
        ->toBe(['program_id' => null, 'track_id' => null, 'student_number' => null]);
    expect(DB::table('staff')->where('user_id', $ben)->value('stall_id'))->toBe($stall);
    expect(DB::table('admins')->where('user_id', $adm)->exists())->toBeTrue();
    expect(DB::table('clinics')->where('user_id', $nurse)->exists())->toBeTrue();
});

test('orphan sessions and reset tokens are removed before the keys are added', function () {
    rollBackRoleTables();
    DB::table('sessions')->insert(['id' => 's1', 'user_id' => 999, 'payload' => '', 'last_activity' => 0]);
    DB::table('password_reset_tokens')->insert(['email' => 'ghost@example.com', 'token' => 't']);

    $this->artisan('migrate')->assertSuccessful();

    expect(DB::table('sessions')->count())->toBe(0)
        ->and(DB::table('password_reset_tokens')->count())->toBe(0);
});

test('users no longer carries role columns', function () {
    foreach (['course', 'track', 'year_level', 'student_number', 'stall_id'] as $column) {
        expect(Schema::hasColumn('users', $column))->toBeFalse();
    }
});

test('rolling back the drop copies the values back onto users', function () {
    $bsit = Program::firstWhere('code', 'BSIT');
    $user = User::createWithProfile(['name' => 'A', 'email' => 'a@x.com', 'password' => 'Password1!', 'role' => 'student'],
        ['student_number' => '21-1', 'program_id' => $bsit->id, 'track_id' => $bsit->tracks->firstWhere('code', 'NS')->id]);
    $this->artisan('migrate:rollback', ['--step' => 1])->assertSuccessful();
    expect((array) DB::table('users')->where('id', $user->id)->first(['course', 'track', 'student_number']))
        ->toBe(['course' => 'BSIT', 'track' => 'NS', 'student_number' => '21-1']);
});
