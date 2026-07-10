<?php

namespace Database\Factories;

use App\Enums\EventType;
use App\Enums\TremorLevel;
use App\Models\DetectionEvent;
use App\Models\Device;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<DetectionEvent>
 */
class DetectionEventFactory extends Factory
{
    /**
     * Define the model's default state.
     * Defaults to a tremor event for convenience; use fog() state for FOG events.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $detectedAt = fake()->dateTimeBetween('-30 days', 'now');
        // Server receives the event 1–8 seconds after device detection (network latency)
        $receivedAt = Carbon::instance($detectedAt)->addSeconds(fake()->numberBetween(1, 8));

        return [
            'event_uuid'         => (string) Str::uuid(),
            'patient_id'         => Patient::factory(),
            'device_id'          => Device::factory(),
            'event_type'         => EventType::Tremor->value,
            'tremor_level'       => TremorLevel::Mild->value,
            'device_detected_at' => $detectedAt,
            'server_received_at' => $receivedAt,
            'cueing_activated'   => false,
            'cueing_stopped_at'  => null,
            'metadata'           => null,
        ];
    }

    /**
     * Tremor event with a specific level.
     */
    public function tremor(TremorLevel $level = TremorLevel::Mild): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type'   => EventType::Tremor->value,
            'tremor_level' => $level->value,
            'cueing_activated' => false,
            'cueing_stopped_at' => null,
        ]);
    }

    /**
     * FOG event — cueing is activated; tremor_level is null.
     */
    public function fog(): static
    {
        $detectedAt = fake()->dateTimeBetween('-30 days', 'now');
        $cueingStoppedAt = Carbon::instance($detectedAt)
            ->addSeconds(fake()->numberBetween(15, 180));

        return $this->state(fn (array $attributes) => [
            'event_type'        => EventType::Fog->value,
            'tremor_level'      => null,
            'cueing_activated'  => true,
            'cueing_stopped_at' => $cueingStoppedAt,
        ]);
    }

    /**
     * FOG event where cueing is still active (not yet stopped).
     */
    public function fogActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type'        => EventType::Fog->value,
            'tremor_level'      => null,
            'cueing_activated'  => true,
            'cueing_stopped_at' => null,
        ]);
    }

    /**
     * Attach generic metadata as might be reported by a future firmware version.
     */
    public function withMetadata(array $metadata = []): static
    {
        $defaults = [
            'firmware_version' => '1.0.0',
            'rms_value'        => round(fake()->randomFloat(4, 0.1, 5.0), 4),
            'motion_score'     => round(fake()->randomFloat(2, 0.0, 100.0), 2),
        ];

        return $this->state(fn (array $attributes) => [
            'metadata' => array_merge($defaults, $metadata),
        ]);
    }
}
