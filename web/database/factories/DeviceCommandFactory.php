<?php

namespace Database\Factories;

use App\Enums\CommandStatus;
use App\Enums\CommandType;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<DeviceCommand>
 */
class DeviceCommandFactory extends Factory
{
    /**
     * Define the model's default state — a pending stop_cueing command.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_id'       => Device::factory(),
            'issued_by'       => User::factory(),
            'command_type'    => CommandType::StopCueing->value,
            'status'          => CommandStatus::Pending->value,
            'payload'         => null,
            'issued_at'       => now(),
            'acknowledged_at' => null,
        ];
    }

    /**
     * Command that has been delivered but not yet acknowledged.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'          => CommandStatus::Sent->value,
            'acknowledged_at' => null,
        ]);
    }

    /**
     * Command that has been acknowledged by the ESP32.
     */
    public function acknowledged(): static
    {
        $issuedAt = fake()->dateTimeBetween('-1 hour', '-5 minutes');

        return $this->state(fn (array $attributes) => [
            'status'          => CommandStatus::Acknowledged->value,
            'issued_at'       => $issuedAt,
            'acknowledged_at' => Carbon::instance($issuedAt)
                ->addSeconds(fake()->numberBetween(3, 30)),
        ]);
    }

    /**
     * Command that failed to be delivered or executed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'          => CommandStatus::Failed->value,
            'acknowledged_at' => null,
        ]);
    }

    /**
     * System-generated command (no issuing user).
     */
    public function systemGenerated(): static
    {
        return $this->state(fn (array $attributes) => [
            'issued_by' => null,
        ]);
    }
}
