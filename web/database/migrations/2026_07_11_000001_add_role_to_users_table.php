<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the `role` column to the existing users table.
 *
 * This migration extends the default Laravel users table without modifying
 * or re-creating it. The role column prepares the table for Phase 3 RBAC.
 *
 * Supported roles: doctor, caregiver (see App\Enums\UserRole)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Placed after email_verified_at for logical grouping.
            // Default is 'doctor' to handle any pre-existing rows gracefully.
            $table->string('role', 20)
                  ->default('doctor')
                  ->after('email_verified_at')
                  ->comment('User role: doctor or caregiver (App\\Enums\\UserRole)');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
