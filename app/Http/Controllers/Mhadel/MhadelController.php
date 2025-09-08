<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\ReservationRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MhadelController extends Controller
{
	/**
	 * Display the Mhadel dashboard.
	 */
	public function dashboard(Request $request)
	{
		$tab = $request->query('tab', 'overview');
		$today = Carbon::today();
		$startOfMonth = Carbon::now()->startOfMonth();
		$startOfYear = Carbon::now()->startOfYear();
		
		$stats = [
			'pending' => Reservation::where('status', 'approved_IOSA')->count(),
			'approved_today' => Reservation::where('status', 'approved_mhadel')
				->whereDate('updated_at', $today)->count(),
			'rejected_today' => Reservation::where('status', 'rejected_mhadel')
				->whereDate('updated_at', $today)->count(),
			'total_month' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel', 'rejected_mhadel'])
				->where('created_at', '>=', $startOfMonth)->count(),
		];
		
		$recent_reservations = Reservation::with(['user', 'venue'])
			->whereIn('status', ['approved_IOSA', 'approved_mhadel', 'rejected_mhadel'])
			->orderBy('created_at', 'desc')
			->take(5)
			->get();
		
		// Finance datasets
		// Actual revenue: COMPLETED only
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
		
		// Expected revenue datasets: use final_price from approved_mhadel and approved_OTP
		$monthlyExpectedRaw = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
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
		$quarterlyExpectedRaw = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
			->where('start_date', '>=', $startOfYear)
			->whereNotNull('final_price')
			->selectRaw('QUARTER(start_date) as q, SUM(final_price) as expected_revenue')
			->groupBy('q')
			->pluck('expected_revenue', 'q');
		$expectedRevenueQuarterly = [];
		for ($i = 1; $i <= 4; $i++) {
			$expectedRevenueQuarterly[] = (float) ($quarterlyExpectedRaw[$i] ?? 0);
		}
		
		// Debug: Log the expected revenue data
		\Log::info('Expected Revenue Debug:', [
			'monthlyExpectedRaw' => $monthlyExpectedRaw->toArray(),
			'expectedRevenueSeries' => $expectedRevenueSeries,
			'startOfYear' => $startOfYear->toDateString(),
			'count_approved_IOSA' => Reservation::where('status', 'approved_IOSA')->count(),
			'count_approved_mhadel' => Reservation::where('status', 'approved_mhadel')->count(),
			'count_with_final_price' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])->whereNotNull('final_price')->count(),
			'sample_reservations' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])->take(3)->get(['id', 'status', 'final_price', 'start_date'])->toArray()
		]);
		
		$approvalsVsRejections = [
			'approved' => Reservation::where('status', 'approved_mhadel')->count(),
			'rejected' => Reservation::where('status', 'rejected_mhadel')->count(),
		];
		
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
		
		// Trends datasets
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
		
		// Department data by revenue
		// Department distribution by revenue: completed only
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
		
		// Additional data for enhanced charts (completed only)
		$totalRevenue = Reservation::where('status', 'completed')
			->where('updated_at', '>=', $startOfMonth)
			->sum('final_price');
		
		$averageRevenue = Reservation::where('status', 'completed')
			->where('updated_at', '>=', $startOfMonth)
			->whereNotNull('final_price')
			->avg('final_price');
		
		// Calculate expected revenue from pending reservations
		$expectedRevenue = Reservation::whereIn('status', ['approved_mhadel', 'approved_OTP'])
			->where('start_date', '>=', $startOfMonth)
			->whereNotNull('final_price')
			->sum('final_price');
		
		// Debug: Log the single expected revenue calculation
		\Log::info('Single Expected Revenue Debug:', [
			'expectedRevenue' => $expectedRevenue,
			'startOfMonth' => $startOfMonth->toDateString(),
			'count_approved_IOSA_current_month' => Reservation::where('status', 'approved_IOSA')
				->where('start_date', '>=', $startOfMonth)
				->count(),
			'count_approved_mhadel_current_month' => Reservation::where('status', 'approved_mhadel')
				->where('start_date', '>=', $startOfMonth)
				->count(),
			'count_with_final_price_current_month' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
				->where('start_date', '>=', $startOfMonth)
				->whereNotNull('final_price')
				->count(),
			'sample_current_month' => Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
				->where('start_date', '>=', $startOfMonth)
				->take(3)->get(['id', 'status', 'final_price', 'start_date'])->toArray()
		]);
		
		// Debug: Show all reservation statuses in the database
		\Log::info('All Reservation Statuses:', [
			'status_counts' => Reservation::selectRaw('status, COUNT(*) as count')
				->groupBy('status')
				->pluck('count', 'status')
				->toArray(),
			'statuses_with_final_price' => Reservation::whereNotNull('final_price')
				->selectRaw('status, COUNT(*) as count, SUM(final_price) as total_price')
				->groupBy('status')
				->get()
				->toArray()
		]);
		
		// Calculate revenue growth (compare with previous month)
		$previousMonth = Carbon::now()->subMonth()->startOfMonth();
		$previousMonthRevenue = Reservation::where('status', 'completed')
			->where('updated_at', '>=', $previousMonth)
			->where('updated_at', '<', $startOfMonth)
			->sum('final_price');
		
		$revenueGrowth = $previousMonthRevenue > 0 
			? round((($totalRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
			: 0;
		
		// Additional metrics for trends
		$totalUsers = \App\Models\User::count();
		$totalVenues = \App\Models\Venue::where('is_available', true)->count();
		$totalReservations = Reservation::count();
		
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

		// Ratings Analytics Data
		$ratingsData = $this->getRatingsAnalytics();
		
		return view('mhadel.dashboard', [
			'tab' => $tab,
			'stats' => $stats,
			'recent_reservations' => $recent_reservations,
			'revenueSeries' => $revenueSeries,
			'revenueQuarterly' => $revenueQuarterly,
			'expectedRevenueSeries' => $expectedRevenueSeries,
			'expectedRevenueQuarterly' => $expectedRevenueQuarterly,
			'approvalsVsRejections' => $approvalsVsRejections,
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
			'statusFlow' => $statusFlow,
			'peakHours' => $peakHours,
			'monthlyComparison' => $monthlyComparison,
			'ratingsData' => $ratingsData,
		]);
	}

	/**
	 * Show profile edit page for the authenticated Mhadel user.
	 */
	public function profile()
	{
		$user = \Illuminate\Support\Facades\Auth::user();
		return view('mhadel.profile', compact('user'));
	}

	/**
	 * Update profile information for the authenticated Mhadel user.
	 */
	public function updateProfile(Request $request)
	{
		$user = \Illuminate\Support\Facades\Auth::user();
		$request->validate([
			'first_name' => 'nullable|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'name' => 'nullable|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
			'department' => 'nullable|string|max:255',
			'password' => 'nullable|string|min:8|confirmed',
		]);

		$user->first_name = $request->first_name ?? $user->first_name;
		$user->last_name = $request->last_name ?? $user->last_name;
		$user->name = $request->name ?? trim(($request->first_name ?? $user->first_name).' '.($request->last_name ?? $user->last_name)) ?: $user->name;
		$user->email = $request->email;
		$user->department = $request->department ?? $user->department;
		if ($request->filled('password')) {
			$user->password = \Illuminate\Support\Facades\Hash::make($request->password);
		}
		$user->save();

		return redirect()->route('mhadel.profile')->with('success', 'Profile updated successfully.');
	}

	/**
	 * Reports page with filters, KPIs, table and CSV export.
	 */
	public function reports(Request $request)
	{
		$start = $request->query('start_date');
		$end = $request->query('end_date');
		$status = $request->query('status');
		$venueId = $request->query('venue_id');
		$department = $request->query('department');
		$export = $request->query('export') === 'csv';

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
			'approved' => (clone $cloneForAgg)->whereIn('status', ['approved_mhadel','approved_OTP','approved'])->count(),
			'rejected' => (clone $cloneForAgg)->whereIn('status', ['rejected_mhadel','rejected_OTP','rejected'])->count(),
			'revenue' => (float) ((clone $cloneForAgg)->whereNotNull('final_price')->sum('final_price')),
		];

		if ($export) {
			$rows = $query->orderByDesc('start_date')->get();
			$headers = [
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="mhadel_reports.csv"'
			];
			$callback = function() use ($rows) {
				$handle = fopen('php://output', 'w');
				fputcsv($handle, ['Event Title','Venue','Start','End','Status','Department','Requester','Final Price']);
				foreach ($rows as $r) {
					fputcsv($handle, [
						$r->event_title,
						optional($r->venue)->name,
						optional($r->start_date)->format('Y-m-d H:i'),
						optional($r->end_date)->format('Y-m-d H:i'),
						$r->status,
						$r->department,
						optional($r->user)->name,
						$r->final_price
					]);
				}
				fclose($handle);
			};
			return response()->streamDownload($callback, 'mhadel_reports.csv', $headers);
		}

		$results = $query->orderByDesc('start_date')->paginate(10)->withQueryString();

		// Events list (for Events tab)
		$eventsQuery = Event::with(['venue']);
		if ($start) { $eventsQuery->whereDate('start_date', '>=', $start); }
		if ($end) { $eventsQuery->whereDate('end_date', '<=', $end); }
		if ($venueId) { $eventsQuery->where('venue_id', $venueId); }
		if ($department) { $eventsQuery->where('department', $department); }
		$events = $eventsQuery->orderByDesc('start_date')->paginate(10)->withQueryString();

		// Events charts data
		$eventsTimelineData = $events->getCollection()->groupBy(function($event) {
			return $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M Y') : 'Unknown';
		})->map(function($group) {
			return $group->count();
		})->sortKeys()->values()->toArray();
		$eventsTimelineLabels = $events->getCollection()->groupBy(function($event) {
			return $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M Y') : 'Unknown';
		})->keys()->sort()->values()->toArray();

		$eventsStatusData = [
			'upcoming' => $events->getCollection()->filter(function($event) { return $event->start_date && now()->isBefore($event->start_date); })->count(),
			'ongoing' => $events->getCollection()->filter(function($event) { return $event->start_date && $event->end_date && now()->isBetween($event->start_date, $event->end_date); })->count(),
			'completed' => $events->getCollection()->filter(function($event) { return $event->end_date && now()->isAfter($event->end_date); })->count(),
			'unknown' => $events->getCollection()->filter(function($event) { return !$event->start_date || !$event->end_date; })->count(),
		];

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

		// Add stats for view compatibility
		$stats = [
			'total' => $kpis['total']
		];

		return view('mhadel.reports.index', [
			'kpis' => $kpis,
			'results' => $results,
			'events' => $events,
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
			'eventsTimelineData' => $eventsTimelineData,
			'eventsTimelineLabels' => $eventsTimelineLabels,
			'eventsStatusData' => $eventsStatusData,
		]);
	}

	/**
	 * Show a specific report.
	 */
	public function showReport(Reservation $report)
	{
		// Redirect to the reservations show page since we removed the reports show view
		return redirect()->route('mhadel.reservations.show', $report);
	}

	/**
	 * Export reports to Excel.
	 */
	public function exportReports(Request $request)
	{
		$exportType = $request->query('export_type', 'both');
		$startDate = $request->query('export_start_date');
		$endDate = $request->query('export_end_date');
		$includeFilters = $request->query('include_filters', false);
		$includeSummary = $request->query('include_summary', true);
		$exportStatuses = $request->query('export_statuses');

		if ($includeFilters) {
			$startDate = $startDate ?: $request->query('start_date');
			$endDate = $endDate ?: $request->query('end_date');
		}

		$fileName = 'mhadel_reports_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

		if ($exportType === 'events') {
			$query = Event::with(['venue']);
			if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
			$events = $query->orderBy('start_date')->get();

			return \Maatwebsite\Excel\Facades\Excel::download(
				new \App\Exports\MhadelEventsExport($events),
				$fileName
			);
		} elseif ($exportType === 'reservations') {
			$query = Reservation::with(['user', 'venue']);
			if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
			if ($exportStatuses) { $query->whereIn('status', explode(',', $exportStatuses)); }
			$reservations = $query->orderBy('start_date')->get();

			return \Maatwebsite\Excel\Facades\Excel::download(
				new \App\Exports\MhadelReservationsExport($reservations),
				$fileName
			);
		} else {
			$eventsQuery = Event::with(['venue']);
			if ($startDate) { $eventsQuery->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $eventsQuery->whereDate('end_date', '<=', $endDate); }
			$events = $eventsQuery->orderBy('start_date')->get();

			$reservationsQuery = Reservation::with(['user', 'venue']);
			if ($startDate) { $reservationsQuery->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $reservationsQuery->whereDate('end_date', '<=', $endDate); }
			if ($exportStatuses) { $reservationsQuery->whereIn('status', explode(',', $exportStatuses)); }
			$reservations = $reservationsQuery->orderBy('start_date')->get();

			return \Maatwebsite\Excel\Facades\Excel::download(
				new \App\Exports\MhadelCombinedExport($reservations, $events, (bool) $includeSummary),
				$fileName
			);
		}
	}

	/**
	 * Display reports filed by GSU.
	 */
	public function gsuReports(Request $request)
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

		return view('mhadel.gsu-reports.index', compact(
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
	public function showGsuReport(Report $report)
	{
		$report->load(['reporter', 'reportedUser', 'reservation', 'event']);
		
		return view('mhadel.gsu-reports.show', compact('report'));
	}

	/**
	 * Export GSU reports to Excel.
	 */
	public function exportGsuReports(Request $request)
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
	 * Update report status.
	 */
	public function updateGsuReportStatus(Request $request, Report $report)
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