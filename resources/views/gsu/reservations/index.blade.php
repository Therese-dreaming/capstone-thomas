@extends('layouts.gsu')

@section('title', 'GSU Reservations')
@section('page-title', 'Final Approved Reservations')
@section('page-subtitle', 'View and track reservations approved by OTP')

@section('header-actions')
	<a href="{{ route('drjavier.reservations.export', array_merge(request()->query(), ['status' => 'approved'])) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm mr-2 flex items-center">
		<i class="fas fa-file-excel mr-2"></i>Export to Excel
	</a>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
	.font-inter { font-family: 'Inter', sans-serif; }
	.font-poppins { font-family: 'Poppins', sans-serif; }
	.animate-fadeIn { animation: fadeIn 0.3s ease-out; }
	@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
	.reservation-card { transition: all 0.3s ease; }
	.reservation-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3), 0 10px 10px -5px rgba(0,0,0,0.2); }
	.status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
	.status-approved { background-color: #10B981; color: #1F2937; }
	.status-rejected { background-color: #EF4444; color: #1F2937; }
	.status-pending { background-color: #F59E0B; color: #1F2937; }
	.view-toggle-btn.active { background-color: white; color: #800000; font-weight: 500; }
	.view-toggle-btn:not(.active) { background-color: #f3f4f6; color: #6b7280; }
	.calendar-day { aspect-ratio: 1/1; display: flex; flex-direction: column; justify-content: center; align-items: center; font-size: 0.9rem; padding: 0.25rem; min-width: 5.5rem; max-width: 6rem; }
	.calendar-day:hover:not(.disabled) { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
</style>

<div class="space-y-6 font-inter">
	<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
		<div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
			<div class="flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-800 flex items-center">
					<i class="fas fa-calendar-check text-maroon mr-3"></i>
					Final Approved Reservations
				</h2>
				<div class="flex items-center space-x-2">
					<div class="relative">
						<input type="text" placeholder="Search reservations..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
						<div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
							<i class="fas fa-search"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="p-4 border-b border-gray-200 bg-gray-50">
			<div class="flex items-center justify-between">
				<div class="flex items-center space-x-2">
					<button onclick="showListView()" id="listViewBtn" class="view-toggle-btn active px-4 py-2 rounded-lg font-medium transition-all duration-200">
						<i class="fas fa-list mr-2"></i>List View
					</button>
					<button onclick="showCalendarView()" id="calendarViewBtn" class="view-toggle-btn px-4 py-2 rounded-lg font-medium transition-all duration-200">
						<i class="fas fa-calendar mr-2"></i>Calendar View
					</button>
				</div>
				<div class="text-sm text-gray-500">
					Showing {{ $reservations->count() }} final approved reservations
				</div>
			</div>
		</div>

		<div id="listView" class="p-6">
			@if($reservations->count() > 0)
				<div class="space-y-4">
					@foreach($reservations as $reservation)
						<div class="reservation-card bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
							<div class="flex items-center justify-between mb-4">
								<div class="flex items-center space-x-3">
									<span class="status-badge status-approved">Final Approved</span>
									<span class="text-sm text-gray-500">{{ $reservation->created_at->format('M d, Y H:i') }}</span>
								</div>
								<div class="flex items-center space-x-2">
									<a href="{{ route('gsu.reservations.show', $reservation->id) }}" class="btn-dark-blue px-3 py-2 rounded-lg text-sm font-medium transition-colors">
										<i class="fas fa-eye mr-1"></i>View Details
									</a>
								</div>
							</div>
							<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
								<div>
									<h3 class="font-semibold text-gray-800 text-lg mb-3">{{ $reservation->event_title }}</h3>
									<div class="space-y-2 text-sm text-gray-600">
										<div class="flex items-center"><i class="fas fa-user mr-2 text-maroon w-4"></i><span>{{ $reservation->user->name }}</span></div>
										<div class="flex items-center"><i class="fas fa-calendar mr-2 text-maroon w-4"></i><span>{{ $reservation->start_date->format('M d, Y') }}</span></div>
										<div class="flex items-center"><i class="fas fa-clock mr-2 text-maroon w-4"></i><span>{{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}</span></div>
									</div>
								</div>
								<div>
									<h4 class="font-medium text-gray-800 mb-3">Venue & Capacity</h4>
									<div class="space-y-2 text-sm text-gray-600">
										<div class="flex items-center"><i class="fas fa-map-marker-alt mr-2 text-maroon w-4"></i><span>{{ $reservation->venue->name }}</span></div>
										<div class="flex items-center"><i class="fas fa-users mr-2 text-maroon w-4"></i><span>{{ $reservation->capacity ?? 'N/A' }} participants</span></div>
									</div>
								</div>
								<div>
									<h4 class="font-medium text-gray-800 mb-3">Pricing</h4>
									<div class="space-y-2 text-sm">
										<div class="flex items-center justify-between"><span class="text-gray-600">Final Price:</span><span class="font-medium text-green-800 text-lg">₱{{ number_format($reservation->final_price ?? 0, 2) }}</span></div>
										<div class="flex items-center justify-between"><span class="text-gray-600">Rate/Hour:</span><span class="font-medium text-gray-800">₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</span></div>
									</div>
								</div>
								<div>
									<h4 class="font-medium text-gray-800 mb-3">Equipment</h4>
									@if($reservation->equipment_details && count($reservation->equipment_details) > 0)
										<div class="space-y-1 text-sm">
											@foreach($reservation->equipment_details as $eq)
												<div class="text-xs bg-gray-100 px-2 py-1 rounded"><span class="font-medium">{{ $eq['name'] }}</span> <span class="text-gray-500">({{ $eq['quantity'] }})</span></div>
											@endforeach
										</div>
									@else
										<div class="text-gray-500 text-xs">No equipment requested</div>
									@endif
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="mt-6">
					{{ $reservations->links() }}
				</div>
			@else
				<div class="text-center py-12">
					<i class="fas fa-calendar-check text-6xl text-gray-300 mb-6"></i>
					<h3 class="text-2xl font-bold text-gray-700 mb-4">No Final Approved Reservations</h3>
					<p class="text-gray-500 mb-6">Approved reservations will appear here.</p>
				</div>
			@endif
		</div>
		<div id="calendarView" class="p-6 hidden">
			<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
				<div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
					<div class="flex items-center justify-between">
						<h2 class="text-2xl font-bold text-gray-800 flex items-center">
							<i class="fas fa-calendar-alt text-maroon mr-3"></i>
							Reservation Calendar
						</h2>
						<div class="flex items-center space-x-2 bg-white rounded-lg shadow-md p-1.5">
							<button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors"><i class="fas fa-chevron-left"></i></button>
							<span id="currentMonth" class="font-medium text-gray-700 px-3"></span>
							<button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors"><i class="fas fa-chevron-right"></i></button>
						</div>
					</div>
				</div>
				<div class="p-6">
					<div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
						<div class="flex items-center"><div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div><span class="text-gray-600">Final Approved</span></div>
						<div class="flex items-center"><div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div><span class="text-gray-600">Today</span></div>
					</div>
					<div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Reservation Details Modal -->
<div id="reservationDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
	<div class="flex items-center justify-center min-h-screen p-4">
		<div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins animate-fadeIn">
			<div class="p-6 border-b border-gray-200">
				<div class="flex items-center justify-between">
					<h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
						<i class="fas fa-calendar-check text-maroon mr-2"></i>
						Reservation Details
					</h3>
					<button onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
			<div class="p-6" id="reservationModalContent"></div>
			<div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
				<button onclick="closeReservationModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
				<a id="viewFullDetailsLink" href="#" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-colors">View Full Details</a>
			</div>
		</div>
	</div>
</div>

<script>
function showListView(){document.getElementById('listView').classList.remove('hidden');document.getElementById('calendarView').classList.add('hidden');document.getElementById('listViewBtn').classList.add('active');document.getElementById('calendarViewBtn').classList.remove('active');}
function showCalendarView(){document.getElementById('listView').classList.add('hidden');document.getElementById('calendarView').classList.remove('hidden');document.getElementById('calendarViewBtn').classList.add('active');document.getElementById('listViewBtn').classList.remove('active');renderCalendar();}
let currentDate=new Date();let currentMonth=currentDate.getMonth();let currentYear=currentDate.getFullYear();
const reservationsData=@json($reservations);

function formatDateLocal(d){const y=d.getFullYear();const m=String(d.getMonth()+1).padStart(2,'0');const day=String(d.getDate()).padStart(2,'0');return `${y}-${m}-${day}`;}

function renderCalendar(){
	const calendar=document.getElementById('calendar');
	const firstDay=new Date(currentYear,currentMonth,1);
	const startDate=new Date(firstDay);
	startDate.setDate(startDate.getDate()-firstDay.getDay());
	const monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
	document.getElementById('currentMonth').textContent=`${monthNames[currentMonth]} ${currentYear}`;
	let html='';
	const dayNames=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
	dayNames.forEach(d=>{html+=`<div class="text-center py-2 text-sm font-medium text-gray-500 bg-gray-50">${d}</div>`});
	for(let i=0;i<42;i++){
		const date=new Date(startDate);
		date.setDate(startDate.getDate()+i);
		const isCurrentMonth=date.getMonth()===currentMonth;
		const isToday=date.toDateString()===new Date().toDateString();
		let dayClass='calendar-day text-center py-3 relative rounded-lg transition-all duration-200 cursor-pointer';
		if(!isCurrentMonth){dayClass+=' text-gray-400 bg-gray-50';}
		else if(isToday){dayClass+=' bg-maroon text-white font-bold';}
		else{dayClass+=' bg-white hover:bg-gray-50';}
		const dateStringLocal=formatDateLocal(date);
		const dayReservations=reservationsData.data.filter(r=>{
			const rLocal=formatDateLocal(new Date(r.start_date));
			return rLocal===dateStringLocal;
		});
		let reservationIndicator='';
		if(dayReservations.length>0){
			reservationIndicator=`<div class="absolute w-3 h-3 bg-green-600 rounded-full" style="top:4px;right:4px;" title="${dayReservations.length} reservation(s) on this date"></div>`;
		}
		html+=`<div class="${dayClass}" onclick="showReservationsForDate('${dateStringLocal}')"><div class="text-sm font-medium">${date.getDate()}</div>${reservationIndicator}</div>`;
	}
	calendar.innerHTML=html;
}
function previousMonth(){currentMonth--;if(currentMonth<0){currentMonth=11;currentYear--;}renderCalendar();}
function nextMonth(){currentMonth++;if(currentMonth>11){currentMonth=0;currentYear++;}renderCalendar();}
function showReservationsForDate(dateOrString){
	const dateString=typeof dateOrString==='string'?dateOrString:formatDateLocal(dateOrString);
	const dayReservations=reservationsData.data.filter(r=>{
		const rLocal=formatDateLocal(new Date(r.start_date));
		return rLocal===dateString;
	});
	if(dayReservations.length===0)return;
	const r=dayReservations[0];
	const start=new Date(r.start_date);
	const end=new Date(r.end_date);
	const equipmentHtml=(r.equipment_details&&r.equipment_details.length)?r.equipment_details.map(e=>`<div class="text-xs bg-gray-100 px-2 py-1 rounded"><span class="font-medium">${e.name}</span> <span class="text-gray-500">(${e.quantity})</span></div>`).join(''):'<div class="text-gray-500 text-xs">No equipment requested</div>';
	const pricingRows=`<div class="flex items-center justify-between"><span class="text-gray-600">Final Price:</span><span class="font-medium text-green-800 text-lg">₱${Number(r.final_price||0).toFixed(2)}</span></div><div class="flex items-center justify-between"><span class="text-gray-600">Rate/Hour:</span><span class="font-medium text-gray-800">₱${Number(r.price_per_hour||0).toFixed(2)}</span></div><div class="flex items-center justify-between"><span class="text-gray-600">Duration:</span><span class="font-medium text-blue-600">${Number(r.duration_hours||0)} hours</span></div>`;
	const content=`<div class="space-y-4"><div class="bg-gray-50 p-4 rounded-lg"><h4 class="font-semibold text-gray-800 text-lg mb-2">${r.event_title}</h4><div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm"><div><p class="text-gray-600"><strong>Requester:</strong> ${r.user?.name||''}</p><p class="text-gray-600"><strong>Date:</strong> ${start.toLocaleDateString()}</p><p class="text-gray-600"><strong>Time:</strong> ${start.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} - ${end.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</p></div><div><p class="text-gray-600"><strong>Venue:</strong> ${r.venue?.name||''}</p><p class="text-gray-600"><strong>Capacity:</strong> ${r.capacity||''}</p><p class="text-gray-600"><strong>Purpose:</strong> ${r.purpose||''}</p></div></div><div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4"><div><h5 class="font-medium text-gray-800 mb-2">Pricing</h5><div class="space-y-2 text-sm">${pricingRows}</div></div><div><h5 class="font-medium text-gray-800 mb-2">Equipment</h5><div class="space-y-1">${equipmentHtml}</div></div></div></div></div>`;
	document.getElementById('reservationModalContent').innerHTML=content;
	const link=document.getElementById('viewFullDetailsLink');
	link.href=`/gsu/reservations/${r.id}`;
	document.getElementById('reservationDetailsModal').classList.remove('hidden');
	document.body.style.overflow='hidden';
}
function closeReservationModal(){document.getElementById('reservationDetailsModal').classList.add('hidden');document.body.style.overflow='auto';}
document.getElementById('reservationDetailsModal').addEventListener('click',function(e){if(e.target===this)closeReservationModal();});
</script>
@endsection 