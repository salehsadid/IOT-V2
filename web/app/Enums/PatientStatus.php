<?php

namespace App\Enums;

/**
 * Status of a patient record in the system.
 */
enum PatientStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            PatientStatus::Active   => 'Active',
            PatientStatus::Inactive => 'Inactive',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
