@extends('layouts.mhadel')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-subtitle', 'Analyze reservations and export data')

@section('content')
<div class="space-y-6 font-poppins">
	<!-- Filters -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
		<form method="GET" action="{{ route('mhadel.reports') }}" class="space-y-4" id="reportsFilterForm">
			<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
				<div>
					<label class="block text-xs text-gray-600 mb-1">Start Date</label>
					<input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full border-gray-300 rounded-lg">
				</div>
				<div>
					<label class="block text-xs text-gray-600 mb-1">End Date</label>
					<input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full border-gray-300 rounded-lg">
				</div>
				<div>
					<label class="block text-xs text-gray-600 mb-1">Status</label>
					<select name="status" class="w-full border-gray-300 rounded-lg">
						<option value="">All</option>
						@foreach(['pending','approved_IOSA','approved_mhadel','approved_OTP','rejected_mhadel','rejected_OTP'] as $s)
							<option value="{{ $s }}" {{ ($filters['status']??'')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="block text-xs text-gray-600 mb-1">Department</label>
					<input type="text" name="department" value="{{ $filters['department'] ?? '' }}" placeholder="e.g. Engineering" class="w-full border-gray-300 rounded-lg" autocomplete="off">
				</div>
				<div>
					<label class="block text-xs text-gray-600 mb-1">Venue ID</label>
					<input type="number" name="venue_id" value="{{ $filters['venue_id'] ?? '' }}" class="w-full border-gray-300 rounded-lg" placeholder="ID" min="0">
				</div>
			</div>
			<div class="flex flex-wrap items-center justify-between gap-3">
				<div class="flex flex-wrap items-center gap-2">
					<span class="text-xs text-gray-500 mr-1">Quick ranges:</span>
					<button type="button" data-range="this_week" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">This Week</button>
					<button type="button" data-range="this_month" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">This Month</button>
					<button type="button" data-range="ytd" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">YTD</button>
					<button type="button" data-range="clear" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50">Clear</button>
				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('mhadel.reports', ['export'=>'csv'] + request()->query()) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-800">
						<i class="fas fa-file-csv mr-2"></i>Export CSV
					</a>
					<button class="px-4 py-2 bg-gray-800 text-white rounded-lg">Apply</button>
				</div>
			</div>
		</form>
	</div>

	<!-- KPIs -->
	<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
		<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center">
			<div class="rounded-md bg-blue-50 p-2 mr-3"><i class="fas fa-list text-blue-600"></i></div>
			<div>
				<p class="text-xs text-gray-500">Total</p>
				<div class="text-2xl font-bold text-gray-800">{{ number_format($kpis['total'] ?? 0) }}</div>
			</div>
		</div>
		<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center">
			<div class="rounded-md bg-green-50 p-2 mr-3"><i class="fas fa-check-circle text-green-600"></i></div>
			<div>
				<p class="text-xs text-gray-500">Approved</p>
				<div class="text-2xl font-bold text-green-600">{{ number_format($kpis['approved'] ?? 0) }}</div>
			</div>
		</div>
		<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center">
			<div class="rounded-md bg-red-50 p-2 mr-3"><i class="fas fa-times-circle text-red-600"></i></div>
			<div>
				<p class="text-xs text-gray-500">Rejected</p>
				<div class="text-2xl font-bold text-red-600">{{ number_format($kpis['rejected'] ?? 0) }}</div>
			</div>
		</div>
		<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center">
			<div class="rounded-md bg-yellow-50 p-2 mr-3"><i class="fas fa-peso-sign text-yellow-600"></i></div>
			<div>
				<p class="text-xs text-gray-500">Revenue</p>
				<div class="text-2xl font-bold text-gray-800">{{ '₱'.number_format($kpis['revenue'] ?? 0, 2) }}</div>
			</div>
		</div>
	</div>

	<!-- Results -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-200 flex items-center justify-between">
			<h3 class="text-lg font-semibold text-gray-800">Results</h3>
			<div class="text-xs text-gray-500">Showing {{ $results->firstItem() ?? 0 }}–{{ $results->lastItem() ?? 0 }} of {{ $results->total() ?? 0 }}</div>
		</div>
		<div class="p-0 overflow-x-auto">
			<table class="min-w-full text-sm">
				<thead class="bg-gray-50 sticky top-0">
					<tr class="text-left text-gray-600">
						<th class="py-3 px-6">Event Title</th>
						<th class="py-3 px-6">Venue</th>
						<th class="py-3 px-6">Start</th>
						<th class="py-3 px-6">End</th>
						<th class="py-3 px-6">Status</th>
						<th class="py-3 px-6">Department</th>
						<th class="py-3 px-6">Requester</th>
						<th class="py-3 px-6 text-right">Final Price</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-100">
					@forelse($results as $r)
					@php
						$st = $r->status;
						$badge = 'bg-gray-100 text-gray-700';
						if ($st === 'approved_IOSA' || $st === 'approved_mhadel' || $st === 'approved_OTP' || $st === 'approved') { $badge = 'bg-green-100 text-green-700'; }
						if (str_starts_with($st, 'rejected') || $st === 'rejected') { $badge = 'bg-red-100 text-red-700'; }
						if ($st === 'pending') { $badge = 'bg-yellow-100 text-yellow-700'; }
					@endphp
					<tr class="hover:bg-gray-50">
						<td class="py-3 px-6 text-gray-800">{{ $r->event_title }}</td>
						<td class="py-3 px-6">{{ optional($r->venue)->name }}</td>
						<td class="py-3 px-6">{{ optional($r->start_date)->format('M d, Y g:i A') }}</td>
						<td class="py-3 px-6">{{ optional($r->end_date)->format('M d, Y g:i A') }}</td>
						<td class="py-3 px-6"><span class="px-2 py-0.5 rounded {{ $badge }} text-xs">{{ str_replace('_',' ', $r->status) }}</span></td>
						<td class="py-3 px-6">{{ $r->department }}</td>
						<td class="py-3 px-6">{{ optional($r->user)->name }}</td>
						<td class="py-3 px-6 text-right">{{ is_null($r->final_price) ? '—' : ('₱'.number_format((float)$r->final_price, 2)) }}</td>
					</tr>
					@empty
					<tr>
						<td colspan="8" class="py-10 text-center">
							<div class="flex flex-col items-center">
								<i class="fas fa-folder-open text-3xl text-gray-300 mb-2"></i>
								<p class="text-sm text-gray-600">No results match your filters.</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="p-4 border-t border-gray-100 flex items-center justify-between">
			<div class="text-xs text-gray-500">Page total: {{ '₱'.number_format($results->sum('final_price') ?? 0, 2) }}</div>
			{{ $results->links() }}
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
(function(){
	var form=document.getElementById('reportsFilterForm');
	if(!form) return;
	function fmt(d){ var z=n=>('0'+n).slice(-2); return d.getFullYear()+'-'+z(d.getMonth()+1)+'-'+z(d.getDate()); }
	function setRange(type){
		var s=form.querySelector('input[name="start_date"]');
		var e=form.querySelector('input[name="end_date"]');
		var now=new Date();
		if(type==='this_week'){
			var day=(now.getDay()+6)%7; // Monday start
			var monday=new Date(now); monday.setDate(now.getDate()-day);
			var sunday=new Date(monday); sunday.setDate(monday.getDate()+6);
			s.value=fmt(monday); e.value=fmt(sunday);
		}
		if(type==='this_month'){
			var first=new Date(now.getFullYear(), now.getMonth(), 1);
			var last=new Date(now.getFullYear(), now.getMonth()+1, 0);
			s.value=fmt(first); e.value=fmt(last);
		}
		if(type==='ytd'){
			var first=new Date(now.getFullYear(),0,1);
			s.value=fmt(first); e.value=fmt(now);
		}
		if(type==='clear'){
			s.value=''; e.value='';
		}
	}
	Array.prototype.forEach.call(document.querySelectorAll('[data-range]'), function(btn){
		btn.addEventListener('click', function(){ setRange(btn.getAttribute('data-range')); form.submit(); });
	});
})();
</script>
@endpush 