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
        $assignments = [
            'PKN-0001' => 'ESP32-A1B2C3D4',
            'PKN-0002' => 'ESP32-E5F6A7B8',
            'PKN-0003' => 'ESP32-C9D0E1F2',
            'PKN-0004' => 'ESP32-G3H4I5J6',
            'PKN-0005' => 'ESP32-K7L8M9N0',
        ];

        $eventPlan = [
            'PKN-0001' => [
                ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 5000, 'days_ago' => 13],
                ['type' => 'TREMOR', 'max_level' => 2, 'duration' => 12000, 'days_ago' => 11],
                ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 3000, 'days_ago' => 9],
                ['type' => 'TREMOR', 'max_level' => 3, 'duration' => 25000, 'days_ago' => 7],
            ],
            'PKN-0002' => [
                ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 4500, 'days_ago' => 12],
                ['type' => 'FOG',    'max_level' => null, 'duration' => 45000, 'days_ago' => 10],
                ['type' => 'TREMOR', 'max_level' => 2, 'duration' => 8000, 'days_ago' => 8],
                ['type' => 'FOG',    'max_level' => null, 'duration' => 62000, 'days_ago' => 5],
            ],
            'PKN-0003' => [
                ['type' => 'FOG',    'max_level' => null, 'duration' => 90000, 'days_ago' => 14],
                ['type' => 'FOG',    'max_level' => null, 'duration' => 120000, 'days_ago' => 12],
                ['type' => 'TREMOR', 'max_level' => 2, 'duration' => 10000, 'days_ago' => 10],
                ['type' => 'FOG',    'max_level' => null, 'duration' => 75000, 'days_ago' => 7],
                ['type' => 'TREMOR', 'max_level' => 3, 'duration' => 15000, 'days_ago' => 1],
            ],
            'PKN-0004' => [
                ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 2000, 'days_ago' => 10],
                ['type' => 'TREMOR', 'max_level' => 1, 'duration' => 3500, 'days_ago' => 6],
            ],
            'PKN-0005' => [
                ['type' => 'TREMOR', 'max_level' => 2, 'duration' => 11000, 'days_ago' => 11],
                ['type' => 'FOG',    'max_level' => null, 'duration' => 55000, 'days_ago' => 9],
                ['type' => 'TREMOR', 'max_level' => 3, 'duration' => 21000, 'days_ago' => 6],
            ],
        ];

        $total = 0;

        foreach ($eventPlan as $patientCode => $events) {
            $device_id = $assignments[$patientCode];

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
        }

        $this->command->info("  ✔ Detection events seeded: {$total} events across 5 patients.");
    }
}
