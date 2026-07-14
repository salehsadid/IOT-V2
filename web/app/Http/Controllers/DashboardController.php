<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\DetectionEvent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_code', 'PKN-0001')->first();
        $age = $patient ? Carbon::parse($patient->date_of_birth)->age : 'N/A';
        
        // Phase 14: Daily Statistics
        $today = Carbon::today();
        
        $stats = [
            'todayTremorCount' => DetectionEvent::whereDate('start_time', $today)->where('event_type', 'TREMOR')->count(),
            'todayFogCount'    => DetectionEvent::whereDate('start_time', $today)->where('event_type', 'FOG')->count(),
            'highestTremor'    => DetectionEvent::whereDate('start_time', $today)->where('event_type', 'TREMOR')->max('max_level') ?? 0,
            
            // Lifetime Statistics
            'avgTremorDuration'=> round(DetectionEvent::where('event_type', 'TREMOR')->avg('duration_ms') / 1000, 1),
            'avgFogDuration'   => round(DetectionEvent::where('event_type', 'FOG')->avg('duration_ms') / 1000, 1),
            'totalTremorEvents'=> DetectionEvent::where('event_type', 'TREMOR')->count(),
            'totalFogEvents'   => DetectionEvent::where('event_type', 'FOG')->count(),
        ];
        
        return view('dashboard.index', compact('user', 'patient', 'age', 'stats'));
    }

    public function updatePatient(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        $patient = Patient::where('patient_code', 'PKN-0001')->first();
        if ($patient) {
            $patient->update([
                'full_name' => $validated['full_name'],
                'date_of_birth' => $validated['date_of_birth'],
            ]);
            return back()->with('success', 'Patient details updated successfully!');
        }

        return back()->with('error', 'Patient not found.');
    }
}
