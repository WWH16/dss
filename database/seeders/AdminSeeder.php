<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the administrator account.
     *
     * Usage:
     *   php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        // CHANGED: no longer seeds clinic@gmail.com; admins create clinic accounts from Staff & Admins.
        $user = User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'role'              => 'admin',
            'name'              => 'System Administrator',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info(($user->wasRecentlyCreated ? 'Created: ' : 'Exists, skipped: ') . $user->email);
    }
}
