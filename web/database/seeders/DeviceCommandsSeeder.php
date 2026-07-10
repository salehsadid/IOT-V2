<?php

namespace Database\Seeders;

use App\Enums\CommandStatus;
use App\Enums\CommandType;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds sample device commands.
 *
 * Creates:
 * - 1 pending stop_cueing command (not yet polled by device)
 * - 1 acknowledged stop_cueing command (completed with response time)
 *
 * These demonstrate the full command lifecycle for development purposes.
 */
class DeviceCommandsSeeder extends Seeder
{
    public function run(): void
    {
        // Use the active devices for demo commands
        $activeDevice1 = Device::where('device_uid', 'ESP32-A1B2C3D4')->first();
        $activeDevice2 = Device::where('device_uid', 'ESP32-E5F6A7B8')->first();

        $caregiver = User::where('email', 'm.santos@parkinson-monitor.test')->first();
        $doctor    = User::where('email', 'dr.sarah.ahmed@parkinson-monitor.test')->first();

        if (! $activeDevice1 || ! $caregiver) {
            $this->command->warn('  ⚠ Required device or user not found. Skipping commands seeder.');
            return;
        }

        // --- Command 1: Pending stop_cueing ---
        // Simulates a caregiver pressing "Stop Cueing" that the device hasn't polled yet.
        DeviceCommand::firstOrCreate(
            [
                'device_id'    => $activeDevice1->id,
                'command_type' => CommandType::StopCueing->value,
                'status'       => CommandStatus::Pending->value,
            ],
            [
                'issued_by'       => $caregiver->id,
                'payload'         => null,
                'issued_at'       => now()->subMinutes(2),
                'acknowledged_at' => null,
            ]
        );

        // --- Command 2: Acknowledged stop_cueing ---
        // Simulates a completed command from a previous FOG episode.
        if ($activeDevice2 && $doctor) {
            $issuedAt       = Carbon::now()->subHours(3)->subMinutes(15);
            $acknowledgedAt = $issuedAt->copy()->addSeconds(7);

            DeviceCommand::firstOrCreate(
                [
                    'device_id'    => $activeDevice2->id,
                    'command_type' => CommandType::StopCueing->value,
                    'status'       => CommandStatus::Acknowledged->value,
                ],
                [
                    'issued_by'       => $doctor->id,
                    'payload'         => null,
                    'issued_at'       => $issuedAt,
                    'acknowledged_at' => $acknowledgedAt,
                ]
            );
        }

        $this->command->info('  ✔ Device commands seeded: 1 pending, 1 acknowledged.');
    }
}
