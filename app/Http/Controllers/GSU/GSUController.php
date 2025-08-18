<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GSUController extends Controller
{
	public function dashboard()
	{
		$today = Carbon::today();
		$startOfMonth = Carbon::now()->startOfMonth();
		$stats = [
			'approved' => Reservation::where('status', 'approved_OTP')->count(),
			'approved_today' => Reservation::where('status', 'approved_OTP')->whereDate('updated_at', $today)->count(),
			'total_month' => Reservation::where('status', 'approved_OTP')->where('created_at', '>=', $startOfMonth)->count(),
		];
		$recent = Reservation::with(['user','venue'])->where('status','approved_OTP')->orderByDesc('created_at')->limit(5)->get();
		return view('gsu.dashboard', compact('stats','recent'));
	}
} 