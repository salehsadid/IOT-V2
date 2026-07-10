<?php

namespace Database\Seeders;

use App\Enums\DeviceStatus;
use App\Models\Device;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds one ESP32 device for each patient.
 *
 * IMPORTANT:
 * - device_uid is the permanent public hardware identifier. Never use the DB id in communications.
 * - api_token is a 64-char random string (cryptographically safe via Str::random).
 *   Column is sized 100 chars to support future SHA-256 hashing without schema changes.
 * - Tokens are printed to console only during seeding for development reference.
 *   In production, tokens would be generated and handed off securely.
 */
class DevicesSeeder extends Seeder
{
    public function run(): void
    {
        $deviceData = [
            'PKN-0001' => ['device_uid' => 'ESP32-A1B2C3D4', 'device_name' => 'Ahmad Monitor Unit',   'status' => DeviceStatus::Active->value,  'firmware_version' => '1.0.0'],
            'PKN-0002' => ['device_uid' => 'ESP32-E5F6A7B8', 'device_name' => 'Fatimah Monitor Unit', 'status' => DeviceStatus::Active->value,  'firmware_version' => '1.0.0'],
            'PKN-0003' => ['device_uid' => 'ESP32-C9D0E1F2', 'device_name' => 'Thomas Monitor Unit',  'status' => DeviceStatus::Offline->value, 'firmware_version' => '1.0.0'],
            'PKN-0004' => ['device_uid' => 'ESP32-G3H4I5J6', 'device_name' => 'Rosmah Monitor Unit',  'status' => DeviceStatus::Active->value,  'firmware_version' => '1.0.0'],
            'PKN-0005' => ['device_uid' => 'ESP32-K7L8M9N0', 'device_name' => 'Lim Monitor Unit',     'status' => DeviceStatus::Offline->value, 'firmware_version' => '1.0.0'],
        ];

        foreach ($deviceData as $patientCode => $data) {
            $patient = Patient::where('patient_code', $patientCode)->first();

            if (! $patient) {
                $this->command->warn("  ⚠ Patient {$patientCode} not found. Skipping device.");
                continue;
            }

            $existing = Device::where('device_uid', $data['device_uid'])->first();

            if ($existing) {
                $this->command->line("  · Device {$data['device_uid']} already exists. Skipping.");
                continue;
            }

            $token = Str::random(64);

            Device::create([
                'patient_id'       => $patient->id,
                'device_uid'       => $data['device_uid'],
                'device_name'      => $data['device_name'],
                'api_token'        => $token,
                'firmware_version' => $data['firmware_version'],
                'last_seen_at'     => in_array($data['status'], [DeviceStatus::Active->value])
                    ? now()->subMinutes(rand(1, 3))
                    : now()->subHours(rand(2, 48)),
                'status'           => $data['status'],
            ]);

            // Print token ONCE during seeding (for development reference only).
            $this->command->line("  · {$data['device_uid']} → token: {$token}");
        }

        $this->command->info('  ✔ Devices seeded: 1 device per patient (5 total).');
    }
}
