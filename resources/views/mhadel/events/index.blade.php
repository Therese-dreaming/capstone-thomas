@extends('layouts.mhadel')

@section('title', 'Events Management')
@section('page-title', 'Events Management')
@section('page-subtitle', 'Create, manage, and monitor all your events')

@section('header-actions')
	<div class="flex items-center space-x-3">
		<form action="{{ route('mhadel.events.update-statuses') }}" method="POST" class="inline">
			@csrf
			<button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
				<i class="fas fa-sync-alt mr-2"></i>Update to Ongoing
			</button>
		</form>
		<a href="{{ route('mhadel.events.create') }}" class="bg-maroon text-white px-6 py-3 rounded-xl hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
			<i class="fas fa-plus mr-2"></i>Create New Event
		</a>
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
	
	/* Ensure status badge container is always visible */
	.event-card .absolute {
		position: absolute;
		top: 1rem;
		right: 1rem;
		z-index: 30;
		pointer-events: none;
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
		padding: 0.75rem;
		text-align: center;
	}
	
	.stats-number {
		font-size: 1.25rem;
		font-weight: 700;
		color: #1e293b;
		margin-bottom: 0.25rem;
		line-height: 1;
	}
	
	.stats-label {
		font-size: 0.7rem;
		color: #64748b;
		font-weight: 500;
		line-height: 1.2;
	}
	
	.modal {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		display: none;
		justify-content: center;
		align-items: center;
		z-index: 1000;
	}
	
	.modal.show {
		display: flex;
	}
	
	.modal-content {
		background: white;
		border-radius: 1rem;
		padding: 2rem;
		max-width: 400px;
		width: 90%;
		text-align: center;
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
	}
</style>

<div class="space-y-8 font-inter">
	<!-- Header Section -->
	<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
			<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
				<div>
					<h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">Events Management</h1>
					<p class="text-gray-600 text-lg">Create, organize, and monitor all your events in one place</p>
				</div>
				<div class="search-container">
					<form method="GET" action="{{ route('mhadel.events.index') }}">
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

	<!-- Advanced Filters Section -->
	<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200 bg-gray-50">
			<div class="flex items-center justify-between mb-4">
				<h3 class="text-lg font-semibold text-gray-800 flex items-center">
					<i class="fas fa-filter text-maroon mr-2"></i>
					Advanced Filters
				</h3>
				<button type="button" id="toggleFilters" class="text-sm text-maroon hover:text-red-700 font-medium">
					<i class="fas fa-chevron-down mr-1" id="filterToggleIcon"></i>
					<span id="filterToggleText">Show Filters</span>
				</button>
			</div>
			
			<div id="filtersContainer" class="hidden">
				<form method="GET" action="{{ route('mhadel.events.index') }}" class="space-y-4">
					<!-- Preserve existing search and status -->
					@if(request('search'))
						<input type="hidden" name="search" value="{{ request('search') }}">
					@endif
					@if(request('status') && request('status') !== 'all')
						<input type="hidden" name="status" value="{{ request('status') }}">
					@endif
					
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
						<!-- Date Range Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Start Date From</label>
							<input type="date" name="start_date_from" value="{{ request('start_date_from') }}" 
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
						</div>
						
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Start Date To</label>
							<input type="date" name="start_date_to" value="{{ request('start_date_to') }}" 
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
						</div>
						
						<!-- Venue Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Venue</label>
							<select name="venue_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
								<option value="">All Venues</option>
								@php
									$venues = \App\Models\Venue::orderBy('name')->get();
								@endphp
								@foreach($venues as $venue)
									<option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>
										{{ $venue->name }}
									</option>
								@endforeach
							</select>
						</div>
						
						<!-- Department Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Department</label>
							<select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
								<option value="">All Departments</option>
								@php
									$departments = \App\Models\Event::whereNotNull('department')->distinct()->pluck('department')->filter()->sort();
								@endphp
								@foreach($departments as $dept)
									<option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
										{{ $dept }}
									</option>
								@endforeach
							</select>
						</div>
						
						<!-- Organizer Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Organizer</label>
							<input type="text" name="organizer" value="{{ request('organizer') }}" 
								   placeholder="Search by organizer name..."
								   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
						</div>
						
						<!-- Equipment Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Equipment</label>
							<select name="has_equipment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
								<option value="">All Events</option>
								<option value="1" {{ request('has_equipment') == '1' ? 'selected' : '' }}>With Equipment</option>
								<option value="0" {{ request('has_equipment') == '0' ? 'selected' : '' }}>Without Equipment</option>
							</select>
						</div>
						
						<!-- Duration Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
							<select name="duration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
								<option value="">Any Duration</option>
								<option value="1" {{ request('duration') == '1' ? 'selected' : '' }}>1 Hour or Less</option>
								<option value="2" {{ request('duration') == '2' ? 'selected' : '' }}>2-4 Hours</option>
								<option value="5" {{ request('duration') == '5' ? 'selected' : '' }}>5-8 Hours</option>
								<option value="9" {{ request('duration') == '9' ? 'selected' : '' }}>More than 8 Hours</option>
							</select>
						</div>
						
						<!-- Created Date Filter -->
						<div class="space-y-2">
							<label class="block text-sm font-medium text-gray-700">Created Date</label>
							<select name="created_period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon text-sm">
								<option value="">All Time</option>
								<option value="today" {{ request('created_period') == 'today' ? 'selected' : '' }}>Today</option>
								<option value="week" {{ request('created_period') == 'week' ? 'selected' : '' }}>This Week</option>
								<option value="month" {{ request('created_period') == 'month' ? 'selected' : '' }}>This Month</option>
								<option value="year" {{ request('created_period') == 'year' ? 'selected' : '' }}>This Year</option>
							</select>
						</div>
					</div>
					
					<!-- Filter Actions -->
					<div class="flex items-center justify-between pt-4 border-t border-gray-200">
						<div class="flex items-center space-x-3">
							<button type="submit" class="bg-maroon text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
								<i class="fas fa-search mr-2"></i>
								<span>Apply Filters</span>
							</button>
							<a href="{{ route('mhadel.events.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
								<i class="fas fa-times mr-2"></i>
								<span>Clear All</span>
							</a>
						</div>
						<div class="text-sm text-gray-600">
							<i class="fas fa-info-circle mr-1"></i>
							Filters will also apply to Excel export
						</div>
					</div>
				</form>
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
	
	<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
		<div class="stats-card">
			<div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-calendar text-blue-600 text-sm"></i>
			</div>
			<div class="stats-number text-base">{{ $events->total() }}</div>
			<div class="stats-label">
				@if(request('status') && request('status') !== 'all')
					{{ ucfirst(request('status')) }} Events
				@else
					Total Events
				@endif
			</div>
		</div>
		
		<div class="stats-card">
			<div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-clock text-green-600 text-sm"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'upcoming')->count() }}</div>
			<div class="stats-label">Upcoming</div>
		</div>
		
		<div class="stats-card">
			<div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-play text-yellow-600 text-sm"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'ongoing')->count() }}</div>
			<div class="stats-label">Ongoing</div>
		</div>
		
		<div class="stats-card">
			<div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-check text-gray-600 text-sm"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'completed')->count() }}</div>
			<div class="stats-label">Completed</div>
		</div>
		
		<div class="stats-card">
			<div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-times text-red-600 text-sm"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'cancelled')->count() }}</div>
			<div class="stats-label">Cancelled</div>
		</div>

		<div class="stats-card">
			<div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
				<i class="fas fa-building text-purple-600 text-sm"></i>
			</div>
			<div class="stats-number">{{ $events->where('status', 'pending_venue')->count() }}</div>
			<div class="stats-label">Pending Venue</div>
		</div>
	</div>

	<!-- Status Tabs -->
	<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200 bg-gray-50">
			<div class="flex flex-wrap items-center justify-between gap-4">
				<div class="flex flex-wrap gap-2">
				@php
					$current = request('status', 'all');
					$searchQuery = request('search');
					$baseUrl = route('mhadel.events.index');
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

				<a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->except(['page', 'status']), ['status' => 'pending_venue'])) }}" 
				   class="tab-button {{ $current == 'pending_venue' ? 'active' : '' }}">
					<i class="fas fa-building mr-2"></i>Pending Venue
				</a>
				</div>
				
				<!-- Export Button -->
				<div class="flex-shrink-0">
					<a href="{{ route('mhadel.events.export', request()->query()) }}" 
					   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2 text-sm font-medium">
						<i class="fas fa-file-excel mr-2"></i>
						<span>Export to Excel</span>
					</a>
				</div>
			</div>
			
			<!-- Active Filters Display -->
			@if(request('status') || request('search') || request('start_date_from') || request('start_date_to') || request('venue_id') || request('department') || request('organizer') || request('has_equipment') || request('duration') || request('created_period'))
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
					
					@if(request('start_date_from') || request('start_date_to'))
					<span class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded-full">
						Date Range: {{ request('start_date_from') ? \Carbon\Carbon::parse(request('start_date_from'))->format('M j') : 'Any' }} - {{ request('start_date_to') ? \Carbon\Carbon::parse(request('start_date_to'))->format('M j') : 'Any' }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'start_date_from', 'start_date_to'])) }}" class="ml-2 hover:text-green-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('venue_id'))
					@php $venue = \App\Models\Venue::find(request('venue_id')); @endphp
					<span class="inline-flex items-center px-2 py-1 bg-purple-600 text-white text-xs rounded-full">
						Venue: {{ $venue->name ?? 'Unknown' }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'venue_id'])) }}" class="ml-2 hover:text-purple-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('department'))
					<span class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs rounded-full">
						Department: {{ request('department') }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'department'])) }}" class="ml-2 hover:text-indigo-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('organizer'))
					<span class="inline-flex items-center px-2 py-1 bg-teal-600 text-white text-xs rounded-full">
						Organizer: {{ request('organizer') }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'organizer'])) }}" class="ml-2 hover:text-teal-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('has_equipment'))
					<span class="inline-flex items-center px-2 py-1 bg-orange-600 text-white text-xs rounded-full">
						Equipment: {{ request('has_equipment') == '1' ? 'With Equipment' : 'Without Equipment' }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'has_equipment'])) }}" class="ml-2 hover:text-orange-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('duration'))
					@php
						$durationText = [
							'1' => '1 Hour or Less',
							'2' => '2-4 Hours', 
							'5' => '5-8 Hours',
							'9' => 'More than 8 Hours'
						][request('duration')] ?? 'Unknown';
					@endphp
					<span class="inline-flex items-center px-2 py-1 bg-pink-600 text-white text-xs rounded-full">
						Duration: {{ $durationText }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'duration'])) }}" class="ml-2 hover:text-pink-200">
							<i class="fas fa-times"></i>
						</a>
					</span>
					@endif
					
					@if(request('created_period'))
					@php
						$periodText = [
							'today' => 'Today',
							'week' => 'This Week',
							'month' => 'This Month',
							'year' => 'This Year'
						][request('created_period')] ?? 'Unknown';
					@endphp
					<span class="inline-flex items-center px-2 py-1 bg-gray-600 text-white text-xs rounded-full">
						Created: {{ $periodText }}
						<a href="{{ $baseUrl }}?{{ http_build_query(request()->except(['page', 'created_period'])) }}" class="ml-2 hover:text-gray-200">
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
						<div class="event-card group flex flex-col">
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
									@case('pending_venue')
										<span class="status-badge bg-purple-100 text-purple-800 border border-purple-200">Pending Venue</span>
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
									@if($event->equipment_details && count($event->equipment_details) > 0)
										<div class="flex items-center text-sm text-gray-700">
											<i class="fas fa-tools mr-3 text-maroon w-4"></i>
											<span>{{ count($event->equipment_details) }} equipment item{{ count($event->equipment_details) > 1 ? 's' : '' }}</span>
										</div>
									@endif
								</div>
							</div>


							<!-- Event Footer -->
							<div class="px-6 py-4 border-t border-gray-100 bg-gray-50 mt-auto">
								<div class="flex items-center justify-between">
									<div class="text-xs text-gray-500">
										<i class="far fa-clock mr-1"></i>
										Created {{ $event->created_at->diffForHumans() }}
									</div>
									<div class="flex space-x-2">
										<a href="{{ route('mhadel.events.show', $event) }}" 
										   class="action-button bg-blue-50 text-blue-600 hover:bg-blue-100" title="View Details">
											<i class="fas fa-eye"></i>
										</a>
										@if($event->status !== 'completed' && $event->status !== 'cancelled')
										<a href="{{ route('mhadel.events.edit', $event) }}" 
										   class="action-button bg-green-50 text-green-600 hover:bg-green-100" title="Edit Event">
											<i class="fas fa-edit"></i>
										</a>
										@endif
										@if($event->status !== 'cancelled' && $event->status !== 'completed')
										<button type="button" 
												onclick="openCancelModal({{ $event->id }}, '{{ $event->title }}')"
												class="action-button bg-yellow-50 text-yellow-600 hover:bg-yellow-100" title="Cancel Event">
											<i class="fas fa-ban"></i>
										</button>
										@endif
										
										@if(!$event->venue_id && $event->status === 'pending_venue')
										<a href="{{ route('mhadel.events.edit', $event) }}" 
											class="action-button bg-blue-50 text-blue-600 hover:bg-blue-100" title="Assign Venue">
											<i class="fas fa-building"></i>
										</a>
										@endif
										
										@if($event->status !== 'completed')
										<button type="button" 
												onclick="openDeleteModal({{ $event->id }}, '{{ $event->title }}')"
												class="action-button bg-red-50 text-red-600 hover:bg-red-100" title="Delete Event">
											<i class="fas fa-trash"></i>
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
							{{ $events->appends(request()->query())->links() }}
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
							Get started by creating your first event.
						@endif
					</p>
					<div class="flex flex-col sm:flex-row gap-3 justify-center">
						@if(request('search') || (request('status') && request('status') !== 'all'))
							<a href="{{ route('mhadel.events.index') }}" 
							   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
								<i class="fas fa-times mr-2"></i>Clear Filters
							</a>
						@endif
						<a href="{{ route('mhadel.events.create') }}" 
						   class="inline-flex items-center px-6 py-3 bg-maroon text-white rounded-xl hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
							<i class="fas fa-plus mr-2"></i>Create New Event
						</a>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
	<div class="modal-content">
		<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
			<i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
		</div>
		<h3 class="text-xl font-bold text-gray-800 mb-2">Delete Event</h3>
		<p class="text-gray-600 mb-6">Are you sure you want to delete "<span id="eventTitle" class="font-semibold"></span>"? This action cannot be undone.</p>
		
		<div class="flex justify-center space-x-3">
			<button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
				Cancel
			</button>
			<form id="deleteForm" method="POST" class="inline">
				@csrf
				@method('DELETE')
				<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
					Delete Event
				</button>
			</form>
		</div>
	</div>
</div>

<!-- Cancel Event Modal -->
<div id="cancelModal" class="modal">
	<div class="modal-content">
		<div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
			<i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
		</div>
		<h3 class="text-xl font-bold text-gray-800 mb-2">Cancel Event</h3>
		<p class="text-gray-600 mb-6">Are you sure you want to cancel "<span id="cancelEventTitle" class="font-semibold"></span>"? This will mark the event as cancelled but preserve all information.</p>
		
		<div class="flex justify-center space-x-3">
			<button onclick="closeCancelModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
				Keep Event
			</button>
			<form id="cancelForm" method="POST" class="inline">
				@csrf
				<button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
					Cancel Event
				</button>
			</form>
		</div>
	</div>
</div>



<script>
function openDeleteModal(eventId, eventTitle) {
	document.getElementById('eventTitle').textContent = eventTitle;
	document.getElementById('deleteForm').action = `/mhadel/events/${eventId}`;
	document.getElementById('deleteModal').classList.add('show');
	document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
	document.getElementById('deleteModal').classList.remove('show');
	document.body.style.overflow = 'auto';
}

function openCancelModal(eventId, eventTitle) {
	document.getElementById('cancelEventTitle').textContent = eventTitle;
	document.getElementById('cancelForm').action = `/mhadel/events/${eventId}/cancel`;
	document.getElementById('cancelModal').classList.add('show');
	document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
	document.getElementById('cancelModal').classList.remove('show');
	document.body.style.overflow = 'auto';
}



// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeDeleteModal();
	}
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeCancelModal();
	}
});



// Close modal with Escape key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeDeleteModal();
		closeCancelModal();
	}
});

// Filter toggle functionality
document.getElementById('toggleFilters').addEventListener('click', function() {
	const container = document.getElementById('filtersContainer');
	const icon = document.getElementById('filterToggleIcon');
	const text = document.getElementById('filterToggleText');
	
	if (container.classList.contains('hidden')) {
		container.classList.remove('hidden');
		icon.classList.remove('fa-chevron-down');
		icon.classList.add('fa-chevron-up');
		text.textContent = 'Hide Filters';
	} else {
		container.classList.add('hidden');
		icon.classList.remove('fa-chevron-up');
		icon.classList.add('fa-chevron-down');
		text.textContent = 'Show Filters';
	}
});

// Auto-show filters if any filter is active
document.addEventListener('DOMContentLoaded', function() {
	const hasActiveFilters = {{ 
		request('start_date_from') || 
		request('start_date_to') || 
		request('venue_id') || 
		request('department') || 
		request('organizer') || 
		request('has_equipment') || 
		request('duration') || 
		request('created_period') ? 'true' : 'false' 
	}};
	
	if (hasActiveFilters) {
		document.getElementById('toggleFilters').click();
	}
});
</script>
@endsection
