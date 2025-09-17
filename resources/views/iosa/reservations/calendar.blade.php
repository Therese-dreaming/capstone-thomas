@extends('layouts.iosa')

@section('title', 'Calendar')
@section('page-title', 'Calendar & Official Events')
@section('page-subtitle', 'All reservations and scheduled events')

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
		<!-- View Toggle and Calendar Status Filter -->
		<div class="p-4 border-b border-gray-200 bg-gray-50">
			<div class="flex items-center justify-between">
				<div class="flex items-center space-x-4">
					<h3 class="text-lg font-medium text-gray-800">View:</h3>
					<div class="flex items-center space-x-2">
						<button onclick="showCalendarView()" id="calendar-view-btn" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn active">
							<i class="fas fa-calendar-alt mr-2"></i>Calendar
						</button>
						<button onclick="showScheduleView()" id="schedule-view-btn" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							<i class="fas fa-clock mr-2"></i>Schedule
						</button>
					</div>
				</div>
				<div class="flex items-center space-x-4">
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
						<button onclick="filterCalendarByStatus('completed')" id="filter-completed" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							Completed
						</button>
						<button onclick="filterCalendarByStatus('rejected')" id="filter-rejected" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							Rejected
						</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Calendar View -->
		<div id="calendar-view" class="p-6">
			<div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
				<div class="flex items-center"><div class="w-4 h-4 bg-yellow-400 rounded-md mr-2"></div><span class="text-gray-600">Pending</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-blue-400 rounded-md mr-2"></div><span class="text-gray-600">IOSA Approved</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-green-400 rounded-md mr-2"></div><span class="text-gray-600">Approved by OTP</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div><span class="text-gray-600">Approved by PPGS</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-gray-400 rounded-md mr-2"></div><span class="text-gray-600">Completed</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-red-400 rounded-md mr-2"></div><span class="text-gray-600">Rejected</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-purple-400 rounded-md mr-2"></div><span class="text-gray-600">Official Event</span></div>
				<div class="flex items-center"><div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div><span class="text-gray-600">Today</span></div>
			</div>
			<div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto"></div>
		</div>

		<!-- Schedule View -->
		<div id="schedule-view" class="p-6 hidden">
			<div class="flex items-center justify-between mb-6">
				<div class="flex items-center space-x-4">
					<button onclick="previousWeek()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
						<i class="fas fa-chevron-left"></i>
					</button>
					<span id="currentWeek" class="font-medium text-gray-700 px-3 font-montserrat"></span>
					<button onclick="nextWeek()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
						<i class="fas fa-chevron-right"></i>
					</button>
				</div>
				<div class="flex items-center space-x-2">
					<button onclick="showToday()" class="px-3 py-1 text-sm bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
						Today
					</button>
				</div>
			</div>
			<div id="schedule-table" class="overflow-x-auto">
				<!-- Schedule table will be rendered here -->
			</div>
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



@section('scripts')
<script>
	console.log('Script starting...');
	
	const reservations = @json($reservations ?? []);
	const events = @json($events ?? []);
	let currentDate = new Date();
	let currentMonth = currentDate.getMonth();
	let currentYear = currentDate.getFullYear();
	let currentCalendarFilter = 'all';
	let currentWeekStart = new Date();
	currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay());
	
	function formatDateLocal(d){const y=d.getFullYear();const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return `${y}-${m}-${day}`;}
	function timeLabel(s){const d=new Date(s);return d.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});} 
	
	// Format time display for schedule view
	function formatTimeDisplay(date) {
		const d = new Date(date);
		return d.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});
	}
	
	function previousMonth(){ currentDate.setMonth(currentDate.getMonth()-1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function nextMonth(){ currentDate.setMonth(currentDate.getMonth()+1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function updateHeader(){ const names=['January','February','March','April','May','June','July','August','September','October','November','December']; document.getElementById('currentMonth').textContent=`${names[currentMonth]} ${currentYear}`; }
	
	// View toggle functions
	function showCalendarView() {
		document.getElementById('calendar-view').classList.remove('hidden');
		document.getElementById('schedule-view').classList.add('hidden');
		document.getElementById('calendar-view-btn').classList.add('active');
		document.getElementById('schedule-view-btn').classList.remove('active');
		renderCalendar();
	}
	
	function showScheduleView() {
		document.getElementById('calendar-view').classList.add('hidden');
		document.getElementById('schedule-view').classList.remove('hidden');
		document.getElementById('schedule-view-btn').classList.add('active');
		document.getElementById('calendar-view-btn').classList.remove('active');
		renderSchedule();
	}
	
	// Calendar filter function
	function filterCalendarByStatus(status) {
		currentCalendarFilter = status;
		
		// Update button styles
		document.querySelectorAll('.calendar-filter-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		document.getElementById(`filter-${status}`).classList.add('active');
		
		// Re-render current view
		if (document.getElementById('schedule-view').classList.contains('hidden')) {
			renderCalendar();
		} else {
			renderSchedule();
		}
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
					label: 'OTP Approved',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
				};
			case 'approved_OTP':
				return {
					label: 'PPGS Approved',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
				};
			case 'completed':
				return {
					label: 'Completed',
					bgColor: 'bg-gray-100',
					textColor: 'text-gray-800',
					borderColor: 'border-gray-300'
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
	
	// Schedule navigation functions
	function previousWeek() {
		currentWeekStart.setDate(currentWeekStart.getDate() - 7);
		renderSchedule();
		updateWeekHeader();
	}
	
	function nextWeek() {
		currentWeekStart.setDate(currentWeekStart.getDate() + 7);
		renderSchedule();
		updateWeekHeader();
	}
	
	function showToday() {
		currentWeekStart = new Date();
		currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay());
		renderSchedule();
		updateWeekHeader();
	}
	
	function updateWeekHeader() {
		const endDate = new Date(currentWeekStart);
		endDate.setDate(currentWeekStart.getDate() + 6);
		const startStr = currentWeekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
		const endStr = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
		document.getElementById('currentWeek').textContent = `${startStr} - ${endStr}`;
	}
	
	// Make functions globally available immediately
	window.showCalendarView = showCalendarView;
	window.showScheduleView = showScheduleView;
	window.filterCalendarByStatus = filterCalendarByStatus;
	window.previousWeek = previousWeek;
	window.nextWeek = nextWeek;
	window.showToday = showToday;
	window.openDetails = openDetails;
	window.closeDetailsModal = closeDetailsModal;
	window.previousMonth = previousMonth;
	window.nextMonth = nextMonth;
	
	document.addEventListener('DOMContentLoaded', ()=>{ 
		console.log('DOM loaded');
		console.log('Reservations:', reservations);
		console.log('Events:', events);
		console.log('Calendar element:', document.getElementById('calendar'));
		console.log('Schedule element:', document.getElementById('schedule-table'));
		
		try {
			renderCalendar(); 
			updateHeader(); 
			updateWeekHeader();
			console.log('Calendar rendered successfully');
		} catch (error) {
			console.error('Error rendering calendar:', error);
		}
	});
	
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
					} else if (currentCalendarFilter === 'completed') {
						return ['completed'].includes(reservation.status);
					} else if (currentCalendarFilter === 'rejected') {
						return ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'].includes(reservation.status);
					}
					return true;
				});
			}
			
			// Use the same date range logic as the modal details
			const dayStart = new Date(`${dateStr}T00:00`);
			const dayEnd = new Date(`${dateStr}T23:59:59`);
			
			const dayReservations = filteredReservations.filter(e => 
				new Date(e.start_date) < dayEnd && new Date(e.end_date) > dayStart
			);
			const dayEvents = (events || []).filter(e => 
				new Date(e.start_date) < dayEnd && new Date(e.end_date) > dayStart
			);
			
			// Debug logging for September 7, 2025
			if (dateStr === '2025-09-07') {
				console.log(`Date: ${dateStr}`);
				console.log('Day start:', dayStart);
				console.log('Day end:', dayEnd);
				console.log('All events:', events);
				console.log('Filtered events for this day:', dayEvents);
				dayEvents.forEach(event => {
					console.log(`Event: ${event.title}, Start: ${event.start_date}, End: ${event.end_date}`);
				});
			}
			
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
				} else if (reservation.status === 'completed') {
					statusColor = 'bg-gray-400'; // Gray for completed
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
				} else if (currentCalendarFilter === 'completed') {
					return ['completed'].includes(reservation.status);
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
							<div><i class="fas fa-calendar mr-2 text-gray-500"></i>${new Date(e.start_date).toLocaleDateString()}</div>
							<div><i class="fas fa-clock mr-2 text-gray-500"></i>${timeLabel(e.start_date)} – ${timeLabel(e.end_date)}</div>
							<div><i class="fas fa-user mr-2 text-gray-500"></i>${organizer}</div>
							<div><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>${venue}</div>
						</div>
					</div>
					<div class="flex items-center space-x-2">
						<span class="px-3 py-1 rounded-full text-xs font-medium text-blue-700 bg-white border border-blue-300">Official Event</span>
						<a href="/iosa/events/${e.id}" 
							class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" title="View Event">
							<i class="fas fa-eye text-sm"></i>
						</a>
					</div>
				</div>
			</div>`);
		});

		// Reservations with correct status
		dayReservations.forEach(e=>{
			const venue=e.venue?.name || e.venue || '—'; const user=e.user?.name || e.user || '—';
			const statusInfo = getStatusInfo(e.status);
			
			// IOSA cannot edit reservations
			const canEdit = false;
			
			wrap.insertAdjacentHTML('beforeend', `<div class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
				<div class="flex items-start justify-between">
					<div class="flex-1">
						<h4 class="font-semibold text-gray-800 text-lg">${e.event_title}</h4>
						<div class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
							<div><i class="fas fa-calendar mr-2 text-gray-500"></i>${new Date(e.start_date).toLocaleDateString()}</div>
							<div><i class="fas fa-clock mr-2 text-gray-500"></i>${timeLabel(e.start_date)} – ${timeLabel(e.end_date)}</div>
							<div><i class="fas fa-user mr-2 text-gray-500"></i>${user}</div>
							<div><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>${venue}</div>
							<div><i class="fas fa-users mr-2 text-gray-500"></i>${e.capacity ?? ''} attendees</div>
							${e.final_price ? `<div><i class="fas fa-tag mr-2 text-gray-500"></i>₱${parseFloat(e.final_price).toLocaleString('en-US',{minimumFractionDigits:2})}</div>` : ''}
						</div>
						${e.purpose ? `<div class="mt-2 text-sm text-gray-600"><strong>Purpose:</strong> ${e.purpose}</div>` : ''}
					</div>
					<div class="flex items-center space-x-2">
						<span class="px-3 py-1 rounded-full text-xs font-medium ${statusInfo.textColor} bg-white border ${statusInfo.borderColor}">${statusInfo.label}</span>
						<a href="/iosa/reservations/${e.id}" 
							class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" title="View Reservation">
							<i class="fas fa-eye text-sm"></i>
						</a>
					</div>
				</div>
			</div>`);
		});
		document.getElementById('calendarDetailsModal').classList.remove('hidden');
		document.body.style.overflow='hidden';
	}
	
	// Function to get status-based colors for schedule items
	function getStatusColors(status, isEvent) {
		if (isEvent) {
			return {
				bgColor: '#f3e8ff', // Light purple for events
				borderColor: '#c084fc'
			};
		}
		
		switch(status) {
			case 'pending':
				return {
					bgColor: '#fef3c7', // Light yellow
					borderColor: '#f59e0b'
				};
			case 'approved_IOSA':
				return {
					bgColor: '#dbeafe', // Light blue
					borderColor: '#3b82f6'
				};
			case 'approved_mhadel':
				return {
					bgColor: '#dcfce7', // Light green
					borderColor: '#22c55e'
				};
			case 'approved_OTP':
				return {
					bgColor: '#bbf7d0', // Darker green
					borderColor: '#16a34a'
				};
			case 'completed':
				return {
					bgColor: '#f3f4f6', // Light gray
					borderColor: '#9ca3af'
				};
			case 'rejected_IOSA':
			case 'rejected_mhadel':
			case 'rejected_OTP':
				return {
					bgColor: '#fee2e2', // Light red
					borderColor: '#ef4444'
				};
			default:
				return {
					bgColor: '#ffffff', // White
					borderColor: '#d1d5db'
				};
		}
	}
	function renderSchedule() {
		const scheduleTable = document.getElementById('schedule-table');
		
		// Get filtered data
		let filteredReservations = reservations || [];
		let filteredEvents = events || [];
		
		if (currentCalendarFilter !== 'all') {
			filteredReservations = (reservations || []).filter(reservation => {
				if (currentCalendarFilter === 'pending') {
					return ['pending', 'approved_IOSA'].includes(reservation.status);
				} else if (currentCalendarFilter === 'approved') {
					return ['approved_mhadel', 'approved_OTP'].includes(reservation.status);
				} else if (currentCalendarFilter === 'completed') {
					return ['completed'].includes(reservation.status);
				} else if (currentCalendarFilter === 'rejected') {
					return ['rejected_IOSA', 'rejected_mhadel', 'rejected_OTP'].includes(reservation.status);
				}
				return true;
			});
		}
		
		// Generate time slots (8 AM to 10 PM)
		const timeSlots = [];
		for (let hour = 8; hour <= 22; hour++) {
			timeSlots.push(hour);
		}
		
		// Set consistent row height for the schedule
		const rowHeight = 60; // 60px per hour
		
		// Generate week days
		const weekDays = [];
		for (let i = 0; i < 7; i++) {
			const day = new Date(currentWeekStart);
			day.setDate(currentWeekStart.getDate() + i);
			weekDays.push(day);
		}
		
		// Create a grid to store items by day and hour
		const scheduleGrid = {};
		weekDays.forEach(day => {
			const dayKey = formatDateLocal(day);
			scheduleGrid[dayKey] = {};
			timeSlots.forEach(hour => {
				scheduleGrid[dayKey][hour] = [];
			});
		});
		
		// Populate the grid with reservations
		filteredReservations.forEach(reservation => {
			const reservationStart = new Date(reservation.start_date);
			const reservationEnd = new Date(reservation.end_date);
			const dayKey = formatDateLocal(reservationStart);
			const startHour = reservationStart.getHours();
			const endHour = reservationEnd.getHours();
			
			// Only include items that are within our time range
			if (startHour >= 8 && startHour <= 22) {
				if (scheduleGrid[dayKey] && scheduleGrid[dayKey][startHour]) {
					scheduleGrid[dayKey][startHour].push({
						type: 'reservation',
						data: reservation,
						startHour: startHour,
						endHour: endHour,
						startMinutes: reservationStart.getMinutes(),
						endMinutes: reservationEnd.getMinutes()
					});
				}
			}
		});
		
		// Populate the grid with events
		filteredEvents.forEach(event => {
			const eventStart = new Date(event.start_date);
			const eventEnd = new Date(event.end_date);
			const dayKey = formatDateLocal(eventStart);
			const startHour = eventStart.getHours();
			const endHour = eventEnd.getHours();
			
			// Only include items that are within our time range
			if (startHour >= 8 && startHour <= 22) {
				if (scheduleGrid[dayKey] && scheduleGrid[dayKey][startHour]) {
					scheduleGrid[dayKey][startHour].push({
						type: 'event',
						data: event,
						startHour: startHour,
						endHour: endHour,
						startMinutes: eventStart.getMinutes(),
						endMinutes: eventEnd.getMinutes()
					});
				}
			}
		});
		
		// Add legend for status colors
		let html = '<div class="mb-4 p-4 bg-white rounded-lg border border-gray-200">';
		html += '<h4 class="text-sm font-medium text-gray-700 mb-2">Status Colors:</h4>';
		html += '<div class="flex flex-wrap gap-3 text-xs">';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #fef3c7; border: 1px solid #f59e0b;"></div><span class="text-gray-600">Pending</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #dbeafe; border: 1px solid #3b82f6;"></div><span class="text-gray-600">IOSA Approved</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #dcfce7; border: 1px solid #22c55e;"></div><span class="text-gray-600">Approved by OTP</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #bbf7d0; border: 1px solid #16a34a;"></div><span class="text-gray-600">Approved by PPGS</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #f3f4f6; border: 1px solid #9ca3af;"></div><span class="text-gray-600">Completed</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #fee2e2; border: 1px solid #ef4444;"></div><span class="text-gray-600">Rejected</span></div>';
		html += '<div class="flex items-center"><div class="w-3 h-3 rounded mr-2" style="background-color: #f3e8ff; border: 1px solid #c084fc;"></div><span class="text-gray-600">Official Event</span></div>';
		html += '</div>';
		html += '</div>';
		
		html += '<table class="w-full border-collapse border border-gray-200 bg-white rounded-lg overflow-hidden shadow-sm">';
		
		// Header row with days
		html += '<thead class="bg-gray-50">';
		html += '<tr>';
		html += '<th class="border border-gray-200 p-3 text-left text-sm font-medium text-gray-700 w-20">Time</th>';
		weekDays.forEach(day => {
			const isToday = formatDateLocal(day) === formatDateLocal(new Date());
			const dayClass = isToday ? 'bg-maroon text-white' : 'text-gray-700';
			html += `<th class="border border-gray-200 p-3 text-center text-sm font-medium ${dayClass}">`;
			html += `<div>${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>`;
			html += `<div class="text-xs">${day.getDate()}</div>`;
			html += '</th>';
		});
		html += '</tr>';
		html += '</thead>';
		
		// Time slots rows
		html += '<tbody>';
		timeSlots.forEach(hour => {
			const timeLabel = hour === 0 ? '12:00 AM' : 
							 hour < 12 ? `${hour}:00 AM` : 
							 hour === 12 ? '12:00 PM' : 
							 `${hour - 12}:00 PM`;
			
			html += `<tr style="height: ${rowHeight}px;">`;
			html += `<td class="border border-gray-200 p-2 text-xs text-gray-600 font-medium bg-gray-50" style="height: ${rowHeight}px;">${timeLabel}</td>`;
			
			weekDays.forEach(day => {
				const dayKey = formatDateLocal(day);
				const dayItems = scheduleGrid[dayKey][hour] || [];
				
				if (dayItems.length > 0) {
					// For overlapping items, just use the standard row height
					// This ensures all items are visible in the same cell
					const cellHeight = rowHeight;
					
					html += `<td class="border border-gray-200 p-1 relative" style="height: ${cellHeight}px;">`;
					
					// Display all items in the same cell (may overlap/cut off)
					dayItems.forEach((item, index) => {
						const statusInfo = getStatusInfo(item.data.status);
						const isEvent = item.type === 'event';
						const title = isEvent ? item.data.title : item.data.event_title;
						const organizer = isEvent ? (item.data.organizer || '—') : (item.data.user?.name || '—');
						const venue = isEvent ? (item.data.venue?.name || '—') : (item.data.venue?.name || '—');
						
						// Get status-based colors
						const statusColors = getStatusColors(item.data.status, isEvent);
						
						// Calculate duration for display
						const itemDuration = item.endHour - item.startHour + 1;
						const itemHeight = itemDuration * rowHeight - 4; // Subtract padding
						
						// Display item with proper height spanning
						html += `<div class="p-1 rounded border text-xs cursor-pointer hover:shadow-md transition-shadow mb-1 last:mb-0" 
							style="background-color: ${statusColors.bgColor}; border-color: ${statusColors.borderColor}; height: ${itemHeight}px; min-height: ${itemHeight}px;"
							title="${title} - ${organizer}">
							<div class="h-full flex flex-col justify-center">
								<div class="font-semibold text-gray-800 truncate text-xs mb-1">${title}</div>
								<div class="text-xs text-gray-600">
									<div><i class="fas fa-clock mr-1"></i>${formatTimeDisplay(item.data.start_date)} - ${formatTimeDisplay(item.data.end_date)}</div>
									<div class="truncate"><i class="fas fa-user mr-1"></i>${organizer}</div>
								</div>
								<div class="mt-1 flex items-center justify-between">
									<span class="px-1 py-0.5 rounded text-xs font-medium ${statusInfo.textColor} bg-white border ${statusInfo.borderColor}">${statusInfo.label}</span>
									${!isEvent ? 
										`<a href="/iosa/reservations/${item.data.id}" class="ml-1 p-0.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors" title="View Reservation">
											<i class="fas fa-eye"></i>
										</a>` : 
										`<a href="/iosa/events/${item.data.id}" class="ml-1 p-0.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors" title="View Event">
											<i class="fas fa-eye"></i>
										</a>`
									}
								</div>
							</div>
						</div>`;
					});
					
					html += '</td>';
				} else {
					// Empty cell
					html += `<td class="border border-gray-200 p-1 relative" style="height: ${rowHeight}px;"></td>`;
				}
			});
			html += '</tr>';
		});
		html += '</tbody>';
		html += '</table>';
		scheduleTable.innerHTML = html;
	}
	
	function closeDetailsModal(){ 
		document.getElementById('calendarDetailsModal').classList.add('hidden'); 
		document.body.style.overflow='auto'; 
	}
</script>
@endsection
@endsection