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

        // Check for new Tremor
        if ($validated['tremor_level'] > 0 && (!$prevStatus || $prevStatus['tremor_level'] == 0)) {
            \App\Services\TelegramService::sendAlert("Patient is experiencing a Tremor! (Level {$validated['tremor_level']})");
        }

        // Check for new FOG
        if ($validated['fog_active'] && (!$prevStatus || !$prevStatus['fog_active'])) {
            \App\Services\TelegramService::sendAlert("Patient is experiencing Freezing of Gait (FOG)!");
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
