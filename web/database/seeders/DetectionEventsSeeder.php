<?php

namespace Database\Seeders;

use App\Enums\EventType;
use App\Enums\TremorLevel;
use App\Models\DetectionEvent;
use App\Models\Device;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeds realistic detection events for all 5 patients.
 *
 * Generates a mix of:
 * - Tremor events at levels 1, 2, and 3
 * - FOG events with cueing activated (some with stopped cueing, one still active)
 *
 * Timestamps are spread over the last 14 days for realistic-looking logs.
 *
 * ARCHITECTURE NOTE:
 * In the real system, these events are created by the Laravel API when the ESP32
 * POSTs a confirmed event. The seeder simulates this for development/demo purposes.
 */
class DetectionEventsSeeder extends Seeder
{
    public function run(): void
    {
        $device_id = 'ESP32-A1B2C3D4';

        $events = [
            ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 5000, 'days_ago' => 13],
            ['type' => 'FOG',    'max_level' => null, 'duration' => 12000, 'days_ago' => 11],
            ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 3000, 'days_ago' => 9],
            ['type' => 'TREMOR', 'max_level' => 3, 'duration' => 25000, 'days_ago' => 7],
            ['type' => 'FOG',    'max_level' => null, 'duration' => 8000, 'days_ago' => 2],
            ['type' => 'TREMOR', 'max_level' => 2, 'duration' => 10000, 'days_ago' => 1],
        ];

        $total = 0;

        foreach ($events as $index => $event) {
            $baseHour  = 7 + ($index * 2) % 14;
            $endTime = Carbon::now()
                ->subDays($event['days_ago'])
                ->setHour($baseHour)
                ->setMinute(rand(0, 59))
                ->setSecond(rand(0, 59));
            
            $startTime = $endTime->copy()->subMilliseconds($event['duration']);

            DetectionEvent::create([
                'device_id'   => $device_id,
                'event_type'  => $event['type'],
                'start_level' => $event['type'] === 'TREMOR' ? 1 : null,
                'max_level'   => $event['max_level'],
                'duration_ms' => $event['duration'],
                'start_time'  => $startTime,
                'end_time'    => $endTime,
            ]);

            $total++;
        }

        $this->command->info("  ✔ Detection events seeded: {$total} events for Saleh.");
    }
}
