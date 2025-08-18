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
    public function index()
    {
        $events = Event::latest()->paginate(10);
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
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'max_participants' => 'nullable|integer|min:1',
        ]);

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
            'status' => $request->status,
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
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'max_participants' => 'nullable|integer|min:1',
        ]);

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
            'status' => $request->status,
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
}
