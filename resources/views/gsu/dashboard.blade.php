@extends('layouts.gsu')

@section('title', 'GSU Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Final Approved Reservations Overview')

@section('content')
<div class="space-y-6">
	<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
			<p class="text-sm text-gray-500 font-medium">Total Final Approved</p>
			<h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] ?? 0 }}</h3>
		</div>
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
			<p class="text-sm text-gray-500 font-medium">Approved Today</p>
			<h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved_today'] ?? 0 }}</h3>
		</div>
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
			<p class="text-sm text-gray-500 font-medium">This Month</p>
			<h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_month'] ?? 0 }}</h3>
		</div>
	</div>

	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-200">
			<h2 class="text-lg font-semibold text-gray-800 flex items-center">
				<i class="fas fa-list-alt text-maroon mr-2"></i>
				Recent Final Approved Reservations
			</h2>
		</div>
		<div class="p-6">
			@if(($recent ?? collect())->count() > 0)
				<div class="space-y-4">
					@foreach($recent as $reservation)
						<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
							<div>
								<h4 class="font-medium text-gray-800">{{ $reservation->event_title }}</h4>
								<p class="text-sm text-gray-600">{{ $reservation->user->name }} • {{ $reservation->start_date->format('M d, Y') }}</p>
							</div>
							<a href="{{ route('gsu.reservations.show', $reservation->id) }}" class="text-sm text-maroon hover:text-red-700 font-medium">View</a>
						</div>
					@endforeach
				</div>
			@else
				<div class="text-center py-8 text-gray-500">No items</div>
			@endif
		</div>
	</div>
</div>
@endsection 