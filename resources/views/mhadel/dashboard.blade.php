@extends('layouts.mhadel')

@section('title', 'Ms. Mhadel Dashboard')
@section('page-title', 'Ms. Mhadel Dashboard')
@section('page-subtitle', 'Second Level Approval - Reservation Management')

@section('content')
<div class="space-y-6 font-poppins">
	<!-- Tabs (server-driven) -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="px-6 pt-4">
			<div class="flex overflow-x-auto scrollbar-hide">
				<a href="{{ route('mhadel.dashboard', ['tab' => 'overview']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ ($tab ?? 'overview') === 'overview' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }}">Overview</a>
				<a href="{{ route('mhadel.dashboard', ['tab' => 'finance']) }}" class="ml-2 px-4 py-2 rounded-lg text-sm font-medium {{ ($tab ?? 'overview') === 'finance' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }}">Finance</a>
				<a href="{{ route('mhadel.dashboard', ['tab' => 'trends']) }}" class="ml-2 px-4 py-2 rounded-lg text-sm font-medium {{ ($tab ?? 'overview') === 'trends' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-50' }}">Trends</a>
			</div>
		</div>
		<div class="p-6 border-t border-gray-100">
			@if(($tab ?? 'overview') === 'overview')
				<!-- Hero (compact) -->
				<div class="rounded-xl p-5 bg-gradient-to-r from-gray-50 to-white border border-gray-100">
					<div class="flex items-start justify-between">
						<div>
							<h1 class="text-xl font-bold text-gray-800 font-montserrat">Welcome, {{ Auth::user()->name }}!</h1>
							<p class="text-sm text-gray-600 mt-1">Review IOSA-approved reservations and make final decisions.</p>
						</div>
						<div class="text-right">
							<div class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</div>
							<div class="text-xs text-gray-600">Pending Review</div>
						</div>
					</div>
				</div>

				<!-- Quick Stats (more spacing) -->
				<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
					<div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 flex items-center">
						<div class="rounded-md bg-yellow-50 p-2 mr-3"><i class="fas fa-clock text-yellow-500"></i></div>
						<div>
							<p class="text-xs text-gray-500">Pending Review</p>
							<h3 class="text-xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
							<p class="text-[11px] text-gray-500">IOSA Approved</p>
						</div>
					</div>
					<div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 flex items-center">
						<div class="rounded-md bg-green-50 p-2 mr-3"><i class="fas fa-check-circle text-green-500"></i></div>
						<div>
							<p class="text-xs text-gray-500">Approved Today</p>
							<h3 class="text-xl font-bold text-gray-800">{{ $stats['approved_today'] }}</h3>
							<p class="text-[11px] text-gray-500">Forwarded to OTP</p>
						</div>
					</div>
					<div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 flex items-center">
						<div class="rounded-md bg-red-50 p-2 mr-3"><i class="fas fa-times-circle text-red-500"></i></div>
						<div>
							<p class="text-xs text-gray-500">Rejected Today</p>
							<h3 class="text-xl font-bold text-gray-800">{{ $stats['rejected_today'] }}</h3>
							<p class="text-[11px] text-gray-500">Final Rejection</p>
						</div>
					</div>
					<div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 flex items-center">
						<div class="rounded-md bg-blue-50 p-2 mr-3"><i class="fas fa-calendar-alt text-blue-500"></i></div>
						<div>
							<p class="text-xs text-gray-500">Total This Month</p>
							<h3 class="text-xl font-bold text-gray-800">{{ $stats['total_month'] }}</h3>
							<p class="text-[11px] text-gray-500">All Reservations</p>
						</div>
					</div>
				</div>

				<!-- Quick Actions Tiles (more spacing) -->
				<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
					<a href="{{ route('mhadel.reservations.index') }}" class="bg-gray-800 text-white p-5 rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
						<div class="flex items-center">
							<i class="fas fa-calendar-check text-2xl mr-3"></i>
							<div>
								<h3 class="text-sm font-semibold">Review Reservations</h3>
								<p class="text-white/80 text-xs">View and manage pending reservations</p>
							</div>
						</div>
					</a>
					<a href="{{ route('mhadel.venues.index') }}" class="bg-blue-600 text-white p-5 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
						<div class="flex items-center">
							<i class="fas fa-building text-2xl mr-3"></i>
							<div>
								<h3 class="text-sm font-semibold">Manage Venues</h3>
								<p class="text-white/80 text-xs">Create and manage venues</p>
							</div>
						</div>
					</a>
					<a href="{{ route('mhadel.events.index') }}" class="bg-green-600 text-white p-5 rounded-lg hover:bg-green-700 transition-colors shadow-sm">
						<div class="flex items-center">
							<i class="fas fa-calendar-alt text-2xl mr-3"></i>
							<div>
								<h3 class="text-sm font-semibold">Manage Events</h3>
								<p class="text-white/80 text-xs">Create and manage events</p>
							</div>
						</div>
					</a>
				</div>

				<!-- Recent IOSA Approved Reservations (more spacing) -->
				<div class="bg-white rounded-lg shadow-sm border border-gray-100 mt-8">
					<div class="p-5 border-b border-gray-200 flex items-center justify-between">
						<h2 class="text-base font-bold text-gray-800 font-montserrat flex items-center">
							<i class="fas fa-history text-gray-700 mr-2"></i>
							Recent IOSA Approved Reservations
						</h2>
						<a href="{{ route('mhadel.reservations.index') }}" class="text-gray-800 hover:text-black text-sm font-medium">View All</a>
					</div>
					<div class="p-5">
						@if($recent_reservations->count() > 0)
							<div class="space-y-4">
								@foreach($recent_reservations as $reservation)
									<div class="flex items-center justify-between p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
										<div class="flex-1">
											<div class="flex items-center mb-2">
												<span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 rounded text-[11px] font-medium mr-2">Pending Review</span>
												<span class="text-sm text-gray-500">{{ $reservation->created_at->diffForHumans() }}</span>
											</div>
											<div class="text-sm font-semibold text-gray-800">{{ $reservation->event_title }}</div>
											<div class="flex items-center text-sm text-gray-600 mt-1">
												<i class="fas fa-user mr-2 text-gray-500"></i>
												<span>{{ $reservation->user->name }}</span>
												<span class="mx-2">•</span>
												<i class="fas fa-calendar mr-2 text-gray-500"></i>
												<span>{{ $reservation->start_date->format('M d, Y') }}</span>
											</div>
										</div>
										<div>
											<a href="{{ route('mhadel.reservations.show', $reservation->id) }}" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded text-sm hover:bg-blue-100 transition-colors" title="View Details">
												<i class="fas fa-eye"></i>
											</a>
										</div>
									</div>
								@endforeach
							</div>
						@else
							<div class="text-center py-8">
								<i class="fas fa-calendar-check text-3xl text-gray-300 mb-2"></i>
								<p class="text-sm text-gray-600">No Pending Reservations</p>
							</div>
						@endif
					</div>
				</div>
			@elseif(($tab ?? 'overview') === 'finance')
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Revenue (₱)</h3>
						<div style="height: 240px;"><canvas id="chartRevenue"></canvas></div>
					</div>
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Approvals vs Rejections</h3>
						<div style="height: 240px;"><canvas id="chartApprovals"></canvas></div>
					</div>
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Top Venues by Revenue</h3>
						<div style="height: 280px;"><canvas id="chartTopVenues"></canvas></div>
					</div>
				</div>
			@else
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Reservations by Department</h3>
						<div style="height: 240px;"><canvas id="chartDepartments"></canvas></div>
					</div>
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
						<h3 class="text-lg font-semibold text-gray-800 mb-4">Utilization Over Time</h3>
						<div style="height: 240px;"><canvas id="chartUtilization"></canvas></div>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>

@if(($tab ?? 'overview') !== 'overview')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	(function(){
		var labelsMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var revenueSeries = @json($revenueSeries ?? []);
		var approvals = @json($approvalsVsRejections['approved'] ?? 0);
		var rejections = @json($approvalsVsRejections['rejected'] ?? 0);
		var topVenues = @json($topVenues ?? []);
		var byDepartment = @json($byDepartment ?? []);
		var utilizationWeeks = @json($utilizationWeeks ?? []);
		function peso(v){ try { return '\u20B1' + Number(v||0).toLocaleString(); } catch(e){ return 'PHP ' + (v||0); } }

		function baseOptions(){
			return {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(17,24,39,0.95)', borderColor: 'rgba(255,255,255,0.08)', borderWidth: 1, titleColor: '#E5E7EB', bodyColor: '#E5E7EB', padding: 10 } },
				scales: { x: { grid: { color: 'rgba(17,24,39,0.06)' }, ticks: { color: '#6B7280' } }, y: { grid: { color: 'rgba(17,24,39,0.06)' }, ticks: { color: '#6B7280' } } }
			};
		}

		function prepareCanvas(id, height){
			var c = document.getElementById(id);
			if (!c) return null;
			c.style.height = height + 'px';
			c.style.maxHeight = height + 'px';
			if (Chart && Chart.getChart) { var inst = Chart.getChart(c); if (inst) inst.destroy(); }
			return c;
		}

		@if(($tab ?? 'overview') === 'finance')
		var rev = prepareCanvas('chartRevenue', 240);
		if (rev) new Chart(rev, { type: 'line', data: { labels: labelsMonths, datasets: [{ label: 'Revenue', data: revenueSeries, borderColor: '#8B1818', backgroundColor: 'rgba(139,24,24,0.12)', fill: true, tension: 0.35, pointRadius: 0, borderWidth: 2 }] }, options: (function(){ var o=baseOptions(); o.scales.y.ticks.callback=function(v){ return peso(v); }; return o; })() });
		var appr = prepareCanvas('chartApprovals', 240);
		if (appr) new Chart(appr, { type: 'bar', data: { labels: ['Approved','Rejected'], datasets: [{ data: [approvals, rejections], backgroundColor: ['#10B981','#EF4444'], borderRadius: 8, maxBarThickness: 36 }] }, options: (function(){ var o=baseOptions(); o.scales.x.grid.display=false; return o; })() });
		var tv = prepareCanvas('chartTopVenues', 280);
		if (tv) { var vl = topVenues.map(function(v){ return v.venue; }); var vt = topVenues.map(function(v){ return v.total; }); new Chart(tv, { type: 'bar', data: { labels: vl, datasets: [{ data: vt, backgroundColor: '#8B1818', borderRadius: 8, barPercentage: 0.7, categoryPercentage: 0.7 }] }, options: (function(){ var o=baseOptions(); o.indexAxis='y'; o.scales.x.ticks.callback=function(v){ return peso(v); }; return o; })() }); }
		@else
		var dept = prepareCanvas('chartDepartments', 240);
		if (dept) new Chart(dept, { type: 'doughnut', data: { labels: byDepartment.map(function(d){ return d.department; }), datasets: [{ data: byDepartment.map(function(d){ return d.count; }), backgroundColor: ['#2563EB','#10B981','#8B1818','#F59E0B','#6B7280','#A855F7'] }] }, options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } }, cutout: '60%' } });
		var util = prepareCanvas('chartUtilization', 240);
		if (util) new Chart(util, { type: 'line', data: { labels: utilizationWeeks.map(function(u){ return 'W'+u.week; }), datasets: [{ data: utilizationWeeks.map(function(u){ return u.hours; }), borderColor: '#2563EB', backgroundColor: 'rgba(37,99,235,0.12)', fill: true, tension: 0.35, pointRadius: 0, borderWidth: 2 }] }, options: baseOptions() });
		@endif
	})();
</script>
@endif
@endsection 