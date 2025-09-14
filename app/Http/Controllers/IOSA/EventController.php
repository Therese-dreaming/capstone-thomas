<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Models\User;
use App\Models\Notification;
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
        return view('iosa.events.create');
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
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required_with:equipment.*.quantity|string',
            'equipment.*.quantity' => 'required_with:equipment.*.name|integer|min:1',
        ]);

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

        // Create event without venue - needs venue assignment
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'venue_id' => null, // No venue assigned yet
            'organizer' => $request->organizer,
            'department' => $request->department,
            'status' => 'pending_venue', // New status for events waiting for venue assignment
            'max_participants' => $request->max_participants,
            'equipment_details' => $equipmentDetails,
            'created_by' => Auth::id(),
            'needs_venue_assignment' => true,
            'created_by_role' => 'iosa',
        ]);

        // Notify Mhadel users about the new event that needs venue assignment
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

        return redirect()->route('iosa.events.index')
            ->with('success', 'Event created successfully! It has been sent to Ms. Mhadel for venue assignment.');
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        // Only allow editing if event is in pending_venue status
        if ($event->status !== 'pending_venue') {
            return redirect()->route('iosa.events.show', $event)
                ->with('error', 'This event can no longer be edited as it has progressed beyond the pending venue status.');
        }

        return view('iosa.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage
     */
    public function update(Request $request, Event $event)
    {
        // Only allow updating if event is in pending_venue status
        if ($event->status !== 'pending_venue') {
            return redirect()->route('iosa.events.show', $event)
                ->with('error', 'This event can no longer be updated as it has progressed beyond the pending venue status.');
        }

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
}
