<?php

namespace App\Models;

use App\Enums\EventType;
use App\Enums\TremorLevel;
use Database\Factories\DetectionEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a single confirmed detection event stored by the Laravel server.
 *
 * ARCHITECTURE REMINDER:
 * The ESP32 makes ALL detection and classification decisions.
 * Laravel receives confirmed events and stores them. It does NOT re-analyse data.
 *
 * event_uuid guarantees idempotency: duplicate submissions from the ESP32
 * (e.g. due to network retries) can be safely detected and ignored.
 */
class DetectionEvent extends Model
{
    /** @use HasFactory<DetectionEventFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_uuid',
        'patient_id',
        'device_id',
        'event_type',
        'tremor_level',
        'device_detected_at',
        'server_received_at',
        'cueing_activated',
        'cueing_stopped_at',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_type'         => EventType::class,
            'tremor_level'       => TremorLevel::class,
            'device_detected_at' => 'datetime',
            'server_received_at' => 'datetime',
            'cueing_activated'   => 'boolean',
            'cueing_stopped_at'  => 'datetime',
            'metadata'           => 'array',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The patient associated with this event.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * The device that reported this event.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isTremor(): bool
    {
        return $this->event_type === EventType::Tremor;
    }

    public function isFog(): bool
    {
        return $this->event_type === EventType::Fog;
    }

    public function isCueingActive(): bool
    {
        return $this->cueing_activated && $this->cueing_stopped_at === null;
    }

    /**
     * Duration of cueing in seconds, or null if cueing was not stopped yet.
     */
    public function getCueingDurationSecondsAttribute(): ?int
    {
        if (! $this->cueing_activated || ! $this->cueing_stopped_at) {
            return null;
        }

        return (int) $this->device_detected_at->diffInSeconds($this->cueing_stopped_at);
    }
}
