<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the development users for authentication testing.
 *
 * Includes the requested demo accounts:
 * - doctor@example.com
 * - caregiver@example.com
 *
 * Password for all: password
 */
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            'name'              => 'Admin',
            'email'             => 'admin@parkinson-monitor.test',
            'password'          => Hash::make('password'),
            'role'              => UserRole::Doctor->value,
            'email_verified_at' => now(),
        ];

        User::firstOrCreate(
            ['email' => $userData['email']],
            $userData
        );

        $this->command->info('  ✔ User seeded: admin@parkinson-monitor.test');
    }
}
