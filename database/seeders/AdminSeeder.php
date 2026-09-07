<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the initial administrator account.
     *
     * Only creates the admin if no admin account exists yet.
     * Credentials are pulled from .env or use secure defaults.
     *
     * Usage:
     *   php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        if (User::where('role', 'admin')->exists()) {
            $this->command->warn('An admin account already exists — skipping.');
            return;
        }

        $user = User::create([
            'role'              => 'admin',
            'name'              => env('ADMIN_NAME', 'System Administrator'),
            'email'             => env('ADMIN_EMAIL', 'admin@isu.edu.ph'),
            'password'          => Hash::make(env('ADMIN_PASSWORD', 'Admin@DSS2026!')),
            'email_verified_at' => now(),
        ]);

        $this->command->info("✓ Admin account created: {$user->email}");
    }
}
