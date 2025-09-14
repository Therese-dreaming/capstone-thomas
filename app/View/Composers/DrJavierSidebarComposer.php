<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Reservation;
use App\Models\Report;

class DrJavierSidebarComposer
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
            'reservations' => Reservation::where('status', 'approved_mhadel')->count(),
            'gsu_reports' => Report::whereIn('status', ['pending', 'investigating'])->count(),
        ];

        $view->with('sidebarCounts', $sidebarCounts);
    }
}
