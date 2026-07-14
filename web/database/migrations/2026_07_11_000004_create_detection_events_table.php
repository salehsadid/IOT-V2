<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `detection_events` table.
 *
 * Stores ONLY confirmed detection events reported by ESP32 devices.
 * Raw sensor readings are NEVER stored here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_events', function (Blueprint $table) {
            $table->id();

            // The ESP32 device ID string
            $table->string('device_id');

            // tremor | fog
            $table->string('event_type', 20);

            // Tremor specific
            $table->tinyInteger('start_level')->unsigned()->nullable();
            $table->tinyInteger('max_level')->unsigned()->nullable();

            // Timing
            $table->integer('duration_ms');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->timestamps();

            // Indexes for faster history queries
            $table->index('device_id');
            $table->index('event_type');
            $table->index('start_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_events');
    }
};
