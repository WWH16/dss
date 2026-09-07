<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:admin
                            {--name= : The admin name}
                            {--email= : The admin email}
                            {--password= : The admin password}';

    /**
     * The console command description.
     */
    protected $description = 'Create an administrator account for the DSS portal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('  ┌─────────────────────────────────────────┐');
        $this->info('  │  DSS Portal — Create Admin Account      │');
        $this->info('  └─────────────────────────────────────────┘');
        $this->info('');

        $name     = $this->option('name')     ?: $this->ask('Full name');
        $email    = $this->option('email')    ?: $this->ask('Email address');
        $password = $this->option('password') ?: $this->secret('Password (min 8 chars, mixed case, number, symbol)');

        // Validate inputs
        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => [
                    'required',
                    Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
                ],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("  ✗ {$error}");
            }
            return self::FAILURE;
        }

        // Check if any admin already exists
        $existingAdmins = User::where('role', 'admin')->count();
        if ($existingAdmins > 0) {
            $this->warn("  ⚠  There are already {$existingAdmins} admin account(s).");
            if (!$this->confirm('  Do you still want to create another admin?', false)) {
                $this->info('  Cancelled.');
                return self::SUCCESS;
            }
        }

        $user = User::create([
            'role'              => 'admin',
            'name'              => trim($name),
            'email'             => trim($email),
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('');
        $this->info("  ✓ Admin account created successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID',    $user->id],
                ['Name',  $user->name],
                ['Email', $user->email],
                ['Role',  'admin'],
            ]
        );
        $this->info('');
        $this->info('  You can now log in at /login with these credentials.');
        $this->info('');

        return self::SUCCESS;
    }
}
