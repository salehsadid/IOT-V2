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
        $users = [
            // --- Doctors ---
            [
                'name'              => 'Demo Doctor',
                'email'             => 'doctor@example.com',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Doctor->value,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Dr. Sarah Ahmed',
                'email'             => 'dr.sarah.ahmed@parkinson-monitor.test',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Doctor->value,
                'email_verified_at' => now(),
            ],
            // --- Caregivers ---
            [
                'name'              => 'Demo Caregiver',
                'email'             => 'caregiver@example.com',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Caregiver->value,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Maria Santos',
                'email'             => 'm.santos@parkinson-monitor.test',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Caregiver->value,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('  ✔ Users seeded: Demo Doctor and Caregiver added.');
    }
}
