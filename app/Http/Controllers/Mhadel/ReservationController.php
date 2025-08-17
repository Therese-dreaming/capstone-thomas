<?php

namespace App\Http\Controllers\Mhadel;

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
            ->where('status', 'approved_IOSA'); // Only show IOSA approved reservations
        
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'approved_IOSA'); // Map 'pending' to 'approved_IOSA' for Mhadel's context
            } elseif ($request->status === 'approved') {
                $query->where('status', 'approved_mhadel');
            } elseif ($request->status === 'rejected') {
                $query->where('status', 'rejected_mhadel');
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
            'total' => Reservation::where('status', 'approved_IOSA')->count(),
            'pending' => Reservation::where('status', 'approved_IOSA')->count(),
            'approved' => Reservation::where('status', 'approved_mhadel')->count(),
            'rejected' => Reservation::where('status', 'rejected_mhadel')->count(),
        ];
        
        $venues = \App\Models\Venue::orderBy('name')->get();
        
        return view('mhadel.reservations.index', compact('reservations', 'stats', 'venues'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'venue'])->findOrFail($id);

        return view('mhadel.reservations.show', compact('reservation'));
    }

    /**
     * Approve a reservation.
     */
    public function approve(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'approved_IOSA') {
            return redirect()->back()->with('error', 'Only IOSA approved reservations can be approved by Ms. Mhadel.');
        }
        
        $notes = $request->input('notes', '');
        $approvalNote = "[Ms. Mhadel Approved on " . now()->format('M d, Y H:i') . "]";
        if ($notes) {
            $approvalNote .= "\nNotes: " . $notes;
        }
        
        $reservation->update([
            'status' => 'approved_mhadel',
            'notes' => $reservation->notes . "\n" . $approvalNote
        ]);
        
        return redirect()->back()->with('success', 'Reservation approved successfully. Forwarded to Dr. Javier for final approval.');
    }

    /**
     * Reject a reservation.
     */
    public function reject(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'approved_IOSA') {
            return redirect()->back()->with('error', 'Only IOSA approved reservations can be rejected by Ms. Mhadel.');
        }
        
        $notes = $request->input('notes', '');
        if (!$notes) {
            return redirect()->back()->with('error', 'Please provide a reason for rejection.');
        }
        
        $rejectionNote = "[Ms. Mhadel Rejected on " . now()->format('M d, Y H:i') . "]\nReason: " . $notes;
        $reservation->update([
            'status' => 'rejected_mhadel',
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
            return redirect()->back()->with('error', 'No activity grid found for this reservation.');
        }
        
        $filename = 'activity_grid_' . $reservation->event_title . '_' . $reservation->id . '.txt';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename); // Sanitize filename
        
        return response($reservation->activity_grid)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
} 