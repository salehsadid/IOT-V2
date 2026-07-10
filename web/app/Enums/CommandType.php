<?php

namespace App\Enums;

/**
 * Type of command issued to an ESP32 device.
 *
 * Commands flow: Doctor/Caregiver → Laravel → (polled by) ESP32.
 *
 * StopCueing — instructs the device to immediately cease vibration motor cueing.
 *
 * Additional command types (e.g., start_cueing, restart_device) may be added
 * in future phases without requiring schema redesign.
 */
enum CommandType: string
{
    case StopCueing = 'stop_cueing';

    public function label(): string
    {
        return match($this) {
            CommandType::StopCueing => 'Stop Cueing',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
