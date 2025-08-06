<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MhadelController extends Controller
{
    /**
     * Display the Mhadel dashboard.
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'pending' => Reservation::where('status', 'approved_IOSA')->count(),
            'approved_today' => Reservation::where('status', 'approved_mhadel')
                ->whereDate('updated_at', $today)->count(),
            'rejected_today' => Reservation::where('status', 'rejected_mhadel')
                ->whereDate('updated_at', $today)->count(),
            'total_month' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel', 'rejected_mhadel'])
                ->where('created_at', '>=', $startOfMonth)->count(),
        ];
        
        $recent_reservations = Reservation::with(['user', 'venue'])
            ->whereIn('status', ['approved_IOSA', 'approved_mhadel', 'rejected_mhadel'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('mhadel.dashboard', compact('stats', 'recent_reservations'));
    }
} 