<?php

namespace App\Enums;

/**
 * Lifecycle status of a device command.
 *
 * Pending      — Command created, not yet delivered to device.
 * Sent         — Command delivered to device (device polled and received it).
 * Acknowledged — Device confirmed successful execution.
 * Failed       — Command could not be delivered or device reported failure.
 */
enum CommandStatus: string
{
    case Pending      = 'pending';
    case Sent         = 'sent';
    case Acknowledged = 'acknowledged';
    case Failed       = 'failed';

    public function label(): string
    {
        return match($this) {
            CommandStatus::Pending      => 'Pending',
            CommandStatus::Sent         => 'Sent',
            CommandStatus::Acknowledged => 'Acknowledged',
            CommandStatus::Failed       => 'Failed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
