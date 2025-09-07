<?php

namespace App\Http\Controllers\IOSA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Report;

class IOSAController extends Controller
{
    /**
     * Display the IOSA dashboard.
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        
        $stats = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'approved_today' => Reservation::where('status', 'approved_IOSA')
                ->whereDate('created_at', today())->count(),
            'rejected_today' => Reservation::where('status', 'rejected_IOSA')
                ->whereDate('created_at', today())->count(),
            'total_month' => Reservation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Key Metrics for Analytics Tab
        $totalUsers = \App\Models\User::count();
        $totalVenues = \App\Models\Venue::where('is_available', true)->count();
        $totalReservations = Reservation::count();
        

        // Revenue data - COMPLETED reservations only
        $monthlyRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('MONTH(updated_at) as m, SUM(final_price) as revenue')
            ->groupBy('m')
            ->pluck('revenue', 'm');
        $revenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueSeries[] = (float) ($monthlyRaw[$i] ?? 0);
        }
        
        // Quarterly revenue data
        $quarterlyRaw = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfYear)
            ->selectRaw('QUARTER(updated_at) as q, SUM(final_price) as revenue')
            ->groupBy('q')
            ->pluck('revenue', 'q');
        $revenueQuarterly = [];
        for ($i = 1; $i <= 4; $i++) {
            $revenueQuarterly[] = (float) ($quarterlyRaw[$i] ?? 0);
        }
        
        // Expected revenue datasets: use final_price from approved_IOSA and approved_mhadel
        $monthlyExpectedRaw = Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
            ->where('start_date', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('MONTH(start_date) as m, SUM(final_price) as expected_revenue')
            ->groupBy('m')
            ->pluck('expected_revenue', 'm');
        $expectedRevenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $expectedRevenueSeries[] = (float) ($monthlyExpectedRaw[$i] ?? 0);
        }
        
        // Quarterly expected revenue data
        $quarterlyExpectedRaw = Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
            ->where('start_date', '>=', $startOfYear)
            ->whereNotNull('final_price')
            ->selectRaw('QUARTER(start_date) as q, SUM(final_price) as expected_revenue')
            ->groupBy('q')
            ->pluck('expected_revenue', 'q');
        $expectedRevenueQuarterly = [];
        for ($i = 1; $i <= 4; $i++) {
            $expectedRevenueQuarterly[] = (float) ($quarterlyExpectedRaw[$i] ?? 0);
        }
        
        // Approval performance - IOSA only (approved_IOSA vs rejected_IOSA)
        $approvalsVsRejections = [
            'approved' => Reservation::where('status', 'approved_IOSA')->count(),
            'rejected' => Reservation::where('status', 'rejected_IOSA')->count(),
        ];
        
        // Top venues by revenue (completed only)
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
                    'venue' => $r->venue ? $r->venue->name : 'Unknown Venue',
                    'total' => (float) ($r->total ?? 0),
                ];
            })
            ->filter(function ($item) {
                return $item['total'] > 0; // Only show venues with actual revenue
            })
            ->values();
        
        // Top venues by bookings count
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
                    'venue' => $r->venue ? $r->venue->name : 'Unknown Venue',
                    'total' => (int) ($r->total_bookings ?? 0),
                ];
            })
            ->filter(function ($item) {
                return $item['total'] > 0; // Only show venues with actual bookings
            })
            ->values();
        
        // Department distribution by count: regardless of status
        $byDepartment = Reservation::whereNotNull('department')
            ->selectRaw('department, COUNT(*) as c')
            ->groupBy('department')
            ->orderByDesc('c')
            ->take(6)
            ->get()
            ->map(function ($r) {
                return [ 'department' => $r->department, 'count' => (int) $r->c ];
            });
        
        // Department data by revenue (completed only)
        $byDepartmentRevenue = Reservation::whereNotNull('department')
            ->where('status', 'completed')
            ->whereNotNull('final_price')
            ->selectRaw('department, SUM(final_price) as total_revenue')
            ->groupBy('department')
            ->orderByDesc('total_revenue')
            ->take(6)
            ->get()
            ->map(function ($r) {
                return [ 'department' => $r->department, 'revenue' => (float) $r->total_revenue ];
            });
        
        // Venue utilization data
        $utilizationWeeks = Reservation::where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '>=', $startOfMonth)
            ->selectRaw('WEEK(start_date, 1) as wk, SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hrs')
            ->groupBy('wk')
            ->orderBy('wk')
            ->get()
            ->map(function ($r) {
                return [ 'week' => (int) $r->wk, 'hours' => (int) $r->hrs ];
            })
            ->values();

        // Fill up venue utilization to include all weeks of the current month
        $utilMap = collect($utilizationWeeks)->keyBy('week');
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
        
        // Revenue metrics (completed only)
        $totalRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('final_price');
        
        $averageRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $startOfMonth)
            ->whereNotNull('final_price')
            ->avg('final_price');
        
        // Calculate expected revenue from IOSA approved reservations
        $expectedRevenue = Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
            ->where('start_date', '>=', $startOfMonth)
            ->whereNotNull('final_price')
            ->sum('final_price');
        
        // Calculate revenue growth (compare with previous month)
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthRevenue = Reservation::where('status', 'completed')
            ->where('updated_at', '>=', $previousMonth)
            ->where('updated_at', '<', $startOfMonth)
            ->sum('final_price');
        
        $revenueGrowth = $previousMonthRevenue > 0 
            ? round((($totalRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;
        
        // Status flow data
        $statusFlow = [
            'submitted' => Reservation::where('status', 'pending')->count(),
            'iosa_approved' => Reservation::where('status', 'approved_IOSA')->count(),
            'mhadel_approved' => Reservation::where('status', 'approved_mhadel')->count(),
            'final_approved' => Reservation::where('status', 'approved_OTP')->count(),
            'rejected' => Reservation::whereIn('status', ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'])->count(),
        ];
        
        // Peak booking hours data
        $peakHours = Reservation::selectRaw('HOUR(start_date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($r) {
                return [
                    'hour' => $r->hour,
                    'count' => (int) $r->count
                ];
            });
        
        // Monthly comparison data (last 6 months)
        $monthlyComparison = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyComparison[] = [
                'month' => $monthStart->format('M'),
                'reservations' => Reservation::whereBetween('start_date', [$monthStart, $monthEnd])->count(),
                'revenue' => (float) Reservation::where('status', 'completed')
                    ->whereBetween('updated_at', [$monthStart, $monthEnd])
                    ->sum('final_price')
            ];
        }

        // Data for Monthly Trends Chart (last 6 months) - COMPLETED reservations only
        $monthlyTrends = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyTrends[] = Reservation::where('status', 'completed')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
        }

        // Data for Departments Chart
        $departmentsData = Reservation::selectRaw('department, COUNT(*) as count')
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get()
            ->pluck('count', 'department')
            ->toArray();

        $recent_reservations = Reservation::with(['user', 'venue'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('iosa.dashboard', compact(
            'stats', 
            'totalUsers',
            'totalVenues',
            'totalReservations',
            'revenueSeries',
            'revenueQuarterly',
            'expectedRevenueSeries',
            'expectedRevenueQuarterly',
            'approvalsVsRejections',
            'topVenues',
            'topVenuesByBookings',
            'byDepartment',
            'byDepartmentRevenue',
            'utilizationWeeks',
            'totalRevenue',
            'averageRevenue',
            'expectedRevenue',
            'revenueGrowth',
            'statusFlow',
            'peakHours',
            'monthlyComparison',
            'monthlyLabels',
            'monthlyTrends',
            'departmentsData',
            'recent_reservations'
        ));
    }

    /**
     * Display reservation reports and analytics.
     */
    public function reservationReports(Request $request)
    {
        // Get all reservations for IOSA to view
        $query = Reservation::with(['user', 'venue'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Get results
        $results = $query->paginate(15)->withQueryString();

        // Calculate KPIs
        $kpis = [
            'total' => Reservation::count(),
            'approved' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel', 'approved_OTP', 'completed'])->count(),
            'rejected' => Reservation::whereIn('status', ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'])->count(),
            'revenue' => Reservation::where('status', 'completed')->sum('final_price') ?? 0,
        ];

        // Get venues for filter
        $venues = \App\Models\Venue::where('is_available', true)->get();

        return view('iosa.reports.reservation-reports', compact(
            'results',
            'kpis',
            'venues'
        ));
    }

    /**
     * Export reservation reports to Excel.
     */
    public function exportReservationReports(Request $request)
    {
        // Prepare filters for the export class
        $filters = [];

        // Map export filters to the format expected by the export class
        if ($request->filled('export_start_date')) {
            $filters['start_date'] = $request->export_start_date;
        }
        if ($request->filled('export_end_date')) {
            $filters['end_date'] = $request->export_end_date;
        }
        if ($request->filled('export_statuses')) {
            $statuses = explode(',', $request->export_statuses);
            $filters['export_statuses'] = $statuses;
        }

        // Apply current page filters if requested
        if ($request->filled('include_filters')) {
            if ($request->filled('start_date')) {
                $filters['start_date'] = $request->start_date;
            }
            if ($request->filled('end_date')) {
                $filters['end_date'] = $request->end_date;
            }
            if ($request->filled('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->filled('venue_id')) {
                $filters['venue_id'] = $request->venue_id;
            }
            if ($request->filled('department')) {
                $filters['department'] = $request->department;
            }
        }

        // Create export with properly formatted filters
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\IOSAReservationsExport($filters),
            'iosa-reservation-reports-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    /**
     * Display reports filed by GSU.
     */
    public function reports(Request $request)
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

        return view('iosa.reports.index', compact(
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
    public function showReport(Report $report)
    {
        $report->load(['reporter', 'reportedUser', 'reservation', 'event']);
        
        return view('iosa.reports.show', compact('report'));
    }

    /**
     * Update report status.
     */
    public function updateReportStatus(Request $request, Report $report)
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