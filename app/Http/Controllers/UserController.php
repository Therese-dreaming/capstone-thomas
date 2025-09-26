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
use App\Models\ReservationRating;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationSubmitted;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
	public function dashboard()
	{
		$user = Auth::user();
		
		// Get recent reservations
		$reservations = Reservation::where('user_id', $user->id)
			->with(['venue'])
			->latest()
			->take(5)
			->get();

		// Calculate stats
		$stats = [
			'total' => Reservation::where('user_id', $user->id)->count(),
			'approved' => Reservation::where('user_id', $user->id)->whereIn('status', ['approved', 'approved_OTP'])->count(),
			'pending' => Reservation::where('user_id', $user->id)->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel'])->count(),
			'completed' => Reservation::where('user_id', $user->id)->where('status', 'completed')->count(),
			'rejected' => Reservation::where('user_id', $user->id)->whereIn('status', ['rejected', 'rejected_OTP'])->count(),
		];

		// Get monthly data for the last 6 months
		$monthlyData = $this->getMonthlyData($user->id);

		return view('user.dashboard', compact('reservations', 'stats', 'monthlyData'));
	}

	private function getMonthlyData($userId)
	{
		$months = [];
		$data = [];
		
		// Get last 6 months
		for ($i = 5; $i >= 0; $i--) {
			$date = Carbon::now()->subMonths($i);
			$months[] = $date->format('M Y');
			
			$count = Reservation::where('user_id', $userId)
				->whereYear('created_at', $date->year)
				->whereMonth('created_at', $date->month)
				->count();
			
			$data[] = $count;
		}

		return [
			'labels' => $months,
			'data' => $data
		];
	}

	public function index()
	{
		$currentStatus = request()->query('status', 'all');
		$searchQuery = request()->query('q');
		
		$query = Reservation::where('user_id', Auth::id())
			->with(['venue', 'ratings'])
			->latest();

		// Add search functionality
		if ($searchQuery) {
			$query->where(function ($q) use ($searchQuery) {
				$q->where('event_title', 'like', "%{$searchQuery}%")
				  ->orWhere('reservation_id', 'like', "%{$searchQuery}%")
				  ->orWhere('purpose', 'like', "%{$searchQuery}%")
				  ->orWhereHas('venue', function ($venueQuery) use ($searchQuery) {
					  $venueQuery->where('name', 'like', "%{$searchQuery}%");
				  });
			});
		}

		switch ($currentStatus) {
			case 'pending':
				$query->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel']);
				break;
			case 'approved':
				$query->whereIn('status', ['approved', 'approved_OTP']);
				break;
			case 'completed':
				$query->where('status', 'completed');
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
			'completed' => Reservation::where('user_id', Auth::id())->where('status', 'completed')->count(),
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
				'custom_equipment_name' => 'nullable|array',
				'custom_equipment_name.*' => 'nullable|string|max:255',
				'custom_equipment_quantity' => 'nullable|array',
				'custom_equipment_quantity.*' => 'nullable|integer|min:1',
				'price_per_hour' => 'required|numeric|min:0',
				'base_price' => 'required|numeric|min:0',
				'department' => 'required|string|max:255',
				'other_department' => 'nullable|string|max:255',
		]);

		$venue = Venue::find($request->venue_id);
		if (!$venue) {
			return back()->withErrors(['venue_id' => 'Selected venue not found.'])->withInput();
		}

		// Use calendar days (midnight) instead of exact time for 3-day rule
		$minDate = now()->addDays(3)->startOfDay();
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

		// Handle custom equipment requests
		if ($request->has('custom_equipment_name') && is_array($request->custom_equipment_name)) {
			$customEquipmentRequests = [];
			$customNames = $request->input('custom_equipment_name', []);
			$customQuantities = $request->input('custom_equipment_quantity', []);
			
			foreach ($customNames as $index => $name) {
				if (!empty(trim($name))) {
					$quantity = isset($customQuantities[$index]) ? (int)$customQuantities[$index] : 1;
					$customEquipmentRequests[] = [
						'name' => trim($name),
						'quantity' => max(1, $quantity)
					];
				}
			}
			if (!empty($customEquipmentRequests)) {
				$data['custom_equipment_requests'] = $customEquipmentRequests;
			}
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
		if ($request->expectsJson() || $request->has('ajax')) {
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

	public function updateProfile(Request $request)
	{
		$user = Auth::user();
		$request->validate([
			'first_name' => 'nullable|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'name' => 'nullable|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
		]);

		$user->first_name = $request->first_name ?? $user->first_name;
		$user->last_name = $request->last_name ?? $user->last_name;
		$user->name = $request->name ?? trim(($request->first_name ?? $user->first_name).' '.($request->last_name ?? $user->last_name)) ?: $user->name;
		$user->email = $request->email;
		$user->save();

		return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
	}

	public function updatePassword(Request $request)
	{
		$user = Auth::user();
		$request->validate([
			'current_password' => 'required|string',
			'password' => 'required|string|min:8|confirmed',
		]);

		if (!Hash::check($request->current_password, $user->password)) {
			return back()->with('error', 'Current password is incorrect.');
		}

		$user->password = Hash::make($request->password);
		$user->save();

		return redirect()->route('user.profile')->with('success', 'Password updated successfully.');
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

		// Only allow editing of pending reservations (not yet approved by IOSA)
		if ($reservation->status !== 'pending') {
			return redirect()->route('user.reservations.show', $reservation->id)
				->with('error', 'This reservation cannot be edited. You can only edit reservations that have not been reviewed by IOSA yet.');
		}

		$venues = Venue::where('is_available', true)->get();
		
		return view('user.reservations.edit', compact('reservation', 'venues'));
	}

	public function update(Request $request, $id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->firstOrFail();

		// Only allow updating of pending reservations (not yet approved by IOSA)
		if ($reservation->status !== 'pending') {
			return redirect()->route('user.reservations.show', $reservation->id)
				->with('error', 'This reservation cannot be updated. You can only edit reservations that have not been reviewed by IOSA yet.');
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
			'custom_equipment_name' => 'nullable|array',
			'custom_equipment_name.*' => 'nullable|string|max:255',
			'custom_equipment_quantity' => 'nullable|array',
			'custom_equipment_quantity.*' => 'nullable|integer|min:1',
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

		// Handle equipment data (same as store method)
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
		} else {
			// If no equipment selected, clear equipment_details
			$data['equipment_details'] = [];
		}

		// Handle custom equipment requests
		if ($request->has('custom_equipment_name') && is_array($request->custom_equipment_name)) {
			$customEquipmentRequests = [];
			$customNames = $request->input('custom_equipment_name', []);
			$customQuantities = $request->input('custom_equipment_quantity', []);
			
			foreach ($customNames as $index => $name) {
				if (!empty(trim($name))) {
					$quantity = isset($customQuantities[$index]) ? (int)$customQuantities[$index] : 1;
					$customEquipmentRequests[] = [
						'name' => trim($name),
						'quantity' => max(1, $quantity)
					];
				}
			}
			if (!empty($customEquipmentRequests)) {
				$data['custom_equipment_requests'] = $customEquipmentRequests;
			}
		} else {
			// Clear custom equipment if not provided
			$data['custom_equipment_requests'] = [];
		}

		// Calculate duration hours
		$startDate = \Carbon\Carbon::parse($request->start_date);
		$endDate = \Carbon\Carbon::parse($request->end_date);
		$durationHours = ceil($startDate->diffInSeconds($endDate) / 3600);
		$data['duration_hours'] = max(1, $durationHours);

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

	public function cancel(Request $request, $id)
	{
		$reservation = Reservation::where('id', $id)
			->where('user_id', Auth::id())
			->firstOrFail();

		// Only allow cancellation of pending reservations (not yet approved by IOSA)
		if ($reservation->status !== 'pending') {
			return response()->json([
				'success' => false,
				'message' => 'This reservation cannot be cancelled. You can only cancel reservations that have not been reviewed by IOSA yet.'
			], 400);
		}

		// Validate cancellation reason
		$request->validate([
			'cancellation_reason' => 'required|string|min:10|max:500'
		]);

		// Update status to cancelled with reason and timestamp
		$reservation->update([
			'status' => 'cancelled',
			'cancellation_reason' => $request->cancellation_reason,
			'cancelled_at' => now()
		]);

		// Create notification for user
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'Reservation cancelled',
			'body' => 'Your reservation "' . $reservation->event_title . '" has been cancelled. Reason: ' . Str::limit($request->cancellation_reason, 100),
			'type' => 'self_info',
			'related_id' => 0,
			'related_type' => Reservation::class,
		]);

		// Notify admins about cancellation with reason
		$adminUsers = User::whereIn('role', ['admin', 'IOSA', 'OTP', 'PPGS'])->get();
		foreach ($adminUsers as $admin) {
			Notification::create([
				'user_id' => $admin->id,
				'title' => 'Reservation cancelled by user',
				'body' => 'User ' . Auth::user()->name . ' cancelled reservation "' . $reservation->event_title . '". Reason: ' . Str::limit($request->cancellation_reason, 100),
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

	/**
	 * Rate a reservation (1-5 stars)
	 */
	public function rateReservation(Reservation $reservation, Request $request)
	{
		// Only allow rating completed reservations
		if ($reservation->status !== 'completed') {
			if ($request->expectsJson()) {
				return response()->json(['success' => false, 'message' => 'Only completed reservations can be rated.']);
			}
			return back()->with('error', 'Only completed reservations can be rated.');
		}

		// Ensure user can only rate their own reservations
		if ($reservation->user_id !== auth()->id()) {
			if ($request->expectsJson()) {
				return response()->json(['success' => false, 'message' => 'You can only rate your own reservations.']);
			}
			return back()->with('error', 'You can only rate your own reservations.');
		}

		$request->validate([
			'rating' => 'required|integer|min:1|max:5',
			'comment' => 'nullable|string|max:1000'
		]);

		$userId = auth()->id();

		// Check if user has already rated this reservation
		$existingRating = ReservationRating::where('reservation_id', $reservation->id)
			->where('user_id', $userId)
			->first();

		if ($existingRating) {
			// Update existing rating
			$existingRating->update([
				'rating' => $request->rating,
				'comment' => $request->comment
			]);
			$message = 'Your rating has been updated successfully!';
			$isNewRating = false;
		} else {
			// Create new rating
			ReservationRating::create([
				'reservation_id' => $reservation->id,
				'user_id' => $userId,
				'rating' => $request->rating,
				'comment' => $request->comment
			]);
			$message = 'Thank you for rating your reservation!';
			$isNewRating = true;
		}

		// Create notifications for all relevant roles
		$this->createRatingNotifications($reservation, $request->rating, $request->comment, $isNewRating);

		if ($request->expectsJson()) {
			return response()->json([
				'success' => true,
				'message' => $message,
				'average_rating' => $reservation->fresh()->average_rating,
				'total_ratings' => $reservation->fresh()->total_ratings
			]);
		}

		return back()->with('success', $message);
	}

	/**
	 * Get reservation rating details
	 */
	public function getReservationRating(Reservation $reservation)
	{
		$userId = auth()->id();
		$userRating = $reservation->getUserRating($userId);
		
		return response()->json([
			'average_rating' => $reservation->average_rating,
			'total_ratings' => $reservation->total_ratings,
			'user_rating' => $userRating ? $userRating->rating : null,
			'user_comment' => $userRating ? $userRating->comment : null,
			'can_rate' => $reservation->status === 'completed' && $reservation->user_id === $userId
		]);
	}

	/**
	 * Create notifications for reservation ratings
	 */
	private function createRatingNotifications($reservation, $rating, $comment, $isNewRating)
	{
		$userName = auth()->user()->name;
		$reservationTitle = $reservation->event_title;
		$ratingText = $this->getRatingText($rating);
		
		$action = $isNewRating ? 'rated' : 'updated their rating for';
		$notificationTitle = $isNewRating ? 'New Reservation Rating' : 'Reservation Rating Updated';
		
		$ratingMessage = "User {$userName} has {$action} reservation '{$reservationTitle}' with {$ratingText} ({$rating}/5 stars)";
		
		if ($comment) {
			$ratingMessage .= ". Comment: " . Str::limit($comment, 100);
		}

		// Notify IOSA users
		$iosaUsers = \App\Models\User::where('role', 'IOSA')->get();
		foreach ($iosaUsers as $user) {
			\App\Models\Notification::create([
				'user_id' => $user->id,
				'title' => $notificationTitle,
				'body' => $ratingMessage,
				'type' => 'rating',
				'related_id' => $reservation->id,
				'related_type' => 'App\\Models\\Reservation',
				'read_at' => null
			]);
		}

		// Notify GSU users
		$gsuUsers = \App\Models\User::where('role', 'GSU')->get();
		foreach ($gsuUsers as $user) {
			\App\Models\Notification::create([
				'user_id' => $user->id,
				'title' => $notificationTitle,
				'body' => $ratingMessage,
				'type' => 'rating',
				'related_id' => $reservation->id,
				'related_type' => 'App\\Models\\Reservation',
				'read_at' => null
			]);
		}

		// Notify Ms. Mhadel users
		$mhadelUsers = \App\Models\User::where('role', 'Ms. Mhadel')->get();
		foreach ($mhadelUsers as $user) {
			\App\Models\Notification::create([
				'user_id' => $user->id,
				'title' => $notificationTitle,
				'body' => $ratingMessage,
				'type' => 'rating',
				'related_id' => $reservation->id,
				'related_type' => 'App\\Models\\Reservation',
				'read_at' => null
			]);
		}

		// Notify OTP users
		$otpUsers = \App\Models\User::where('role', 'OTP')->get();
		foreach ($otpUsers as $user) {
			\App\Models\Notification::create([
				'user_id' => $user->id,
				'title' => $notificationTitle,
				'body' => $ratingMessage,
				'type' => 'rating',
				'related_id' => $reservation->id,
				'related_type' => 'App\\Models\\Reservation',
				'read_at' => null
			]);
		}
	}

	/**
	 * Get rating text based on numeric rating
	 */
	private function getRatingText($rating)
	{
		switch ($rating) {
			case 1:
				return 'Poor';
			case 2:
				return 'Fair';
			case 3:
				return 'Good';
			case 4:
				return 'Very Good';
			case 5:
				return 'Excellent';
			default:
				return 'Unknown';
		}
	}
}
