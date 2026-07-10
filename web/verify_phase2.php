<?php

// Bootstrap Laravel properly
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Device;
use App\Models\DetectionEvent;
use App\Models\DeviceCommand;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\EventType;
use App\Enums\TremorLevel;
use App\Enums\CommandStatus;
use App\Enums\CommandType;
use App\Enums\DeviceStatus;

$pass = 0;
$fail = 0;

function check(string $label, bool $result): void {
    global $pass, $fail;
    if ($result) {
        echo "  PASS: {$label}" . PHP_EOL;
        $pass++;
    } else {
        echo "  FAIL: {$label}" . PHP_EOL;
        $fail++;
    }
}

echo PHP_EOL . "=== Phase 2 Automated Verification ===" . PHP_EOL . PHP_EOL;

// Row counts
check('User count = 4',            User::count() === 4);
check('Patient count = 5',         Patient::count() === 5);
check('Device count = 5',          Device::count() === 5);
check('DetectionEvent count = 23', DetectionEvent::count() === 23);
check('DeviceCommand count = 2',   DeviceCommand::count() === 2);

// User role enum
$doc = User::where('email', 'dr.sarah.ahmed@parkinson-monitor.test')->first();
check('User role cast to UserRole enum',         $doc->role instanceof UserRole);
check('Doctor role label = Doctor',              $doc->role->label() === 'Doctor');
check('isDoctor() returns true for doctor',      $doc->isDoctor());
check('isCaregiver() returns false for doctor',  !$doc->isCaregiver());

$cg = User::where('email', 'm.santos@parkinson-monitor.test')->first();
check('Caregiver role cast correctly',  $cg->role === UserRole::Caregiver);
check('isCaregiver() = true',           $cg->isCaregiver());

// Patient relationships
$patient = Patient::with(['devices', 'detectionEvents'])->where('patient_code', 'PKN-0003')->first();
check('Patient::devices relationship returns 1 device',          $patient->devices->count() === 1);
check('Patient::detectionEvents relationship returns 6 events',  $patient->detectionEvents->count() === 6);
check('Patient status cast to PatientStatus enum',               $patient->status instanceof \App\Enums\PatientStatus);
check('Patient isActive() = true',                               $patient->isActive());

// Device relationships
$device = Device::with(['patient', 'commands'])->where('device_uid', 'ESP32-A1B2C3D4')->first();
check('Device::patient relationship returns correct patient',  $device->patient->full_name === 'Ahmad Bin Rosli');
check('Device::patient is Patient instance',                   $device->patient instanceof Patient);
check('api_token is hidden from toArray()',                    !array_key_exists('api_token', $device->toArray()));
check('Device status cast to DeviceStatus enum',              $device->status instanceof DeviceStatus);
check('Device isOnline() for active device',                  $device->isOnline());

// DetectionEvent FOG
$fogEvent = DetectionEvent::where('event_type', 'fog')->first();
check('FOG event_type cast to EventType::Fog',   $fogEvent->event_type === EventType::Fog);
check('FOG isFog() = true',                      $fogEvent->isFog());
check('FOG isTremor() = false',                  !$fogEvent->isTremor());
check('FOG tremor_level is null',                is_null($fogEvent->tremor_level));
check('FOG cueing_activated = true',             $fogEvent->cueing_activated === true);
check('FOG device() returns Device instance',    $fogEvent->device instanceof Device);
check('FOG patient() returns Patient instance',  $fogEvent->patient instanceof Patient);

// DetectionEvent Tremor
$tremorEvent = DetectionEvent::where('event_type', 'tremor')->where('tremor_level', 3)->first();
check('Severe tremor_level cast to TremorLevel::Severe',  $tremorEvent->tremor_level === TremorLevel::Severe);
check('TremorLevel::Severe label = Severe',               $tremorEvent->tremor_level->label() === 'Severe');
check('Tremor isTremor() = true',                         $tremorEvent->isTremor());
check('Tremor cueing_activated = false',                  $tremorEvent->cueing_activated === false);

// DeviceCommand
$pendingCmd = DeviceCommand::where('status', 'pending')->first();
check('Pending status cast to CommandStatus::Pending',       $pendingCmd->status === CommandStatus::Pending);
check('isPending() = true',                                  $pendingCmd->isPending());
check('acknowledged_at is null for pending',                 is_null($pendingCmd->acknowledged_at));
check('command_type cast to CommandType::StopCueing',        $pendingCmd->command_type === CommandType::StopCueing);
check('DeviceCommand::device() returns Device instance',     $pendingCmd->device instanceof Device);

$ackedCmd = DeviceCommand::where('status', 'acknowledged')->first();
check('Acknowledged cmd isAcknowledged() = true',    $ackedCmd->isAcknowledged());
check('acknowledged_at is set',                      $ackedCmd->acknowledged_at !== null);
check('Response time > 0 seconds',                   $ackedCmd->response_time_seconds > 0);
check('Issuer relationship returns User instance',   $ackedCmd->issuer instanceof User);

// UUID uniqueness
$uuids = DetectionEvent::pluck('event_uuid')->toArray();
check('All 23 event_uuids are unique', count($uuids) === count(array_unique($uuids)));

// Metadata
$eventWithMeta = DetectionEvent::whereNotNull('metadata')->first();
check('metadata cast to PHP array',                    is_array($eventWithMeta->metadata));
check('metadata contains firmware_version key',        isset($eventWithMeta->metadata['firmware_version']));
check('metadata contains sensor_placement key',        isset($eventWithMeta->metadata['sensor_placement']));

// FOG count vs Tremor count
$fogCount    = DetectionEvent::where('event_type', 'fog')->count();
$tremorCount = DetectionEvent::where('event_type', 'tremor')->count();
check('Has both FOG and Tremor events',   $fogCount > 0 && $tremorCount > 0);
check('FOG + Tremor = 23 total',         ($fogCount + $tremorCount) === 23);

// Tremor levels distribution
$mildCount     = DetectionEvent::where('tremor_level', TremorLevel::Mild->value)->count();
$moderateCount = DetectionEvent::where('tremor_level', TremorLevel::Moderate->value)->count();
$severeCount   = DetectionEvent::where('tremor_level', TremorLevel::Severe->value)->count();
check('Mild tremor events exist (level 1)',     $mildCount > 0);
check('Moderate tremor events exist (level 2)', $moderateCount > 0);
check('Severe tremor events exist (level 3)',   $severeCount > 0);

echo PHP_EOL;
echo "=== Results: {$pass} passed, {$fail} failed ===" . PHP_EOL . PHP_EOL;
