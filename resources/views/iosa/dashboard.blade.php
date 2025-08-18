@extends('layouts.iosa')

@section('title', 'IOSA Dashboard')
@section('page-title', 'IOSA Dashboard')
@section('page-subtitle', 'Reservation Approval Management')

@section('styles')
<style>
	.stat-card { transition: all 0.3s ease; }
	.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
	.action-card { transition: all 0.3s ease; }
	.action-card:hover { transform: translateY(-3px); }
	@keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
	.animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
</style>
@endsection

@section('content')
<div class="space-y-6 font-poppins animate-fade-in">
	<!-- Welcome Section -->
	<div class="bg-white border border-gray-100 rounded-xl shadow-md p-6">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-2xl font-bold mb-1 font-montserrat text-gray-800">Welcome, {{ auth()->user()->name }}!</h2>
				<p class="text-gray-600">You are logged in as IOSA. Manage venue reservation approvals here.</p>
			</div>
			<div class="text-right">
				<div class="text-3xl font-bold text-gray-800">{{ date('M d, Y') }}</div>
				<div class="text-gray-600">{{ date('l') }}</div>
			</div>
		</div>
	</div>

	<!-- Quick Stats -->
	<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 stat-card">
			<div class="flex items-center">
				<div class="rounded-full bg-blue-50 p-3 mr-4">
					<i class="fas fa-clock text-blue-500 text-xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 font-medium">Pending Approvals</p>
					<h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
				</div>
			</div>
		</div>
		
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 stat-card">
			<div class="flex items-center">
				<div class="rounded-full bg-green-50 p-3 mr-4">
					<i class="fas fa-check-circle text-green-500 text-xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 font-medium">Approved Today</p>
					<h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved_today'] }}</h3>
				</div>
			</div>
		</div>
		
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 stat-card">
			<div class="flex items-center">
				<div class="rounded-full bg-red-50 p-3 mr-4">
					<i class="fas fa-times-circle text-red-500 text-xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 font-medium">Rejected Today</p>
					<h3 class="text-2xl font-bold text-gray-800">{{ $stats['rejected_today'] }}</h3>
				</div>
			</div>
		</div>
		
		<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 stat-card">
			<div class="flex items-center">
				<div class="rounded-full bg-purple-50 p-3 mr-4">
					<i class="fas fa-calendar-alt text-purple-500 text-xl"></i>
				</div>
				<div>
					<p class="text-sm text-gray-500 font-medium">Total This Month</p>
					<h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_month'] }}</h3>
				</div>
			</div>
		</div>
	</div>

	<!-- Recent Activity -->
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
		<!-- Recent Reservations -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100">
			<div class="p-6 border-b border-gray-200">
				<h3 class="text-lg font-semibold text-gray-800 flex items-center font-montserrat">
					<i class="fas fa-calendar-check text-gray-700 mr-2"></i>
					Recent Reservations
				</h3>
			</div>
			<div class="p-6">
				<div class="space-y-4">
					@forelse($recent_reservations as $reservation)
						<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
							<div>
								<h4 class="font-medium text-gray-800">{{ $reservation->event_title }}</h4>
								<p class="text-sm text-gray-600">{{ $reservation->user->name }}</p>
								<p class="text-xs text-gray-500">{{ $reservation->start_date->format('M d, Y') }} • {{ $reservation->start_date->format('g:i A') }} - {{ $reservation->end_date->format('g:i A') }}</p>
							</div>
							@if($reservation->status === 'pending')
								<span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Pending</span>
							@elseif($reservation->status === 'approved_IOSA')
								<span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">IOSA Approved</span>
							@elseif($reservation->status === 'rejected_IOSA')
								<span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">IOSA Rejected</span>
							@else
								<span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">{{ ucfirst($reservation->status) }}</span>
							@endif
						</div>
					@empty
						<div class="text-center py-4">
							<p class="text-gray-500 text-sm">No recent reservations</p>
						</div>
					@endforelse
				</div>
				
				<div class="mt-4 text-center">
					<a href="{{ route('iosa.reservations.index') }}" class="text-gray-800 hover:text-black font-medium">
						View All Reservations →
					</a>
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100">
			<div class="p-6 border-b border-gray-200">
				<h3 class="text-lg font-semibold text-gray-800 flex items-center font-montserrat">
					<i class="fas fa-bolt text-gray-700 mr-2"></i>
					Quick Actions
				</h3>
			</div>
			<div class="p-6">
				<div class="grid grid-cols-2 gap-4">
					<a href="{{ route('iosa.reservations.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors action-card">
						<i class="fas fa-calendar-check text-blue-500 text-2xl mb-2"></i>
						<span class="text-sm font-medium text-gray-700">Review Pending</span>
					</a>
					
					<a href="#" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors action-card">
						<i class="fas fa-chart-bar text-green-500 text-2xl mb-2"></i>
						<span class="text-sm font-medium text-gray-700">View Reports</span>
					</a>
					
					<a href="#" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors action-card">
						<i class="fas fa-cog text-purple-500 text-2xl mb-2"></i>
						<span class="text-sm font-medium text-gray-700">Settings</span>
					</a>
					
					<a href="#" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors action-card">
						<i class="fas fa-user text-orange-500 text-2xl mb-2"></i>
						<span class="text-sm font-medium text-gray-700">Profile</span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- System Information -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100">
		<div class="p-6 border-b border-gray-200">
			<h3 class="text-lg font-semibold text-gray-800 flex items-center font-montserrat">
				<i class="fas fa-info-circle text-gray-700 mr-2"></i>
				System Information
			</h3>
		</div>
		<div class="p-6">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div>
					<h4 class="font-medium text-gray-800 mb-2">Role</h4>
					<p class="text-gray-600">IOSA Administrator</p>
				</div>
				<div>
					<h4 class="font-medium text-gray-800 mb-2">Permissions</h4>
					<p class="text-gray-600">Reservation Approval & Management</p>
				</div>
				<div>
					<h4 class="font-medium text-gray-800 mb-2">Last Login</h4>
					<p class="text-gray-600">{{ date('M d, Y H:i') }}</p>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection 