@extends('layouts.mhadel')

@section('title', 'Events Management')
@section('page-title', 'Events Management')

@section('header-actions')
	<a href="{{ route('mhadel.events.create') }}" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-opacity-80 transition">
		<i class="fas fa-plus mr-2"></i>Add New Event
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
	.table-header { background: linear-gradient(to right, #F9FAFB, #FFFFFF); }
	.badge { padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
	.tab-active { border-bottom: 2px solid #4B5563; color: #111827; font-weight: 500; }
	.card { transition: all 0.25s ease; }
	.card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -8px rgba(0,0,0,0.15); }
</style>
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
	<div class="p-6 border-b border-gray-200 table-header">
		<div class="flex items-center justify-between">
			<div>
				<h3 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
					<i class="fas fa-bullhorn mr-3 text-gray-700"></i>
					All Events
				</h3>
				<p class="text-gray-600 text-sm">Manage your events</p>
			</div>
			<div class="relative">
				<form method="GET" action="{{ route('mhadel.events.index') }}">
					<input type="text" name="q" value="{{ request('q') }}" placeholder="Search events..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 transition-colors">
					<div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
						<i class="fas fa-search"></i>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Status Tabs -->
	<div class="flex border-b border-gray-200 bg-gray-50">
		@php($current = request('status','all'))
		<a href="{{ route('mhadel.events.index', ['status'=>'all'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ $current=='all' ? 'tab-active' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">All</a>
		<a href="{{ route('mhadel.events.index', ['status'=>'upcoming'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ $current=='upcoming' ? 'tab-active' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Upcoming</a>
		<a href="{{ route('mhadel.events.index', ['status'=>'ongoing'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ $current=='ongoing' ? 'tab-active' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Ongoing</a>
		<a href="{{ route('mhadel.events.index', ['status'=>'completed'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ $current=='completed' ? 'tab-active' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Completed</a>
		<a href="{{ route('mhadel.events.index', ['status'=>'cancelled'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ $current=='cancelled' ? 'tab-active' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Cancelled</a>
	</div>

	<!-- Cards Grid -->
	<div class="p-6">
		@if($events->count())
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				@foreach($events as $event)
					<div class="card border border-gray-200 rounded-xl p-5 bg-white relative">
						<!-- Status Badge -->
						<div class="absolute top-4 right-4">
							@switch($event->status)
								@case('upcoming')
									<span class="badge bg-blue-100 text-blue-800">Upcoming</span>
									@break
								@case('ongoing')
									<span class="badge bg-green-100 text-green-800">Ongoing</span>
									@break
								@case('completed')
									<span class="badge bg-gray-100 text-gray-800">Completed</span>
									@break
								@case('cancelled')
									<span class="badge bg-red-100 text-red-800">Cancelled</span>
									@break
							@endswitch
						</div>

						<div class="flex items-start space-x-4">
							<div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600">
								<i class="fas fa-calendar-alt"></i>
							</div>
							<div class="flex-1">
								<h3 class="font-bold text-gray-900 text-lg">{{ $event->title }}</h3>
								@if($event->description)
									<p class="text-sm text-gray-600 mt-1">{{ Str::limit($event->description, 120) }}</p>
								@endif
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4 text-sm text-gray-700">
									<div><i class="fas fa-calendar mr-2 text-gray-500"></i>{{ $event->start_date->format('M d, Y') }}</div>
									<div><i class="fas fa-clock mr-2 text-gray-500"></i>{{ $event->start_date->format('g:i A') }} – {{ $event->end_date->format('g:i A') }}</div>
									<div><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>{{ $event->venue->name ?? 'No venue' }}</div>
									<div><i class="fas fa-user mr-2 text-gray-500"></i>{{ $event->organizer }}</div>
									<div><i class="fas fa-users mr-2 text-gray-500"></i>
										@if($event->max_participants)
											Max: {{ $event->max_participants }}
										@else
											No limit
										@endif
									</div>
									<div class="text-gray-500 text-xs"><i class="far fa-clock mr-2"></i>Created {{ $event->created_at->diffForHumans() }}</div>
								</div>
								<div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
									<a href="{{ route('mhadel.events.show', $event) }}" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="View">
										<i class="fas fa-eye"></i>
									</a>
									<a href="{{ route('mhadel.events.edit', $event) }}" class="px-2.5 py-1.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="Edit">
										<i class="fas fa-edit"></i>
									</a>
									<form action="{{ route('mhadel.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Delete">
											<i class="fas fa-trash"></i>
										</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>

			<!-- Pagination -->
			<div class="mt-8 flex justify-center">
				{{ $events->appends(request()->except('page'))->links() }}
			</div>
		@else
			<div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
				<i class="fas fa-calendar text-gray-400 text-5xl mb-4"></i>
				<p class="text-gray-600 mb-4">No events found for this filter</p>
				<a href="{{ route('mhadel.events.create') }}" class="inline-block px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-md">
					<i class="fas fa-plus mr-2"></i> Add your first event
				</a>
			</div>
		@endif
	</div>
</div>
@endsection
