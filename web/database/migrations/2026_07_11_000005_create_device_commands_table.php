<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `device_commands` table.
 *
 * Stores remote commands issued by doctors/caregivers to ESP32 devices.
 *
 * COMMAND FLOW:
 *   Doctor/Caregiver → Dashboard → Laravel (stores command here)
 *   → ESP32 polls GET /api/commands (Phase 11)
 *   → ESP32 receives command, executes, calls POST /api/commands/{id}/ack
 *   → Laravel updates status to 'acknowledged', sets acknowledged_at
 *
 * DESIGN NOTES:
 * - issued_by is nullable (FK to users). It is null if the system generates
 *   the command automatically (e.g. scheduled task). In Phase 2, all commands
 *   are manually issued by users.
 *
 * - payload is a generic JSON column for optional command parameters.
 *   StopCueing currently needs no parameters, but future commands
 *   (e.g. set_cue_interval, update_threshold) may require them.
 *   No schema change will be needed to add parameterised commands.
 *
 * - issued_at records when the command was created (not necessarily when it
 *   was polled by the device). acknowledged_at records ESP32 confirmation time.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_commands', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                  ->constrained('devices')
                  ->cascadeOnDelete();

            $table->foreignId('issued_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who issued the command; null if system-generated');

            $table->string('command_type', 30)
                  ->comment('Command identifier (App\\Enums\\CommandType), e.g. stop_cueing');

            $table->string('status', 20)
                  ->default('pending')
                  ->comment('Command lifecycle status (App\\Enums\\CommandStatus)');

            $table->json('payload')
                  ->nullable()
                  ->comment('Optional command parameters; extensible without schema changes');

            $table->timestamp('issued_at')
                  ->useCurrent()
                  ->comment('When this command was created by a user or the system');

            $table->timestamp('acknowledged_at')
                  ->nullable()
                  ->comment('When the ESP32 confirmed successful command execution');

            $table->timestamps();

            // Index for device polling query: device_id + status = 'pending'
            $table->index(['device_id', 'status'], 'dc_device_status_idx');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_commands');
    }
};
