<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusChanged;

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
        
        $reservations = $query->select([
            'id', 'user_id', 'venue_id', 'event_title', 'start_date', 'end_date', 
            'purpose', 'status', 'notes', 'base_price', 'discount_percentage', 'final_price', 
            'price_per_hour', 'duration_hours', 'created_at'
        ])->with(['user', 'venue'])->orderBy('created_at', 'desc')->paginate(10);
        
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
     * Calendar view: show final approved reservations (OTP approved) and official events.
     */
    public function calendar(Request $request)
    {
        $reservations = Reservation::with(['user','venue'])
            ->whereIn('status', ['approved_OTP'])
            ->orderBy('start_date')
            ->get(['id','user_id','venue_id','event_title','start_date','end_date','status','final_price','capacity','purpose']);

        $events = Event::with(['venue'])
            ->whereIn('status', ['upcoming','ongoing'])
            ->orderBy('start_date')
            ->get(['id','venue_id','title','organizer','start_date','end_date','status','max_participants']);

        return view('mhadel.reservations.calendar', [
            'reservations' => $reservations,
            'events' => $events,
        ]);
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
        $newBasePrice = $request->input('base_price', 0);
        $discount = $request->input('discount', 0);
        $newFinalPrice = $request->input('final_price', 0);
        
        $approvalNote = "[Ms. Mhadel Approved on " . now()->format('M d, Y H:i') . "]";
        if ($notes) {
            $approvalNote .= "\nNotes: " . $notes;
        }
        
        // Add pricing information to notes
        $approvalNote .= "\nPricing Review:";
        if ($reservation->base_price > 0) {
            $approvalNote .= "\n- User's Base Price: ₱" . number_format($reservation->base_price, 2);
        } else {
            $approvalNote .= "\n- User's Base Price: Free Event";
        }
        
        if ($newBasePrice > 0) {
            $approvalNote .= "\n- Ms. Mhadel's Base Price: ₱" . number_format($newBasePrice, 2);
            if ($discount > 0) {
                $approvalNote .= "\n- Discount Applied: " . $discount . "%";
            }
            $approvalNote .= "\n- Final Price: ₱" . number_format($newFinalPrice, 2);
        } else {
            $approvalNote .= "\n- Final Pricing: Free Event";
        }
        
        $reservation->update([
            'status' => 'approved_mhadel',
            'notes' => $reservation->notes . "\n" . $approvalNote,
            'base_price' => $newBasePrice,
            'discount_percentage' => $discount,
            'final_price' => $newFinalPrice
        ]);
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was approved by Ms. Mhadel',
            'body' => 'Reservation "' . $reservation->event_title . '" is forwarded to OTP for final approval.',
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Notify OTP reviewer(s)
        $otpUser = User::where('role', 'otp')->first();
        if ($otpUser) {
            Notification::create([
                'user_id' => $otpUser->id,
                'title' => 'Reservation requires final approval',
                'body' => 'Reservation "' . $reservation->event_title . '" is awaiting your final approval.',
                'type' => 'reservation_action',
                'related_id' => $reservation->id,
                'related_type' => Reservation::class,
            ]);
        }
        
        // Self notification to Ms. Mhadel actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You approved a reservation',
            'body' => 'You approved "' . $reservation->event_title . '" and forwarded to OTP.',
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester with pricing details
        $pricing = [
            'base_price' => $newBasePrice > 0 ? '₱' . number_format($newBasePrice, 2) : 'Free',
            'discount' => $discount > 0 ? $discount . '%' : '—',
            'final_price' => $newFinalPrice > 0 ? '₱' . number_format($newFinalPrice, 2) : 'Free',
        ];
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'approved_mhadel',
            'Ms. Mhadel',
            ['pricing' => $pricing]
        ));
        
        return redirect()->back()->with('success', 'Reservation approved successfully. Forwarded to OTP for final approval.');
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
        
        Notification::create([
            'user_id' => $reservation->user_id,
            'title' => 'Your reservation was rejected by Ms. Mhadel',
            'body' => 'Reservation "' . $reservation->event_title . '" was rejected. Reason: ' . $notes,
            'type' => 'reservation_status',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Self notification to Ms. Mhadel actor
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'You rejected a reservation',
            'body' => 'You rejected "' . $reservation->event_title . '". Reason: ' . $notes,
            'type' => 'self_info',
            'related_id' => $reservation->id,
            'related_type' => Reservation::class,
        ]);
        
        // Email requester with reason
        Mail::to($reservation->user->email)->send(new ReservationStatusChanged(
            $reservation,
            $reservation->user,
            'rejected_mhadel',
            'Ms. Mhadel',
            ['reason' => $notes]
        ));
        
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