<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DrJavierController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'pending' => Reservation::where('status', 'approved_mhadel')->count(),
            'approved_today' => Reservation::where('status', 'approved_OTP')
                ->whereDate('updated_at', $today)->count(),
            'rejected_today' => Reservation::where('status', 'rejected_OTP')
                ->whereDate('updated_at', $today)->count(),
            'total_month' => Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP'])
                ->where('created_at', '>=', $startOfMonth)->count(),
        ];
        
        $recent_reservations = Reservation::with(['user', 'venue'])
            ->whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('drjavier.dashboard', compact('stats', 'recent_reservations'));
    }
} 