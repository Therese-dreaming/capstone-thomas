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
					'venue' => optional($r->venue)->name ?? 'Unknown',
					'total' => (float) ($r->total ?? 0),
				];
			});
		
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
		
		$utilizationWeeks = Reservation::where('start_date', '>=', $startOfMonth)
			->selectRaw('WEEK(start_date, 1) as wk, SUM(TIMESTAMPDIFF(HOUR, start_date, end_date)) as hrs')
			->groupBy('wk')
			->orderBy('wk')
			->get()
			->map(function ($r) {
				return [ 'week' => (int) $r->wk, 'hours' => (int) $r->hrs ];
			});
		
		return view('mhadel.dashboard', [
			'tab' => $tab,
			'stats' => $stats,
			'recent_reservations' => $recent_reservations,
			'revenueSeries' => $revenueSeries,
			'approvalsVsRejections' => $approvalsVsRejections,
			'topVenues' => $topVenues,
			'byDepartment' => $byDepartment,
			'utilizationWeeks' => $utilizationWeeks,
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