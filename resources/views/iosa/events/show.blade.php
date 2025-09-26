@extends('layouts.iosa')

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
					<a href="{{ route('iosa.events.index') }}" 
					   class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
						<i class="fas fa-arrow-left mr-2"></i>
						<span>Back to Events</span>
					</a>
					
					@if($event->status === 'completed')
					<div class="w-full bg-green-100 text-green-800 px-6 py-3 rounded-xl flex items-center justify-center font-medium">
						<i class="fas fa-check-circle mr-2"></i>
						<span>Event Completed</span>
					</div>
					@elseif($event->status === 'cancelled')
					<div class="w-full bg-red-100 text-red-800 px-6 py-3 rounded-xl flex items-center justify-center font-medium">
						<i class="fas fa-times-circle mr-2"></i>
						<span>Event Cancelled</span>
					</div>
					@elseif($event->status === 'pending_venue')
					<div class="w-full bg-yellow-100 text-yellow-800 px-6 py-3 rounded-xl flex items-center justify-center font-medium">
						<i class="fas fa-clock mr-2"></i>
						<span>Pending Venue Assignment</span>
					</div>
					@else
					<div class="w-full bg-blue-100 text-blue-800 px-6 py-3 rounded-xl flex items-center justify-center font-medium">
						<i class="fas fa-info-circle mr-2"></i>
						<span>Event {{ ucfirst($event->status) }}</span>
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
			@elseif($event->status === 'pending_venue')
			<div class="info-card pending-venue-card">
				<div class="p-6 border-b border-yellow-200 bg-yellow-50">
					<h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
						<i class="fas fa-clock text-yellow-600 mr-3"></i>
						Venue Assignment
					</h2>
				</div>
				<div class="p-6">
					<div class="flex items-center text-yellow-700">
						<i class="fas fa-info-circle mr-3 text-yellow-600 w-5"></i>
						<div>
							<div class="font-medium">Venue Assignment Pending</div>
							<div class="text-sm text-yellow-600">This event is waiting for Ms. Mhadel to assign a venue. You will be notified once a venue is assigned.</div>
						</div>
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
								<div class="text-sm text-gray-600">Waiting for venue assignment from Ms. Mhadel</div>
								<div class="mt-4">
									<a href="{{ route('iosa.events.edit', $event) }}" 
									   class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-maroon rounded-lg hover:bg-red-800 transition-colors duration-300">
										<i class="fas fa-edit mr-2"></i>
										Edit Event
									</a>
								</div>
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
						
						@if($event->status === 'pending_venue')
						<div class="flex items-start">
							<div class="w-3 h-3 bg-yellow-500 rounded-full mt-2 mr-3"></div>
							<div>
								<div class="font-medium text-gray-800">Venue Assignment Pending</div>
								<div class="text-sm text-gray-600">Waiting for Ms. Mhadel to assign venue</div>
							</div>
						</div>
						@endif
						
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
@endsection

