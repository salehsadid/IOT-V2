<?php

namespace Database\Factories;

use App\Enums\DeviceStatus;
use App\Models\Device;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // device_uid: permanent hardware identifier (never use DB id publicly)
        // Format: ESP32-XXXXXXXX (8 uppercase hex chars = 4-byte ID space)
        $deviceUid = 'ESP32-' . strtoupper(substr(md5(Str::random(16)), 0, 8));

        return [
            'patient_id'       => Patient::factory(),
            'device_uid'       => $deviceUid,
            'device_name'      => 'Parkinson Monitor Unit',
            // Str::random(64) generates a cryptographically random 64-char token.
            // Column is sized 100 chars to accommodate future SHA-256 hashing.
            'api_token'        => Str::random(64),
            'firmware_version' => '1.0.0',
            'last_seen_at'     => null,
            'status'           => DeviceStatus::Offline->value,
        ];
    }

    /**
     * Mark the device as active and recently seen.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => DeviceStatus::Active->value,
            'last_seen_at' => now()->subMinutes(fake()->numberBetween(1, 4)),
        ]);
    }

    /**
     * Mark the device as offline with a stale last_seen_at.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => DeviceStatus::Offline->value,
            'last_seen_at' => fake()->dateTimeBetween('-2 hours', '-10 minutes'),
        ]);
    }
}
