@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
	<!-- Welcome Section -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-2xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h2>
				<p class="text-gray-600">Here are your most recent reservations.</p>
			</div>
			<div class="flex items-center gap-2">
				<a href="{{ route('user.reservations.calendar') }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
					<i class="fas fa-calendar-alt mr-2"></i> View Calendar
				</a>
				<a href="{{ route('user.reservations.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
					My Reservations
				</a>
			</div>
		</div>
	</div>

	<!-- Recent Reservations -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200">
			<h3 class="text-lg font-semibold text-gray-800 flex items-center">
				<i class="fas fa-bookmark mr-2"></i>
				Recent Reservations
			</h3>
		</div>
		<div class="p-6">
			@if(($reservations ?? collect())->count() > 0)
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					@foreach($reservations as $reservation)
						<div class="border border-gray-200 rounded-xl p-5 bg-white">
							<h4 class="font-bold text-gray-800 text-lg">{{ $reservation->event_title }}</h4>
							<div class="mt-2 text-sm text-gray-600 flex items-center">
								<i class="fas fa-map-marker-alt mr-2"></i>
								{{ $reservation->venue->name ?? 'No venue' }}
							</div>
							<div class="mt-1 text-sm text-gray-500 flex items-center">
								<i class="far fa-clock mr-2"></i>
								{{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }} -
								{{ $reservation->end_date ? $reservation->end_date->format('g:i A') : 'No end time' }}
							</div>
							<div class="mt-4 flex justify-end">
								<a href="{{ route('user.reservations.show', $reservation->id) }}" class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-sm">
									<i class="fas fa-eye mr-1"></i> View
								</a>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
					<i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
					<p class="text-gray-600 mb-4">No recent reservations found</p>
					<a href="{{ route('user.reservations.calendar') }}" class="inline-block px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
						<i class="fas fa-calendar-plus mr-2"></i> Make a Reservation
					</a>
				</div>
			@endif
		</div>
	</div>
</div>
@endsection