<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusChanged;
use Illuminate\Support\Facades\Storage;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'venue'])
            ->whereIn('status', ['pending', 'approved_IOSA', 'rejected_IOSA']); // Show pending and IOSA processed
        
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->status === 'approved') {
                $query->where('status', 'approved_IOSA');
            } elseif ($request->status === 'rejected') {
                $query->where('status', 'rejected_IOSA');
            }
        }
        
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }
        if ($request->filled('venue')) {
            $query->whereHas('venue', function($q) use ($request) {
                $q->where('name', $request->venue);
            });
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total' => Reservation::whereIn('status', ['pending', 'approved_IOSA', 'rejected_IOSA'])->count(),
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved' => Reservation::where('status', 'approved_IOSA')->count(),
            'rejected' => Reservation::where('status', 'rejected_IOSA')->count(),
        ];
        
        $venues = \App\Models\Venue::orderBy('name')->get();
        
        return view('iosa.reservations.index', compact('reservations', 'stats', 'venues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'venue'])->findOrFail($id);
        
        return view('iosa.reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Approve a reservation.
     */
    public function approve(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be approved by IOSA.');
        }
        
        $notes = $request->input('notes', '');
        $approvalNote = "[IOSA Approved on " . now()->format('M d, Y H:i') . "]";
        if ($notes) {
            $approvalNote .= "\nNotes: " . $notes;
        }
        
        $reservation->update([
            'status' => 'approved_IOSA',
            'notes' => $reservation->notes . "\n" . $approvalNote
        ]);
        
        // Self notification to IOSA actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You approved a reservation',
            'body' => 'You approved "' . $reservation->event_title . '" and forwarded to Ms. Mhadel.',
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Notify Mhadel role about new item to review
        $mhadelUser = User::where('role', 'mhadel')->first();
        if ($mhadelUser) {
            Notification::create([
                'user_id' => $mhadelUser->id,
                'title' => 'Reservation forwarded by IOSA',
                'body' => 'Reservation "' . $reservation->event_title . '" is awaiting your review.',
                'type' => 'reservation_action',
                'related_id' => $reservation->id,
                'related_type' => Reservation::class,
            ]);
        }
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was approved by IOSA',
            'body' => 'Reservation "' . $reservation->event_title . '" is forwarded to Ms. Mhadel.',
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'approved_IOSA',
            'IOSA'
        ));
        
        return redirect()->back()->with('success', 'Reservation approved successfully. Forwarded to Ms. Mhadel for review.');
    }

    /**
     * Reject a reservation.
     */
    public function reject(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be rejected by IOSA.');
        }
        
        $notes = $request->input('notes', '');
        if (!$notes) {
            return redirect()->back()->with('error', 'Please provide a reason for rejection.');
        }
        
        $rejectionNote = "[IOSA Rejected on " . now()->format('M d, Y H:i') . "]\nReason: " . $notes;
        $reservation->update([
            'status' => 'rejected_IOSA',
            'notes' => $reservation->notes . "\n" . $rejectionNote
        ]);
        
        // Self notification to IOSA actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You rejected a reservation',
            'body' => 'You rejected "' . $reservation->event_title . '". Reason: ' . $notes,
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was rejected by IOSA',
            'body' => 'Reservation "' . $reservation->event_title . '" was rejected. Reason: ' . $notes,
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'rejected_IOSA',
            'IOSA',
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
            return redirect()->back()->with('error', 'No activity grid found for this reservation.');
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
        $filename = 'activity_grid_' . $reservation->event_title . '_' . $reservation->id . '.txt';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        
        return response($reservation->activity_grid)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
} 