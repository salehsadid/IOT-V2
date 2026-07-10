<?php

namespace App\Enums;

/**
 * Represents the role of a web application user.
 *
 * Doctor    — clinical staff with full monitoring access.
 * Caregiver — support staff with monitoring and cueing control access.
 *
 * No admin role is defined in this phase.
 * Authentication and RBAC are implemented in Phase 3.
 */
enum UserRole: string
{
    case Doctor    = 'doctor';
    case Caregiver = 'caregiver';

    /**
     * Return a human-readable display label.
     */
    public function label(): string
    {
        return match($this) {
            UserRole::Doctor    => 'Doctor',
            UserRole::Caregiver => 'Caregiver',
        };
    }

    /**
     * Return all valid string values (useful for validation rules).
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
