<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommandController extends Controller
{
    public function stopBuzzer(Request $request)
    {
        // For this single-patient Phase 14 demo, we hardcode the device ID.
        $deviceId = 'ESP32-A1B2C3D4'; 
        
        // Queue the command for the next heartbeat pickup (valid for 60 seconds)
        Cache::put("device_command_{$deviceId}", "STOP_BUZZER", 60);

        return response()->json(['status' => 'success', 'message' => 'Buzzer stop command queued.']);
    }
}
