<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Report;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
	public function index(Request $request)
	{
		$query = Reservation::with(['user','venue'])->where('status','approved_OTP');
		
		// Apply date filters if provided
		if ($request->filled('start_date')) {
			$query->where('start_date', '>=', $request->start_date);
		}
		if ($request->filled('end_date')) {
			$query->where('start_date', '<=', $request->end_date . ' 23:59:59');
		}
		
		// Apply other filters if needed
		if ($request->filled('venue')) {
			$query->where('venue_id', $request->venue);
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
		
		$reservations = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
		$venues = \App\Models\Venue::orderBy('name')->get();
		
		return view('gsu.reservations.index', compact('reservations','venues'));
	}

	public function show(string $id)
	{
		$reservation = Reservation::with(['user','venue'])->where('status','approved_OTP')->findOrFail($id);
		return view('gsu.reservations.show', compact('reservation'));
	}

	    public function pdf(string $id)
    {
        $reservation = Reservation::with(['user','venue'])->where('status','approved_OTP')->findOrFail($id);

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gsu.reservations.pdf', compact('reservation'));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Return PDF as stream for preview
        return $pdf->stream('GSU_Reservation_' . $reservation->id . '.pdf');
    }

    public function export(Request $request)
    {
        $query = Reservation::with(['user','venue'])->where('status','approved_OTP');
        
        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('start_date', '<=', $request->end_date . ' 23:59:59');
        }
        
        // Apply other filters if needed
        if ($request->filled('venue')) {
            $query->where('venue_id', $request->venue);
        }
        
        // Get all filtered reservations (no pagination for export)
        $reservations = $query->orderByDesc('created_at')->get();
        
        // Generate filename with date range if applicable
        $filename = 'GSU_Reservations';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filename .= '_' . $request->start_date . '_to_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
            $filename .= '_from_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
            $filename .= '_until_' . $request->end_date;
        }
        $filename .= '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GSUReservationsExport($reservations), $filename);
    }

    /**
     * Mark a reservation as completed (GSU users only)
     */
    public function markAsComplete(string $id, Request $request)
    {
        $reservation = Reservation::with(['user','venue'])->where('status','approved_OTP')->findOrFail($id);
        
        // Check if reservation can be marked as complete
        if ($reservation->status === 'completed') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Reservation is already marked as completed.']);
            }
            return back()->with('error', 'Reservation is already marked as completed.');
        }

        // Update reservation with completion details
        $reservation->update([
            'status' => 'completed',
            'completion_notes' => $request->input('completion_notes'),
            'completion_date' => now(),
            'completed_by' => 'GSU'
        ]);

        // Create notifications for different roles
        $this->createCompletionNotifications($reservation, 'reservation', $request->input('completion_notes'));

        // Create report if report data is provided
        if ($request->filled(['type', 'severity', 'description'])) {
            $this->createReportFromCompletion($reservation, $request, 'reservation');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Reservation #{$reservation->id} has been marked as completed successfully!"]);
        }

        return back()->with('success', "Reservation #{$reservation->id} has been marked as completed successfully!");
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
     * Report an issue with a reservation (GSU users only)
     */
    public function reportIssue(string $id, Request $request)
    {
        $reservation = Reservation::with(['user','venue'])->where('status','approved_OTP')->findOrFail($id);
        
        $request->validate([
            'type' => 'required|in:accident,problem,violation,damage,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|min:10',
            'actions_taken' => 'nullable|string'
        ]);

        // Create the report
        $report = Report::create([
            'reported_user_id' => $reservation->user_id,
            'reporter_id' => auth()->id(),
            'reservation_id' => $reservation->id,
            'event_id' => null,
            'type' => $request->type,
            'severity' => $request->severity,
            'description' => $request->description,
            'actions_taken' => $request->actions_taken,
            'status' => 'pending'
        ]);

        // Create notifications for different roles
        $this->createReportNotifications($report, 'reservation');

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
        $reportedUser = $report->reportedUser->name;
        $severityColor = [
            'low' => 'blue',
            'medium' => 'yellow', 
            'high' => 'orange',
            'critical' => 'red'
        ][$report->severity] ?? 'gray';

        $reportMessage = "GSU has reported a {$report->severity} severity {$report->type} issue with {$type} '{$itemTitle}' involving user {$reportedUser}. Description: {$report->description}";

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
            'reported_user_id' => $type === 'reservation' ? $item->user_id : null,
            'reporter_id' => auth()->id(),
            'reservation_id' => $type === 'reservation' ? $item->id : null,
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