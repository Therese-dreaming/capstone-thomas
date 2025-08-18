@extends('layouts.gsu')

@section('title', 'Reservation Details - GSU')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'Final Approved Reservation')

@section('content')
<div class="space-y-6">
	<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
		<div class="flex items-center justify-between mb-4">
			<h2 class="text-xl font-bold text-gray-800">{{ $reservation->event_title }}</h2>
			<span class="status-badge status-approved">Final Approved</span>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<div>
				<h3 class="font-semibold text-gray-800 mb-3">Event Information</h3>
				<div class="space-y-2 text-sm text-gray-600">
					<div><strong>Requester:</strong> {{ $reservation->user->name }}</div>
					<div><strong>Date:</strong> {{ $reservation->start_date->format('M d, Y') }}</div>
					<div><strong>Time:</strong> {{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}</div>
					<div><strong>Purpose:</strong> {{ $reservation->purpose }}</div>
				</div>
			</div>
			<div>
				<h3 class="font-semibold text-gray-800 mb-3">Venue & Capacity</h3>
				<div class="space-y-2 text-sm text-gray-600">
					<div><strong>Venue:</strong> {{ $reservation->venue->name }}</div>
					<div><strong>Participants:</strong> {{ $reservation->capacity ?? 'N/A' }}</div>
				</div>
			</div>
			<div>
				<h3 class="font-semibold text-gray-800 mb-3">Pricing</h3>
				<div class="space-y-2 text-sm text-gray-600">
					<div><strong>Final Price:</strong> ₱{{ number_format($reservation->final_price ?? 0, 2) }}</div>
					<div><strong>Rate/Hour:</strong> ₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</div>
				</div>
			</div>
		</div>
		<div>
			<h3 class="font-semibold text-gray-800 mb-3">Equipment Requested</h3>
			@if($reservation->equipment_details && count($reservation->equipment_details) > 0)
				<div class="space-y-1 text-sm">
					@foreach($reservation->equipment_details as $eq)
						<div class="text-xs bg-gray-100 px-2 py-1 rounded"><span class="font-medium">{{ $eq['name'] }}</span> <span class="text-gray-500">({{ $eq['quantity'] }})</span></div>
					@endforeach
				</div>
			@else
				<div class="text-gray-500 text-sm">No equipment requested</div>
			@endif
		</div>
	</div>
</div>
@endsection 