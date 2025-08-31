<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Models\Event;

class IOSAController extends Controller
{
    /**
     * Display the IOSA dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved_today' => Reservation::where('status', 'approved_IOSA')
                ->whereDate('created_at', today())->count(),
            'rejected_today' => Reservation::where('status', 'rejected_IOSA')
                ->whereDate('created_at', today())->count(),
            'total_month' => Reservation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Data for Status Distribution Chart
        $statusDistribution = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved_IOSA' => Reservation::where('status', 'approved_IOSA')->count(),
            'rejected_IOSA' => Reservation::where('status', 'rejected_IOSA')->count(),
            'approved_mhadel' => Reservation::where('status', 'approved_mhadel')->count(),
            'approved_OTP' => Reservation::where('status', 'approved_OTP')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
        ];

        // Data for Monthly Trends Chart (last 6 months)
        $monthlyTrends = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyTrends[] = Reservation::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        // Data for Events Chart
        $eventsData = [
            'upcoming' => Event::where('status', 'upcoming')->count(),
            'ongoing' => Event::where('status', 'ongoing')->count(),
            'completed' => Event::where('status', 'completed')->count(),
            'cancelled' => Event::where('status', 'cancelled')->count(),
        ];

        // Data for Departments Chart
        $departmentsData = Reservation::selectRaw('department, COUNT(*) as count')
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get()
            ->pluck('count', 'department')
            ->toArray();

        // Monthly Events Trend
        $monthlyEventsTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyEventsTrends[] = Event::whereMonth('start_date', $date->month)
                ->whereYear('start_date', $date->year)
                ->count();
        }

        $recent_reservations = Reservation::with(['user', 'venue'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $upcoming_reservations = Reservation::with(['user', 'venue'])
            ->where('start_date', '>=', now())
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        $upcoming_events = Event::with(['venue'])
            ->where('start_date', '>=', now())
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return view('iosa.dashboard', compact(
            'stats', 
            'statusDistribution',
            'monthlyLabels',
            'monthlyTrends',
            'eventsData',
            'departmentsData',
            'monthlyEventsTrends',
            'recent_reservations', 
            'upcoming_reservations', 
            'upcoming_events'
        ));
    }
} 