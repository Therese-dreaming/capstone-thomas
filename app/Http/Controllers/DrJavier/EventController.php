<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DrJavierEventsExport;

class EventController extends Controller
{
    /**
     * Display a listing of events for Dr. Javier users
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

        return view('drjavier.events.index', compact('events', 'venues'));
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        return view('drjavier.events.show', compact('event'));
    }

    /**
     * Export events to Excel
     */
    public function export(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply the same filters as index method
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

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

        // Apply advanced filters
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to . ' 23:59:59');
        }
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }
        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }
        if ($request->filled('organizer')) {
            $query->where('organizer', 'like', "%{$request->organizer}%");
        }

        // Single event export
        if ($request->filled('event_id')) {
            $query->where('id', $request->event_id);
        }

        $events = $query->orderByDesc('created_at')->get();

        $filename = 'drjavier_events_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        return Excel::download(new DrJavierEventsExport($events), $filename);
    }

    /**
     * Display calendar view for events
     */
    public function calendar()
    {
        $events = Event::with(['venue'])->get();
        $reservations = collect(); // Dr. Javier doesn't manage reservations, only views events
        
        return view('drjavier.events.calendar', compact('events', 'reservations'));
    }

}