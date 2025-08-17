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
        // Drop the reservation_equipment pivot table first due to foreign key constraints
        Schema::dropIfExists('reservation_equipment');
        
        // Drop the equipment table
        Schema::dropIfExists('equipment');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate equipment table
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->integer('total_quantity');
            $table->timestamps();
        });

        // Recreate reservation_equipment pivot table
        Schema::create('reservation_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }
};
