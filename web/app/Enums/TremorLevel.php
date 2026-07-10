<?php

namespace App\Enums;

/**
 * Severity classification of a confirmed tremor event.
 *
 * The ESP32 firmware classifies tremor level using rule-based threshold logic.
 * Laravel stores this value as reported; it does not compute or recalculate it.
 *
 * None     (0) — Tremor below detection threshold (informational; rarely stored).
 * Mild     (1) — Low-amplitude tremor detected.
 * Moderate (2) — Medium-amplitude tremor detected.
 * Severe   (3) — High-amplitude tremor detected.
 *
 * Only applicable when EventType::Tremor. Null for FOG events.
 */
enum TremorLevel: int
{
    case None     = 0;
    case Mild     = 1;
    case Moderate = 2;
    case Severe   = 3;

    public function label(): string
    {
        return match($this) {
            TremorLevel::None     => 'None',
            TremorLevel::Mild     => 'Mild',
            TremorLevel::Moderate => 'Moderate',
            TremorLevel::Severe   => 'Severe',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
