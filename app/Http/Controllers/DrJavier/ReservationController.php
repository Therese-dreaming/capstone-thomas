<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Exports\OTPReservationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusChanged;
use Illuminate\Support\Facades\Storage;
use App\Models\Report;

class ReservationController extends Controller
{
	/**
	 * Display a listing of the reservations.
	 */
	public function index(Request $request)
	{
		$query = Reservation::with(['user', 'venue']);
		
		// Apply status filter
		if ($request->filled('status')) {
			if ($request->status === 'pending') {
				$query->where('status', 'approved_mhadel');
			} elseif ($request->status === 'approved') {
				$query->where('status', 'approved_OTP');
			} elseif ($request->status === 'rejected') {
				$query->where('status', 'rejected_OTP');
			}
		} else {
			// Default view shows reservations awaiting OTP (Mhadel approved)
			$query->where('status', 'approved_mhadel');
		}
		
		// Optional filters
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
		
		$reservations = $query->select([
			'id', 'user_id', 'venue_id', 'event_title', 'start_date', 'end_date', 
			'purpose', 'status', 'notes', 'base_price', 'discount_percentage', 'final_price', 
			'price_per_hour', 'duration_hours', 'equipment_details', 'capacity', 'created_at'
		])->with(['user', 'venue'])->orderBy('created_at', 'desc')->paginate(10);
		
		$stats = [
			'total' => Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP'])->count(),
			'pending' => Reservation::where('status', 'approved_mhadel')->count(),
			'approved' => Reservation::where('status', 'approved_OTP')->count(),
			'rejected' => Reservation::where('status', 'rejected_OTP')->count(),
		];
		
		$venues = \App\Models\Venue::orderBy('name')->get();
		
		return view('drjavier.reservations.index', compact('reservations', 'stats', 'venues'));
	}

	/**
	 * Export reservations to CSV (Excel-compatible).
	 */
	public function export(Request $request)
	{
		$query = Reservation::with(['user', 'venue']);
		
		// Apply same filters as index
		if ($request->filled('status')) {
			if ($request->status === 'pending') {
				$query->where('status', 'approved_mhadel');
			} elseif ($request->status === 'approved') {
				$query->where('status', 'approved_OTP');
			} elseif ($request->status === 'rejected') {
				$query->where('status', 'rejected_OTP');
			}
		} else {
			$query->whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP']);
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
		
		$rows = $query->orderBy('created_at', 'desc')->get();
		$filename = 'otp_reservations_' . now()->format('Ymd_His') . '.xlsx';
		return Excel::download(new OTPReservationsExport($rows), $filename);
	}

	/**
	 * Display the specified reservation.
	 */
	public function show(string $id)
	{
		$reservation = Reservation::with(['user', 'venue'])->findOrFail($id);

		return view('drjavier.reservations.show', compact('reservation'));
	}

	/**
	 * Approve a reservation.
	 */
	public function approve(Request $request, string $id)
	{
		$reservation = Reservation::findOrFail($id);
		if ($reservation->status !== 'approved_mhadel') {
			return redirect()->back()->with('error', 'Only Mhadel approved reservations can be approved by OTP.');
		}
		
		$notes = $request->input('notes', '');
		$approvalNote = "[OTP (Office of the President) Approved on " . now()->format('M d, Y H:i') . "]";
		if ($notes) {
			$approvalNote .= "\nNotes: " . $notes;
		}
		
		$reservation->update([
			'status' => 'approved_OTP',
			'notes' => $reservation->notes . "\n" . $approvalNote
		]);
		
		Notification::create([
			'user_id' => $reservation->user_id,
			'title' => 'Your reservation received final approval',
			'body' => 'Reservation "' . $reservation->event_title . '" is now fully approved (OTP).',
			'type' => 'reservation_status',
			'related_id' => $reservation->id,
			'related_type' => Reservation::class,
		]);
		
		// Self notification to OTP actor
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'You approved a reservation (Final)',
			'body' => 'You granted final approval for "' . $reservation->event_title . '".',
			'type' => 'self_info',
			'related_id' => $reservation->id,
			'related_type' => Reservation::class,
		]);
		
		// Email requester final approval
		Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
			$reservation,
			$reservation->user,
			'approved_OTP',
			'OTP'
		));
		
		return redirect()->back()->with('success', 'Reservation approved successfully. This is the final approval.');
	}

	/**
	 * Reject a reservation.
	 */
	public function reject(Request $request, string $id)
	{
		$reservation = Reservation::findOrFail($id);
		if ($reservation->status !== 'approved_mhadel') {
			return redirect()->back()->with('error', 'Only Mhadel approved reservations can be rejected by OTP.');
		}
		
		$notes = $request->input('notes', '');
		if (!$notes) {
			return redirect()->back()->with('error', 'Please provide a reason for rejection.');
		}
		
		$rejectionNote = "[OTP (Office of the President) Rejected on " . now()->format('M d, Y H:i') . "]\nReason: " . $notes;
		$reservation->update([
			'status' => 'rejected_OTP',
			'notes' => $reservation->notes . "\n" . $rejectionNote
		]);
		
		Notification::create([
			'user_id' => $reservation->user_id,
			'title' => 'Your reservation was rejected by OTP',
			'body' => 'Reservation "' . $reservation->event_title . '" was rejected. Reason: ' . $notes,
			'type' => 'reservation_status',
			'related_id' => $reservation->id,
			'related_type' => Reservation::class,
		]);
		
		// Self notification to OTP actor
		Notification::create([
			'user_id' => Auth::id(),
			'title' => 'You rejected a reservation (Final)',
			'body' => 'You rejected "' . $reservation->event_title . '". Reason: ' . $notes,
			'type' => 'self_info',
			'related_id' => $reservation->id,
			'related_type' => Reservation::class,
		]);
		
		// Email requester final rejection
		Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
			$reservation,
			$reservation->user,
			'rejected_OTP',
			'OTP',
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
			return redirect()->back()->with('error', 'No activity grid available for this reservation.');
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
		$filename = 'activity_grid_' . $reservation->id . '_' . date('Y-m-d') . '.txt';
		
		return response($reservation->activity_grid, 200, [
			'Content-Type' => 'text/plain',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		]);
	}

	/**
	 * Display reports filed by GSU.
	 */
	public function gsuReports(Request $request)
	{
		$query = Report::with(['reporter', 'reportedUser', 'reservation', 'event'])
			->orderBy('created_at', 'desc');

		// Apply filters
		if ($request->filled('type')) {
			$query->where('type', $request->type);
		}
		
		if ($request->filled('severity')) {
			$query->where('severity', $request->severity);
		}
		
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}
		
		if ($request->filled('start_date')) {
			$query->whereDate('created_at', '>=', $request->start_date);
		}
		
		if ($request->filled('end_date')) {
			$query->whereDate('created_at', '<=', $request->end_date);
		}

		// Get statistics
		$stats = [
			'total' => Report::count(),
			'pending' => Report::where('status', 'pending')->count(),
			'investigating' => Report::where('status', 'investigating')->count(),
			'resolved' => Report::where('status', 'resolved')->count(),
			'critical' => Report::where('severity', 'critical')->count(),
			'high' => Report::where('severity', 'high')->count(),
		];

		// Get reports by type
		$reportsByType = Report::selectRaw('type, COUNT(*) as count')
			->groupBy('type')
			->orderBy('count', 'desc')
			->get();

		// Get reports by severity
		$reportsBySeverity = Report::selectRaw('severity, COUNT(*) as count')
			->groupBy('severity')
			->orderBy('count', 'desc')
			->get();

		// Get recent reports
		$recentReports = Report::with(['reporter', 'reportedUser', 'reservation', 'event'])
			->orderBy('created_at', 'desc')
			->limit(5)
			->get();

		$reports = $query->paginate(15)->withQueryString();

		return view('drjavier.gsu-reports.index', compact(
			'reports',
			'stats',
			'reportsByType',
			'reportsBySeverity',
			'recentReports'
		));
	}

	/**
	 * Show a specific report.
	 */
	public function showGsuReport(Report $report)
	{
		$report->load(['reporter', 'reportedUser', 'reservation', 'event']);
		
		return view('drjavier.gsu-reports.show', compact('report'));
	}

	/**
	 * Update report status.
	 */
	public function updateGsuReportStatus(Request $request, Report $report)
	{
		$request->validate([
			'status' => 'required|in:pending,investigating,resolved',
			'admin_notes' => 'nullable|string|max:1000'
		]);

		$report->update([
			'status' => $request->status,
			'admin_notes' => $request->admin_notes,
			'resolved_at' => $request->status === 'resolved' ? now() : null
		]);

		return response()->json([
			'success' => true,
			'message' => 'Report status updated successfully'
		]);
	}
} 