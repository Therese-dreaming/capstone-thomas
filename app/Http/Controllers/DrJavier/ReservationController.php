<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Exports\OTPReservationsExport;
use Maatwebsite\Excel\Facades\Excel;

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
		
		return redirect()->back()->with('success', 'Reservation rejected successfully.');
	}

	/**
	 * Download activity grid as text file.
	 */
	public function downloadActivityGrid(string $id)
	{
		$reservation = Reservation::findOrFail($id);
		
		if (!$reservation->activity_grid) {
			return redirect()->back()->with('error', 'No activity grid available for this reservation.');
		}
		
		$filename = 'activity_grid_' . $reservation->id . '_' . date('Y-m-d') . '.txt';
		
		return response($reservation->activity_grid, 200, [
			'Content-Type' => 'text/plain',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		]);
	}
} 