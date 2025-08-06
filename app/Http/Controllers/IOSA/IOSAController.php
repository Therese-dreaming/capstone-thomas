<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;

class IOSAController extends Controller
{
    /**
     * Display the IOSA dashboard.
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved_today' => Reservation::where('status', 'approved_IOSA')
                ->whereDate('updated_at', $today)->count(),
            'rejected_today' => Reservation::where('status', 'rejected_IOSA')
                ->whereDate('updated_at', $today)->count(),
            'total_month' => Reservation::whereIn('status', ['pending', 'approved_IOSA', 'rejected_IOSA'])
                ->where('created_at', '>=', $startOfMonth)->count(),
        ];
        
        $recent_reservations = Reservation::with(['user', 'venue'])
            ->whereIn('status', ['pending', 'approved_IOSA', 'rejected_IOSA'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('iosa.dashboard', compact('stats', 'recent_reservations'));
    }
} 