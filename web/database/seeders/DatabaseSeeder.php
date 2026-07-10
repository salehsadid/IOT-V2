<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master database seeder — orchestrates all project seeders in dependency order.
 *
 * Execution order matters:
 *   1. Users       — no foreign key dependencies
 *   2. Patients    — no foreign key dependencies
 *   3. Devices     — depends on patients
 *   4. DetectionEvents — depends on patients + devices
 *   5. DeviceCommands  — depends on devices + users
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('  Parkinson Monitor — Database Seeder');
        $this->command->info('  =====================================');

        $this->call([
            UsersSeeder::class,
            PatientsSeeder::class,
            DevicesSeeder::class,
            DetectionEventsSeeder::class,
            DeviceCommandsSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('  ✔ All seeders completed successfully.');
        $this->command->info('');
        $this->command->info('  Demo accounts (password: password):');
        $this->command->info('    Doctor:    dr.sarah.ahmed@parkinson-monitor.test');
        $this->command->info('    Doctor:    dr.james.mwangi@parkinson-monitor.test');
        $this->command->info('    Caregiver: m.santos@parkinson-monitor.test');
        $this->command->info('    Caregiver: r.tan@parkinson-monitor.test');
    }
}
