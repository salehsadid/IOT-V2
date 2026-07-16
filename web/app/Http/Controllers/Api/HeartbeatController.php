<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HeartbeatController extends Controller
{
    public function ping(Request $request)
    {
        $validated = $request->validate([
            'device_id'    => 'required|string',
            'hand_ok'      => 'required|boolean',
            'leg_ok'       => 'required|boolean',
            'tremor_level' => 'required|integer',
            'fog_active'   => 'required|boolean',
        ]);

        $deviceId = $validated['device_id'];
        $cacheKey = "device_status_{$deviceId}";

        // Get previous status to detect transitions
        $prevStatus = Cache::get($cacheKey);

        $patient = \App\Models\Patient::first();
        $age = $patient && $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : 'Unknown';
        $name = $patient ? $patient->full_name : 'Unknown';
        $time = now()->format('Y-m-d h:i A');

        // Check for new Tremor (Level 3 only)
        if ($validated['tremor_level'] == 3 && (!$prevStatus || $prevStatus['tremor_level'] < 3)) {
            $msg = "👤 *Patient:* {$name}\n";
            $msg .= "🎂 *Age:* {$age} years\n";
            $msg .= "🫨 *Event:* SEVERE TREMOR DETECTED\n";
            $msg .= "📈 *Level:* {$validated['tremor_level']}\n";
            $msg .= "⏰ *Start Time:* {$time}";
            \App\Services\TelegramService::sendAlert($msg);
        }

        // Check for new FOG
        if ($validated['fog_active'] && (!$prevStatus || !$prevStatus['fog_active'])) {
            $msg = "👤 *Patient:* {$name}\n";
            $msg .= "🎂 *Age:* {$age} years\n";
            $msg .= "🚶 *Event:* FOG DETECTED (Freezing of Gait)\n";
            $msg .= "⏰ *Start Time:* {$time}";
            \App\Services\TelegramService::sendAlert($msg);
        }

        // Store the live status in cache for 15 seconds. If ESP32 dies, this expires.
        Cache::put($cacheKey, [
            'hand_ok'      => $validated['hand_ok'],
            'leg_ok'       => $validated['leg_ok'],
            'tremor_level' => $validated['tremor_level'],
            'fog_active'   => $validated['fog_active'],
            'last_seen'    => now()->timestamp
        ], 15);

        // Check if there is a pending command for this device
        $command = Cache::pull("device_command_{$deviceId}");

        return response()->json([
            'status'  => 'success',
            'command' => $command // e.g., "STOP_BUZZER" or null
        ]);
    }
}
