<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Reservation;
use App\Models\Event;

class GSUSidebarComposer
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
            'reservations' => Reservation::where('status', 'approved_OTP')->count(),
            'events' => Event::where('status', 'approved_OTP')->count(),
        ];

        $view->with('sidebarCounts', $sidebarCounts);
    }
}
