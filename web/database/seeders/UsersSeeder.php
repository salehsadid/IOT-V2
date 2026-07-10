<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the 4 development users: 2 doctors and 2 caregivers.
 *
 * All accounts use the password: password
 * These are development/demo accounts only.
 */
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // --- Doctors ---
            [
                'name'              => 'Dr. Sarah Ahmed',
                'email'             => 'dr.sarah.ahmed@parkinson-monitor.test',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Doctor->value,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Dr. James Mwangi',
                'email'             => 'dr.james.mwangi@parkinson-monitor.test',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Doctor->value,
                'email_verified_at' => now(),
            ],
            // --- Caregivers ---
            [
                'name'              => 'Maria Santos',
                'email'             => 'm.santos@parkinson-monitor.test',
                'password'          => Hash::make('password'),
                'role'              => UserRole::Caregiver->value,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Robert Tan',
                'email'             => 'r.tan@parkinson-monitor.test',
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

        $this->command->info('  ✔ Users seeded: 2 doctors, 2 caregivers.');
    }
}
