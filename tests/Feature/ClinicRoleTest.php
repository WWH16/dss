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
