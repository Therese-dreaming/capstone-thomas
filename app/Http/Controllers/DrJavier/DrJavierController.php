<?php

namespace App\Http\Controllers\DrJavier;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ReservationRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DrJavierController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        
        $stats = [
            'pending' => Reservation::where('status', 'approved_mhadel')->count(),
            'approved' => Reservation::where('status', 'approved_OTP')
                ->whereDate('updated_at', $today)->count(),
            'rejected' => Reservation::where('status', 'rejected_OTP')
                ->whereDate('updated_at', $today)->count(),
            'total' => Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP'])
                ->where('created_at', '>=', $startOfMonth)->count(),
        ];
        
        $recent_reservations = Reservation::with(['user', 'venue'])
            ->whereIn('status', ['approved_mhadel', 'approved_OTP', 'rejected_OTP'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Revenue (completed only) - monthly
        $monthlyRevenueRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('MONTH(updated_at) as m, SUM(final_price) as revenue')
            ->groupBy('m')
            ->pluck('revenue', 'm');
        $revenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueSeries[] = (float) ($monthlyRevenueRaw[$i] ?? 0);
        }

        // Revenue (completed only) - quarterly
        $quarterlyRevenueRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('QUARTER(updated_at) as q, SUM(final_price) as revenue')
            ->groupBy('q')
            ->pluck('revenue', 'q');
        $revenueQuarterly = [];
        for ($i = 1; $i <= 4; $i++) {
            $revenueQuarterly[] = (float) ($quarterlyRevenueRaw[$i] ?? 0);
        }

        // Expected revenue (approved_mhadel + approved_OTP), final_price only - monthly
        $monthlyExpectedRaw = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
            ->where('start_date', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('MONTH(start_date) as m, SUM(final_price) as expected')
            ->groupBy('m')
            ->pluck('expected', 'm');
        $expectedRevenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $expectedRevenueSeries[] = (float) ($monthlyExpectedRaw[$i] ?? 0);
        }

        // Expected revenue quarterly
        $quarterlyExpectedRaw = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
            ->where('start_date', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('QUARTER(start_date) as q, SUM(final_price) as expected')
            ->groupBy('q')
            ->pluck('expected', 'q');
        $expectedRevenueQuarterly = [];
        for ($i = 1; $i <= 4; $i++) {
            $expectedRevenueQuarterly[] = (float) ($quarterlyExpectedRaw[$i] ?? 0);
        }

        // Top venues (completed)
        $topVenues = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('venue_id, SUM(final_price) as total')
            ->groupBy('venue_id')
            ->with('venue')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'venue' => optional($r->venue)->name ?? 'Unknown Venue',
                    'total' => (float) ($r->total ?? 0),
                ];
            })
            ->filter(function ($item) { return $item['total'] > 0; })
            ->values();

        // Top venues by bookings (completed)
        $topVenuesByBookings = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('venue_id, COUNT(*) as total_bookings')
            ->groupBy('venue_id')
            ->with('venue')
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'venue' => optional($r->venue)->name ?? 'Unknown Venue',
                    'total' => (int) ($r->total_bookings ?? 0),
                ];
            })
            ->filter(function ($item) { return $item['total'] > 0; })
            ->values();

        // Department distribution (count: all statuses)
        $byDepartment = Reservation::whereNotNull('department')
            ->selectRaw('department, COUNT(*) as c')
            ->groupBy('department')
            ->orderByDesc('c')
            ->take(6)
            ->get()
            ->map(function ($r) { return ['department' => $r->department, 'count' => (int) $r->c]; });

        // Department distribution (revenue: completed)
        $byDepartmentRevenue = Reservation::where('status', 'completed')
            ->whereNotNull('department')
            ->whereNotNull('final_price')
            ->selectRaw('department, SUM(final_price) as total_revenue')
            ->groupBy('department')
            ->orderByDesc('total_revenue')
            ->take(6)
            ->get()
            ->map(function ($r) { return ['department' => $r->department, 'revenue' => (float) $r->total_revenue]; });

        // Venue utilization (completed) weekly with readable labels within current month
        $utilizationWeeksRaw = Reservation::where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '>=', $startOfMonth)
            ->selectRaw('WEEK(start_date, 1) as wk, SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hrs')
            ->groupBy('wk')
            ->orderBy('wk')
            ->get()
            ->map(function ($r) { return ['week' => (int) $r->wk, 'hours' => (int) $r->hrs]; })
            ->values();

        $utilMap = collect($utilizationWeeksRaw)->keyBy('week');
        $filledUtil = [];
        $cursor = (clone $startOfMonth)->startOfWeek(Carbon::MONDAY);
        $endOfMonth = Carbon::now()->endOfMonth();
        while ($cursor <= $endOfMonth) {
            $wk = (int) $cursor->format('W');
            $rangeStart = $cursor->copy();
            $rangeEnd = $cursor->copy()->endOfWeek(Carbon::SUNDAY);
            if ($rangeStart < $startOfMonth) { $rangeStart = $startOfMonth->copy(); }
            if ($rangeEnd > $endOfMonth) { $rangeEnd = $endOfMonth->copy(); }
            $label = $rangeStart->format('M d') . '–' . $rangeEnd->format('d');
            $filledUtil[] = [
                'week' => $wk,
                'label' => $label,
                'hours' => (int) (optional($utilMap->get($wk))['hours'] ?? 0),
            ];
            $cursor->addWeek();
        }
        $utilizationWeeks = collect($filledUtil)->unique('week')->values();

        // Totals for cards
        $totalRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('final_price');

        $averageRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfMonth)
            ->whereNotNull('final_price')
            ->avg('final_price');

        $expectedRevenue = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
            ->where('start_date', '>=', $startOfMonth)
            ->whereNotNull('final_price')
            ->sum('final_price');

        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $previousMonth)
            ->where('updated_at', '<', $startOfMonth)
            ->sum('final_price');
        $revenueGrowth = $previousMonthRevenue > 0
            ? round((($totalRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;

        $totalUsers = \App\Models\User::count();
        $totalVenues = \App\Models\Venue::where('is_available', true)->count();
        $totalReservations = Reservation::count();

        // Average processing time for OTP decisions
        $avgProcessingTime = Reservation::whereIn('status', ['approved_OTP', 'rejected_OTP'])
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get()
            ->avg(function ($r) { return $r->created_at->diffInHours($r->updated_at); });

        // Status flow
        $statusFlow = [
            'submitted' => Reservation::where('status', 'pending')->count(),
            'iosa_approved' => Reservation::where('status', 'approved_IOSA')->count(),
            'mhadel_approved' => Reservation::where('status', 'approved_mhadel')->count(),
            'final_approved' => Reservation::where('status', 'approved_OTP')->count(),
            'rejected' => Reservation::whereIn('status', ['rejected_IOSA','rejected_mhadel','rejected_OTP'])->count(),
        ];

        // Peak hours
        $peakHours = Reservation::selectRaw('HOUR(start_date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($r) { return ['hour' => $r->hour, 'count' => (int) $r->count]; });

        // Monthly comparison (last 6 months)
        $monthlyComparison = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthlyComparison[] = [
                'month' => $monthStart->format('M'),
                'reservations' => Reservation::whereBetween('start_date', [$monthStart, $monthEnd])->count(),
                'revenue' => (float) Reservation::where('status', 'completed')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->sum('final_price'),
            ];
        }

        // Ratings Analytics Data
        $ratingsData = $this->getRatingsAnalytics();

        return view('drjavier.dashboard', [
            'stats' => $stats,
            'recent_reservations' => $recent_reservations,
            'revenueSeries' => $revenueSeries,
            'revenueQuarterly' => $revenueQuarterly,
            'expectedRevenueSeries' => $expectedRevenueSeries,
            'expectedRevenueQuarterly' => $expectedRevenueQuarterly,
            'topVenues' => $topVenues,
            'topVenuesByBookings' => $topVenuesByBookings,
            'byDepartment' => $byDepartment,
            'byDepartmentRevenue' => $byDepartmentRevenue,
            'utilizationWeeks' => $utilizationWeeks,
            'totalRevenue' => $totalRevenue,
            'averageRevenue' => $averageRevenue,
            'expectedRevenue' => $expectedRevenue,
            'revenueGrowth' => $revenueGrowth,
            'totalUsers' => $totalUsers,
            'totalVenues' => $totalVenues,
            'totalReservations' => $totalReservations,
            'avgProcessingTime' => round($avgProcessingTime ?? 0, 1),
            'statusFlow' => $statusFlow,
            'peakHours' => $peakHours,
            'monthlyComparison' => $monthlyComparison,
            'ratingsData' => $ratingsData,
        ]);
    }

    /**
     * Display reservation reports and analytics for OTP.
     */
    public function reservationReports(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');
        $status = $request->query('status');
        $venueId = $request->query('venue_id');
        $department = $request->query('department');

        $query = Reservation::query()->with(['user', 'venue']);
        if ($start) { $query->whereDate('start_date', '>=', $start); }
        if ($end) { $query->whereDate('end_date', '<=', $end); }
        if ($status) { $query->where('status', $status); }
        if ($venueId) { $query->where('venue_id', $venueId); }
        if ($department) { $query->where('department', $department); }
        
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

        $cloneForAgg = (clone $query);
        $kpis = [
            'total' => (clone $cloneForAgg)->count(),
            'approved' => (clone $cloneForAgg)->whereIn('status', ['approved_OTP','approved'])->count(),
            'rejected' => (clone $cloneForAgg)->whereIn('status', ['rejected_OTP','rejected'])->count(),
            'revenue' => (float) ((clone $cloneForAgg)->whereNotNull('final_price')->sum('final_price')),
        ];

        $results = $query->orderByDesc('start_date')->paginate(10)->withQueryString();

        // Revenue trend data for completed reservations only
        $startOfYear = Carbon::now()->startOfYear();
        
        // Monthly revenue data (completed reservations only)
        $monthlyRevenueRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('MONTH(updated_at) as month, SUM(final_price) as revenue')
            ->groupBy('month')
            ->pluck('revenue', 'month');
        
        $revenueTrendData = [];
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($i = 1; $i <= 12; $i++) {
            $revenueTrendData[] = (float) ($monthlyRevenueRaw[$i] ?? 0);
        }
        
        // Quarterly revenue data (completed reservations only)
        $quarterlyRevenueRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('QUARTER(updated_at) as quarter, SUM(final_price) as revenue')
            ->groupBy('quarter')
            ->pluck('revenue', 'quarter');
        
        $quarterlyRevenueData = [];
        $quarterLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
        
        for ($i = 1; $i <= 4; $i++) {
            $quarterlyRevenueData[] = (float) ($quarterlyRevenueRaw[$i] ?? 0);
        }
        
        // Revenue statistics
        $totalRevenue = Reservation::where('status', 'completed')
            ->whereNotNull('final_price')
            ->sum('final_price');
        
        $averageRevenue = Reservation::where('status', 'completed')
            ->whereNotNull('final_price')
            ->avg('final_price');

        // Status distribution data
        $statusDistribution = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved_IOSA' => Reservation::where('status', 'approved_IOSA')->count(),
            'approved_mhadel' => Reservation::where('status', 'approved_mhadel')->count(),
            'approved_OTP' => Reservation::where('status', 'approved_OTP')->count(),
            'rejected_IOSA' => Reservation::where('status', 'rejected_IOSA')->count(),
            'rejected_mhadel' => Reservation::where('status', 'rejected_mhadel')->count(),
            'rejected_OTP' => Reservation::where('status', 'rejected_OTP')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
        ];

        // Get venues for filter dropdown
        $venues = \App\Models\Venue::orderBy('name')->get();

        // Events data
        $eventsQuery = Event::query()->with(['venue']);
        
        // Apply same filters to events if they exist
        if ($start) { $eventsQuery->whereDate('start_date', '>=', $start); }
        if ($end) { $eventsQuery->whereDate('end_date', '<=', $end); }
        if ($venueId) { $eventsQuery->where('venue_id', $venueId); }
        
        $events = $eventsQuery->orderByDesc('start_date')->paginate(10)->withQueryString();
        
        // Events KPIs
        $eventsKpis = [
            'total' => Event::count(),
            'upcoming' => Event::where('start_date', '>', now())->count(),
            'completed' => Event::where('end_date', '<', now())->count(),
            'this_month' => Event::whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)->count(),
        ];

        // Add stats for view compatibility
        $stats = [
            'total' => $kpis['total']
        ];

        return view('drjavier.reports.reservation-reports', [
            'kpis' => $kpis,
            'results' => $results,
            'events' => $events,
            'eventsKpis' => $eventsKpis,
            'filters' => [
                'start_date' => $start,
                'end_date' => $end,
                'status' => $status,
                'venue_id' => $venueId,
                'department' => $department,
            ],
            'venues' => $venues,
            'stats' => $stats,
            'revenueTrendData' => $revenueTrendData,
            'monthLabels' => $monthLabels,
            'quarterlyRevenueData' => $quarterlyRevenueData,
            'quarterLabels' => $quarterLabels,
            'totalRevenue' => $totalRevenue,
            'averageRevenue' => $averageRevenue,
            'statusDistribution' => $statusDistribution,
        ]);
    }

    /**
     * Export reports to Excel
     */
    public function exportReports(Request $request)
    {
        $exportType = $request->query('export_type', 'both');
        $startDate = $request->query('export_start_date');
        $endDate = $request->query('export_end_date');
        $includeFilters = $request->query('include_filters', false);
        $includeSummary = $request->query('include_summary', true);
        $exportStatuses = $request->query('export_statuses');

        // Apply filters from current page if requested
        if ($includeFilters) {
            $startDate = $startDate ?: $request->query('start_date');
            $endDate = $endDate ?: $request->query('end_date');
        }

        $fileName = 'drjavier_reports_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        if ($exportType === 'events') {
            return $this->exportEventsOnly($startDate, $endDate, $includeSummary, $fileName);
        } elseif ($exportType === 'reservations') {
            return $this->exportReservationsOnly($startDate, $endDate, $exportStatuses, $includeSummary, $fileName);
        } else {
            return $this->exportBoth($startDate, $endDate, $exportStatuses, $includeSummary, $fileName);
        }
    }

    /**
     * Export events only
     */
    private function exportEventsOnly($startDate, $endDate, $includeSummary, $fileName)
    {
        $query = Event::with(['venue']);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        $events = $query->orderBy('start_date')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DrJavierEventsExport($events, $includeSummary),
            $fileName
        );
    }

    /**
     * Export reservations only
     */
    private function exportReservationsOnly($startDate, $endDate, $exportStatuses, $includeSummary, $fileName)
    {
        $query = Reservation::with(['user', 'venue']);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        if ($exportStatuses) {
            $statuses = explode(',', $exportStatuses);
            $query->whereIn('status', $statuses);
        }

        $reservations = $query->orderBy('start_date')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DrJavierReservationsExport($reservations, $includeSummary),
            $fileName
        );
    }

    /**
     * Export both reservations and events
     */
    private function exportBoth($startDate, $endDate, $exportStatuses, $includeSummary, $fileName)
    {
        // Get events
        $eventsQuery = Event::with(['venue']);
        if ($startDate) {
            $eventsQuery->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $eventsQuery->whereDate('end_date', '<=', $endDate);
        }
        $events = $eventsQuery->orderBy('start_date')->get();

        // Get reservations
        $reservationsQuery = Reservation::with(['user', 'venue']);
        if ($startDate) {
            $reservationsQuery->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $reservationsQuery->whereDate('end_date', '<=', $endDate);
        }
        if ($exportStatuses) {
            $statuses = explode(',', $exportStatuses);
            $reservationsQuery->whereIn('status', $statuses);
        }
        $reservations = $reservationsQuery->orderBy('start_date')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DrJavierCombinedExport($reservations, $events, $includeSummary),
            $fileName
        );
    }

    /**
     * Export reservation and event reports to PDF with charts support.
     */
    public function exportReportsPdf(Request $request)
    {
        $exportType = $request->query('export_type', 'both');
        $startDate = $request->query('export_start_date');
        $endDate = $request->query('export_end_date');
        $includeFilters = $request->query('include_filters', false);
        $includeSummary = $request->query('include_summary', true);
        $chartOption = $request->query('chart_option', 'none'); // none, include, only
        $exportStatuses = $request->query('export_statuses');

        if ($includeFilters) {
            $startDate = $startDate ?: $request->query('start_date');
            $endDate = $endDate ?: $request->query('end_date');
        }

        // Fetch data based on export type
        $reservations = collect();
        $events = collect();

        if ($exportType === 'reservations' || $exportType === 'both') {
            $query = Reservation::with(['user', 'venue']);
            if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
            if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
            if ($exportStatuses) { $query->whereIn('status', explode(',', $exportStatuses)); }
            $reservations = $query->orderBy('start_date')->get();
        }

        if ($exportType === 'events' || $exportType === 'both') {
            $query = Event::with(['venue']);
            if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
            if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
            $events = $query->orderBy('start_date')->get();
        }

        $fileName = 'ppgs_reports_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Generate PDF
        $pdf = \PDF::loadView('drjavier.reports.pdf-export', [
            'exportType' => $exportType,
            'reservations' => $reservations,
            'events' => $events,
            'includeSummary' => $includeSummary,
            'chartOption' => $chartOption,
            'dateRange' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);

        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($fileName);
    }

    /**
     * Export GSU reports to Excel.
     */
    public function exportGsuReports(Request $request)
    {
        $query = \App\Models\Report::with(['reporter', 'reportedUser', 'reservation', 'event'])
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

        // Get all filtered reports (no pagination for export)
        $reports = $query->get();

        // Generate filename with date range if applicable
        $filename = 'GSU_Reports';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filename .= '_' . $request->start_date . '_to_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
            $filename .= '_from_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
            $filename .= '_until_' . $request->end_date;
        }
        $filename .= '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GSUReportsExport($reports),
            $filename
        );
    }

    /**
     * Display the profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('drjavier.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        $user->save();

        return redirect()->route('drjavier.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('drjavier.profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Get comprehensive ratings analytics data
     */
    private function getRatingsAnalytics()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Overall ratings statistics
        $totalRatings = ReservationRating::count();
        $averageRating = ReservationRating::avg('rating') ?? 0;
        $ratingsThisMonth = ReservationRating::where('created_at', '>=', $startOfMonth)->count();
        $averageRatingThisMonth = ReservationRating::where('created_at', '>=', $startOfMonth)->avg('rating') ?? 0;
        
        // Rating distribution (1-5 stars)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = ReservationRating::where('rating', $i)->count();
        }
        
        // Monthly ratings trend (last 12 months)
        $monthlyRatingsRaw = ReservationRating::where('created_at', '>=', $startOfYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('month')
            ->get();
        
        $monthlyRatingsData = [];
        $monthlyAverageRatings = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyRatingsRaw->where('month', $i)->first();
            $monthlyRatingsData[] = $monthData ? $monthData->count : 0;
            $monthlyAverageRatings[] = $monthData ? round($monthData->avg_rating, 1) : 0;
        }
        
        // Ratings by venue
        $ratingsByVenue = ReservationRating::join('reservations', 'reservation_ratings.reservation_id', '=', 'reservations.id')
            ->join('venues', 'reservations.venue_id', '=', 'venues.id')
            ->selectRaw('venues.name as venue_name, COUNT(*) as total_ratings, AVG(reservation_ratings.rating) as avg_rating')
            ->groupBy('venues.id', 'venues.name')
            ->having('total_ratings', '>', 0)
            ->orderBy('total_ratings', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'venue' => $item->venue_name,
                    'total_ratings' => $item->total_ratings,
                    'avg_rating' => round($item->avg_rating, 1)
                ];
            });
        
        // Ratings by department
        $ratingsByDepartment = ReservationRating::join('reservations', 'reservation_ratings.reservation_id', '=', 'reservations.id')
            ->selectRaw('reservations.department, COUNT(*) as total_ratings, AVG(reservation_ratings.rating) as avg_rating')
            ->whereNotNull('reservations.department')
            ->groupBy('reservations.department')
            ->having('total_ratings', '>', 0)
            ->orderBy('total_ratings', 'desc')
            ->limit(8)
            ->get()
            ->map(function($item) {
                return [
                    'department' => $item->department,
                    'total_ratings' => $item->total_ratings,
                    'avg_rating' => round($item->avg_rating, 1)
                ];
            });
        
        // Recent ratings with comments
        $recentRatings = ReservationRating::with(['reservation.venue', 'user'])
            ->whereNotNull('comment')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'user_name' => $rating->user->name,
                    'venue_name' => $rating->reservation->venue->name ?? 'N/A',
                    'event_title' => $rating->reservation->event_title,
                    'created_at' => $rating->created_at->format('M d, Y g:i A')
                ];
            });
        
        // Rating growth (comparing this month vs last month)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $ratingsLastMonth = ReservationRating::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $ratingGrowth = $ratingsLastMonth > 0 
            ? round((($ratingsThisMonth - $ratingsLastMonth) / $ratingsLastMonth) * 100, 1)
            : 0;
        
        return [
            'total_ratings' => $totalRatings,
            'average_rating' => round($averageRating, 1),
            'ratings_this_month' => $ratingsThisMonth,
            'average_rating_this_month' => round($averageRatingThisMonth, 1),
            'rating_distribution' => $ratingDistribution,
            'monthly_ratings_data' => $monthlyRatingsData,
            'monthly_average_ratings' => $monthlyAverageRatings,
            'ratings_by_venue' => $ratingsByVenue,
            'ratings_by_department' => $ratingsByDepartment,
            'recent_ratings' => $recentRatings,
            'rating_growth' => $ratingGrowth
        ];
    }
} 