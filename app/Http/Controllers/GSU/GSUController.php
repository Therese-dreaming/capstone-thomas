<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GSUController extends Controller
{
	public function dashboard()
	{
		$today = Carbon::today();
		$startOfMonth = Carbon::now()->startOfMonth();
		$stats = [
			'approved' => Reservation::where('status', 'approved_OTP')->count(),
			'approved_today' => Reservation::where('status', 'approved_OTP')->whereDate('updated_at', $today)->count(),
			'total_month' => Reservation::where('status', 'approved_OTP')->where('created_at', '>=', $startOfMonth)->count(),
		];
		$recent = Reservation::with(['user','venue'])->where('status','approved_OTP')->orderByDesc('created_at')->limit(5)->get();
		return view('gsu.dashboard', compact('stats','recent'));
	}

	/**
	 * Show profile edit page for the authenticated GSU user.
	 */
	public function profile()
	{
		$user = Auth::user();
		
		// Get GSU-specific stats for the profile
		$user->reservations_count = Reservation::where('status', 'completed')->count();
		$user->events_count = \App\Models\Event::where('status', 'completed')->count();
		$user->issues_resolved = \App\Models\Report::where('status', 'resolved')->count();
		$user->pending_tasks = Reservation::where('status', 'approved_OTP')->count();
		
		return view('gsu.profile', compact('user'));
	}

	/**
	 * Update profile information for the authenticated GSU user.
	 */
	public function updateProfile(Request $request)
	{
		$user = Auth::user();
		
		// Validate the request
		$request->validate([
			'first_name' => 'nullable|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
			'department' => 'nullable|string|max:255',
			'current_password' => 'nullable|required_with:password',
			'password' => 'nullable|string|min:8|confirmed',
		]);

		// Verify current password if changing password
		if ($request->filled('password')) {
			if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
				return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
			}
		}

		// Update user information
		$user->first_name = $request->first_name ?? $user->first_name;
		$user->last_name = $request->last_name ?? $user->last_name;
		$user->name = $request->name;
		$user->email = $request->email;
		$user->department = $request->department ?? $user->department;
		
		// Update password if provided
		if ($request->filled('password')) {
			$user->password = Hash::make($request->password);
		}
		
		$user->save();

		return redirect()->route('gsu.profile')->with('success', 'Profile updated successfully.');
	}

	/**
	 * Reports page with filters, KPIs, table and export.
	 */
	public function reports(Request $request)
	{
		$start = $request->query('start_date');
		$end = $request->query('end_date');
		$status = $request->query('status');
		$venueId = $request->query('venue_id');
		$department = $request->query('department');

		// Build base query for KPIs (without status filter)
		$baseQuery = Reservation::query();
		if ($start) { $baseQuery->whereDate('start_date', '>=', $start); }
		if ($end) { $baseQuery->whereDate('end_date', '<=', $end); }
		if ($venueId) { $baseQuery->where('venue_id', $venueId); }
		if ($department) { $baseQuery->where('department', $department); }
		
		// Calculate KPIs from base query (without status filter)
		$kpis = [
			'total' => (clone $baseQuery)->count(),
			'approved' => (clone $baseQuery)->whereIn('status', ['approved_mhadel','approved_OTP','approved'])->count(),
			'rejected' => (clone $baseQuery)->whereIn('status', ['rejected_mhadel','rejected_OTP','rejected'])->count(),
			'revenue' => (float) ((clone $baseQuery)->whereNotNull('final_price')->sum('final_price')),
		];
		
		// Build filtered query for results
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

		return view('gsu.reports.index', [
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

		$fileName = 'gsu_reports_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

		if ($exportType === 'events') {
			$query = Event::with(['venue']);
			if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
			$events = $query->orderBy('start_date')->get();

			return \Maatwebsite\Excel\Facades\Excel::download(
				new \App\Exports\GSUEventsExport($events),
				$fileName
			);
		} elseif ($exportType === 'reservations') {
			$query = Reservation::with(['user', 'venue']);
			if ($startDate) { $query->whereDate('start_date', '>=', $startDate); }
			if ($endDate) { $query->whereDate('end_date', '<=', $endDate); }
			if ($exportStatuses) { $query->whereIn('status', explode(',', $exportStatuses)); }
			$reservations = $query->orderBy('start_date')->get();

			return \Maatwebsite\Excel\Facades\Excel::download(
				new \App\Exports\GSUReservationsExport($reservations),
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
				new \App\Exports\GSUCombinedExport($reservations, $events, (bool) $includeSummary),
				$fileName
			);
		}
	}

	/**
	 * Export reports to PDF with charts support.
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

		$fileName = 'gsu_reports_' . now()->format('Y-m-d_H-i-s') . '.pdf';

		// Generate PDF
		$pdf = \PDF::loadView('gsu.reports.pdf-export', [
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
} 