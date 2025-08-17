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
            $table->json('equipment_details')->nullable()->after('activity_grid');
            $table->decimal('price_per_hour', 10, 2)->nullable()->after('equipment_details');
            $table->decimal('final_price', 10, 2)->nullable()->after('price_per_hour');
            $table->integer('duration_hours')->nullable()->after('final_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['equipment_details', 'price_per_hour', 'final_price', 'duration_hours']);
        });
    }
};
