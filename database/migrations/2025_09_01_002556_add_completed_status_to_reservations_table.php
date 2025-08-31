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
        // Add 'completed' to the reservations status enum
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved_IOSA', 'rejected_IOSA', 'approved_mhadel', 'rejected_mhadel', 'approved_OTP', 'rejected_OTP', 'cancelled', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'completed' from the reservations status enum
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved_IOSA', 'rejected_IOSA', 'approved_mhadel', 'rejected_mhadel', 'approved_OTP', 'rejected_OTP', 'cancelled') DEFAULT 'pending'");
    }
};
