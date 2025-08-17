<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
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
            ->with(['venue'])
            ->latest()
            ->paginate(10);
        return view('user.reservations.index', compact('userReservations'));
    }

    public function calendar()
    {
        $venues = Venue::where('is_available', true)->get();
        return view('user.reservations', compact('venues'));
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
            'activity_grid' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
            'equipment' => 'nullable|array',
            'equipment.*' => 'string',
            'equipment_quantity' => 'nullable|array',
            'equipment_quantity.*' => 'integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
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
        
        // Validate that end date is after start date
        if (strtotime($request->end_date) <= strtotime($request->start_date)) {
            return back()->withErrors(['end_date' => 'End date must be after start date.'])->withInput();
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

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Process equipment details
        if ($request->has('equipment') && is_array($request->equipment)) {
            $equipmentDetails = [];
            foreach ($request->equipment as $equipment) {
                if ($equipment !== 'none') {
                    $quantity = $request->input("equipment_quantity.{$equipment}", 1);
                    $equipmentDetails[] = [
                        'name' => $equipment,
                        'quantity' => $quantity
                    ];
                }
            }
            $data['equipment_details'] = $equipmentDetails;
        }

        // Calculate duration in hours
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $durationHours = ceil($startDate->diffInSeconds($endDate) / 3600);
        
        // Log duration calculation for debugging
        \Log::info('Duration calculation', [
            'start_date' => $startDate->toDateTimeString(),
            'end_date' => $endDate->toDateTimeString(),
            'raw_duration_seconds' => $startDate->diffInSeconds($endDate),
            'calculated_hours' => $durationHours,
            'final_duration_hours' => max(1, $durationHours)
        ]);
        
        $data['duration_hours'] = max(1, $durationHours); // Ensure minimum 1 hour

        // Handle file upload
        if ($request->hasFile('activity_grid')) {
            $file = $request->file('activity_grid');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('activity_grids', $fileName, 'public');
            $data['activity_grid'] = $filePath;
        }

        Reservation::create($data);

        return redirect()->route('user.reservations.index')
            ->with('success', 'Reservation submitted successfully!');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
}
