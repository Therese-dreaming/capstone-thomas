@extends('layouts.gsu')

@section('title', 'Events Management')
@section('page-title', 'Events Management')
@section('page-subtitle', 'View and manage all events')

@section('header-actions')
	<div class="flex items-center space-x-3">
		<form action="{{ route('gsu.events.update-statuses') }}" method="POST" class="inline">
			@csrf
			<button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
				<i class="fas fa-sync-alt mr-2"></i>Update to Ongoing
			</button>
		</form>
	</div>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
	.font-inter { font-family: 'Inter', sans-serif; }
	.font-poppins { font-family: 'Poppins', sans-serif; }
	
	.status-badge {
		padding: 0.5rem 1rem;
		border-radius: 9999px;
		font-size: 0.75rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		position: relative;
		z-index: 20;
		pointer-events: none;
		display: inline-block;
		white-space: nowrap;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}
	
	.event-card {
		background: white;
		border-radius: 1rem;
		box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
		border: 1px solid #f3f4f6;
		transition: all 0.3s ease;
		overflow: visible;
		position: relative;
	}
	
	.event-card:hover {
		box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
		transform: translateY(-4px);
		border-color: #e5e7eb;
	}
	
	.tab-button {
		padding: 0.75rem 1.5rem;
		border-radius: 0.75rem;
		font-weight: 500;
		transition: all 0.3s ease;
		position: relative;
	}
	
	.tab-button.active {
		background: #8B0000;
		color: white;
		box-shadow: 0 4px 6px -1px rgba(139, 0, 0, 0.2);
	}
	
	.tab-button:not(.active) {
		background: transparent;
		color: #6B7280;
	}
	
	.tab-button:not(.active):hover {
		background: rgba(139, 0, 0, 0.05);
		color: #374151;
	}
	
	.search-container {
		position: relative;
		max-width: 400px;
	}
	
	.search-input {
		width: 100%;
		padding: 0.75rem 1rem 0.75rem 3rem;
		border: 2px solid #e5e7eb;
		border-radius: 1rem;
		font-size: 0.875rem;
		transition: all 0.3s ease;
		background: white;
	}
	
	.search-input:focus {
		outline: none;
		border-color: #8B0000;
		box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
	}
	
	.search-icon {
		position: absolute;
		left: 1rem;
		top: 50%;
		transform: translateY(-50%);
		color: #9CA3AF;
	}
	
	.action-button {
		padding: 0.5rem;
		border-radius: 0.5rem;
		transition: all 0.2s ease;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-width: 2.5rem;
	}
	
	.action-button:hover {
		transform: scale(1.1);
	}
	
	.stats-card {
		background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
		border: 1px solid #e2e8f0;
		border-radius: 0.75rem;
		padding: 1rem;
		text-align: center;
	}
	
	.stats-number {
		font-size: 1.5rem;
		font-weight: 700;
		color: #1e293b;
		margin-bottom: 0.25rem;
	}
	
	.stats-label {
		font-size: 0.75rem;
		color: #64748b;
		font-weight: 500;
	}
</style>

<div class="space-y-8 font-inter">
	<!-- Header Section -->
	<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
				<div>
					<h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">Events Management</h1>
					<p class="text-gray-600 text-lg">View and manage all events in the system</p>
				</div>
				<div class="search-container">
					<form method="GET" action="{{ route('gsu.events.index') }}">
						<input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, ID, description, organizer, or venue..." class="search-input">
						@if(request('status') && request('status') !== 'all')
							<input type="hidden" name="status" value="{{ request('status') }}">
						@endif
						<div class="search-icon">
							<i class="fas fa-search"></i>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Statistics Overview -->
	@if(request('status') && request('status') !== 'all')
	<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
		<div class="flex items-center">
			<i class="fas fa-info-circle text-blue-600 mr-3"></i>
			<div>
				<p class="text-blue-800 font-medium">Showing {{ ucfirst(request('status')) }} Events Only</p>
				<p class="text-blue-600 text-sm">The statistics below show counts for the current filter. Use "All Events" to see complete statistics.</p>
			</div>
		</div>
	</div>
	@endif
	
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
		<div class="stats-card">
			<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-calendar text-blue-600 text-lg"></i>
			</div>
			<div class="stats-number">{{ $events->total() }}</div>
			<div class="stats-label">
				@if(request('status') && request('status') !== 'all')
					{{ ucfirst(request('status')) }} Events
				@else
					Total Events
				@endif
			</div>
		</div>
		
		<div class="stats-card">
			<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-clock text-green-600 text-lg"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'upcoming')->count() }}</div>
			<div class="stats-label">Upcoming</div>
		</div>
		
		<div class="stats-card">
			<div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-play text-yellow-600 text-lg"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'ongoing')->count() }}</div>
			<div class="stats-label">Ongoing</div>
		</div>
		
		<div class="stats-card">
			<div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-check text-gray-600 text-lg"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'completed')->count() }}</div>
			<div class="stats-label">Completed</div>
		</div>
		
		<div class="stats-card">
			<div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-times text-red-600 text-lg"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'cancelled')->count() }}</div>
			<div class="stats-label">Cancelled</div>
		</div>
	</div>

	<!-- Status Tabs -->
	<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200 bg-gray-50">
			<div class="flex flex-wrap gap-2">
				@php
					$current = request('status', 'all');
					$searchQuery = request('search');
					$baseUrl = route('gsu.events.index');
				@endphp
				
				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'all'])) }}" 
				   class="tab-button {{ $current == 'all' ? 'active' : '' }}">
					<i class="fas fa-list mr-2"></i>All Events
				</a>
				
				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'upcoming'])) }}" 
				   class="tab-button {{ $current == 'upcoming' ? 'active' : '' }}">
					<i class="fas fa-clock mr-2"></i>Upcoming
				</a>
				
				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'ongoing'])) }}" 
				   class="tab-button {{ $current == 'ongoing' ? 'active' : '' }}">
					<i class="fas fa-play mr-2"></i>Ongoing
				</a>
				
				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'completed'])) }}" 
				   class="tab-button {{ $current == 'completed' ? 'active' : '' }}">
					<i class="fas fa-check mr-2"></i>Completed
				</a>
				
				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'cancelled'])) }}" 
				   class="tab-button {{ $current == 'cancelled' ? 'active' : '' }}">
					<i class="fas fa-times mr-2"></i>Cancelled
				</a>
			</div>
			
			<!-- Active Filters Display -->
			@if(request('status') || request('q'))
			<div class="mt-4 pt-4 border-t border-gray-200">
				<div class="flex flex-wrap items-center gap-2">
					<span class="text-sm text-gray-600">Active filters:</span>
					@if(request('status') && request('status') !== 'all')
					<span class="inline-flex items-center px-2 py-1 bg-maroon text-white text-xs rounded-full">
						Status: {{ ucfirst(request('status')) }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'status'])) }}" class="ml-2 hover:text-red-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					@if(request('search'))
					<span class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs rounded-full">
						Search: "{{ request('search') }}"
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'search'])) }}" class="ml-2 hover:text-blue-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					<a href="{{ $baseUrl }}" class="text-sm text-maroon hover:text-red-800 font-medium">
						Clear all filters
					</a>
				</div>
			</div>
			@endif
		</div>

		<!-- Events Grid -->
		<div class="p-6">
			@if($events->count())
				<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
					@foreach($events as $event)
						<div class="event-card group">
							<!-- Status Badge -->
							<div class="absolute top-4 right-4 z-20">
								@switch($event->status)
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

							<!-- Event Header -->
							<div class="p-6 pb-4">
								<div class="flex items-start space-x-4">
									<div class="w-16 h-16 rounded-xl bg-gradient-to-br from-maroon to-red-800 flex items-center justify-center text-white shadow-lg">
										<i class="fas fa-calendar-alt text-xl"></i>
									</div>
									<div class="flex-1 min-w-0">
										<h3 class="font-bold text-gray-900 text-xl mb-2 line-clamp-2">{{ $event->title }}</h3>
										<div class="text-xs text-gray-500 font-mono mb-2">
											ID: {{ $event->event_id ?? 'N/A' }}
										</div>
										@if($event->description)
											<p class="text-sm text-gray-600 line-clamp-2">{{ $event->description }}</p>
										@endif
									</div>
								</div>
							</div>

							<!-- Event Details -->
							<div class="px-6 pb-4">
								<div class="space-y-3">
									<div class="flex items-center text-sm text-gray-700">
										<i class="fas fa-calendar mr-3 text-maroon w-4"></i>
										<span class="font-medium">{{ $event->start_date->format('M d, Y') }}</span>
									</div>
									<div class="flex items-center text-sm text-gray-700">
										<i class="fas fa-clock mr-3 text-maroon w-4"></i>
										<span>{{ $event->start_date->format('g:i A') }} – {{ $event->end_date->format('g:i A') }}</span>
									</div>
									@if($event->venue)
										<div class="flex items-center text-sm text-gray-700">
											<i class="fas fa-map-marker-alt mr-3 text-maroon w-4"></i>
											<span>{{ $event->venue->name }}</span>
										</div>
									@endif
									@if($event->organizer)
										<div class="flex items-center text-sm text-gray-700">
											<i class="fas fa-user mr-3 text-maroon w-4"></i>
											<span>{{ $event->organizer }}</span>
										</div>
									@endif
									@if($event->department)
										<div class="flex items-center text-sm text-gray-700">
											<i class="fas fa-building mr-3 text-maroon w-4"></i>
											<span>{{ $event->department }}</span>
										</div>
									@endif
									@if($event->max_participants)
										<div class="flex items-center text-sm text-gray-700">
											<i class="fas fa-users mr-3 text-maroon w-4"></i>
											<span>Max: {{ $event->max_participants }} participants</span>
										</div>
									@endif
								</div>
							</div>


							<!-- Event Footer -->
							<div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
								<div class="flex items-center justify-between">
									<div class="text-xs text-gray-500">
										<i class="far fa-clock mr-1"></i>
										Created {{ $event->created_at->diffForHumans() }}
									</div>
									<div class="flex space-x-2">
										<a href="{{ route('gsu.events.show', $event) }}" 
										   class="action-button bg-blue-50 text-blue-600 hover:bg-blue-100" title="View Details">
											<i class="fas fa-eye"></i>
										</a>
										
										@if($event->status !== 'cancelled' && $event->status !== 'completed')
										<button onclick="openCompleteModal({{ $event->id }}, '{{ $event->title }}', 'event')" 
												class="action-button bg-green-50 text-green-600 hover:bg-green-100" 
												title="Mark as Complete">
											<i class="fas fa-check-circle"></i>
										</button>
										@endif
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				<!-- Pagination -->
				@if($events->hasPages())
					<div class="mt-8 flex justify-center">
						<div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200">
							{{ $events->appends(request()->except('page'))->links() }}
						</div>
					</div>
				@endif
			@else
				<div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
					<div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
						<i class="fas fa-calendar text-gray-400 text-4xl"></i>
					</div>
					<h3 class="text-xl font-semibold text-gray-700 mb-2">No events found</h3>
					<p class="text-gray-500 mb-6">
						@if(request('search') && request('status') && request('status') !== 'all')
							No {{ request('status') }} events match your search for "{{ request('search') }}".
						@elseif(request('search'))
							No events match your search for "{{ request('search') }}".
						@elseif(request('status') && request('status') !== 'all')
							No {{ request('status') }} events at the moment.
						@else
							No events have been created yet.
						@endif
					</p>
					@if(request('search') || (request('status') && request('status') !== 'all'))
						<a href="{{ route('gsu.events.index') }}" 
						   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
							<i class="fas fa-times mr-2"></i>Clear Filters
						</a>
					@endif
				</div>
			@endif
		</div>
	</div>
</div>

<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
	<div class="flex items-center justify-center min-h-screen p-4">
		<div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins">
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
				<div class="mb-4">
					<p class="text-gray-700 mb-2">Are you sure you want to mark this event as completed?</p>
					<div id="completeItemDetails" class="bg-gray-50 p-3 rounded-lg text-sm text-gray-600"></div>
				</div>
				<div class="mb-4">
					<label for="completionNotes" class="block text-sm font-medium text-gray-700 mb-2">Completion Notes (Optional)</label>
					<textarea id="completionNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200" placeholder="Add any notes about the completion..."></textarea>
				</div>
				
				<!-- Report Issue Section -->
				<div class="mb-4 border-t border-gray-200 pt-4">
					<div class="flex items-center justify-between mb-3">
						<h4 class="text-sm font-medium text-gray-700">Report Issue (Optional)</h4>
						<button type="button" onclick="toggleReportSection()" class="text-sm text-maroon hover:text-red-700 font-medium">
							<i class="fas fa-exclamation-triangle mr-1"></i>
							<span id="reportToggleText">Report Issue</span>
						</button>
					</div>
					
					<div id="reportSection" class="hidden space-y-3">
						<div class="grid grid-cols-2 gap-3">
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
				
				<div class="text-sm text-gray-600 mb-4">
					<i class="fas fa-info-circle text-blue-600 mr-1"></i>
					This will notify IOSA, Ms. Mhadel, and OTP about the completion.
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
	const reportSection = document.getElementById('reportSection');
	const toggleText = document.getElementById('reportToggleText');
	
	if (reportSection.classList.contains('hidden')) {
		reportSection.classList.remove('hidden');
		toggleText.textContent = 'Hide Report';
	} else {
		reportSection.classList.add('hidden');
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