<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusChanged;
use Illuminate\Support\Facades\Storage;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'venue']);
        
        // Set default status to 'pending' if no status is specified
        $status = $request->query('status', 'pending');
        
        if ($status === 'all') {
            // Show all reservations regardless of status
            // No additional filtering needed
        } elseif ($status === 'pending') {
            $query->where('status', 'approved_IOSA'); // Map 'pending' to 'approved_IOSA' for Mhadel's context
        } elseif ($status === 'approved') {
            $query->where('status', 'approved_mhadel');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected_mhadel');
        }
        
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }
        if ($request->filled('venue')) {
            $query->where('venue_id', $request->venue);
        }
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }
        
        // Add search functionality
        if ($request->filled('search')) {
            $searchQuery = $request->search;
            $query->where(function ($q) use ($searchQuery) {
                $q->where('event_title', 'like', "%{$searchQuery}%")
                  ->orWhere('reservation_id', 'like', "%{$searchQuery}%")
                  ->orWhere('purpose', 'like', "%{$searchQuery}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchQuery) {
                      $userQuery->where('name', 'like', "%{$searchQuery}%")
                                ->orWhere('email', 'like', "%{$searchQuery}%");
                  })
                  ->orWhereHas('venue', function ($venueQuery) use ($searchQuery) {
                      $venueQuery->where('name', 'like', "%{$searchQuery}%");
                  });
            });
        }
        
        $reservations = $query->select([
            'id', 'user_id', 'venue_id', 'event_title', 'start_date', 'end_date', 
            'purpose', 'status', 'notes', 'base_price', 'discount_percentage', 'final_price', 
            'price_per_hour', 'duration_hours', 'created_at'
        ])->with(['user', 'venue'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        $stats = [
            'total' => Reservation::count(), // All reservations regardless of status
            'pending' => Reservation::where('status', 'approved_IOSA')->count(),
            'approved' => Reservation::where('status', 'approved_mhadel')->count(),
            'rejected' => Reservation::where('status', 'rejected_mhadel')->count(),
        ];
        
        $venues = \App\Models\Venue::orderBy('name')->get();
        
        return view('mhadel.reservations.index', compact('reservations', 'stats', 'venues'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'venue'])->findOrFail($id);

        return view('mhadel.reservations.show', compact('reservation'));
    }

    /**
     * Calendar view: show all reservations (regardless of status) and official events.
     */
    public function calendar(Request $request)
    {
        // Get all reservations for calendar view (regardless of status)
        $reservations = Reservation::with(['user','venue'])
            ->orderBy('start_date')
            ->get(['id','user_id','venue_id','event_title','start_date','end_date','status','final_price','capacity','purpose']);

        $events = Event::with(['venue'])
            ->orderBy('start_date')
            ->get(['id','venue_id','title','organizer','start_date','end_date','status','max_participants']);

        return view('mhadel.reservations.calendar', [
            'reservations' => $reservations,
            'events' => $events,
        ]);
    }

    /**
     * Approve a reservation.
     */
    public function approve(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'approved_IOSA') {
            return redirect()->back()->with('error', 'Only IOSA approved reservations can be approved by Ms. Mhadel.');
        }
        
        $notes = $request->input('notes', '');
        $newBasePrice = $request->input('base_price', 0);
        $discount = $request->input('discount', 0);
        $newFinalPrice = $request->input('final_price', 0);
        
        $approvalNote = "[Ms. Mhadel Approved on " . now()->format('M d, Y H:i') . "]";
        if ($notes) {
            $approvalNote .= "\nNotes: " . $notes;
        }
        
        // Add pricing information to notes
        $approvalNote .= "\nPricing Review:";
        if ($reservation->base_price > 0) {
            $approvalNote .= "\n- User's Base Price: ₱" . number_format($reservation->base_price, 2);
        } else {
            $approvalNote .= "\n- User's Base Price: Free Event";
        }
        
        if ($newBasePrice > 0) {
            $approvalNote .= "\n- Ms. Mhadel's Base Price: ₱" . number_format($newBasePrice, 2);
            if ($discount > 0) {
                $approvalNote .= "\n- Discount Applied: " . $discount . "%";
            }
            $approvalNote .= "\n- Final Price: ₱" . number_format($newFinalPrice, 2);
        } else {
            $approvalNote .= "\n- Final Pricing: Free Event";
        }
        
        $reservation->update([
            'status' => 'approved_mhadel',
            'notes' => $reservation->notes . "\n" . $approvalNote,
            'base_price' => $newBasePrice,
            'discount_percentage' => $discount,
            'final_price' => $newFinalPrice
        ]);
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was approved by Ms. Mhadel',
            'body' => 'Reservation "' . $reservation->event_title . '" is forwarded to OTP for final approval.',
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Notify OTP reviewer(s)
        $otpUser = User::where('role', 'otp')->first();
        if ($otpUser) {
            Notification::create([
                'user_id' => $otpUser->id,
                'title' => 'Reservation requires final approval',
                'body' => 'Reservation "' . $reservation->event_title . '" is awaiting your final approval.',
                'type' => 'reservation_action',
                'related_id' => $reservation->id,
                'related_type' => Reservation::class,
            ]);
        }
        
        // Self notification to Ms. Mhadel actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You approved a reservation',
            'body' => 'You approved "' . $reservation->event_title . '" and forwarded to OTP.',
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester with pricing details
        $pricing = [
            'base_price' => $newBasePrice > 0 ? '₱' . number_format($newBasePrice, 2) : 'Free',
            'discount' => $discount > 0 ? $discount . '%' : '—',
            'final_price' => $newFinalPrice > 0 ? '₱' . number_format($newFinalPrice, 2) : 'Free',
        ];
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'approved_mhadel',
            'Ms. Mhadel',
            ['pricing' => $pricing]
        ));
        
        return redirect()->back()->with('success', 'Reservation approved successfully. Forwarded to OTP for final approval.');
    }

    /**
     * Reject a reservation.
     */
    public function reject(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'approved_IOSA') {
            return redirect()->back()->with('error', 'Only IOSA approved reservations can be rejected by Ms. Mhadel.');
        }
        
        $notes = $request->input('notes', '');
        if (!$notes) {
            return redirect()->back()->with('error', 'Please provide a reason for rejection.');
        }
        
        $rejectionNote = "[Ms. Mhadel Rejected on " . now()->format('M d, Y H:i') . "]\nReason: " . $notes;
        $reservation->update([
            'status' => 'rejected_mhadel',
            'notes' => $reservation->notes . "\n" . $rejectionNote
        ]);
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was rejected by Ms. Mhadel',
            'body' => 'Reservation "' . $reservation->event_title . '" was rejected. Reason: ' . $notes,
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Self notification to Ms. Mhadel actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You rejected a reservation',
            'body' => 'You rejected "' . $reservation->event_title . '". Reason: ' . $notes,
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester with reason
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'rejected_mhadel',
            'Ms. Mhadel',
            ['reason' => $notes]
        ));
        
        return redirect()->back()->with('success', 'Reservation rejected successfully.');
    }

    /**
     * Download activity grid file.
     */
    public function downloadActivityGrid(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        if (!$reservation->activity_grid) {
            return redirect()->back()->with('error', 'No activity grid found for this reservation.');
        }
        
        // Check if activity_grid is a file path (stored file)
        if (Storage::disk('public')->exists($reservation->activity_grid)) {
            $filePath = $reservation->activity_grid;
            $originalName = basename($filePath);
            
            // Extract original filename without timestamp prefix
            if (preg_match('/^\d+_(.+)$/', $originalName, $matches)) {
                $originalName = $matches[1];
            }
            
            return Storage::disk('public')->download($filePath, $originalName);
        }
        
        // Fallback: if it's plain text (legacy data), download as text file
        $filename = 'activity_grid_' . $reservation->event_title . '_' . $reservation->id . '.txt';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        
        return response($reservation->activity_grid)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(string $id)
    {
        $reservation = Reservation::with(['user', 'venue'])->findOrFail($id);
        
        // Check if reservation can be edited (not rejected or cancelled)
        if (in_array($reservation->status, ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP', 'cancelled'])) {
            return redirect()->back()->with('error', 'Cannot edit rejected or cancelled reservations.');
        }
        
        $venues = \App\Models\Venue::where('is_available', true)->orderBy('name')->get();
        
        return view('mhadel.reservations.edit', compact('reservation', 'venues'));
    }

    /**
     * Update the specified reservation.
     */
    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Check if reservation can be edited (not rejected or cancelled)
        if (in_array($reservation->status, ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP', 'cancelled'])) {
            return redirect()->back()->with('error', 'Cannot edit rejected or cancelled reservations.');
        }
        
        $request->validate([
            'event_title' => 'required|string|max:255',
            'purpose' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'department' => 'nullable|string|max:255',
            'venue_id' => 'required|exists:venues,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved_IOSA,approved_mhadel,approved_OTP,completed,rejected_IOSA,rejected_mhadel,rejected_OTP,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        // Check for conflicts with other reservations/events at the same venue
        $conflicts = Reservation::where('id', '!=', $id)
            ->where('venue_id', $request->venue_id)
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_date', '<', $request->end_date)
                      ->where('end_date', '>', $request->start_date);
                });
            })
            ->get();
        
        $eventConflicts = Event::where('venue_id', $request->venue_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_date', '<', $request->end_date)
                      ->where('end_date', '>', $request->start_date);
                });
            })
            ->get();
        
        if ($conflicts->count() > 0 || $eventConflicts->count() > 0) {
            return redirect()->back()->with('error', 'Schedule conflict detected with existing reservations/events. Please choose a different time or venue.')->withInput();
        }
        
        // Calculate pricing
        $venue = \App\Models\Venue::find($request->venue_id);
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $durationHours = $startDate->diffInHours($endDate);
        
        $basePrice = $venue->price_per_hour * $durationHours;
        $discount = $request->discount_percentage ?? 0;
        $finalPrice = $basePrice * (1 - $discount / 100);
        
        // Update the reservation
        $reservation->update([
            'event_title' => $request->event_title,
            'purpose' => $request->purpose,
            'capacity' => $request->capacity,
            'department' => $request->department,
            'venue_id' => $request->venue_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_hours' => $durationHours,
            'base_price' => $basePrice,
            'discount_percentage' => $discount,
            'final_price' => $finalPrice,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);
        
        // Create notification for the user
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation has been updated',
            'body' => 'Reservation "' . $reservation->event_title . '" has been updated by Ms. Mhadel.',
            'type' => 'reservation_updated',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Self notification for Ms. Mhadel
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You updated a reservation',
            'body' => 'You updated "' . $reservation->event_title . '".',
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        return redirect()->route('mhadel.reservations.show', $reservation->id)
            ->with('success', 'Reservation updated successfully!');
    }

    /**
     * Update reservation schedule (date/time).
     */
    public function updateSchedule(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Check if reservation can be edited (not rejected or cancelled)
        if (in_array($reservation->status, ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit rejected or cancelled reservations.'
            ], 400);
        }
        
        $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);
        
        $newStartDate = $request->start_datetime;
        $newEndDate = $request->end_datetime;
        
        // Check for conflicts with other reservations/events at the same venue
        $conflicts = Reservation::where('id', '!=', $id)
            ->where('venue_id', $reservation->venue_id)
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        $eventConflicts = Event::where('venue_id', $reservation->venue_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        if ($conflicts->count() > 0 || $eventConflicts->count() > 0) {
            $conflictDetails = [];
            
            foreach ($conflicts as $conflict) {
                $conflictDetails[] = [
                    'type' => 'Reservation',
                    'title' => $conflict->event_title,
                    'start' => $conflict->start_date,
                    'end' => $conflict->end_date,
                    'user' => $conflict->user->name ?? 'Unknown'
                ];
            }
            
            foreach ($eventConflicts as $conflict) {
                $conflictDetails[] = [
                    'type' => 'Event',
                    'title' => $conflict->title,
                    'start' => $conflict->start_date,
                    'end' => $conflict->end_date,
                    'user' => $conflict->organizer ?? 'Official Event'
                ];
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict detected with existing reservations/events.',
                'conflicts' => $conflictDetails
            ], 409);
        }
        
        // Update the reservation
        $reservation->update([
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
        ]);
        
        // Create notification for the user
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation schedule has been updated',
            'body' => 'Reservation "' . $reservation->event_title . '" schedule has been updated by Ms. Mhadel.',
            'type' => 'reservation_updated',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Self notification for Ms. Mhadel
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You updated a reservation schedule',
            'body' => 'You updated the schedule for "' . $reservation->event_title . '".',
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reservation schedule updated successfully.'
        ]);
    }

    /**
     * Check for schedule conflicts without updating.
     */
    public function checkConflicts(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);
        
        $newStartDate = $request->start_datetime;
        $newEndDate = $request->end_datetime;
        $newVenueId = $request->venue_id;
        
        // Check for conflicts with other reservations at the same venue
        $conflicts = Reservation::where('id', '!=', $id)
            ->where('venue_id', $newVenueId)
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        // Check for conflicts with events at the same venue
        $eventConflicts = Event::where('venue_id', $newVenueId)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        $allConflicts = [];
        
        foreach ($conflicts as $conflict) {
            $allConflicts[] = [
                'type' => 'Reservation',
                'title' => $conflict->event_title,
                'start' => $conflict->start_date->format('M d, Y g:i A'),
                'end' => $conflict->end_date->format('M d, Y g:i A'),
                'user' => $conflict->user->name ?? 'Unknown'
            ];
        }
        
        foreach ($eventConflicts as $conflict) {
            $allConflicts[] = [
                'type' => 'Event',
                'title' => $conflict->title,
                'start' => $conflict->start_date->format('M d, Y g:i A'),
                'end' => $conflict->end_date->format('M d, Y g:i A'),
                'user' => $conflict->organizer ?? 'Official Event'
            ];
        }
        
        return response()->json([
            'success' => true,
            'conflicts' => $allConflicts
        ]);
    }
} 