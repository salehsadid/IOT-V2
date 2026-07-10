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
        // Map patient_code to device_uid for clarity
        $assignments = [
            'PKN-0001' => 'ESP32-A1B2C3D4',
            'PKN-0002' => 'ESP32-E5F6A7B8',
            'PKN-0003' => 'ESP32-C9D0E1F2',
            'PKN-0004' => 'ESP32-G3H4I5J6',
            'PKN-0005' => 'ESP32-K7L8M9N0',
        ];

        // Event definitions per patient — realistic clinical scenarios
        $eventPlan = [
            'PKN-0001' => [
                // Ahmad — moderate tremor patient
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 13, 'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 11, 'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 9,  'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Severe,    'days_ago' => 7,  'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 3,  'cue' => false],
            ],
            'PKN-0002' => [
                // Fatimah — tremor + FOG
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 12, 'cue' => false],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 10, 'cue' => true,  'cue_duration' => 45],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 8,  'cue' => false],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 5,  'cue' => true,  'cue_duration' => 62],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 2,  'cue' => false],
            ],
            'PKN-0003' => [
                // Thomas — advanced, frequent FOG
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 14, 'cue' => true,  'cue_duration' => 90],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 12, 'cue' => true,  'cue_duration' => 120],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 10, 'cue' => false],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 7,  'cue' => true,  'cue_duration' => 75],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 4,  'cue' => true,  'cue_stopped' => false], // still active
                ['type' => EventType::Tremor, 'level' => TremorLevel::Severe,    'days_ago' => 1,  'cue' => false],
            ],
            'PKN-0004' => [
                // Rosmah — early stage, mild tremor only
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 10, 'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 6,  'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Mild,     'days_ago' => 2,  'cue' => false],
            ],
            'PKN-0005' => [
                // Lim — moderate tremor, occasional FOG
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 11, 'cue' => false],
                ['type' => EventType::Fog,    'level' => null,                  'days_ago' => 9,  'cue' => true,  'cue_duration' => 55],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Severe,    'days_ago' => 6,  'cue' => false],
                ['type' => EventType::Tremor, 'level' => TremorLevel::Moderate,  'days_ago' => 3,  'cue' => false],
            ],
        ];

        $total = 0;

        foreach ($eventPlan as $patientCode => $events) {
            $patient = Patient::where('patient_code', $patientCode)->first();
            $device  = Device::where('device_uid', $assignments[$patientCode])->first();

            if (! $patient || ! $device) {
                $this->command->warn("  ⚠ Missing patient/device for {$patientCode}. Skipping events.");
                continue;
            }

            foreach ($events as $index => $event) {
                // Spread events across the day (morning–evening hours)
                $baseHour  = 7 + ($index * 2) % 14;
                $detectedAt = Carbon::now()
                    ->subDays($event['days_ago'])
                    ->setHour($baseHour)
                    ->setMinute(rand(0, 59))
                    ->setSecond(rand(0, 59));

                $receivedAt     = $detectedAt->copy()->addSeconds(rand(1, 8));
                $cueingActivated = $event['cue'] ?? false;

                $cueingStoppedAt = null;
                if ($cueingActivated) {
                    $stopped = $event['cue_stopped'] ?? true;
                    if ($stopped && isset($event['cue_duration'])) {
                        $cueingStoppedAt = $detectedAt->copy()->addSeconds($event['cue_duration']);
                    }
                    // If cue_stopped = false, cueing is still active (null stays null)
                }

                DetectionEvent::create([
                    'event_uuid'         => (string) Str::uuid(),
                    'patient_id'         => $patient->id,
                    'device_id'          => $device->id,
                    'event_type'         => $event['type']->value,
                    'tremor_level'       => $event['level']?->value,
                    'device_detected_at' => $detectedAt,
                    'server_received_at' => $receivedAt,
                    'cueing_activated'   => $cueingActivated,
                    'cueing_stopped_at'  => $cueingStoppedAt,
                    'metadata'           => $this->buildMetadata($event['type']),
                ]);

                $total++;
            }
        }

        $this->command->info("  ✔ Detection events seeded: {$total} events across 5 patients.");
    }

    /**
     * Build realistic metadata for seeded events.
     * Includes sample values that future firmware versions may report.
     */
    private function buildMetadata(EventType $type): array
    {
        return [
            'firmware_version'  => '1.0.0',
            'rms_value'         => round(rand(100, 500) / 100, 3),
            'motion_score'      => round(rand(20, 95) / 1, 1),
            'sensor_placement'  => $type === EventType::Fog ? 'ankle' : 'wrist',
            'calibration_ver'   => 'cal-v1',
        ];
    }
}
