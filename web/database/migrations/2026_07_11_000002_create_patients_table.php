<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `patients` table.
 *
 * Represents a Parkinson's disease patient being monitored by the system.
 *
 * DESIGN NOTES:
 * - No assigned_doctor_id or assigned_caregiver_id in this phase.
 *   Patient-user relationships will be added via a separate migration in Phase 3
 *   once the authentication system exists.
 * - patient_code is the human-readable unique identifier (e.g. PKN-0001).
 *   The auto-increment id remains the internal primary key and foreign key reference.
 * - status is managed at the application level (see App\Enums\PatientStatus).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('patient_code', 20)
                  ->unique()
                  ->comment('Human-readable unique patient identifier, e.g. PKN-0001');

            $table->string('full_name', 150);

            $table->string('gender', 10)
                  ->comment('male | female | other');

            $table->date('date_of_birth')
                  ->nullable()
                  ->comment('Used for age calculation; nullable for privacy if not provided');

            $table->string('phone', 30)
                  ->nullable();

            $table->string('emergency_contact', 200)
                  ->nullable()
                  ->comment('Name and/or phone of the emergency contact person');

            $table->string('status', 20)
                  ->default('active')
                  ->comment('Patient record status (App\\Enums\\PatientStatus)');

            $table->text('notes')
                  ->nullable()
                  ->comment('Free-text clinical or administrative notes');

            $table->timestamps();

            // Indexes for common dashboard queries
            $table->index('status');
            $table->index('full_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
