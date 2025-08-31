<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of events for GSU users
     */
    public function index(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('start_date', '<=', $request->end_date . ' 23:59:59');
        }

        $events = $query->orderByDesc('created_at')->paginate(12);
        $venues = Venue::orderBy('name')->get();

        return view('gsu.events.index', compact('events', 'venues'));
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        return view('gsu.events.show', compact('event'));
    }

    /**
     * Mark an event as completed (GSU users only)
     */
    public function markAsComplete(Event $event, Request $request)
    {
        // Check if event can be marked as complete
        if ($event->status === 'completed') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Event is already marked as completed.']);
            }
            return back()->with('error', 'Event is already marked as completed.');
        }

        if ($event->status === 'cancelled') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cancelled events cannot be marked as completed.']);
            }
            return back()->with('error', 'Cancelled events cannot be marked as completed.');
        }

        // Update event with completion details
        $event->update([
            'status' => 'completed',
            'completion_notes' => $request->input('completion_notes'),
            'completion_date' => now(),
            'completed_by' => 'GSU'
        ]);

        // Create notifications for different roles
        $this->createCompletionNotifications($event, 'event', $request->input('completion_notes'));

        // Create report if report data is provided
        if ($request->filled(['type', 'severity', 'description'])) {
            $this->createReportFromCompletion($event, $request, 'event');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Event '{$event->title}' has been marked as completed successfully!"]);
        }

        return back()->with('success', "Event '{$event->title}' has been marked as completed successfully!");
    }

    /**
     * Create notifications for event/reservation completion
     */
    private function createCompletionNotifications($item, $type, $notes = null)
    {
        $title = $type === 'event' ? $item->title : $item->event_title;
        $completionMessage = "GSU has marked the {$type} '{$title}' as completed.";
        
        if ($notes) {
            $completionMessage .= " Notes: {$notes}";
        }

        // Notify IOSA users
        $iosaUsers = \App\Models\User::where('role', 'IOSA')->get();
        foreach ($iosaUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "{$type} Completed",
                'body' => $completionMessage,
                'type' => 'completion',
                'related_id' => $item->id,
                'related_type' => $type === 'event' ? 'App\\Models\\Event' : 'App\\Models\\Reservation',
                'read_at' => null
            ]);
        }

        // Notify Ms. Mhadel users
        $mhadelUsers = \App\Models\User::where('role', 'Mhadel')->get();
        foreach ($mhadelUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "{$type} Completed",
                'body' => $completionMessage,
                'type' => 'completion',
                'related_id' => $item->id,
                'related_type' => $type === 'event' ? 'App\\Models\\Event' : 'App\\Models\\Reservation',
                'read_at' => null
            ]);
        }

        // Notify OTP users
        $otpUsers = \App\Models\User::where('role', 'OTP')->get();
        foreach ($otpUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "{$type} Completed",
                'body' => $completionMessage,
                'type' => 'completion',
                'related_id' => $item->id,
                'related_type' => $type === 'event' ? 'App\\Models\\Event' : 'App\\Models\\Reservation',
                'read_at' => null
            ]);
        }
    }

    /**
     * Update event statuses (only ongoing updates)
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

        return back()->with('success', "Event statuses updated successfully! {$updatedCount} events were updated to ongoing status.");
    }

    /**
     * Display calendar view with events and reservations
     */
    public function calendar(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Get all events (not just for the month, so JavaScript can filter properly)
        $events = Event::with(['venue'])->get();
        
        // Get all approved reservations (not just for the month, so JavaScript can filter properly)
        $reservations = \App\Models\Reservation::with(['user', 'venue'])
            ->where('status', 'approved_OTP')
            ->get();
        
        return view('gsu.calendar.index', compact('events', 'reservations'));
    }

    /**
     * Report an issue with an event (GSU users only)
     */
    public function reportIssue(Event $event, Request $request)
    {
        $request->validate([
            'type' => 'required|in:accident,problem,violation,damage,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|min:10',
            'actions_taken' => 'nullable|string'
        ]);

        // Create the report
        $report = Report::create([
            'reported_user_id' => null, // Events don't have a specific user, so we'll leave this null
            'reporter_id' => auth()->id(),
            'reservation_id' => null,
            'event_id' => $event->id,
            'type' => $request->type,
            'severity' => $request->severity,
            'description' => $request->description,
            'actions_taken' => $request->actions_taken,
            'status' => 'pending'
        ]);

        // Create notifications for different roles
        $this->createReportNotifications($report, 'event');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => "Issue reported successfully. Report #{$report->id} has been created."
            ]);
        }

        return back()->with('success', "Issue reported successfully. Report #{$report->id} has been created.");
    }

    /**
     * Create notifications for issue reports
     */
    private function createReportNotifications($report, $type)
    {
        $itemTitle = $type === 'event' ? $report->event->title : $report->reservation->event_title;
        $reportedUser = $report->reportedUser ? $report->reportedUser->name : 'Event Organizer';
        $severityColor = [
            'low' => 'blue',
            'medium' => 'yellow', 
            'high' => 'orange',
            'critical' => 'red'
        ][$report->severity] ?? 'gray';

        $reportMessage = "GSU has reported a {$report->severity} severity {$report->type} issue with {$type} '{$itemTitle}' involving {$reportedUser}. Description: {$report->description}";

        // Notify IOSA users
        $iosaUsers = \App\Models\User::where('role', 'IOSA')->get();
        foreach ($iosaUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "Issue Report - {$report->severity} severity",
                'body' => $reportMessage,
                'type' => 'report',
                'related_id' => $report->id,
                'related_type' => 'App\\Models\\Report',
                'read_at' => null
            ]);
        }

        // Notify Ms. Mhadel users
        $mhadelUsers = \App\Models\User::where('role', 'Mhadel')->get();
        foreach ($mhadelUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "Issue Report - {$report->severity} severity",
                'body' => $reportMessage,
                'type' => 'report',
                'related_id' => $report->id,
                'related_type' => 'App\\Models\\Report',
                'read_at' => null
            ]);
        }

        // Notify OTP users
        $otpUsers = \App\Models\User::where('role', 'OTP')->get();
        foreach ($otpUsers as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "Issue Report - {$report->severity} severity",
                'body' => $reportMessage,
                'type' => 'report',
                'related_id' => $report->id,
                'related_type' => 'App\\Models\\Report',
                'read_at' => null
            ]);
        }
    }

    /**
     * Create report from completion data
     */
    private function createReportFromCompletion($item, $request, $type)
    {
        // Create the report
        $report = Report::create([
            'reported_user_id' => null, // Events don't have a specific user
            'reporter_id' => auth()->id(),
            'reservation_id' => null,
            'event_id' => $type === 'event' ? $item->id : null,
            'type' => $request->type,
            'severity' => $request->severity,
            'description' => $request->description,
            'actions_taken' => $request->actions_taken,
            'status' => 'pending'
        ]);

        // Create notifications for different roles
        $this->createReportNotifications($report, $type);
    }
} 