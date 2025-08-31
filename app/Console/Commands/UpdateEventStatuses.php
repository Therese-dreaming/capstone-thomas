<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class UpdateEventStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update event statuses based on scheduled dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Automatic event status updates have been disabled.');
        $this->info('GSU users now manually control event completion status.');
        $this->info('Only ongoing status updates (upcoming -> ongoing) are still automatic.');
        
        $now = Carbon::now();
        $updatedCount = 0;

        // Only update upcoming events to ongoing (this is still useful)
        $ongoingEvents = Event::where('status', 'upcoming')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        foreach ($ongoingEvents as $event) {
            $event->update(['status' => 'ongoing']);
            $updatedCount++;
            $this->line("Event '{$event->title}' marked as ongoing");
        }

        $this->info("Completed! Updated {$updatedCount} events to ongoing status.");
        $this->info("Note: Events are no longer automatically marked as completed.");
        $this->info("GSU users must manually mark events as complete.");
        
        return 0;
    }
}
