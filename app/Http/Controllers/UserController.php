<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationSubmitted;

class UserController extends Controller
{
	public function dashboard()
	{
		$user = Auth::user();
		$reservations = Reservation::where('user_id', $user->id)
			->with(['venue'])
			->latest()
			->take(5)
			->get();

		return view('user.dashboard', compact('reservations'));
	}

	public function index()
	{
		$currentStatus = request()->query('status', 'all');
		$query = Reservation::where('user_id', Auth::id())
			->with(['venue'])
			->latest();

		switch ($currentStatus) {
			case 'pending':
				$query->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel']);
				break;
			case 'approved':
				$query->whereIn('status', ['approved', 'approved_OTP']);
				break;
			case 'rejected':
				$query->whereIn('status', ['rejected', 'rejected_OTP']);
				break;
			case 'all':
			default:
				// no extra filter
				break;
		}

		$userReservations = $query->paginate(10)->withQueryString();

		$counts = [
			'all' => Reservation::where('user_id', Auth::id())->count(),
			'pending' => Reservation::where('user_id', Auth::id())->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel'])->count(),
			'approved' => Reservation::where('user_id', Auth::id())->whereIn('status', ['approved', 'approved_OTP'])->count(),
			'rejected' => Reservation::where('user_id', Auth::id())->whereIn('status', ['rejected', 'rejected_OTP'])->count(),
		];

		return view('user.reservations.index', compact('userReservations', 'currentStatus', 'counts'));
	}

	public function show($id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->with(['venue'])
			->firstOrFail();

		return view('user.reservations.show', compact('reservation'));
	}

	public function calendar()
	{
		$venues = Venue::where('is_available', true)->get();
		return view('user.reservations', compact('venues'));
	}

	public function storeReservation(Request $request)
	{
		$request->validate([
			'event_title' => 'required|string|max:255',
			'capacity' => 'required|integer|min:1',
			'venue_id' => 'required|exists:venues,id',
			'purpose' => 'required|string',
			'start_date' => 'required|date|after:now',
			'end_date' => 'required|date|after:start_date',
			'activity_grid' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
			'equipment' => 'nullable|array',
			'equipment.*' => 'string',
			'equipment_quantity' => 'nullable|array',
			'equipment_quantity.*' => 'integer|min:1',
			'price_per_hour' => 'required|numeric|min:0',
			'base_price' => 'required|numeric|min:0',
			'department' => 'required|string|max:255',
			'other_department' => 'nullable|string|max:255',
		]);

		$venue = Venue::find($request->venue_id);
		if (!$venue || $venue->capacity < $request->capacity) {
			return back()->withErrors(['capacity' => 'No suitable venue found for the specified capacity.'])->withInput();
		}

		$minDate = now()->addDays(3);
		if (strtotime($request->start_date) < strtotime($minDate)) {
			return back()->withErrors(['start_date' => 'Reservations must be made at least 3 days in advance.'])->withInput();
		}
		if (strtotime($request->end_date) <= strtotime($request->start_date)) {
			return back()->withErrors(['end_date' => 'End date must be after start date.'])->withInput();
		}

		// Enhanced overlap validation: block any non-cancelled/rejected reservations
		$blockingStatuses = ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'];
		$conflict = Reservation::where('venue_id', $request->venue_id)
			->where(function($q) use ($request) {
				$q->where('start_date', '<', $request->end_date)
					->where('end_date', '>', $request->start_date);
			})
			->whereIn('status', $blockingStatuses)
			->exists();
		if ($conflict) {
			return back()->withErrors(['start_date' => 'The selected time overlaps with an existing reservation for this venue.'])->withInput();
		}

		// Block if overlaps with official events (upcoming/ongoing)
		$eventConflict = Event::where('venue_id', $request->venue_id)
			->whereIn('status', ['upcoming','ongoing'])
			->where(function($q) use ($request) {
				$q->where('start_date', '<', $request->end_date)
					->where('end_date', '>', $request->start_date);
			})
			->exists();
		if ($eventConflict) {
			return back()->withErrors(['start_date' => 'The selected time overlaps with an official event for this venue.'])->withInput();
		}

		$data = $request->all();
		$data['user_id'] = Auth::id();
		$data['status'] = 'pending';
		
		// Handle department field - if "Other" is selected, use the other_department value
		if ($request->department === 'Other' && $request->filled('other_department')) {
			$data['department'] = $request->other_department;
		} else {
			$data['department'] = $request->department;
		}

		if ($request->has('equipment') && is_array($request->equipment)) {
			$equipmentDetails = [];
			foreach ($request->equipment as $equipment) {
				if ($equipment !== 'none') {
					$quantity = $request->input("equipment_quantity.{$equipment}", 1);
					$equipmentDetails[] = [
						'name' => $equipment,
						'quantity' => $quantity
					];
				}
			}
			$data['equipment_details'] = $equipmentDetails;
		}

		$startDate = Carbon::parse($request->start_date);
		$endDate = Carbon::parse($request->end_date);
		$durationHours = ceil($startDate->diffInSeconds($endDate) / 3600);
		\Log::info('Duration calculation', [
			'start_date' => $startDate->toDateTimeString(),
			'end_date' => $endDate->toDateTimeString(),
			'raw_duration_seconds' => $startDate->diffInSeconds($endDate),
			'calculated_hours' => $durationHours,
			'final_duration_hours' => max(1, $durationHours)
		]);
		$data['duration_hours'] = max(1, $durationHours);

		if ($request->hasFile('activity_grid')) {
			$file = $request->file('activity_grid');
			$fileName = time() . '_' . $file->getClientOriginalName();
			$filePath = $file->storeAs('activity_grids', $fileName, 'public');
			$data['activity_grid'] = $filePath;
		}

		\Log::info('Creating reservation with data:', [
			'user_id' => $data['user_id'],
			'base_price' => $data['base_price'] ?? 'not set',
			'price_per_hour' => $data['price_per_hour'] ?? 'not set',
			'duration_hours' => $data['duration_hours'] ?? 'not set'
		]);

		$reservation = Reservation::create($data);

		// Self notification to the requester
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'Reservation submitted successfully',
			'body' => 'Your reservation "' . ($request->event_title) . '" has been submitted for review.',
			'type' => 'self_info',
			'related_id' => 0,
			'related_type' => Reservation::class,
		]);

		// Send email confirmation
		Mail::to(Auth::user()->email)->send(new ReservationSubmitted($reservation, Auth::user()));

		// Notify IOSA role about new reservation submission
		$iosaUsers = User::where('role', 'iosa')->get();
		foreach ($iosaUsers as $u) {
			Notification::create([
				'user_id' => $u->id,
				'title' => 'New reservation submitted',
				'body' => 'User ' . (Auth::user()->name ?? 'A user') . ' submitted "' . ($request->event_title) . '" for review.',
				'type' => 'reservation_action',
				'related_id' => $reservation->id,
				'related_type' => Reservation::class,
			]);
		}

		// Return JSON response for AJAX requests
		if ($request->expectsJson()) {
			return response()->json([
				'success' => true,
				'message' => 'Reservation submitted successfully!',
				'reservation_id' => $reservation->id
			]);
		}
		
		// Return redirect for regular form submissions
		return redirect()->route('user.reservations.index')
			->with('success', 'Reservation submitted successfully!');
	}

	public function profile()
	{
		$user = Auth::user();
		return view('user.profile', compact('user'));
	}

	// New: API to get unavailable time slots for a venue and date
	public function unavailable(Request $request)
	{
		$request->validate([
			'venue_id' => 'required|exists:venues,id',
			'date' => 'required|date',
		]);
		$venueId = $request->input('venue_id');
		$date = Carbon::parse($request->input('date'));
		$dayStart = $date->copy()->startOfDay();
		$dayEnd = $date->copy()->endOfDay();

		$blockingStatuses = ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'];
		$rows = Reservation::where('venue_id', $venueId)
			->whereIn('status', $blockingStatuses)
			->where(function($q) use ($dayStart, $dayEnd) {
				$q->where('start_date', '<', $dayEnd)
					->where('end_date', '>', $dayStart);
			})
			->orderBy('start_date')
			->get(['start_date','end_date','event_title']);

		$slots = $rows->map(function($r){
			return [
				'start' => Carbon::parse($r->start_date)->format('H:i'),
				'end' => Carbon::parse($r->end_date)->format('H:i'),
				'title' => $r->event_title,
			];
		});

		// Include official events as unavailable
		$eventRows = Event::where('venue_id', $venueId)
			->whereIn('status', ['upcoming','ongoing'])
			->where(function($q) use ($dayStart, $dayEnd) {
				$q->where('start_date', '<', $dayEnd)
					->where('end_date', '>', $dayStart);
			})
			->orderBy('start_date')
			->get(['start_date','end_date','title']);

		$eventSlots = $eventRows->map(function($e){
			return [
				'start' => Carbon::parse($e->start_date)->format('H:i'),
				'end' => Carbon::parse($e->end_date)->format('H:i'),
				'title' => $e->title,
			];
		});

		return response()->json(['slots' => $slots->concat($eventSlots)->values()]);
	}

	public function edit($id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->with(['venue'])
			->firstOrFail();

		// Only allow editing of pending reservations
		if (!in_array($reservation->status, ['pending', 'approved_IOSA', 'approved_mhadel'])) {
			return redirect()->route('user.reservations.show', $reservation->id)
				->with('error', 'This reservation cannot be edited.');
		}

		$venues = Venue::where('is_available', true)->get();
		
		return view('user.reservations.edit', compact('reservation', 'venues'));
	}

	public function update(Request $request, $id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->firstOrFail();

		// Only allow updating of pending reservations
		if (!in_array($reservation->status, ['pending', 'approved_IOSA', 'approved_mhadel'])) {
			return redirect()->route('user.reservations.show', $reservation->id)
				->with('error', 'This reservation cannot be updated.');
		}

		$request->validate([
			'event_title' => 'required|string|max:255',
			'capacity' => 'required|integer|min:1',
			'venue_id' => 'required|exists:venues,id',
			'purpose' => 'required|string',
			'start_date' => 'required|date|after:now',
			'end_date' => 'required|date|after:start_date',
			'activity_grid' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
			'equipment' => 'nullable|array',
			'equipment.*' => 'string',
			'equipment_quantity' => 'nullable|array',
			'equipment_quantity.*' => 'integer|min:1',
			'price_per_hour' => 'required|numeric|min:0',
			'base_price' => 'required|numeric|min:0',
			'department' => 'required|string|max:255',
			'other_department' => 'nullable|string|max:255',
		]);

		// Handle department field - if "Other" is selected, use the other_department value
		$department = $request->department;
		if ($request->department === 'Other' && $request->filled('other_department')) {
			$department = $request->other_department;
		}

		$data = [
			'event_title' => $request->event_title,
			'capacity' => $request->capacity,
			'venue_id' => $request->venue_id,
			'purpose' => $request->purpose,
			'start_date' => $request->start_date,
			'end_date' => $request->end_date,
			'price_per_hour' => $request->price_per_hour,
			'base_price' => $request->base_price,
			'department' => $department,
		];

		// Handle equipment data
		if ($request->has('equipment')) {
			$equipmentData = [];
			foreach ($request->equipment as $equipment) {
				if ($equipment !== 'none') {
					$quantity = $request->input("equipment_quantity.{$equipment}", 1);
					$equipmentData[$equipment] = $quantity;
				}
			}
			$data['equipment'] = $equipmentData;
		}

		// Handle activity grid file
		if ($request->hasFile('activity_grid')) {
			$data['activity_grid'] = $request->file('activity_grid')->store('activity_grids', 'public');
		}

		$reservation->update($data);

		// Create notification
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'Reservation updated successfully',
			'body' => 'Your reservation "' . $request->event_title . '" has been updated.',
			'type' => 'self_info',
			'related_id' => 0,
			'related_type' => Reservation::class,
		]);

		// Return JSON response for AJAX requests
		if ($request->expectsJson()) {
			return response()->json([
				'success' => true,
				'message' => 'Reservation updated successfully!',
				'reservation_id' => $reservation->id
			]);
		}

		return redirect()->route('user.reservations.show', $reservation->id)
			->with('success', 'Reservation updated successfully!');
	}

	public function cancel($id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->firstOrFail();

		// Only allow cancellation of pending reservations
		if (!in_array($reservation->status, ['pending', 'approved_IOSA', 'approved_mhadel'])) {
			return response()->json([
				'success' => false,
				'message' => 'This reservation cannot be cancelled.'
			], 400);
		}

		// Update status to cancelled
		$reservation->update(['status' => 'cancelled']);

		// Create notification
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'Reservation cancelled',
			'body' => 'Your reservation "' . $reservation->event_title . '" has been cancelled.',
			'type' => 'self_info',
			'related_id' => 0,
			'related_type' => Reservation::class,
		]);

		// Notify admins about cancellation
		$adminUsers = User::whereIn('role', ['admin', 'iosa', 'mhadel'])->get();
		foreach ($adminUsers as $admin) {
			Notification::create([
				'user_id' => $admin->id,
				'title' => 'Reservation cancelled',
				'body' => 'User ' . Auth::user()->name . ' cancelled reservation "' . $reservation->event_title . '".',
				'type' => 'reservation_action',
				'related_id' => $reservation->id,
				'related_type' => Reservation::class,
			]);
		}

		return response()->json([
			'success' => true,
			'message' => 'Reservation cancelled successfully!'
		]);
	}
}
