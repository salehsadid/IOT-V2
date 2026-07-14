<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = Patient::where('patient_code', 'PKN-0001')->first();
        $age = $patient ? Carbon::parse($patient->date_of_birth)->age : 'N/A';
        
        return view('dashboard.index', compact('user', 'patient', 'age'));
    }
}
