@extends('layouts.gsu')

@section('title', 'Calendar')
@section('page-title', 'Calendar')
@section('page-subtitle', 'View events and reservations by month')

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
					Calendar of Events & Reservations
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
				<div class="flex items-center space-x-4">
					<h3 class="text-lg font-medium text-gray-800">Filter by Status:</h3>
					<div class="flex items-center space-x-2">
						<button onclick="filterCalendarByStatus('all')" id="filter-all" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn active">
							All
						</button>
						<button onclick="filterCalendarByStatus('upcoming')" id="filter-upcoming" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							Upcoming
						</button>
						<button onclick="filterCalendarByStatus('ongoing')" id="filter-ongoing" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							Ongoing
						</button>
						<button onclick="filterCalendarByStatus('completed')" id="filter-completed" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
							Completed
						</button>
					</div>
				</div>
				<div class="flex items-center space-x-2">
					<span class="text-sm font-medium text-gray-700">View:</span>
					<button onclick="switchView('calendar')" id="view-calendar" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn active">
						<i class="fas fa-calendar mr-1"></i>Calendar
					</button>
					<button onclick="switchView('schedule')" id="view-schedule" class="px-3 py-2 text-sm font-medium rounded-lg border transition-colors calendar-filter-btn">
						<i class="fas fa-list mr-1"></i>Schedule
					</button>
				</div>
			</div>
		</div>
		
		<div class="p-6">
			<!-- Calendar View -->
			<div id="calendar-view">
				<div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
					<div class="flex items-center"><div class="w-4 h-4 bg-blue-400 rounded-md mr-2"></div><span class="text-gray-600">Events</span></div>
					<div class="flex items-center"><div class="w-4 h-4 bg-green-400 rounded-md mr-2"></div><span class="text-gray-600">Reservations</span></div>
					<div class="flex items-center"><div class="w-4 h-4 bg-gray-400 rounded-md mr-2"></div><span class="text-gray-600">Completed</span></div>
					<div class="flex items-center"><div class="w-4 h-4 bg-red-400 rounded-md mr-2"></div><span class="text-gray-600">Cancelled</span></div>
					<div class="flex items-center"><div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div><span class="text-gray-600">Today</span></div>
				</div>
				<div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto"></div>
			</div>
			
			<!-- Schedule View -->
			<div id="schedule-view" class="hidden">
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center space-x-2">
						<button onclick="previousWeek()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
							<i class="fas fa-chevron-left"></i>
						</button>
						<span id="scheduleWeek" class="font-medium text-gray-700 px-3"></span>
						<button onclick="nextWeek()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
							<i class="fas fa-chevron-right"></i>
						</button>
					</div>
					<button onclick="goToCurrentWeek()" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-colors text-sm">
						<i class="fas fa-calendar-day mr-2"></i>This Week
					</button>
				</div>
				<div id="schedule-content" class="space-y-4"></div>
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
						<span id="modalDateTitle">Events & Reservations</span>
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



@endsection

@push('scripts')
<script>
	const events = @json($events ?? []);
	const reservations = @json($reservations ?? []);
	let currentDate = new Date();
	let currentMonth = currentDate.getMonth();
	let currentYear = currentDate.getFullYear();
	let currentCalendarFilter = 'all';
	let currentView = 'calendar';
	let currentWeekStart = new Date();
	currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay()); // Start of current week (Sunday)
	
	function formatDateLocal(d){const y=d.getFullYear();const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return `${y}-${m}-${day}`;}
	function timeLabel(s){const d=new Date(s);return d.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});}
	
	// Helper function to format time for display
	function formatTimeDisplay(date) {
		return date.toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});
	} 
	
	function previousMonth(){ currentDate.setMonth(currentDate.getMonth()-1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function nextMonth(){ currentDate.setMonth(currentDate.getMonth()+1); currentMonth=currentDate.getMonth(); currentYear=currentDate.getFullYear(); renderCalendar(); updateHeader(); }
	function updateHeader(){ const names=['January','February','March','April','May','June','July','August','September','October','November','December']; document.getElementById('currentMonth').textContent=`${names[currentMonth]} ${currentYear}`; }
	
	// View switching function
	function switchView(view) {
		currentView = view;
		
		// Update button styles
		document.getElementById('view-calendar').classList.remove('active');
		document.getElementById('view-schedule').classList.remove('active');
		document.getElementById(`view-${view}`).classList.add('active');
		
		// Show/hide views
		if (view === 'calendar') {
			document.getElementById('calendar-view').classList.remove('hidden');
			document.getElementById('schedule-view').classList.add('hidden');
			renderCalendar();
		} else {
			document.getElementById('calendar-view').classList.add('hidden');
			document.getElementById('schedule-view').classList.remove('hidden');
			// Ensure schedule view is properly initialized
			if (!currentWeekStart) {
				currentWeekStart = new Date();
				currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay());
			}
			renderSchedule();
		}
	}
	
	// Schedule navigation functions
	function previousWeek() {
		currentWeekStart.setDate(currentWeekStart.getDate() - 7);
		renderSchedule();
	}
	
	function nextWeek() {
		currentWeekStart.setDate(currentWeekStart.getDate() + 7);
		renderSchedule();
	}
	
	function goToCurrentWeek() {
		currentWeekStart = new Date();
		currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay());
		renderSchedule();
	}
	
	// Render schedule view
	function renderSchedule() {
		const scheduleContent = document.getElementById('schedule-content');
		const weekEnd = new Date(currentWeekStart);
		weekEnd.setDate(currentWeekStart.getDate() + 6);
		
		// Update week display
		const weekStartStr = currentWeekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
		const weekEndStr = weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
		document.getElementById('scheduleWeek').textContent = `${weekStartStr} - ${weekEndStr}`;
		
		// Get filtered data
		let filteredEvents = events || [];
		let filteredReservations = reservations || [];
		
		if (currentCalendarFilter !== 'all') {
			filteredEvents = (events || []).filter(event => {
				return event.status === currentCalendarFilter;
			});
			filteredReservations = (reservations || []).filter(reservation => {
				return reservation.status === currentCalendarFilter;
			});
		}
		
		// Filter events and reservations for the week
		const weekEvents = filteredEvents.filter(event => {
			const eventStart = new Date(event.start_date);
			const eventEnd = new Date(event.end_date);
			return eventStart <= weekEnd && eventEnd >= currentWeekStart;
		});
		
		const weekReservations = filteredReservations.filter(reservation => {
			const reservationStart = new Date(reservation.start_date);
			const reservationEnd = new Date(reservation.end_date);
			return reservationStart <= weekEnd && reservationEnd >= currentWeekStart;
		});
		

		
		// Combine and sort all items
		const allItems = [];
		
		weekEvents.forEach(event => {
			allItems.push({
				type: 'event',
				data: event,
				date: new Date(event.start_date),
				startTime: new Date(event.start_date),
				endTime: new Date(event.end_date)
			});
		});
		
		weekReservations.forEach(reservation => {
			allItems.push({
				type: 'reservation',
				data: reservation,
				date: new Date(reservation.start_date),
				startTime: new Date(reservation.start_date),
				endTime: new Date(reservation.end_date)
			});
		});
		
		// Sort by date and time
		allItems.sort((a, b) => a.startTime - b.startTime);
		
		// Create time slots (hourly from 6 AM to 10 PM)
		const timeSlots = [];
		for (let hour = 6; hour <= 22; hour++) {
			timeSlots.push(hour);
		}
		
		// Create days array
		const days = [];
		for (let i = 0; i < 7; i++) {
			const dayDate = new Date(currentWeekStart);
			dayDate.setDate(currentWeekStart.getDate() + i);
			days.push({
				date: dayDate,
				key: formatDateLocal(dayDate),
				name: dayDate.toLocaleDateString('en-US', { weekday: 'short' }),
				display: dayDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
				isToday: formatDateLocal(dayDate) === formatDateLocal(new Date())
			});
		}
		
		// Create a grid to store items by day and hour
		const scheduleGrid = {};
		days.forEach(day => {
			scheduleGrid[day.key] = {};
			timeSlots.forEach(hour => {
				scheduleGrid[day.key][hour] = [];
			});
		});
		
		// Create a map to track which items are already placed to avoid duplicates
		const placedItems = new Set();
		
		// Populate the grid with items
		allItems.forEach(item => {
			const dayKey = formatDateLocal(item.date);
			const startHour = item.startTime.getHours();
			const endHour = item.endTime.getHours();
			
			// Calculate the duration in hours
			const durationHours = Math.ceil((item.endTime - item.startTime) / (1000 * 60 * 60));
			
			// Create a unique identifier for this item
			const itemId = `${dayKey}-${item.type}-${item.data.id || item.data.title}`;
			
			// Only place the item once, in its start hour
			if (!placedItems.has(itemId) && startHour >= 6 && startHour <= 22) {
				if (scheduleGrid[dayKey] && scheduleGrid[dayKey][startHour]) {
					// Add duration information to the item
					const itemWithDuration = {
						...item,
						durationHours: durationHours,
						startHour: startHour,
						endHour: endHour,
						itemId: itemId
					};
					scheduleGrid[dayKey][startHour].push(itemWithDuration);
					placedItems.add(itemId);
				}
			}
		});
		
		// Render schedule table
		let html = `
			<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
				<div class="overflow-x-auto">
					<table class="w-full border-collapse">
						<thead>
							<tr class="bg-gray-50 border-b border-gray-200">
								<th class="w-20 p-3 text-left text-sm font-medium text-gray-700 border-r border-gray-200">Time</th>
		`;
		
		// Add day headers
		days.forEach(day => {
			html += `
				<th class="p-3 text-center text-sm font-medium text-gray-700 border-r border-gray-200 ${day.isToday ? 'bg-maroon text-white' : ''}">
					<div class="font-semibold">${day.name}</div>
					<div class="text-xs ${day.isToday ? 'text-white' : 'text-gray-500'}">${day.display}</div>
					${day.isToday ? '<div class="text-xs mt-1">TODAY</div>' : ''}
				</th>
			`;
		});
		
		html += `
							</tr>
						</thead>
						<tbody>
		`;
		
		// Add time slots
		timeSlots.forEach(hour => {
			const timeLabel = hour === 0 ? '12:00 AM' : 
							 hour < 12 ? `${hour}:00 AM` : 
							 hour === 12 ? '12:00 PM' : 
							 `${hour - 12}:00 PM`;
			html += `
				<tr class="border-b border-gray-100 hover:bg-gray-50">
					<td class="w-20 p-2 text-xs font-medium text-gray-600 border-r border-gray-200 bg-gray-50">
						${timeLabel}
					</td>
			`;
			
			// Add cells for each day
			days.forEach(day => {
				const dayItems = scheduleGrid[day.key][hour] || [];
				
				if (dayItems.length > 0) {
					// Show items for this time slot
					dayItems.forEach(item => {
						const statusInfo = getStatusInfo(item.data.status);
						const isEvent = item.type === 'event';
						const title = isEvent ? item.data.title : item.data.event_title;
						const organizer = isEvent ? (item.data.organizer || '—') : (item.data.user?.name || '—');
						const venue = isEvent ? (item.data.venue?.name || '—') : (item.data.venue?.name || '—');
						
						// Calculate rowspan for multi-hour events
						// For an event from 2:00 PM to 5:00 PM, it should span 4 slots (2PM, 3PM, 4PM, 5PM)
						const startHour = item.startHour;
						const endHour = item.endHour;
						const rowspan = Math.max(1, endHour - startHour + 1);
						
						html += `
							<td class="p-2 border-r border-gray-200 align-middle" rowspan="${rowspan}">
								<div class="h-full p-2 rounded-lg border ${isEvent ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200'} text-xs flex flex-col justify-center">
									<div class="flex items-center justify-between mb-1">
										<span class="font-semibold text-gray-800 truncate">${title}</span>
										<span class="px-1 py-0.5 rounded text-xs font-medium ${statusInfo.textColor} bg-white border ${statusInfo.borderColor}">${statusInfo.label}</span>
									</div>
									<div class="text-gray-600 space-y-0.5">
										<div><i class="fas fa-clock mr-1"></i>${formatTimeDisplay(item.startTime)} - ${formatTimeDisplay(item.endTime)}</div>
										<div><i class="fas fa-user mr-1"></i>${organizer}</div>
										<div><i class="fas fa-map-marker-alt mr-1"></i>${venue}</div>
										${item.data.capacity ? `<div><i class="fas fa-users mr-1"></i>${item.data.capacity}</div>` : ''}
										${item.data.final_price ? `<div><i class="fas fa-tag mr-1"></i>₱${parseFloat(item.data.final_price).toLocaleString('en-US',{minimumFractionDigits:0})}</div>` : ''}
									</div>
									${item.data.description || item.data.purpose ? `<div class="mt-1 text-gray-600 text-xs"><strong>${isEvent ? 'Desc' : 'Purpose'}:</strong> ${(item.data.description || item.data.purpose).substring(0, 50)}${(item.data.description || item.data.purpose).length > 50 ? '...' : ''}</div>` : ''}
								</div>
							</td>
						`;
					});
				} else {
					// Empty cell
					html += `<td class="p-2 border-r border-gray-200 align-top bg-gray-25"></td>`;
				}
			});
			
			html += `</tr>`;
		});
		
		html += `
						</tbody>
					</table>
				</div>
			</div>
		`;
		
		// Add summary section
		if (allItems.length > 0) {
			html += `
				<div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
					<h3 class="text-lg font-semibold text-gray-800 mb-3">Week Summary</h3>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
						<div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-200">
							<div class="text-2xl font-bold text-blue-600">${weekEvents.length}</div>
							<div class="text-blue-800">Events</div>
						</div>
						<div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
							<div class="text-2xl font-bold text-green-600">${weekReservations.length}</div>
							<div class="text-green-800">Reservations</div>
						</div>
						<div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-200">
							<div class="text-2xl font-bold text-purple-600">${allItems.length}</div>
							<div class="text-purple-800">Total</div>
						</div>
						<div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-200">
							<div class="text-2xl font-bold text-gray-600">${days.filter(d => d.isToday).length > 0 ? 'Today' : 'This Week'}</div>
							<div class="text-gray-800">Active</div>
						</div>
					</div>
				</div>
			`;
		}
		
		scheduleContent.innerHTML = html;
	}
	
	// Calendar filter function
	function filterCalendarByStatus(status) {
		currentCalendarFilter = status;
		
		// Update button styles
		document.querySelectorAll('.calendar-filter-btn').forEach(btn => {
			btn.classList.remove('active');
		});
		document.getElementById(`filter-${status}`).classList.add('active');
		
		// Re-render current view with new filter
		if (currentView === 'calendar') {
			renderCalendar();
		} else {
			renderSchedule();
		}
	}
	
	// Function to get status label and styling
	function getStatusInfo(status) {
		switch(status) {
			case 'upcoming':
				return {
					label: 'Upcoming',
					bgColor: 'bg-blue-100',
					textColor: 'text-blue-800',
					borderColor: 'border-blue-300'
				};
			case 'ongoing':
				return {
					label: 'Ongoing',
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
			case 'cancelled':
				return {
					label: 'Cancelled',
					bgColor: 'bg-red-100',
					textColor: 'text-red-800',
					borderColor: 'border-red-300'
				};
			case 'approved_OTP':
				return {
					label: 'Approved',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
				};
			case 'pending_venue':
				return {
					label: 'Pending Venue',
					bgColor: 'bg-yellow-100',
					textColor: 'text-yellow-800',
					borderColor: 'border-yellow-300'
				};
			case 'confirmed':
				return {
					label: 'Confirmed',
					bgColor: 'bg-green-100',
					textColor: 'text-green-800',
					borderColor: 'border-green-300'
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
	
	document.addEventListener('DOMContentLoaded', ()=>{ 
		renderCalendar(); 
		updateHeader(); 
		// Initialize schedule view with proper week start
		currentWeekStart = new Date();
		currentWeekStart.setDate(currentDate.getDate() - currentDate.getDay()); // Start of current week (Sunday)
		renderSchedule(); // Initialize schedule view as well
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
			
			// Get filtered events and reservations based on current filter
			let filteredEvents = events || [];
			let filteredReservations = reservations || [];
			
			if (currentCalendarFilter !== 'all') {
				filteredEvents = (events || []).filter(event => {
					return event.status === currentCalendarFilter;
				});
				filteredReservations = (reservations || []).filter(reservation => {
					return reservation.status === currentCalendarFilter;
				});
			}
			
			const dayEvents=filteredEvents.filter(e=> formatDateLocal(new Date(e.start_date))===dateStr );
			const dayReservations=filteredReservations.filter(e=> formatDateLocal(new Date(e.start_date))===dateStr );
			
			let marks='';
			if(dayEvents.length){
				// Determine the status color based on the event status
				let statusColor = 'bg-blue-400'; // Default for events
				const event = dayEvents[0]; // Use first event for color
				
				if (event.status === 'upcoming') {
					statusColor = 'bg-blue-400'; // Blue for upcoming
				} else if (event.status === 'ongoing') {
					statusColor = 'bg-green-400'; // Green for ongoing
				} else if (event.status === 'completed') {
					statusColor = 'bg-gray-400'; // Gray for completed
				} else if (event.status === 'cancelled') {
					statusColor = 'bg-red-400'; // Red for cancelled
				}
				
				marks+=`<div class="absolute w-3 h-3 ${statusColor} rounded-full" style="top:4px;left:4px" title="${dayEvents.length} event(s)"></div>`;
			}
			if(dayReservations.length){
				// Determine the status color based on the reservation status
				let statusColor = 'bg-green-400'; // Default for reservations
				const reservation = dayReservations[0]; // Use first reservation for color
				
				if (reservation.status === 'approved_OTP') {
					statusColor = 'bg-green-400'; // Green for approved
				} else if (reservation.status === 'completed') {
					statusColor = 'bg-gray-400'; // Gray for completed
				} else if (reservation.status === 'cancelled') {
					statusColor = 'bg-red-400'; // Red for cancelled
				}
				
				marks+=`<div class="absolute w-3 h-3 ${statusColor} rounded-full" style="top:4px;right:4px" title="${dayReservations.length} reservation(s)"></div>`;
			}
			html+=`<div class="${dayClass} cursor-pointer" onclick="openDetails('${dateStr}')"><div class="text-sm font-medium">${date.getDate()}</div>${marks}</div>`;
		}
		calendar.innerHTML=html;
	}
	
	function openDetails(dateStr){
		const start=new Date(`${dateStr}T00:00`), end=new Date(`${dateStr}T23:59:59`);
		
		// Get filtered events and reservations based on current filter
		let filteredEvents = events || [];
		let filteredReservations = reservations || [];
		
		if (currentCalendarFilter !== 'all') {
			filteredEvents = (events || []).filter(event => {
				return event.status === currentCalendarFilter;
			});
			filteredReservations = (reservations || []).filter(reservation => {
				return reservation.status === currentCalendarFilter;
			});
		}
		
		const dayEvents=filteredEvents.filter(e=> new Date(e.start_date)<end && new Date(e.end_date)>start );
		const dayReservations=filteredReservations.filter(e=> new Date(e.start_date)<end && new Date(e.end_date)>start );
		if(!dayReservations.length && !dayEvents.length){
			document.getElementById('modalDateTitle').textContent=`Entries for ${new Date(`${dateStr}T00:00`).toLocaleDateString()}`;
			const wrap=document.getElementById('calendarDetailsContent');
			wrap.innerHTML=`<div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
				<i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
				<p class="text-gray-600">No events or reservations for this date</p>
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
			const statusInfo = getStatusInfo(e.status);
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
						${e.description ? `<div class=\"mt-2 text-sm text-gray-600\"><strong>Description:</strong> ${e.description}</div>` : ''}
					</div>
					<span class="ml-4 px-3 py-1 rounded-full text-xs font-medium ${statusInfo.textColor} bg-white border ${statusInfo.borderColor}">${statusInfo.label}</span>
				</div>
			</div>`);
		});

		// Reservations
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