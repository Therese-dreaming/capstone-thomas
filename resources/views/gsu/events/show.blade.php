@extends('layouts.gsu')

@section('title', $event->title)
@section('page-title', 'Event Details')
@section('page-subtitle', 'View complete event information')

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
	.font-inter { font-family: 'Inter', sans-serif; }
	.font-poppins { font-family: 'Poppins', sans-serif; }
	
	.status-badge {
		padding: 0.75rem 1.5rem;
		border-radius: 9999px;
		font-size: 0.875rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		display: inline-block;
		white-space: nowrap;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}
	
	.info-card {
		background: white;
		border-radius: 1rem;
		box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
		border: 1px solid #f3f4f6;
		transition: all 0.3s ease;
		overflow: hidden;
	}
	
	.info-card:hover {
		box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
		transform: translateY(-2px);
	}
	
	.glass-effect {
		background: rgba(255, 255, 255, 0.95);
		backdrop-filter: blur(10px);
		border: 1px solid rgba(255, 255, 255, 0.2);
	}
	
	.highlight-box {
		background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
		border: 1px solid #e2e8f0;
		border-radius: 0.75rem;
		padding: 1.5rem;
	}
</style>

<div class="space-y-8 font-inter">
	<!-- Header Section -->
	<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
				<div class="flex-1">
					<div class="flex items-center space-x-4 mb-4">
						<div class="w-16 h-16 rounded-xl bg-gradient-to-br from-maroon to-red-800 flex items-center justify-center text-white shadow-lg">
							<i class="fas fa-calendar-alt text-2xl"></i>
						</div>
						<div>
							<h1 class="text-3xl font-bold text-gray-800 font-poppins">{{ $event->title }}</h1>
							<div class="text-sm text-gray-500 font-mono mb-2">
								Event ID: {{ $event->event_id ?? 'N/A' }}
							</div>
							@switch($event->status)
								@case('pending_venue')
									<span class="status-badge bg-yellow-100 text-yellow-800 border border-yellow-200">Pending Venue</span>
									@break
								@case('upcoming')
									<span class="status-badge bg-blue-100 text-blue-800 border border-blue-200">Upcoming</span>
									@break
								@case('ongoing')
									<span class="status-badge bg-green-100 text-green-800 border border-green-200">Ongoing</span>
									@break
								@case('completed')
									<span class="status-badge bg-gray-100 text-gray-800 border border-gray-200">Completed</span>
									@break
								@case('cancelled')
									<span class="status-badge bg-red-100 text-red-800 border border-red-200">Cancelled</span>
									@break
							@endswitch
						</div>
					</div>
					@if($event->description)
						<p class="text-gray-600 text-lg">{{ $event->description }}</p>
					@endif
				</div>
				<div class="flex flex-col space-y-3">
					<a href="{{ route('gsu.events.index') }}" 
					   class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
						<i class="fas fa-arrow-left mr-2"></i>
						<span>Back to Events</span>
					</a>
					
					@if($event->status !== 'cancelled' && $event->status !== 'completed')
					<button onclick="openCompleteModal({{ $event->id }}, '{{ $event->title }}', 'event')" 
							class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
						<i class="fas fa-check-circle mr-2"></i>
						<span>Mark as Complete</span>
					</button>
					@elseif($event->status === 'completed')
					<div class="w-full bg-green-100 text-green-800 px-6 py-3 rounded-xl flex items-center justify-center font-medium">
						<i class="fas fa-check-circle mr-2"></i>
						<span>Event Completed</span>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	<!-- Main Content -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
		<!-- Main Content Area -->
		<div class="lg:col-span-2 space-y-6">
			<!-- Event Information -->
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-info-circle text-maroon mr-3"></i>
						Event Information
					</h2>
				</div>
				<div class="p-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div class="space-y-4">
							<div class="flex items-center text-gray-700">
								<i class="fas fa-calendar mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Date</div>
									<div class="text-sm text-gray-600">{{ $event->start_date->format('l, F d, Y') }}</div>
								</div>
							</div>
							
							<div class="flex items-center text-gray-700">
								<i class="fas fa-clock mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Time</div>
									<div class="text-sm text-gray-600">{{ $event->start_date->format('g:i A') }} – {{ $event->end_date->format('g:i A') }}</div>
								</div>
							</div>
							
							<div class="flex items-center text-gray-700">
								<i class="fas fa-hourglass-half mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Duration</div>
									<div class="text-sm text-gray-600">{{ $event->start_date->diffInHours($event->end_date) }} hours</div>
								</div>
							</div>
						</div>
						
						<div class="space-y-4">
							@if($event->organizer)
							<div class="flex items-center text-gray-700">
								<i class="fas fa-user mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Organizer</div>
									<div class="text-sm text-gray-600">{{ $event->organizer }}</div>
								</div>
							</div>
							@endif
							
							@if($event->department)
							<div class="flex items-center text-gray-700">
								<i class="fas fa-building mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Department</div>
									<div class="text-sm text-gray-600">{{ $event->department }}</div>
								</div>
							</div>
							@endif
							
							@if($event->max_participants)
							<div class="flex items-center text-gray-700">
								<i class="fas fa-users mr-3 text-maroon w-5"></i>
								<div>
									<div class="font-medium">Max Participants</div>
									<div class="text-sm text-gray-600">{{ $event->max_participants }} people</div>
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>

			<!-- Venue Information -->
			@if($event->venue)
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-map-marker-alt text-maroon mr-3"></i>
						Venue Information
					</h2>
				</div>
				<div class="p-6">
					<div class="space-y-4">
						<div class="flex items-center text-gray-700">
							<i class="fas fa-map-marker-alt mr-3 text-maroon w-5"></i>
							<div>
								<div class="font-medium">Venue Name</div>
								<div class="text-sm text-gray-600">{{ $event->venue->name }}</div>
							</div>
						</div>
						
						@if($event->venue->description)
						<div class="flex items-start text-gray-700">
							<i class="fas fa-info-circle mr-3 text-maroon w-5 mt-1"></i>
							<div>
								<div class="font-medium">Description</div>
								<div class="text-sm text-gray-600">{{ $event->venue->description }}</div>
							</div>
						</div>
						@endif
						
						@if($event->venue->capacity)
						<div class="flex items-center text-gray-700">
							<i class="fas fa-users mr-3 text-maroon w-5"></i>
							<div>
								<div class="font-medium">Capacity</div>
								<div class="text-sm text-gray-600">{{ $event->venue->capacity }} people</div>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
			@endif

			<!-- Equipment Information -->
			@if($event->equipment_details && count($event->equipment_details) > 0)
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-tools text-maroon mr-3"></i>
						Equipment Requirements
					</h2>
				</div>
				<div class="p-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						@foreach($event->equipment_details as $equipment)
							<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
								<div class="flex items-center">
									<i class="fas fa-wrench text-maroon mr-3"></i>
									<span class="font-medium text-gray-800">{{ $equipment['name'] ?? 'Unknown Equipment' }}</span>
								</div>
								<span class="text-sm text-gray-600 bg-white px-2 py-1 rounded-full border">
									Qty: {{ $equipment['quantity'] ?? 1 }}
								</span>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			@endif

			<!-- Custom Equipment Requests -->
			@if($event->custom_equipment_requests && count($event->custom_equipment_requests) > 0)
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-plus-circle text-maroon mr-3"></i>
						Custom Equipment Requests
					</h2>
				</div>
				<div class="p-6">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						@foreach($event->custom_equipment_requests as $customEquipment)
							<div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
								<div class="flex items-center">
									<i class="fas fa-plus text-orange-600 mr-3"></i>
									<span class="font-medium text-gray-800">{{ $customEquipment['name'] ?? 'Unknown Equipment' }}</span>
								</div>
								<span class="text-sm text-orange-700 bg-white px-2 py-1 rounded-full border border-orange-300">
									Qty: {{ $customEquipment['quantity'] ?? 1 }}
								</span>
							</div>
						@endforeach
					</div>
					<div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
						<div class="flex items-start">
							<i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
							<div class="text-sm text-blue-700">
								<strong>Note:</strong> Custom equipment requests need to be arranged separately and may require additional coordination.
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			@if((!$event->equipment_details || count($event->equipment_details) === 0) && (!$event->custom_equipment_requests || count($event->custom_equipment_requests) === 0))
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-tools text-maroon mr-3"></i>
						Equipment Requirements
					</h2>
				</div>
				<div class="p-6">
					<div class="text-center py-8">
						<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
							<i class="fas fa-tools text-gray-400 text-2xl"></i>
						</div>
						<p class="text-gray-600 font-medium">No Equipment Required</p>
						<p class="text-sm text-gray-500 mt-1">This event does not require any special equipment.</p>
					</div>
				</div>
			</div>
			@endif
		</div>

		<!-- Sidebar -->
		<div class="space-y-6">
			<!-- Event Status -->
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-chart-line text-maroon mr-3"></i>
						Event Status
					</h2>
				</div>
				<div class="p-6">
					<div class="text-center">
						@switch($event->status)
							@case('pending_venue')
								<div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-clock text-yellow-600 text-2xl"></i>
								</div>
								<div class="text-lg font-semibold text-yellow-800 mb-2">Pending Venue</div>
								<div class="text-sm text-gray-600">Waiting for venue assignment from OTP</div>
								@break
							@case('upcoming')
								<div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-clock text-blue-600 text-2xl"></i>
								</div>
								<div class="text-lg font-semibold text-blue-800 mb-2">Upcoming</div>
								<div class="text-sm text-gray-600">Event is scheduled for the future</div>
								@break
							@case('ongoing')
								<div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-play text-green-600 text-2xl"></i>
								</div>
								<div class="text-lg font-semibold text-green-800 mb-2">Ongoing</div>
								<div class="text-sm text-gray-600">Event is currently happening</div>
								@break
							@case('completed')
								<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-check text-gray-600 text-2xl"></i>
								</div>
								<div class="text-lg font-semibold text-gray-800 mb-2">Completed</div>
								<div class="text-sm text-gray-600">Event has finished successfully</div>
								@break
							@case('cancelled')
								<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
									<i class="fas fa-times text-red-600 text-2xl"></i>
								</div>
								<div class="text-lg font-semibold text-red-800 mb-2">Cancelled</div>
								<div class="text-sm text-gray-600">Event has been cancelled</div>
								@break
						@endswitch
					</div>
				</div>
			</div>

			<!-- Event Timeline -->
			<div class="info-card">
				<div class="p-6 border-b border-gray-200 bg-gray-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-history text-maroon mr-3"></i>
						Event Timeline
					</h2>
				</div>
				<div class="p-6">
					<div class="space-y-4">
						<div class="flex items-start">
							<div class="w-3 h-3 bg-green-500 rounded-full mt-2 mr-3"></div>
							<div>
								<div class="font-medium text-gray-800">Event Created</div>
								<div class="text-sm text-gray-600">{{ $event->created_at->format('M d, Y g:i A') }}</div>
							</div>
						</div>
						
						<div class="flex items-start">
							<div class="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3"></div>
							<div>
								<div class="font-medium text-gray-800">Event Scheduled</div>
								<div class="text-sm text-gray-600">{{ $event->start_date->format('M d, Y g:i A') }}</div>
							</div>
						</div>
						
						<div class="flex items-start">
							<div class="w-3 h-3 bg-purple-500 rounded-full mt-2 mr-3"></div>
							<div>
								<div class="font-medium text-gray-800">Event Ends</div>
								<div class="text-sm text-gray-600">{{ $event->end_date->format('M d, Y g:i A') }}</div>
							</div>
						</div>
						
						@if($event->status === 'completed')
						<div class="flex items-start">
							<div class="w-3 h-3 bg-gray-500 rounded-full mt-2 mr-3"></div>
							<div>
								<div class="font-medium text-gray-800">Event Completed</div>
								<div class="text-sm text-gray-600">Marked as completed by GSU</div>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
	<div class="flex items-center justify-center min-h-screen p-4">
		<div id="completeModalContainer" class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins transition-all duration-300">
			<div class="p-6 border-b border-gray-200 bg-gray-50">
				<div class="flex items-center justify-between">
					<h3 class="text-xl font-bold text-gray-800 font-montserrat">
						<i class="fas fa-check-circle text-green-600 mr-2"></i>
						Mark as Complete
					</h3>
					<button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
						<i class="fas fa-times"></i>
					</button>
				</div>
			</div>
			<div class="p-6">
				<!-- Item Details Section -->
				<div class="mb-4">
					<p class="text-gray-700 mb-2">Are you sure you want to mark this event as completed?</p>
					<div id="completeItemDetails" class="bg-gray-50 p-3 rounded-lg text-sm text-gray-600"></div>
				</div>
				
				<!-- Two Column Layout Container -->
				<div id="modalContent" class="space-y-4">
					<!-- Left Column (or full width when no report) -->
					<div id="leftColumn" class="space-y-4">
						<div>
							<label for="completionNotes" class="block text-sm font-medium text-gray-700 mb-2">Completion Notes (Optional)</label>
							<textarea id="completionNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200" placeholder="Add any notes about the completion..."></textarea>
						</div>
						
						<!-- Report Issue Toggle -->
						<div class="border-t border-gray-200 pt-4">
							<div class="flex items-center justify-between mb-3">
								<h4 class="text-sm font-medium text-gray-700">Report Issue (Optional)</h4>
								<button type="button" onclick="toggleReportSection()" class="text-sm text-maroon hover:text-red-700 font-medium">
									<i class="fas fa-exclamation-triangle mr-1"></i>
									<span id="reportToggleText">Report Issue</span>
								</button>
							</div>
						</div>
						
						<div class="text-sm text-gray-600">
							<i class="fas fa-info-circle text-blue-600 mr-1"></i>
							This will notify IOSA, OTP, and PPGS about the completion.
						</div>
					</div>
					
					<!-- Right Column (Report Section - appears when toggled) -->
					<div id="rightColumn" class="hidden">
						<div id="reportSection" class="space-y-4 bg-red-50 p-4 rounded-lg border border-red-200">
							<h5 class="font-medium text-red-800 mb-3">
								<i class="fas fa-exclamation-triangle mr-2"></i>
								Issue Report Details
							</h5>
							
							<div class="grid grid-cols-1 gap-3">
								<div>
									<label for="reportType" class="block text-xs font-medium text-gray-700 mb-1">Issue Type</label>
									<select id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm">
										<option value="">Select type...</option>
										<option value="accident">Accident</option>
										<option value="problem">Problem</option>
										<option value="violation">Violation</option>
										<option value="damage">Damage</option>
										<option value="other">Other</option>
									</select>
								</div>
								<div>
									<label for="reportSeverity" class="block text-xs font-medium text-gray-700 mb-1">Severity</label>
									<select id="reportSeverity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm">
										<option value="">Select severity...</option>
										<option value="low">Low</option>
										<option value="medium">Medium</option>
										<option value="high">High</option>
										<option value="critical">Critical</option>
									</select>
								</div>
							</div>
							
							<div>
								<label for="reportDescription" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
								<textarea id="reportDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm" placeholder="Describe what happened..."></textarea>
							</div>
							
							<div>
								<label for="reportActions" class="block text-xs font-medium text-gray-700 mb-1">Actions Taken (Optional)</label>
								<textarea id="reportActions" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm" placeholder="What actions were taken to address the issue..."></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
				<button onclick="closeCompleteModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
				<button onclick="confirmComplete()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
					<i class="fas fa-check mr-2"></i>Mark Complete
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let currentCompleteItem = null;

function openCompleteModal(id, title, type) {
	currentCompleteItem = { id: id, title: title, type: type };
	
	const itemDetails = document.getElementById('completeItemDetails');
	itemDetails.innerHTML = `
		<div class="font-medium text-gray-800">${title}</div>
		<div class="text-gray-600">${type === 'event' ? 'Event' : 'Reservation'}</div>
	`;
	
	document.getElementById('completionNotes').value = '';
	document.getElementById('completeModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeCompleteModal() {
	document.getElementById('completeModal').classList.add('hidden');
	document.body.style.overflow = 'auto';
	currentCompleteItem = null;
}

function confirmComplete() {
	if (!currentCompleteItem) return;
	
	const notes = document.getElementById('completionNotes').value;
	const { id, type } = currentCompleteItem;
	
	// Check if report section is visible and has data
	const reportSection = document.getElementById('reportSection');
	const hasReport = !reportSection.classList.contains('hidden') && 
		document.getElementById('reportType').value && 
		document.getElementById('reportSeverity').value && 
		document.getElementById('reportDescription').value;
	
	// Create form data
	const formData = new FormData();
	formData.append('_token', '{{ csrf_token() }}');
	formData.append('completion_notes', notes);
	
	// Add report data if present
	if (hasReport) {
		formData.append('type', document.getElementById('reportType').value);
		formData.append('severity', document.getElementById('reportSeverity').value);
		formData.append('description', document.getElementById('reportDescription').value);
		formData.append('actions_taken', document.getElementById('reportActions').value);
	}
	
	// Determine the route based on type
	const route = type === 'event' 
		? `{{ route('gsu.events.complete', ':id') }}`.replace(':id', id)
		: `{{ route('gsu.reservations.complete', ':id') }}`.replace(':id', id);
	
	// Send request
	fetch(route, {
		method: 'POST',
		body: formData,
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'application/json'
		}
	})
	.then(response => {
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		return response.json();
	})
	.then(data => {
		if (data.success) {
			// Show success message
			let message = 'Event marked as completed successfully!';
			if (hasReport) {
				message += ' Issue has also been reported.';
			}
			showNotification(message, 'success');
			closeCompleteModal();
			
			// Reload the page to reflect changes
			setTimeout(() => {
				window.location.reload();
			}, 1500);
		} else {
			showNotification(data.message || 'Error marking event as complete', 'error');
		}
	})
	.catch(error => {
		console.error('Error:', error);
		// Check if it's a JSON parsing error
		if (error.message.includes('JSON')) {
			showNotification('Server returned an invalid response. Please try again.', 'error');
		} else {
			showNotification('Error marking event as complete. Please try again.', 'error');
		}
	});
}

function toggleReportSection() {
	const rightColumn = document.getElementById('rightColumn');
	const modalContent = document.getElementById('modalContent');
	const modalContainer = document.getElementById('completeModalContainer');
	const toggleText = document.getElementById('reportToggleText');
	
	if (rightColumn.classList.contains('hidden')) {
		// Show report section - switch to 2-column layout
		rightColumn.classList.remove('hidden');
		modalContent.classList.add('grid', 'grid-cols-2', 'gap-6');
		modalContainer.classList.remove('max-w-md');
		modalContainer.classList.add('max-w-4xl');
		toggleText.textContent = 'Hide Report';
	} else {
		// Hide report section - switch back to single column
		rightColumn.classList.add('hidden');
		modalContent.classList.remove('grid', 'grid-cols-2', 'gap-6');
		modalContainer.classList.remove('max-w-4xl');
		modalContainer.classList.add('max-w-md');
		toggleText.textContent = 'Report Issue';
		// Clear form fields when hiding
		document.getElementById('reportType').value = '';
		document.getElementById('reportSeverity').value = '';
		document.getElementById('reportDescription').value = '';
		document.getElementById('reportActions').value = '';
	}
}

function showNotification(message, type) {
	// Create notification element
	const notification = document.createElement('div');
	notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
		type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
	}`;
	notification.innerHTML = `
		<div class="flex items-center">
			<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
			<span>${message}</span>
		</div>
	`;
	
	document.body.appendChild(notification);
	
	// Animate in
	setTimeout(() => {
		notification.classList.remove('translate-x-full');
	}, 100);
	
	// Remove after 5 seconds
	setTimeout(() => {
		notification.classList.add('translate-x-full');
		setTimeout(() => {
			document.body.removeChild(notification);
		}, 300);
	}, 5000);
}
</script>
@endsection 