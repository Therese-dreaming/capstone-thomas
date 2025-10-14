<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Reservation;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
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
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('event_id', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('organizer', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($searchTerm) {
                      $venueQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply date range filters
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to . ' 23:59:59');
        }

        // Apply venue filter
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Apply organizer filter
        if ($request->filled('organizer')) {
            $query->where('organizer', 'like', "%{$request->organizer}%");
        }

        // Apply equipment filter
        if ($request->filled('has_equipment')) {
            if ($request->has_equipment == '1') {
                $query->whereNotNull('equipment_details')
                      ->where(function($q) {
                          $q->whereJsonLength('equipment_details', '>', 0)
                            ->orWhere('equipment_details', '!=', '[]');
                      });
            } else {
                $query->where(function($q) {
                    $q->whereNull('equipment_details')
                      ->orWhereJsonLength('equipment_details', 0)
                      ->orWhere('equipment_details', '[]');
                });
            }
        }

        // Apply duration filter
        if ($request->filled('duration')) {
            $duration = $request->duration;
            $query->whereNotNull('start_date')
                  ->whereNotNull('end_date')
                  ->where(function($q) use ($duration) {
                      switch ($duration) {
                          case '1':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) <= 1');
                              break;
                          case '2':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 2 AND 4');
                              break;
                          case '5':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 5 AND 8');
                              break;
                          case '9':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) > 8');
                              break;
                      }
                  });
        }

        // Apply created date filter
        if ($request->filled('created_period')) {
            $period = $request->created_period;
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Get paginated results
        $events = $query->latest()->paginate(10)->withQueryString();

        return view('mhadel.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $venues = \App\Models\Venue::where('is_available', true)->get();
        return view('mhadel.events.create', compact('venues'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_id' => 'required|exists:venues,id',
            'organizer' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required_with:equipment|string',
            'equipment.*.quantity' => 'required_with:equipment|integer|min:1',
        ]);

        // Automatically determine event status based on scheduled date
        $now = now();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        
        $status = 'upcoming'; // Default status
        
        if ($startDate <= $now && $endDate >= $now) {
            $status = 'ongoing';
        }
        // Note: Events are no longer automatically marked as completed
        // GSU users must manually mark events as complete

        // Conflict checks: reservations (blocking statuses) and other events (not cancelled)
        $blockingStatuses = ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'];
        $conflictReservation = Reservation::where('venue_id', $request->venue_id)
            ->whereIn('status', $blockingStatuses)
            ->where(function($q) use ($request) {
                $q->where('start_date', '<', $request->end_date)
                    ->where('end_date', '>', $request->start_date);
            })
            ->exists();
        if ($conflictReservation) {
            return back()->withErrors(['start_date' => 'This schedule overlaps with an existing reservation for the selected venue.'])->withInput();
        }

        $conflictEvent = Event::where('venue_id', $request->venue_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($request) {
                $q->where('start_date', '<', $request->end_date)
                    ->where('end_date', '>', $request->start_date);
            })
            ->exists();
        if ($conflictEvent) {
            return back()->withErrors(['start_date' => 'This schedule overlaps with another event for the selected venue.'])->withInput();
        }

        // Process equipment details
        $equipmentDetails = [];
        if ($request->has('equipment') && is_array($request->equipment)) {
            foreach ($request->equipment as $equipment) {
                if (!empty($equipment['name']) && !empty($equipment['quantity'])) {
                    $equipmentDetails[] = [
                        'name' => $equipment['name'],
                        'quantity' => (int) $equipment['quantity']
                    ];
                }
            }
        }

        Event::create([
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
        ]);

        return redirect()->route('mhadel.events.index')
            ->with('success', 'Event created successfully! It will now appear on the final calendar.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('mhadel.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $venues = \App\Models\Venue::where('is_available', true)->orderBy('name')->get();
        return view('mhadel.events.edit', compact('event', 'venues'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_id' => 'required|exists:venues,id',
            'organizer' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled,pending_venue',
            'event_type' => 'nullable|in:academic,administrative,student_activity,community_service,other',
        ]);

        // Use the status from the request instead of auto-determining
        $status = $request->status;
        
        $now = now();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        // If event was in pending_venue status and now has a venue, update status accordingly
        if ($event->status === 'pending_venue' && $request->venue_id) {
            if ($startDate <= $now && $endDate >= $now) {
                $status = 'ongoing';
            } else {
                $status = 'upcoming';
            }
        }
        // Otherwise only auto-determine status if it's explicitly set as upcoming
        elseif ($status === 'upcoming') {
            if ($startDate <= $now && $endDate >= $now) {
                $status = 'ongoing';
            } elseif ($startDate < $now && $endDate < $now) {
                $status = 'completed';
            }
        }
        
        // If event was previously cancelled, keep it cancelled unless explicitly changed
        if ($event->status === 'cancelled') {
            $status = 'cancelled';
        }

        $blockingStatuses = ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'];
        $conflictReservation = Reservation::where('venue_id', $request->venue_id)
            ->whereIn('status', $blockingStatuses)
            ->where(function($q) use ($request) {
                $q->where('start_date', '<', $request->end_date)
                    ->where('end_date', '>', $request->start_date);
            })
            ->exists();
        if ($conflictReservation) {
            return back()->withErrors(['start_date' => 'This schedule overlaps with an existing reservation for the selected venue.'])->withInput();
        }

        $conflictEvent = Event::where('venue_id', $request->venue_id)
            ->where('id', '!=', $event->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($request) {
                $q->where('start_date', '<', $request->end_date)
                    ->where('end_date', '>', $request->start_date);
            })
            ->exists();
        if ($conflictEvent) {
            return back()->withErrors(['start_date' => 'This schedule overlaps with another event for the selected venue.'])->withInput();
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'venue_id' => $request->venue_id,
            'organizer' => $request->organizer,
            'department' => $request->department,
            'status' => $status,
            'max_participants' => $request->max_participants,
            'event_type' => $request->event_type,
        ]);

        return redirect()->route('mhadel.events.index')
            ->with('success', 'Event updated successfully! Changes will reflect on the final calendar.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('mhadel.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Cancel the specified event.
     */
    public function cancel(Event $event)
    {
        // Check if event is already cancelled
        if ($event->status === 'cancelled') {
            return back()->with('error', 'Event is already cancelled.');
        }

        // Update event status to cancelled
        $event->update(['status' => 'cancelled']);

        return redirect()->route('mhadel.events.show', $event)
            ->with('success', 'Event cancelled successfully! The event has been marked as cancelled but all information is preserved.');
    }

    /**
     * Manually update event statuses based on current time.
     * Only ongoing status updates are automatic now.
     */
    public function updateStatuses()
    {
        $now = now();
        $updatedCount = 0;

        // Only update upcoming events to ongoing (this is still useful)
        $ongoingEvents = Event::where('status', 'upcoming')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        foreach ($ongoingEvents as $event) {
            $event->update(['status' => 'ongoing']);
            $updatedCount++;
        }

        return back()->with('success', "Event statuses updated successfully! {$updatedCount} events were updated to ongoing status. Note: Events are no longer automatically marked as completed.");
    }

    /**
     * Mark an event as completed (GSU users only)
     */
    public function markAsComplete(Event $event)
    {
        // Check if event can be marked as complete
        if ($event->status === 'completed') {
            return back()->with('error', 'Event is already marked as completed.');
        }

        if ($event->status === 'cancelled') {
            return back()->with('error', 'Cancelled events cannot be marked as completed.');
        }

        // Update event status to completed
        $event->update(['status' => 'completed']);

        return back()->with('success', "Event '{$event->title}' has been marked as completed successfully!");
    }

    /**
     * Update event schedule (date/time).
     */
    public function updateSchedule(Request $request, Event $event)
    {
        // Check if event can be edited (not cancelled)
        if ($event->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit cancelled events.'
            ], 400);
        }
        
        $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);
        
        $newStartDate = $request->start_datetime;
        $newEndDate = $request->end_datetime;
        
        // Check for conflicts with other events at the same venue
        $eventConflicts = Event::where('id', '!=', $event->id)
            ->where('venue_id', $event->venue_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        // Check for conflicts with reservations at the same venue
        $reservationConflicts = Reservation::where('venue_id', $event->venue_id)
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        if ($eventConflicts->count() > 0 || $reservationConflicts->count() > 0) {
            $conflictDetails = [];
            
            foreach ($eventConflicts as $conflict) {
                $conflictDetails[] = [
                    'type' => 'Event',
                    'title' => $conflict->title,
                    'start' => $conflict->start_date,
                    'end' => $conflict->end_date,
                    'user' => $conflict->organizer ?? 'Official Event'
                ];
            }
            
            foreach ($reservationConflicts as $conflict) {
                $conflictDetails[] = [
                    'type' => 'Reservation',
                    'title' => $conflict->event_title,
                    'start' => $conflict->start_date,
                    'end' => $conflict->end_date,
                    'user' => $conflict->user->name ?? 'Unknown'
                ];
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflict detected with existing events/reservations.',
                'conflicts' => $conflictDetails
            ], 409);
        }
        
        // Update the event
        $event->update([
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Event schedule updated successfully.'
        ]);
    }

    /**
     * Check for schedule conflicts without updating.
     */
    public function checkConflicts(Request $request, Event $event = null)
    {
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $newStartDate = $request->start_date;
        $newEndDate = $request->end_date;
        $newVenueId = $request->venue_id;
        
        // Check for conflicts with other events at the same venue
        $eventQuery = Event::where('venue_id', $newVenueId)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            });
        
        // Exclude current event if editing
        if ($event) {
            $eventQuery->where('id', '!=', $event->id);
        }
        
        $eventConflicts = $eventQuery->get();
        
        // Check for conflicts with reservations at the same venue
        $reservationConflicts = Reservation::where('venue_id', $newVenueId)
            ->whereIn('status', ['pending', 'approved_IOSA', 'approved_mhadel', 'approved_OTP'])
            ->where(function($query) use ($newStartDate, $newEndDate) {
                $query->where(function($q) use ($newStartDate, $newEndDate) {
                    $q->where('start_date', '<', $newEndDate)
                      ->where('end_date', '>', $newStartDate);
                });
            })
            ->get();
        
        $allConflicts = [];
        
        foreach ($eventConflicts as $conflict) {
            $allConflicts[] = [
                'type' => 'Event',
                'title' => $conflict->title,
                'start_date' => $conflict->start_date->format('M d, Y g:i A'),
                'end_date' => $conflict->end_date->format('M d, Y g:i A'),
                'user' => $conflict->organizer ?? 'Official Event'
            ];
        }
        
        foreach ($reservationConflicts as $conflict) {
            $allConflicts[] = [
                'type' => 'Reservation',
                'title' => $conflict->event_title,
                'start_date' => $conflict->start_date->format('M d, Y g:i A'),
                'end_date' => $conflict->end_date->format('M d, Y g:i A'),
                'user' => $conflict->user->name ?? 'Unknown'
            ];
        }
        
        return response()->json([
            'success' => true,
            'hasConflicts' => count($allConflicts) > 0,
            'conflicts' => $allConflicts
        ]);
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
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('event_id', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('organizer', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($searchTerm) {
                      $venueQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply date range filters
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to . ' 23:59:59');
        }

        // Apply venue filter
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Apply organizer filter
        if ($request->filled('organizer')) {
            $query->where('organizer', 'like', "%{$request->organizer}%");
        }

        // Apply equipment filter
        if ($request->filled('has_equipment')) {
            if ($request->has_equipment == '1') {
                $query->whereNotNull('equipment_details')
                      ->where(function($q) {
                          $q->whereJsonLength('equipment_details', '>', 0)
                            ->orWhere('equipment_details', '!=', '[]');
                      });
            } else {
                $query->where(function($q) {
                    $q->whereNull('equipment_details')
                      ->orWhereJsonLength('equipment_details', 0)
                      ->orWhere('equipment_details', '[]');
                });
            }
        }

        // Apply duration filter
        if ($request->filled('duration')) {
            $duration = $request->duration;
            $query->whereNotNull('start_date')
                  ->whereNotNull('end_date')
                  ->where(function($q) use ($duration) {
                      switch ($duration) {
                          case '1':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) <= 1');
                              break;
                          case '2':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 2 AND 4');
                              break;
                          case '5':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 5 AND 8');
                              break;
                          case '9':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) > 8');
                              break;
                      }
                  });
        }

        // Apply created date filter
        if ($request->filled('created_period')) {
            $period = $request->created_period;
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Get all filtered events (no pagination for export)
        $events = $query->orderByDesc('created_at')->get();

        // Generate filename with filters if applicable
        $filename = 'Mhadel_Events';
        if ($request->filled('status') && $request->status !== 'all') {
            $filename .= '_' . ucfirst($request->status);
        }
        if ($request->filled('search')) {
            $filename .= '_Search_' . str_replace(' ', '_', $request->search);
        }
        if ($request->filled('venue_id')) {
            $venue = \App\Models\Venue::find($request->venue_id);
            $filename .= '_' . str_replace(' ', '_', $venue->name ?? 'Venue');
        }
        if ($request->filled('department')) {
            $filename .= '_' . str_replace(' ', '_', $request->department);
        }
        if ($request->filled('start_date_from') || $request->filled('start_date_to')) {
            $filename .= '_DateFiltered';
        }
        $filename .= '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MhadelEventsExport($events), $filename);
    }

}
