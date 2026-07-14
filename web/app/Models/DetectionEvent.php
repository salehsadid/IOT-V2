<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetectionEvent extends Model
{
    protected $fillable = [
        'device_id',
        'event_type',
        'start_level',
        'max_level',
        'duration_ms',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'start_level' => 'integer',
        'max_level'  => 'integer',
        'duration_ms' => 'integer',
    ];

    public function isTremor(): bool
    {
        return strtoupper($this->event_type) === 'TREMOR';
    }

    public function isFog(): bool
    {
        return strtoupper($this->event_type) === 'FOG';
    }
}
