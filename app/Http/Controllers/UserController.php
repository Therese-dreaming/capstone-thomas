<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Equipment;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['venue'])
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('reservations'));
    }

    public function index()
    {
        $userReservations = Reservation::where('user_id', Auth::id())
            ->with(['venue', 'equipment'])
            ->latest()
            ->paginate(10); // Changed from get() to paginate()
        return view('user.reservations.index', compact('userReservations'));
    }

    public function calendar()
    {
        $venues = Venue::where('is_available', true)->get();
        $equipment = Equipment::all();
        return view('user.reservations', compact('venues', 'equipment'));
    }

    public function storeReservation(Request $request)
    {
        $request->validate([
            'event_title' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'venue_id' => 'required|exists:venues,id',
            'purpose' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'equipment' => 'nullable|array',
            'activity_grid' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
        ]);

        // Validate that a suitable venue was found
        $venue = Venue::find($request->venue_id);
        if (!$venue || $venue->capacity < $request->capacity) {
            return back()->withErrors(['capacity' => 'No suitable venue found for the specified capacity.'])->withInput();
        }

        // Validate reservation is at least 3 days in advance
        $minDate = now()->addDays(3);
        if (strtotime($request->start_date) < strtotime($minDate)) {
            return back()->withErrors(['start_date' => 'Reservations must be made at least 3 days in advance.'])->withInput();
        }

        // Validate equipment quantities
        $equipmentInput = $request->input('equipment', []);
        $equipmentErrors = [];
        foreach ($equipmentInput as $id => $data) {
            if (isset($data['checked'])) {
                $equipmentItem = Equipment::find($id);
                $qty = isset($data['quantity']) ? (int)$data['quantity'] : 0;
                if ($qty < 1) {
                    $equipmentErrors[] = "Quantity for {$equipmentItem->name} must be at least 1.";
                } elseif ($qty > $equipmentItem->total_quantity) {
                    $equipmentErrors[] = "Requested quantity for {$equipmentItem->name} exceeds available ({$equipmentItem->total_quantity}).";
                }
            }
        }
        if ($equipmentErrors) {
            return back()->withErrors(['equipment' => implode(' ', $equipmentErrors)])->withInput();
        }

        // Validate venue time conflict
        $conflict = Reservation::where('venue_id', $request->venue_id)
            ->where(function($q) use ($request) {
                $q->where('start_date', '<', $request->end_date)
                  ->where('end_date', '>', $request->start_date);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($conflict) {
            return back()->withErrors(['venue_id' => 'This venue is already reserved for the selected date and time.'])->withInput();
        }

        $data = $request->except(['equipment']);
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Handle file upload
        if ($request->hasFile('activity_grid')) {
            $file = $request->file('activity_grid');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('activity_grids', $fileName, 'public');
            $data['activity_grid'] = $filePath;
        }

        $reservation = Reservation::create($data);

        // Attach equipment with quantities
        $attach = [];
        foreach ($equipmentInput as $id => $edata) {
            if (isset($edata['checked']) && isset($edata['quantity']) && $edata['quantity'] > 0) {
                $attach[$id] = ['quantity' => $edata['quantity']];
            }
        }
        if ($attach) {
            $reservation->equipment()->attach($attach);
        }

        return redirect()->route('user.reservations.index')
            ->with('success', 'Reservation submitted successfully!');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
}
