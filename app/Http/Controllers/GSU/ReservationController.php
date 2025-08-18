<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
	public function index(Request $request)
	{
		$query = Reservation::with(['user','venue'])->where('status','approved_OTP');
		if ($request->filled('date_from')) {
			$query->where('start_date', '>=', $request->date_from);
		}
		if ($request->filled('date_to')) {
			$query->where('start_date', '<=', $request->date_to);
		}
		if ($request->filled('venue')) {
			$query->where('venue_id', $request->venue);
		}
		$reservations = $query->orderByDesc('created_at')->paginate(10);
		$venues = \App\Models\Venue::orderBy('name')->get();
		return view('gsu.reservations.index', compact('reservations','venues'));
	}

	public function show(string $id)
	{
		$reservation = Reservation::with(['user','venue'])->where('status','approved_OTP')->findOrFail($id);
		return view('gsu.reservations.show', compact('reservation'));
	}
} 