@extends('layouts.drjavier')

@section('title', 'Dashboard - OTP')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Final approval authority for reservations')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-maroon to-red-800 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Welcome, OTP</h1>
                <p class="text-red-100">Office of the President - Final Approval Authority</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ now()->format('g:i A') }}</div>
                <div class="text-red-100">{{ now()->format('l, F d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-blue-50 p-3 mr-4">
                    <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Pending Review</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-green-50 p-3 mr-4">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Approved Today</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-red-50 p-3 mr-4">
                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Rejected Today</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['rejected'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-purple-50 p-3 mr-4">
                    <i class="fas fa-chart-line text-purple-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total This Month</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-bolt text-maroon mr-2"></i>
                Quick Actions
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('drjavier.reservations.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-calendar-check text-blue-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-medium text-gray-800">Review Reservations</h3>
                        <p class="text-sm text-gray-600">View pending approvals</p>
                    </div>
                </a>
                
                <a href="{{ route('drjavier.reservations.index', ['status' => 'pending']) }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-medium text-gray-800">Pending Reviews</h3>
                        <p class="text-sm text-gray-600">{{ $stats['pending'] }} awaiting approval</p>
                    </div>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-chart-bar text-green-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-medium text-gray-800">View Reports</h3>
                        <p class="text-sm text-gray-600">Analytics and insights</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Mhadel Approved Reservations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list-alt text-maroon mr-2"></i>
                    Recent Mhadel Approved Reservations
                </h2>
                <a href="{{ route('drjavier.reservations.index') }}" class="text-sm text-maroon hover:text-red-700 font-medium">
                    View All
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($recent_reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_reservations as $reservation)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-maroon rounded-full flex items-center justify-center text-white font-medium mr-4">
                                    {{ substr($reservation->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $reservation->event_title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $reservation->user->name }} • {{ $reservation->start_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    Pending Final Approval
                                </span>
                                <a href="{{ route('drjavier.reservations.show', $reservation->id) }}" class="text-sm text-maroon hover:text-red-700 font-medium">
                                    Review
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Pending Approvals</h3>
                    <p class="text-gray-500">All Mhadel approved reservations have been reviewed.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Workflow Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-800 mb-2">Approval Workflow</h3>
                <div class="space-y-2 text-blue-700 text-sm">
                    <p><strong>Step 1:</strong> User submits reservation</p>
                    <p><strong>Step 2:</strong> IOSA reviews and approves</p>
                    <p><strong>Step 3:</strong> Ms. Mhadel reviews and approves</p>
                    <p><strong>Step 4:</strong> <strong>OTP (Office of the President) - Final Approval</strong></p>
                </div>
                <p class="text-blue-600 font-medium mt-3">You are the final authority for all reservation approvals.</p>
            </div>
        </div>
    </div>
</div>
@endsection 