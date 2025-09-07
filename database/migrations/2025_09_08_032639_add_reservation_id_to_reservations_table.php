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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reservation_id', 20)->nullable()->after('id');
            $table->index('reservation_id');
        });
        
        // Generate reservation IDs for existing records
        $reservations = \App\Models\Reservation::whereNull('reservation_id')->get();
        foreach ($reservations as $reservation) {
            $reservation->reservation_id = \App\Models\Reservation::generateReservationId();
            $reservation->save();
        }
        
        // Now make the column unique and not nullable
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reservation_id', 20)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['reservation_id']);
            $table->dropColumn('reservation_id');
        });
    }
};
