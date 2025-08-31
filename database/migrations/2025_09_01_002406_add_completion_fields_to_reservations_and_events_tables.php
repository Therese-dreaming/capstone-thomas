<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add completion fields to reservations table
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('completion_notes')->nullable()->after('notes');
            $table->timestamp('completion_date')->nullable()->after('completion_notes');
            $table->string('completed_by')->nullable()->after('completion_date');
        });

        // Add completion fields to events table
        Schema::table('events', function (Blueprint $table) {
            $table->text('completion_notes')->nullable()->after('max_participants');
            $table->timestamp('completion_date')->nullable()->after('completion_notes');
            $table->string('completed_by')->nullable()->after('completion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove completion fields from reservations table
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['completion_notes', 'completion_date', 'completed_by']);
        });

        // Remove completion fields from events table
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['completion_notes', 'completion_date', 'completed_by']);
        });
    }
};
