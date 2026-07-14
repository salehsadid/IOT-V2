<?php

namespace Database\Seeders;

use App\Enums\PatientStatus;
use App\Models\Patient;
use Illuminate\Database\Seeder;

/**
 * Seeds 5 realistic Parkinson's patient records.
 *
 * patient_code follows the PKN-XXXX convention.
 * No assigned_doctor_id or assigned_caregiver_id — these will be added in Phase 3.
 */
class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        $patientData = [
            'patient_code'      => 'PKN-0001',
            'full_name'         => 'Saleh Sadid Mir',
            'gender'            => 'male',
            'date_of_birth'     => '1950-01-01',
            'phone'             => '+8801234567890',
            'emergency_contact' => 'Emergency Contact — +8801987654321',
            'status'            => PatientStatus::Active->value,
            'notes'             => 'Demo Patient for Phase 14.',
        ];

        Patient::firstOrCreate(
            ['patient_code' => $patientData['patient_code']],
            $patientData
        );

        $this->command->info('  ✔ Patient seeded: Saleh Sadid Mir');
    }
}
