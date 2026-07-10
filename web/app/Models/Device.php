<?php

namespace App\Models;

use App\Enums\DeviceStatus;
use Database\Factories\DeviceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    /** @use HasFactory<DeviceFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'device_uid',
        'device_name',
        'api_token',
        'firmware_version',
        'last_seen_at',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * The api_token must never be exposed in JSON responses or logs.
     *
     * @var list<string>
     */
    protected $hidden = [
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'status'       => DeviceStatus::class,
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The patient this device is assigned to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Detection events reported by this device.
     */
    public function detectionEvents(): HasMany
    {
        return $this->hasMany(DetectionEvent::class);
    }

    /**
     * Remote commands issued to this device.
     */
    public function commands(): HasMany
    {
        return $this->hasMany(DeviceCommand::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::Active;
    }

    /**
     * Returns true if the device has checked in within the last N minutes.
     */
    public function hasRecentHeartbeat(int $minutes = 5): bool
    {
        return $this->last_seen_at?->isAfter(now()->subMinutes($minutes)) ?? false;
    }
}
