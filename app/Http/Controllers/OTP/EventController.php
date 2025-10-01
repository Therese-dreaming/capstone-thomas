<?php

namespace App\Http\Controllers\OTP;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EventsExport;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('event_id', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('organizer', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($searchTerm) {
                      $venueQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply date range filters
        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to . ' 23:59:59');
        }

        // Apply venue filter
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Apply organizer filter
        if ($request->filled('organizer')) {
            $query->where('organizer', 'like', "%{$request->organizer}%");
        }

        // Apply equipment filter
        if ($request->filled('has_equipment')) {
            if ($request->has_equipment == '1') {
                $query->whereNotNull('equipment_details')
                      ->where(function($q) {
                          $q->whereJsonLength('equipment_details', '>', 0)
                            ->orWhere('equipment_details', '!=', '[]');
                      });
            } else {
                $query->where(function($q) {
                    $q->whereNull('equipment_details')
                      ->orWhereJsonLength('equipment_details', 0)
                      ->orWhere('equipment_details', '[]');
                });
            }
        }

        // Apply duration filter
        if ($request->filled('duration')) {
            $duration = $request->duration;
            $query->whereNotNull('start_date')
                  ->whereNotNull('end_date')
                  ->where(function($q) use ($duration) {
                      switch ($duration) {
                          case '1':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) <= 1');
                              break;
                          case '2':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 2 AND 4');
                              break;
                          case '5':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 5 AND 8');
                              break;
                          case '9':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) > 8');
                              break;
                      }
                  });
        }

        // Apply created date filter
        if ($request->filled('created_period')) {
            $period = $request->created_period;
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Get paginated results
        $events = $query->latest()->paginate(12)->withQueryString();

        return view('otp.events.index', compact('events'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('otp.events.show', compact('event'));
    }

    /**
     * Export events to Excel
     */
    public function export(Request $request)
    {
        $query = Event::with(['venue']);

        // Apply the same filters as index method
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('event_id', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('organizer', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhereHas('venue', function ($venueQuery) use ($searchTerm) {
                      $venueQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('start_date_from')) {
            $query->where('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->where('start_date', '<=', $request->start_date_to . ' 23:59:59');
        }

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('organizer')) {
            $query->where('organizer', 'like', "%{$request->organizer}%");
        }

        if ($request->filled('has_equipment')) {
            if ($request->has_equipment == '1') {
                $query->whereNotNull('equipment_details')
                      ->where(function($q) {
                          $q->whereJsonLength('equipment_details', '>', 0)
                            ->orWhere('equipment_details', '!=', '[]');
                      });
            } else {
                $query->where(function($q) {
                    $q->whereNull('equipment_details')
                      ->orWhereJsonLength('equipment_details', 0)
                      ->orWhere('equipment_details', '[]');
                });
            }
        }

        if ($request->filled('duration')) {
            $duration = $request->duration;
            $query->whereNotNull('start_date')
                  ->whereNotNull('end_date')
                  ->where(function($q) use ($duration) {
                      switch ($duration) {
                          case '1':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) <= 1');
                              break;
                          case '2':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 2 AND 4');
                              break;
                          case '5':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) BETWEEN 5 AND 8');
                              break;
                          case '9':
                              $q->whereRaw('TIMESTAMPDIFF(HOUR, start_date, end_date) > 8');
                              break;
                      }
                  });
        }

        if ($request->filled('created_period')) {
            $period = $request->created_period;
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $events = $query->latest()->get();

        // Generate filename with current date and filters
        $filename = 'events_export_' . now()->format('Y-m-d_H-i-s');
        if ($request->filled('status') && $request->status !== 'all') {
            $filename .= '_' . $request->status;
        }
        $filename .= '.xlsx';

        return Excel::download(new EventsExport($events), $filename);
    }
}
