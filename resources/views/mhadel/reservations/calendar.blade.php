@extends('layouts.mhadel')

@section('title', 'Calendar')
@section('page-title', 'Final Approved Calendar & Official Events')
@section('page-subtitle', 'OTP-approved reservations and scheduled events')

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
	.font-inter { font-family: 'Inter', sans-serif; }
	.font-poppins { font-family: 'Poppins', sans-serif; }
	.calendar-day {
		aspect-ratio: 1/1;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 0.9rem;
		padding: 0.25rem;
		min-width: 5.5rem;
		max-width: 6rem;
	}
	.calendar-day:hover:not(.disabled) { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
	.animate-pulse { animation: pulse 2s infinite; }
	@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(128,0,0,0.4); } 70% { box-shadow: 0 0 0 10px rgba(128,0,0,0); } 100% { box-shadow: 0 0 0 0 rgba(128,0,0,0); } }
	
	.calendar-filter-btn {
		border-color: #d1d5db;
		color: #6b7280;
		background-color: #f9fafb;
	}
	
	.calendar-filter-btn:hover {
		background-color: #f3f4f6;
		border-color: #9ca3af;
	}
	
	.calendar-filter-btn.active {
		background-color: #800000;
		color: white;
		border-color: #800000;
	}
</style>

<div class="space-y-6 font-inter font-poppins">
	<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
		<div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
			<div class="flex items-center justify-between">
				<h2 class="text-2xl font-bold text-gray-800 font-montserrat flex items-center">
					<i class="fas fa-calendar-alt mr-3 text-maroon"></i>
					Calendar of Final Approved Reservations & Official Events
				</h2>
				<div class="flex items-center space-x-2 bg-white rounded-lg shadow p-1.5">
					<button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
						<i class="fas fa-chevron-left"></i>
					</button>
					<span id="currentMonth" class="font-medium text-gray-700 px-3 font-montserrat"></span>
					<button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
						<i class="fas fa-chevron-right"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- Calendar Status Filter -->
		<div class="p-4 border-b border-gray-200 bg-gray-50">
			<div class="flex items-center justify-between">
				<h3 class="text-lg font-medium text-gray-800">Filter by Status:</h3>
				<div class="flex items-center space-x-2">
					<button onclick="filterCalendarByStatus('all')" id="filter-all" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn active">
						All
					</button>
					<button onclick="filterCalendarByStatus('pending')" id="filter-pending" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
						Pending
					</button>
					<button onclick="filterCalendarByStatus('approved')" id="filter-approved" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
						Approved
					</button>
					<button onclick="filterCalendarByStatus('rejected')" id="filter-rejected" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
						Rejected
					</button>
				</div>
			</div>
		</div>
		
		<div class="p-6">
			<div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
				<div class="flex items-center"><div class="w-4 h-4 bg-yellow-400 rounded-md mr-2"></div><span class="text-gray-600">Pending</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-blue-400 rounded-md mr-2"></div><span class="text-gray-600">IOSA Approved</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-green-400 rounded-md mr-2"></div><span class="text-gray-600">Mhadel Approved</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div><span class="text-gray-600">Final Approval</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-red-400 rounded-md mr-2"></div><span class="text-gray-600">Rejected</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-purple-400 rounded-md mr-2"></div><span class="text-gray-600">Official Event</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div><span class="text-gray-600">Today</span></div>
			</div>
			<div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto"></div>
		</div>
	</div>
</div>

<!-- Details Modal -->
<div id="calendarDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
	<div class="flex items-center justify-center min-h-screen p-4">
		<div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins">
			<div class="p-6 border-b border-gray-200 bg-gray-50">
				<div class="flex items-center justify-between">
					<h3 class="text-xl font-bold text-gray-800 font-montserrat">
						<i class="fas fa-calendar-day mr-2"></i>
						<span id="modalDateTitle">Reservations</span>
					</h3>
					<button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
			<div class="p-6"><div id="calendarDetailsContent"></div></div>
			<div class="p-6 border-t border-gray-200 flex justify-end"><button onclick="closeDetailsModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Close</button></div>
		</div>
	</div>
</div>

@push('scripts')
<script>
	const reservations = @json($reservations ?? []);
	const events = @json($events ?? []);
	let currentDate = new Date();
	let currentMonth = currentDate.getMonth();
	let currentYear = currentDate.getFullYear();
	let currentCalendarFilter = 'all';
	
	function formatDateLocal(d){const y=d.getFullYear();const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return `${y}-${m}-${day}`;}
	function timeLabel(s){const d=new Date(s);return d.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});} 
	
	function previousMonth(){ currentDate.setMonth(currentDate.getMonth()-1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function nextMonth(){ currentDate.setMonth(currentDate.getMonth()+1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function updateHeader(){ const names=['January','February','March','April','May','June','July','August','September','October','November','December']; document.getElementById('currentMonth').textContent=`${names[currentMonth]} ${currentYear}`; }
	
	// Calendar filter function
	function filterCalendarByStatus(status) {
		currentCalendarFilter = status;
		
		// Update button styles
		document.querySelectorAll('.calendar-filter-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		document.getElementById(`filter-${status}`).classList.add('active');
		
		// Re-render calendar with new filter
		renderCalendar();
	}
	
	// Function to get status label and styling
	function getStatusInfo(status) {
		switch(status) {
			case 'pending':
				return {
					label: 'Pending',
					bgColor: 'bg-yellow-100',
					textColor: 'text-yellow-800',
					borderColor: 'border-yellow-300'
				};
			case 'approved_IOSA':
				return {
					label: 'IOSA Approved',
					bgColor: 'bg-blue-100',
					textColor: 'text-blue-800',
					borderColor: 'border-blue-300'
				};
			case 'approved_mhadel':
				return {
					label: 'Mhadel Approved',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
				};
			case 'approved_OTP':
				return {
					label: 'Final Approved',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
				};
			case 'rejected_IOSA':
			case 'rejected_mhadel':
			case 'rejected_OTP':
				return {
					label: 'Rejected',
					bgColor: 'bg-red-100',
					textColor: 'text-red-800',
					borderColor: 'border-red-300'
				};
			default:
				return {
					label: 'Unknown',
					bgColor: 'bg-gray-100',
					textColor: 'text-gray-800',
					borderColor: 'border-gray-300'
				};
		}
	}
	
	document.addEventListener('DOMContentLoaded', ()=>{ renderCalendar(); updateHeader(); });
	
	function renderCalendar(){
		const calendar=document.getElementById('calendar');
		const firstDay=new Date(currentYear,currentMonth,1);
		const startDate=new Date(firstDay); startDate.setDate(startDate.getDate()-firstDay.getDay());
		let html='';
		['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d=>{ html+=`<div class="text-center py-2 text-sm font-medium text-gray-500 bg-gray-50">${d}</div>`; });
		for(let i=0;i<42;i++){
			const date=new Date(startDate); date.setDate(startDate.getDate()+i);
			const inMonth=date.getMonth()===currentMonth; const isToday=formatDateLocal(date)===formatDateLocal(new Date());
			let dayClass='calendar-day text-center py-3 relative rounded-lg transition-all duration-200';
			if(!inMonth){ dayClass+=' text-gray-400 bg-gray-50'; } else if(isToday){ dayClass+=' bg-maroon text-white font-bold'; } else { dayClass+=' bg-white hover:bg-gray-50'; }
			const dateStr=formatDateLocal(date);
			
			// Get filtered reservations based on current filter
			let filteredReservations = reservations || [];
			if (currentCalendarFilter !== 'all') {
				filteredReservations = (reservations || []).filter(reservation => {
					if (currentCalendarFilter === 'pending') {
						return ['pending', 'approved_IOSA'].includes(reservation.status);
					} else if (currentCalendarFilter === 'approved') {
						return ['approved_mhadel', 'approved_OTP'].includes(reservation.status);
					} else if (currentCalendarFilter === 'rejected') {
						return ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'].includes(reservation.status);
					}
					return true;
				});
			}
			
			const dayReservations=filteredReservations.filter(e=> formatDateLocal(new Date(e.start_date))===dateStr );
			const dayEvents=(events||[]).filter(e=> formatDateLocal(new Date(e.start_date))===dateStr );
			
			let marks='';
			if(dayReservations.length){
				// Determine the status color based on the reservation status
				let statusColor = 'bg-yellow-400'; // Default for pending
				const reservation = dayReservations[0]; // Use first reservation for color
				
				if (reservation.status === 'pending') {
					statusColor = 'bg-yellow-400'; // Yellow for pending
				} else if (reservation.status === 'approved_IOSA') {
					statusColor = 'bg-blue-400'; // Blue for IOSA approved
				} else if (reservation.status === 'approved_mhadel') {
					statusColor = 'bg-green-400'; // Green for Mhadel approved
				} else if (reservation.status === 'approved_OTP') {
					statusColor = 'bg-green-600'; // Darker green for final approval
				} else if (reservation.status === 'rejected_IOSA' || reservation.status === 'rejected_mhadel' || reservation.status === 'rejected_OTP') {
					statusColor = 'bg-red-400'; // Red for any rejection
				}
				
				marks+=`<div class="absolute w-3 h-3 ${statusColor} rounded-full" style="top:4px;right:4px" title="${dayReservations.length} reservation(s)"></div>`;
			}
			if(dayEvents.length){ marks+=`<div class="absolute w-3 h-3 bg-purple-400 rounded-full" style="top:4px;left:4px" title="${dayEvents.length} event(s)"></div>`; }
			html+=`<div class="${dayClass} cursor-pointer" onclick="openDetails('${dateStr}')"><div class="text-sm font-medium">${date.getDate()}</div>${marks}</div>`;
		}
		calendar.innerHTML=html;
	}
	
	function openDetails(dateStr){
		const start=new Date(`${dateStr}T00:00`), end=new Date(`${dateStr}T23:59:59`);
		
		// Get filtered reservations based on current filter
		let filteredReservations = reservations || [];
		if (currentCalendarFilter !== 'all') {
			filteredReservations = (reservations || []).filter(reservation => {
				if (currentCalendarFilter === 'pending') {
					return ['pending', 'approved_IOSA'].includes(reservation.status);
				} else if (currentCalendarFilter === 'approved') {
					return ['approved_mhadel', 'approved_OTP'].includes(reservation.status);
				} else if (currentCalendarFilter === 'rejected') {
					return ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'].includes(reservation.status);
				}
				return true;
			});
		}
		
		const dayReservations=filteredReservations.filter(e=> new Date(e.start_date)<end && new Date(e.end_date)>start );
		const dayEvents=(events||[]).filter(e=> new Date(e.start_date)<end && new Date(e.end_date)>start );
		if(!dayReservations.length && !dayEvents.length){
			document.getElementById('modalDateTitle').textContent=`Entries for ${new Date(`${dateStr}T00:00`).toLocaleDateString()}`;
			const wrap=document.getElementById('calendarDetailsContent');
			wrap.innerHTML=`<div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
				<i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
				<p class="text-gray-600">No reservations or events for this date</p>
			</div>`;
			document.getElementById('calendarDetailsModal').classList.remove('hidden');
			document.body.style.overflow='hidden';
			return;
		}
		document.getElementById('modalDateTitle').textContent=`Entries for ${new Date(`${dateStr}T00:00`).toLocaleDateString()}`;
		const wrap=document.getElementById('calendarDetailsContent'); wrap.innerHTML='';

		// Events first
		dayEvents.forEach(e=>{
			const venue=e.venue?.name || e.venue || '—'; const organizer=e.organizer || '—';
			wrap.insertAdjacentHTML('beforeend', `<div class="bg-blue-50 p-4 rounded-lg mb-4 border border-blue-200">
				<div class="flex items-start justify-between">
					<div class="flex-1">
						<h4 class="font-semibold text-gray-800 text-lg">${e.title}</h4>
						<div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
							<div><i class=\"fas fa-calendar mr-2 text-gray-500\"></i>${new Date(e.start_date).toLocaleDateString()}</div>
							<div><i class=\"fas fa-clock mr-2 text-gray-500\"></i>${timeLabel(e.start_date)} – ${timeLabel(e.end_date)}</div>
							<div><i class=\"fas fa-user mr-2 text-gray-500\"></i>${organizer}</div>
							<div><i class=\"fas fa-map-marker-alt mr-2 text-gray-500\"></i>${venue}</div>
						</div>
					</div>
					<span class="ml-4 px-3 py-1 rounded-full text-xs font-medium text-blue-700 bg-white border border-blue-300">Official Event</span>
				</div>
			</div>`);
		});

		// Reservations with correct status
		dayReservations.forEach(e=>{
			const venue=e.venue?.name || e.venue || '—'; const user=e.user?.name || e.user || '—';
			const statusInfo = getStatusInfo(e.status);
			wrap.insertAdjacentHTML('beforeend', `<div class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
				<div class="flex items-start justify-between">
					<div class="flex-1">
						<h4 class="font-semibold text-gray-800 text-lg">${e.event_title}</h4>
						<div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
							<div><i class=\"fas fa-calendar mr-2 text-gray-500\"></i>${new Date(e.start_date).toLocaleDateString()}</div>
							<div><i class=\"fas fa-clock mr-2 text-gray-500\"></i>${timeLabel(e.start_date)} – ${timeLabel(e.end_date)}</div>
							<div><i class=\"fas fa-user mr-2 text-gray-500\"></i>${user}</div>
							<div><i class=\"fas fa-map-marker-alt mr-2 text-gray-500\"></i>${venue}</div>
							<div><i class=\"fas fa-users mr-2 text-gray-500\"></i>${e.capacity ?? ''} attendees</div>
							${e.final_price ? `<div><i class=\\\"fas fa-tag mr-2 text-gray-500\\\"></i>₱${parseFloat(e.final_price).toLocaleString('en-US',{minimumFractionDigits:2})}</div>` : ''}
						</div>
						${e.purpose ? `<div class=\"mt-2 text-sm text-gray-600\"><strong>Purpose:</strong> ${e.purpose}</div>` : ''}
					</div>
					<span class="ml-4 px-3 py-1 rounded-full text-xs font-medium ${statusInfo.textColor} bg-white border ${statusInfo.borderColor}">${statusInfo.label}</span>
				</div>
			</div>`);
		});
		document.getElementById('calendarDetailsModal').classList.remove('hidden');
		document.body.style.overflow='hidden';
	}
	function closeDetailsModal(){ document.getElementById('calendarDetailsModal').classList.add('hidden'); document.body.style.overflow='auto'; }
</script>
@endpush
@endsection 