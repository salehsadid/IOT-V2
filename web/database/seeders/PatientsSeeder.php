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
        $patients = [
            [
                'patient_code'      => 'PKN-0001',
                'full_name'         => 'Ahmad Bin Rosli',
                'gender'            => 'male',
                'date_of_birth'     => '1948-03-15',
                'phone'             => '+60123456789',
                'emergency_contact' => 'Siti Binti Rosli — +60187654321',
                'status'            => PatientStatus::Active->value,
                'notes'             => 'Diagnosed 2018. Moderate tremor in right hand. Takes Levodopa.',
            ],
            [
                'patient_code'      => 'PKN-0002',
                'full_name'         => 'Fatimah Binti Hamid',
                'gender'            => 'female',
                'date_of_birth'     => '1955-07-22',
                'phone'             => '+60198765432',
                'emergency_contact' => 'Hassan Bin Hamid — +60111234567',
                'status'            => PatientStatus::Active->value,
                'notes'             => 'Diagnosed 2020. Bilateral resting tremor. Occasional FOG episodes.',
            ],
            [
                'patient_code'      => 'PKN-0003',
                'full_name'         => 'Thomas Ng Chee Keong',
                'gender'            => 'male',
                'date_of_birth'     => '1950-11-08',
                'phone'             => '+60172223333',
                'emergency_contact' => 'Linda Ng — +60164445555',
                'status'            => PatientStatus::Active->value,
                'notes'             => 'Diagnosed 2015. Advanced stage. Frequent FOG. Requires walking aid.',
            ],
            [
                'patient_code'      => 'PKN-0004',
                'full_name'         => 'Rosmah Binti Idris',
                'gender'            => 'female',
                'date_of_birth'     => '1963-02-14',
                'phone'             => '+60139876543',
                'emergency_contact' => 'Idris Bin Mahmud — +60149876543',
                'status'            => PatientStatus::Active->value,
                'notes'             => 'Diagnosed 2022. Early stage. Mild hand tremor, no FOG yet observed.',
            ],
            [
                'patient_code'      => 'PKN-0005',
                'full_name'         => 'Lim Chee Wah',
                'gender'            => 'male',
                'date_of_birth'     => '1945-09-30',
                'phone'             => null,
                'emergency_contact' => 'Lim Mei Ling — +60126667788',
                'status'            => PatientStatus::Active->value,
                'notes'             => 'Diagnosed 2016. Moderate stage. Rigidity and tremor. Lives alone.',
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::firstOrCreate(
                ['patient_code' => $patientData['patient_code']],
                $patientData
            );
        }

        $this->command->info('  ✔ Patients seeded: 5 patients (PKN-0001 to PKN-0005).');
    }
}
