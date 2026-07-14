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
        $patient = Patient::where('patient_code', 'PKN-0001')->first();

        if (! $patient) {
            $this->command->warn("  ⚠ Patient PKN-0001 not found. Skipping device.");
            return;
        }

        $device_uid = 'ESP32-A1B2C3D4';
        $existing = Device::where('device_uid', $device_uid)->first();

        if ($existing) {
            $this->command->line("  · Device {$device_uid} already exists. Skipping.");
            return;
        }

        $token = Str::random(64);

        Device::create([
            'patient_id'       => $patient->id,
            'device_uid'       => $device_uid,
            'device_name'      => 'Saleh Monitor Unit',
            'api_token'        => $token,
            'firmware_version' => '1.0.0',
            'last_seen_at'     => now()->subMinutes(1),
            'status'           => DeviceStatus::Active->value,
        ]);

        $this->command->line("  · {$device_uid} → token: {$token}");
        $this->command->info('  ✔ Device seeded.');
    }
}
