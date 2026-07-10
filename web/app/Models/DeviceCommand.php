<?php

namespace App\Models;

use App\Enums\CommandStatus;
use App\Enums\CommandType;
use Database\Factories\DeviceCommandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCommand extends Model
{
    /** @use HasFactory<DeviceCommandFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'device_id',
        'issued_by',
        'command_type',
        'status',
        'payload',
        'issued_at',
        'acknowledged_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'command_type'    => CommandType::class,
            'status'          => CommandStatus::class,
            'payload'         => 'array',
            'issued_at'       => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The device this command targets.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * The user who issued this command (may be null for system-generated commands).
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === CommandStatus::Pending;
    }

    public function isAcknowledged(): bool
    {
        return $this->status === CommandStatus::Acknowledged;
    }

    /**
     * Time between command issue and device acknowledgement, in seconds.
     * Returns null if the command has not been acknowledged.
     */
    public function getResponseTimeSecondsAttribute(): ?int
    {
        if (! $this->acknowledged_at) {
            return null;
        }

        return (int) $this->issued_at->diffInSeconds($this->acknowledged_at);
    }
}
