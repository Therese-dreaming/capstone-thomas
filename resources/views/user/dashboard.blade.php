@extends('layouts.iosa')

@section('title', 'IOSA Dashboard')
@section('page-title', 'IOSA Dashboard')
@section('page-subtitle', 'Reservation Approval Management')

@section('styles')
<style>
    .stat-card { 
        transition: all 0.3s ease; 
    }
    .stat-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); 
    }
    .action-card { 
        transition: all 0.3s ease; 
    }
    .action-card:hover { 
        transform: translateY(-3px); 
    }
    .reservation-item {
        transition: all 0.2s ease;
    }
    .reservation-item:hover {
        background-color: #f3f4f6;
    }
    .status-badge {
        transition: all 0.3s ease;
    }
    .status-badge:hover {
        transform: scale(1.05);
    }
    .chart-container {
        height: 250px;
    }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(10px);} 
        to { opacity: 1; transform: translateY(0);} 
    }
    .animate-fade-in { 
        animation: fadeIn 0.5s ease-in-out; 
    }
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        background-color: #e5e7eb;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease-in-out;
    }
    .notification-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="space-y-6 font-poppins animate-fade-in">
    <!-- Welcome Section with Date -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl shadow-md p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold font-montserrat">Welcome, {{ auth()->user()->name }}!</h1>
                    <p class="text-white/80">You are logged in as IOSA. Manage venue reservation approvals here.</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 text-right">
                <div class="text-white/80 text-sm">Today is</div>
                <div class="text-xl font-semibold">{{ date('F d, Y') }}</div>
                <div class="text-white/80 text-sm">{{ date('l') }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <!-- Pending Approvals -->
                <div class="bg-white rounded-xl shadow-sm p-5 stat-card border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['pending'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Awaiting review
                        </span>
                    </div>
                </div>

                <!-- Approved Today -->
                <div class="bg-white rounded-xl shadow-sm p-5 stat-card border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Approved Today</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved_today'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-thumbs-up mr-1"></i> Approved by you
                        </span>
                    </div>
                </div>

                <!-- Rejected Today -->
                <div class="bg-white rounded-xl shadow-sm p-5 stat-card border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Rejected Today</p>
                            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected_today'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-thumbs-down mr-1"></i> Rejected by you
                        </span>
                    </div>
                </div>

                <!-- Total This Month -->
                <div class="bg-white rounded-xl shadow-sm p-5 stat-card border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total This Month</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_month'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-chart-line mr-1"></i> All reservations
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800 font-montserrat flex items-center">
                        <i class="fas fa-hourglass-half text-blue-600 mr-2"></i>
                        Pending Approvals
                    </h2>
                    <a href="{{ route('iosa.reservations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <span>View All</span>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-5">
                    @php
                        $pendingReservations = \App\Models\Reservation::where('status', 'pending')
                            ->with(['user', 'venue'])
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();
                    @endphp
                    
                    @if($pendingReservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingReservations as $reservation)
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 reservation-item">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-800">{{ $reservation->event_title }}</h3>
                                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                                <i class="fas fa-user mr-1 text-gray-400"></i>
                                                <span>{{ $reservation->user->name }}</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                                <span>{{ $reservation->venue->name ?? 'No venue' }}</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                                <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                                <span>{{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}</span>
                                                <span class="mx-1">•</span>
                                                <i class="fas fa-clock mr-1 text-gray-400"></i>
                                                <span>{{ $reservation->start_date ? $reservation->start_date->format('g:i A') : '' }} - {{ $reservation->end_date ? $reservation->end_date->format('g:i A') : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('iosa.reservations.show', $reservation->id) }}" class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('iosa.reservations.index') }}" class="inline-block text-sm text-blue-600 hover:text-blue-800">
                                View all pending approvals <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-blue-500 text-2xl"></i>
                            </div>
                            <p class="text-gray-600">No pending approvals</p>
                            <p class="text-sm text-gray-500 mt-1">All reservations have been reviewed</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800 font-montserrat flex items-center">
                        <i class="fas fa-history text-gray-700 mr-2"></i>
                        Recent Activity
                    </h2>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                        <span>View All</span>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recent_reservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $reservation->event_title }}</div>
                                        <div class="text-xs text-gray-500">{{ $reservation->venue->name ?? 'No venue' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $reservation->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            {{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $reservation->start_date ? $reservation->start_date->format('g:i A') : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reservation->status === 'pending')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full status-badge">
                                                <i class="fas fa-clock mr-1"></i> Pending
                                            </span>
                                        @elseif($reservation->status === 'approved_IOSA')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full status-badge">
                                                <i class="fas fa-check-circle mr-1"></i> IOSA Approved
                                            </span>
                                        @elseif($reservation->status === 'rejected_IOSA')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full status-badge">
                                                <i class="fas fa-times-circle mr-1"></i> IOSA Rejected
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full status-badge">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('iosa.reservations.show', $reservation->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            View <i class="fas fa-eye ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column (1/3 width) -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Quick Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('iosa.reservations.index') }}" class="flex items-center p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors action-card">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-tasks text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-medium">Review Pending</h3>
                            <p class="text-sm opacity-80">Process reservation requests</p>
                        </div>
                    </a>
                    
                    <a href="#" class="flex items-center p-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors action-card">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-medium">View Reports</h3>
                            <p class="text-sm opacity-80">Analyze reservation data</p>
                        </div>
                    </a>
                    
                    <a href="#" class="flex items-center p-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors action-card">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-medium">Calendar View</h3>
                            <p class="text-sm opacity-80">See scheduled events</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Approval Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Approval Progress</h2>
                
                @php
                    $totalToday = $stats['approved_today'] + $stats['rejected_today'] + $stats['pending'];
                    $approvedPercent = $totalToday > 0 ? round(($stats['approved_today'] / $totalToday) * 100) : 0;
                    $rejectedPercent = $totalToday > 0 ? round(($stats['rejected_today'] / $totalToday) * 100) : 0;
                    $pendingPercent = $totalToday > 0 ? round(($stats['pending'] / $totalToday) * 100) : 0;
                @endphp
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Approved</span>
                            <span class="text-sm font-medium text-gray-700">{{ $approvedPercent }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill bg-green-500" style="width: {{ $approvedPercent }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Rejected</span>
                            <span class="text-sm font-medium text-gray-700">{{ $rejectedPercent }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill bg-red-500" style="width: {{ $rejectedPercent }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">Pending</span>
                            <span class="text-sm font-medium text-gray-700">{{ $pendingPercent }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill bg-yellow-500" style="width: {{ $pendingPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">Notifications</h2>
                <div class="space-y-3">
                    @if($stats['pending'] > 0)
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-bell text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800">Pending Approvals</h3>
                                    <p class="text-xs text-gray-600 mt-1">You have {{ $stats['pending'] }} reservation(s) waiting for your review.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-week text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-800">Upcoming Events</h3>
                                <p class="text-xs text-gray-600 mt-1">There are {{ \App\Models\Reservation::whereDate('start_date', '>=', now())->whereDate('start_date', '<=', now()->addDays(7))->count() }} events scheduled in the next 7 days.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-800">Monthly Summary</h3>
                                <p class="text-xs text-gray-600 mt-1">This month's reservations are {{ $stats['total_month'] > 0 && isset($stats['last_month']) && $stats['last_month'] > 0 ? round((($stats['total_month'] - $stats['last_month']) / $stats['last_month']) * 100) : 0 }}% {{ $stats['total_month'] > ($stats['last_month'] ?? 0) ? 'higher' : 'lower' }} than last month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 font-montserrat">System Information</h2>
                <div class="space-y-3">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                            <i class="fas fa-user-shield text-gray-600"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Role</div>
                            <div class="text-sm font-medium text-gray-800">IOSA Administrator</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                            <i class="fas fa-key text-gray-600"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Permissions</div>
                            <div class="text-sm font-medium text-gray-800">Reservation Approval & Management</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-gray-600"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Last Login</div>
                            <div class="text-sm font-medium text-gray-800">{{ date('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress bars on load
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.progress-bar-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }, 300);
        
        // Add hover effects to action cards
        const actionCards = document.querySelectorAll('.action-card');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('transform', 'scale-105');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('transform', 'scale-105');
            });
        });
    });
</script>
@endsection