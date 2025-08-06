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
        // First, modify the ENUM column to include the new values
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'approved_IOSA', 'rejected_IOSA', 'approved_mhadel', 'rejected_mhadel', 'approved_OTP', 'rejected_OTP', 'cancelled') DEFAULT 'pending'");
        
        // Now update any existing 'approved' statuses to 'approved_IOSA'
        // and 'rejected' statuses to 'rejected_IOSA' to maintain data integrity
        DB::table('reservations')
            ->where('status', 'approved')
            ->update(['status' => 'approved_IOSA']);
            
        DB::table('reservations')
            ->where('status', 'rejected')
            ->update(['status' => 'rejected_IOSA']);

        // Finally, remove the old values from the ENUM
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved_IOSA', 'rejected_IOSA', 'approved_mhadel', 'rejected_mhadel', 'approved_OTP', 'rejected_OTP', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, add back the old values to the ENUM
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'approved_IOSA', 'rejected_IOSA', 'approved_mhadel', 'rejected_mhadel', 'approved_OTP', 'rejected_OTP', 'cancelled') DEFAULT 'pending'");
        
        // Revert any 'approved_IOSA' back to 'approved' and 'rejected_IOSA' back to 'rejected'
        DB::table('reservations')
            ->where('status', 'approved_IOSA')
            ->update(['status' => 'approved']);
            
        DB::table('reservations')
            ->where('status', 'rejected_IOSA')
            ->update(['status' => 'rejected']);

        // Finally, revert the ENUM column back to original values
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'");
    }
};
