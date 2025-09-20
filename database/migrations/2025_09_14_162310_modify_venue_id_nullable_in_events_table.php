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
        Schema::table('events', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['venue_id']);
            
            // Modify the venue_id column to be nullable
            $table->unsignedBigInteger('venue_id')->nullable()->change();
            
            // Re-add the foreign key constraint with set null on delete
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop the nullable foreign key constraint
            $table->dropForeign(['venue_id']);
            
            // Modify the venue_id column back to NOT NULL
            $table->foreignId('venue_id')->nullable(false)->change();
            
            // Re-add the original foreign key constraint
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
    }
};
