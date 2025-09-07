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
            $table->string('event_id', 20)->nullable()->after('id');
            $table->index('event_id');
        });
        
        // Generate event IDs for existing records
        $events = \App\Models\Event::whereNull('event_id')->get();
        foreach ($events as $event) {
            $event->event_id = \App\Models\Event::generateEventId();
            $event->save();
        }
        
        // Now make the column unique and not nullable
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_id', 20)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
