<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Reservation;
use App\Models\Event;
use App\Models\Report;
use Illuminate\Support\Facades\Cache;

class IosaSidebarComposer
{
    /**
     * Create a new sidebar composer.
     */
    public function __construct()
    {
        //
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Real-time sidebar counts without caching for immediate updates
        $sidebarCounts = [
            'reservations' => Reservation::where('status', 'pending')->count(),
            'events' => Event::where('status', 'pending')->count(),
            'reports' => Report::whereIn('status', ['pending', 'investigating'])->count(),
            'gsu_reports' => Report::whereIn('status', ['pending', 'investigating'])->count(),
        ];

        $view->with('sidebarCounts', $sidebarCounts);
    }
}
