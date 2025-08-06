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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('event_title');
            $table->integer('capacity');
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->text('purpose');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('equipment_ids')->nullable();
            $table->string('activity_grid')->nullable(); // File path for uploaded activity grid
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
