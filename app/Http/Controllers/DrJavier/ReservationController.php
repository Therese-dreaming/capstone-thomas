<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'venue'])
            ->where('status', 'approved_mhadel'); // Only show Mhadel approved reservations
        
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'approved_mhadel');
            } elseif ($request->status === 'approved') {
                $query->where('status', 'approved_OTP');
            } elseif ($request->status === 'rejected') {
                $query->where('status', 'rejected_OTP');
            }
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
        
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total' => Reservation::where('status', 'approved_mhadel')->count(),
            'pending' => Reservation::where('status', 'approved_mhadel')->count(),
            'approved' => Reservation::where('status', 'approved_OTP')->count(),
            'rejected' => Reservation::where('status', 'rejected_OTP')->count(),
        ];
        
        $venues = \App\Models\Venue::orderBy('name')->get();
        
        return view('drjavier.reservations.index', compact('reservations', 'stats', 'venues'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'venue', 'equipment'])->findOrFail($id);

        return view('drjavier.reservations.show', compact('reservation'));
    }

    /**
     * Approve a reservation.
     */
    public function approve(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'approved_mhadel') {
            return redirect()->back()->with('error', 'Only Mhadel approved reservations can be approved by Dr. Javier.');
        }
        
        $notes = $request->input('notes', '');
        $approvalNote = "[Dr. Javier (OTP) Approved on " . now()->format('M d, Y H:i') . "]";
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
            return redirect()->back()->with('error', 'Only Mhadel approved reservations can be rejected by Dr. Javier.');
        }
        
        $notes = $request->input('notes', '');
        if (!$notes) {
            return redirect()->back()->with('error', 'Please provide a reason for rejection.');
        }
        
        $rejectionNote = "[Dr. Javier (OTP) Rejected on " . now()->format('M d, Y H:i') . "]\nReason: " . $notes;
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