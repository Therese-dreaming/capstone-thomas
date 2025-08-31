<?php

namespace App\Http\Controllers\Mhadel;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
		$monthlyRaw = Reservation::where('status', 'approved_OTP')
			->where('updated_at', '>=', $startOfYear)
			->selectRaw('MONTH(updated_at) as m, SUM(final_price) as revenue')
			->groupBy('m')
			->pluck('revenue', 'm');
		$revenueSeries = [];
		for ($i = 1; $i <= 12; $i++) {
			$revenueSeries[] = (float) ($monthlyRaw[$i] ?? 0);
		}
		
		// Quarterly revenue data
		$quarterlyRaw = Reservation::where('status', 'approved_OTP')
			->where('updated_at', '>=', $startOfYear)
			->selectRaw('QUARTER(updated_at) as q, SUM(final_price) as revenue')
			->groupBy('q')
			->pluck('revenue', 'q');
		$revenueQuarterly = [];
		for ($i = 1; $i <= 4; $i++) {
			$revenueQuarterly[] = (float) ($quarterlyRaw[$i] ?? 0);
		}
		
		// Expected revenue datasets (IOSA approved and Mhadel approved - waiting for final OTP approval)
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
			'approved' => Reservation::where('status', 'approved_mhadel')
				->where('updated_at', '>=', $startOfMonth)->count(),
			'rejected' => Reservation::where('status', 'rejected_mhadel')
				->where('updated_at', '>=', $startOfMonth)->count(),
		];
		
		$topVenues = Reservation::where('status', 'approved_OTP')
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
		$topVenuesByBookings = Reservation::where('status', 'approved_OTP')
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
		$byDepartmentRevenue = Reservation::whereNotNull('department')
			->where('status', 'approved_OTP')
			->whereNotNull('final_price')
			->selectRaw('department, SUM(final_price) as total_revenue')
			->groupBy('department')
			->orderByDesc('total_revenue')
			->take(6)
			->get()
			->map(function ($r) {
				return [ 'department' => $r->department, 'revenue' => (float) $r->total_revenue ];
			});
		
		$utilizationWeeks = Reservation::whereNotNull('start_date')
			->whereNotNull('end_date')
			->where('start_date', '>=', $startOfMonth)
			->selectRaw('WEEK(start_date, 1) as wk, SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hrs')
			->groupBy('wk')
			->orderBy('wk')
			->get()
			->map(function ($r) {
				return [ 'week' => (int) $r->wk, 'hours' => (int) $r->hrs ];
			})
			->filter(function ($item) {
				return $item['hours'] > 0; // Only show weeks with actual usage
			})
			->values();
		
		// Additional data for enhanced charts
		$totalRevenue = Reservation::where('status', 'approved_OTP')
			->where('updated_at', '>=', $startOfMonth)
			->sum('final_price');
		
		$averageRevenue = Reservation::where('status', 'approved_OTP')
			->where('updated_at', '>=', $startOfMonth)
			->whereNotNull('final_price')
			->avg('final_price');
		
		// Calculate expected revenue from pending reservations
		$expectedRevenue = Reservation::whereIn('status', ['approved_IOSA', 'approved_mhadel'])
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
		$previousMonthRevenue = Reservation::where('status', 'approved_OTP')
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
		
		// Calculate average processing time (in hours)
		$avgProcessingTime = Reservation::whereIn('status', ['approved_mhadel', 'rejected_mhadel'])
			->whereNotNull('created_at')
			->whereNotNull('updated_at')
			->get()
			->avg(function ($r) {
				return $r->created_at->diffInHours($r->updated_at);
			});
		
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
				'revenue' => (float) Reservation::where('status', 'approved_OTP')
					->whereBetween('updated_at', [$monthStart, $monthEnd])
					->sum('final_price')
			];
		}
		
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
			'avgProcessingTime' => round($avgProcessingTime ?? 0, 1),
			'statusFlow' => $statusFlow,
			'peakHours' => $peakHours,
			'monthlyComparison' => $monthlyComparison,
		]);
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

		return view('mhadel.reports.index', [
			'kpis' => $kpis,
			'results' => $results,
			'filters' => [
				'start_date' => $start,
				'end_date' => $end,
				'status' => $status,
				'venue_id' => $venueId,
				'department' => $department,
			],
		]);
	}
} 