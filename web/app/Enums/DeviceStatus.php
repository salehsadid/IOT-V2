<?php

namespace App\Enums;

/**
 * Operational status of an ESP32 device.
 *
 * Active  — device is connected and operational.
 * Inactive — device has been decommissioned or disabled.
 * Offline  — device has not communicated recently (default state for new devices).
 */
enum DeviceStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Offline  = 'offline';

    public function label(): string
    {
        return match($this) {
            DeviceStatus::Active   => 'Active',
            DeviceStatus::Inactive => 'Inactive',
            DeviceStatus::Offline  => 'Offline',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
