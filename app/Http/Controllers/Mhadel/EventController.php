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
        $query = Event::query();

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('organizer', 'like', "%{$searchTerm}%");
            });
        }

        // Get paginated results
        $events = $query->latest()->paginate(10);

        return view('mhadel.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mhadel.events.create');
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
        return view('mhadel.events.edit', compact('event'));
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
        ]);

        // Automatically determine event status based on scheduled date
        $now = now();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        
        $status = 'upcoming'; // Default status
        
        if ($startDate <= $now && $endDate >= $now) {
            $status = 'ongoing';
        } elseif ($startDate < $now && $endDate < $now) {
            $status = 'completed';
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
}
