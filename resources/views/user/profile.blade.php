@extends('layouts.user')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 font-poppins">
	<!-- Profile Header -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
		<div class="flex items-start justify-between">
			<div class="flex items-center space-x-4">
				<div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center text-white">
					<i class="fas fa-user text-2xl"></i>
				</div>
				<div>
					<h3 class="text-2xl font-semibold text-gray-900 font-montserrat">{{ $user->name }}</h3>
					<p class="text-gray-600">{{ $user->email }}</p>
					<p class="text-sm text-gray-500">Member since {{ $user->created_at->format('M Y') }}</p>
				</div>
			</div>
			<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-yellow-100 text-yellow-700 border border-yellow-300' }}">
				<i class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-2"></i>
				{{ $user->email_verified_at ? 'Email Verified' : 'Not Verified' }}
			</span>
		</div>

		<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
			<div>
				<div class="text-xs text-gray-500">Role</div>
				<div class="text-gray-800">{{ ucfirst($user->role ?? 'user') }}</div>
			</div>
			<div>
				<div class="text-xs text-gray-500">Last Updated</div>
				<div class="text-gray-800">{{ $user->updated_at?->format('M d, Y') }}</div>
			</div>
		</div>
	</div>

	<!-- Account Statistics -->
	@php
		$totalCount = $user->reservations()->count();
		$pendingCount = $user->reservations()->whereIn('status', ['pending','approved_IOSA','approved_mhadel'])->count();
		$approvedCount = $user->reservations()->whereIn('status', ['approved','approved_OTP'])->count();
		$rejectedCount = $user->reservations()->whereIn('status', ['rejected','rejected_OTP'])->count();
	@endphp
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
		<h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Account Statistics</h2>
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<div class="text-center p-4 bg-blue-50 rounded-lg">
				<div class="text-2xl font-bold text-blue-600">{{ $totalCount }}</div>
				<div class="text-sm text-gray-600">Total</div>
			</div>
			<div class="text-center p-4 bg-yellow-50 rounded-lg">
				<div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
				<div class="text-sm text-gray-600">Pending/In Review</div>
			</div>
			<div class="text-center p-4 bg-green-50 rounded-lg">
				<div class="text-2xl font-bold text-green-600">{{ $approvedCount }}</div>
				<div class="text-sm text-gray-600">Approved</div>
			</div>
			<div class="text-center p-4 bg-red-50 rounded-lg">
				<div class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</div>
				<div class="text-sm text-gray-600">Rejected</div>
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
		<h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Quick Actions</h2>
		<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
			<a href="{{ route('user.reservations.calendar') }}" class="flex items-center p-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
				<div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
					<i class="fas fa-calendar-plus"></i>
				</div>
				<div>
					<h3 class="font-medium">Make New Reservation</h3>
					<p class="text-sm opacity-80">Book a venue for your event</p>
				</div>
			</a>
			<a href="{{ route('user.reservations.index') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
				<div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
					<i class="fas fa-book-open text-gray-700"></i>
				</div>
				<div>
					<h3 class="font-medium text-gray-800">View My Reservations</h3>
					<p class="text-sm text-gray-600">See your history and status</p>
				</div>
			</a>
			@if(!$user->email_verified_at)
			<form action="{{ route('verification.send') }}" method="POST" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
				@csrf
				<button type="submit" class="flex items-center w-full text-left">
					<div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
						<i class="fas fa-envelope text-gray-700"></i>
					</div>
					<div>
						<h3 class="font-medium text-gray-800">Resend Verification Email</h3>
						<p class="text-sm text-gray-600">Verify your email address</p>
					</div>
				</button>
			</form>
			@endif
		</div>
	</div>
</div>
@endsection 