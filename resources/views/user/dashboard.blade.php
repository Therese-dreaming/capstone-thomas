@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-maroon rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-600">Manage your venue reservations and stay updated with your events.</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Reservations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reservations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $reservations->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Reservations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $reservations->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Reservations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $reservations->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Reservations</h2>
        </div>
        <div class="p-6">
            @if($reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($reservations as $reservation)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800">{{ $reservation->event_title }}</h3>
                                <p class="text-sm text-gray-600">{{ $reservation->venue->name ?? 'No venue' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }}
                                </p>

                            </div>
                            <div class="flex items-center space-x-2">
                                @switch($reservation->status)
                                    @case('pending')
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            Pending
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            Rejected
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-600">No reservations yet</p>
                    <a href="{{ route('user.reservations.index') }}" class="inline-block mt-4 px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                        Make Your First Reservation
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('user.reservations.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-maroon hover:bg-maroon hover:text-white transition-colors">
                <i class="fas fa-plus-circle text-xl mr-3"></i>
                <div>
                    <h3 class="font-medium">New Reservation</h3>
                    <p class="text-sm opacity-75">Book a venue for your event</p>
                </div>
            </a>
            <a href="{{ route('user.profile') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-maroon hover:bg-maroon hover:text-white transition-colors">
                <i class="fas fa-user-cog text-xl mr-3"></i>
                <div>
                    <h3 class="font-medium">Update Profile</h3>
                    <p class="text-sm opacity-75">Manage your account settings</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection 