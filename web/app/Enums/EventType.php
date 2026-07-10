<?php

namespace App\Enums;

/**
 * Type of confirmed detection event reported by the ESP32.
 *
 * IMPORTANT ARCHITECTURE NOTE:
 * Detection decisions are made EXCLUSIVELY by the ESP32 firmware.
 * Laravel only receives and stores confirmed events — it never analyses sensor data.
 *
 * Tremor — Confirmed hand/wrist tremor episode (classification: see TremorLevel).
 * Fog    — Confirmed Freezing of Gait episode (triggers vibration cueing on device).
 */
enum EventType: string
{
    case Tremor = 'tremor';
    case Fog    = 'fog';

    public function label(): string
    {
        return match($this) {
            EventType::Tremor => 'Tremor',
            EventType::Fog    => 'Freezing of Gait',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
