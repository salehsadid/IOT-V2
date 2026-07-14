<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatusController extends Controller
{
    public function live(Request $request)
    {
        // Hardcoded for single-patient Phase 14 demo
        $deviceId = 'ESP32-A1B2C3D4';
        
        $status = Cache::get("device_status_{$deviceId}", [
            'hand_ok'      => false,
            'leg_ok'       => false,
            'tremor_level' => 0,
            'fog_active'   => false,
            'last_seen'    => 0
        ]);

        // Device is offline if we haven't seen a heartbeat in 15 seconds
        $isOnline = (now()->timestamp - $status['last_seen']) < 15;

        return response()->json([
            'online' => $isOnline,
            'status' => $status
        ]);
    }
}
