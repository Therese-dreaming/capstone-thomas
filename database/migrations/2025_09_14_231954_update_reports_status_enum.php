<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include 'investigating' status
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'investigating', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending'");
    }
};
