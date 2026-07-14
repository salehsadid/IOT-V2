<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetectionEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id'   => 'required|string',
            'event_type'  => 'required|string|in:TREMOR,FOG',
            'start_level' => 'nullable|integer',
            'max_level'   => 'nullable|integer',
            'duration_ms' => 'required|integer',
        ]);

        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subMilliseconds($validated['duration_ms']);

        $event = DetectionEvent::create([
            'device_id'   => $validated['device_id'],
            'event_type'  => strtoupper($validated['event_type']),
            'start_level' => $validated['start_level'] ?? null,
            'max_level'   => $validated['max_level'] ?? null,
            'duration_ms' => $validated['duration_ms'],
            'start_time'  => $startTime,
            'end_time'    => $endTime,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Event logged successfully',
            'data' => $event
        ], 201);
    }
}
