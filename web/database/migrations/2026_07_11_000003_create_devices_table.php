<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `devices` table.
 *
 * Represents each physical ESP32 device unit assigned to a patient.
 *
 * DESIGN NOTES:
 * - device_uid is the permanent, public identifier for the ESP32 (e.g. ESP32-A1B2C3D4).
 *   It is distinct from the database `id` which is the internal FK reference.
 *   Do not expose the database `id` in any ESP32 communication.
 *
 * - api_token stores the device's authentication secret.
 *   Column length (100 chars) accommodates:
 *     - Current: plain 64-char random token (for development)
 *     - Future:  SHA-256 hex hash (64 chars) or prefixed hash (e.g. "sha256|..." = 71 chars)
 *   Token hashing strategy is an application-layer concern; no schema changes needed to adopt it.
 *   The api_token column is intentionally hidden in the Device model ($hidden).
 *
 * - last_seen_at is updated each time the device successfully polls the API.
 *   It is used for the "device online/offline" dashboard indicator.
 *
 * - A patient may have one or more devices (hasMany relationship).
 *   No unique constraint on patient_id to allow multiple devices per patient.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->cascadeOnDelete()
                  ->comment('Patient this device is assigned to');

            $table->string('device_uid', 50)
                  ->unique()
                  ->comment('Permanent ESP32 hardware identifier, e.g. ESP32-A1B2C3D4');

            $table->string('device_name', 100)
                  ->comment('Human-readable label for this device unit');

            $table->string('api_token', 100)
                  ->comment('Device authentication token (may be stored hashed in future phases)');

            $table->string('firmware_version', 20)
                  ->nullable()
                  ->comment('Firmware version string reported by the device, e.g. 1.0.0');

            $table->timestamp('last_seen_at')
                  ->nullable()
                  ->comment('Last time this device successfully communicated with the API');

            $table->string('status', 20)
                  ->default('offline')
                  ->comment('Device connectivity status (App\\Enums\\DeviceStatus)');

            $table->timestamps();

            // Indexes for dashboard queries
            $table->index('status');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
