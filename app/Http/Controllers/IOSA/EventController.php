<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Models\User;
use App\Models\Notification;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of events for IOSA users
     */
    public function index(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('event_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($search) {
                      $venueQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('start_date', '<=', $request->end_date . ' 23:59:59');
        }

        $events = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        $venues = Venue::orderBy('name')->get();

        return view('iosa.events.index', compact('events', 'venues'));
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        return view('iosa.events.show', compact('event'));
    }

    /**
     * Display calendar view for events
     */
    public function calendar()
    {
        $events = Event::with(['venue'])->get();
        $reservations = collect(); // IOSA doesn't manage reservations, only views events
        
        return view('iosa.events.calendar', compact('events', 'reservations'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        $venues = Venue::active()->available()->orderBy('name')->get();
        return view('iosa.events.create', compact('venues'));
    }

    /**
     * Store a newly created event in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'organizer' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'venue_id' => 'nullable|exists:venues,id',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required_with:equipment.*.quantity|string',
            'equipment.*.quantity' => 'required_with:equipment.*.name|integer|min:1',
        ]);

        // Check for venue conflicts if venue is selected
        if ($request->venue_id) {
            // Check for conflicting events
            $conflictingEvents = Event::where('venue_id', $request->venue_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    // Check for any overlap: new event starts before existing ends AND new event ends after existing starts
                    $query->where('start_date', '<', $request->end_date)
                          ->where('end_date', '>', $request->start_date);
                })
                ->exists();

            // Check for conflicting reservations
            $conflictingReservations = Reservation::where('venue_id', $request->venue_id)
                ->whereIn('status', ['approved_IOSA', 'approved_mhadel', 'approved_OTP'])
                ->where(function ($query) use ($request) {
                    // Check for any overlap: new event starts before existing ends AND new event ends after existing starts
                    $query->where('start_date', '<', $request->end_date)
                          ->where('end_date', '>', $request->start_date);
                })
                ->exists();

            if ($conflictingEvents || $conflictingReservations) {
                $conflictType = $conflictingEvents ? 'event' : 'reservation';
                return back()->withErrors(['venue_id' => "The selected venue is already booked by another {$conflictType} for the specified time period."])
                            ->withInput();
            }
        }

        // Process equipment details
        $equipmentDetails = [];
        if ($request->has('equipment') && is_array($request->equipment)) {
            foreach ($request->equipment as $equipment) {
                // Only add equipment if both name and quantity are provided and not empty
                if (!empty($equipment['name']) && !empty($equipment['quantity']) && 
                    trim($equipment['name']) !== '' && trim($equipment['quantity']) !== '') {
                    $equipmentDetails[] = [
                        'name' => trim($equipment['name']),
                        'quantity' => (int) $equipment['quantity']
                    ];
                }
            }
        }

        // Determine status and venue assignment based on venue selection
        $status = $request->venue_id ? 'upcoming' : 'pending_venue';
        $needsVenueAssignment = !$request->venue_id;

        // Create event with or without venue
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'venue_id' => $request->venue_id,
            'organizer' => $request->organizer,
            'department' => $request->department,
            'status' => $status,
            'max_participants' => $request->max_participants,
            'equipment_details' => $equipmentDetails,
            'created_by' => Auth::id(),
            'needs_venue_assignment' => $needsVenueAssignment,
            'created_by_role' => 'iosa',
        ]);

        // Notify Mhadel users if venue assignment is needed
        if ($needsVenueAssignment) {
            $mhadelUsers = User::where('role', 'Ms. Mhadel')->get();
            foreach ($mhadelUsers as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'New Event Needs Venue Assignment',
                    'body' => 'IOSA created event "' . $event->title . '" and it needs venue assignment.',
                    'type' => 'event_venue_assignment',
                    'related_id' => $event->id,
                    'related_type' => Event::class,
                ]);
            }
            $message = 'Event created successfully! It has been sent to Ms. Mhadel for venue assignment.';
        } else {
            $message = 'Event created successfully with venue assigned!';
        }

        return redirect()->route('iosa.events.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        return view('iosa.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'organizer' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required_with:equipment.*.quantity|string',
            'equipment.*.quantity' => 'required_with:equipment.*.name|integer|min:1',
        ]);

        // Process equipment details
        $equipmentDetails = [];
        if ($request->has('equipment') && is_array($request->equipment)) {
            foreach ($request->equipment as $equipment) {
                if (!empty($equipment['name']) && !empty($equipment['quantity']) && 
                    trim($equipment['name']) !== '' && trim($equipment['quantity']) !== '') {
                    $equipmentDetails[] = [
                        'name' => trim($equipment['name']),
                        'quantity' => (int) $equipment['quantity']
                    ];
                }
            }
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'organizer' => $request->organizer,
            'department' => $request->department,
            'max_participants' => $request->max_participants,
            'equipment_details' => $equipmentDetails,
        ]);

        // Notify Mhadel users about the event update
        $mhadelUsers = User::where('role', 'Ms. Mhadel')->get();
        foreach ($mhadelUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Event Updated',
                'body' => 'IOSA updated event "' . $event->title . '". Please review the changes.',
                'type' => 'event_updated',
                'related_id' => $event->id,
                'related_type' => Event::class,
            ]);
        }

        return redirect()->route('iosa.events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Check for venue conflicts via AJAX
     */
    public function checkConflicts(Request $request)
    {
        try {
            \Log::info('Conflict check started', ['request' => $request->all()]);
            
            $request->validate([
                'venue_id' => 'required|exists:venues,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'event_id' => 'nullable|exists:events,id', // For editing existing events
            ]);
            
            \Log::info('Validation passed');

            \Log::info('Querying conflicting events...');
            $conflictingEvents = Event::where('venue_id', $request->venue_id)
                ->where('status', '!=', 'cancelled')
                ->when($request->event_id, function ($query) use ($request) {
                    return $query->where('id', '!=', $request->event_id);
                })
                ->where(function ($query) use ($request) {
                    // Check for any overlap: new event starts before existing ends AND new event ends after existing starts
                    $query->where('start_date', '<', $request->end_date)
                          ->where('end_date', '>', $request->start_date);
                })
                ->get(['title', 'start_date', 'end_date']);
            
            \Log::info('Events query completed', ['count' => $conflictingEvents->count()]);

            \Log::info('Querying conflicting reservations...');
            $conflictingReservations = Reservation::where('venue_id', $request->venue_id)
                ->whereIn('status', ['approved_IOSA', 'approved_mhadel', 'approved_OTP'])
                ->when($request->event_id, function ($query) use ($request) {
                    return $query->where('id', '!=', $request->event_id);
                })
                ->where(function ($query) use ($request) {
                    // Check for any overlap: new event starts before existing ends AND new event ends after existing starts
                    $query->where('start_date', '<', $request->end_date)
                          ->where('end_date', '>', $request->start_date);
                })
                ->get(['event_title', 'start_date', 'end_date']); // Use event_title instead of title
            
            \Log::info('Reservations query completed', ['count' => $conflictingReservations->count()]);

            \Log::info('Building response...');
            $allConflicts = collect();
            
            // Process events
            foreach ($conflictingEvents as $event) {
                $allConflicts->push([
                    'title' => $event->title . ' (Event)',
                    'start_date' => $event->start_date->format('M j, Y g:i A'),
                    'end_date' => $event->end_date->format('M j, Y g:i A'),
                ]);
            }
            
            // Process reservations
            foreach ($conflictingReservations as $reservation) {
                $allConflicts->push([
                    'title' => $reservation->event_title . ' (Reservation)',
                    'start_date' => $reservation->start_date->format('M j, Y g:i A'),
                    'end_date' => $reservation->end_date->format('M j, Y g:i A'),
                ]);
            }

            \Log::info('Response built successfully', [
                'hasConflicts' => $allConflicts->count() > 0,
                'conflictCount' => $allConflicts->count()
            ]);

            return response()->json([
                'hasConflicts' => $allConflicts->count() > 0,
                'conflicts' => $allConflicts
            ]);
        } catch (\Exception $e) {
            \Log::error('Conflict check error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to check conflicts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export events to Excel
     */
    public function export(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('event_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($search) {
                      $venueQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('start_date', '<=', $request->end_date . ' 23:59:59');
        }

        // Get all filtered events (no pagination for export)
        $events = $query->orderByDesc('created_at')->get();

        // Generate filename with filters if applicable
        $filename = 'IOSA_Events';
        if ($request->filled('status') && $request->status !== 'all') {
            $filename .= '_' . ucfirst($request->status);
        }
        if ($request->filled('search')) {
            $filename .= '_Search_' . str_replace(' ', '_', $request->search);
        }
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $filename .= '_DateFiltered';
        }
        $filename .= '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\IOSAEventsExport($events), $filename);
    }
}
