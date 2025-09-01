@extends('layouts.gsu')

@section('title', 'GSU Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Final Approved Reservations Overview')

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-inter { font-family: 'Inter', sans-serif; }
    
    .stats-card {
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #3b82f6;
    }
    
    .reservation-item {
        transition: all 0.2s ease-in-out;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .reservation-item:hover {
        transform: translateX(2px);
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .glass-effect {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="space-y-6 font-inter animate-fadeIn">
    <!-- Welcome Section -->
    <div class="glass-effect rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 bg-blue-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center font-poppins mb-2">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        Welcome to GSU Dashboard
                    </h1>
                    <p class="text-base text-gray-600 font-medium">Monitor and manage final approved reservations with comprehensive insights</p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 mb-1">Last Updated</div>
                    <div class="text-base font-semibold text-gray-700">{{ now()->format('M d, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-200 flex items-center group">
            <div class="rounded-lg bg-green-500 p-3 mr-3 group-hover:scale-105 transition-transform duration-300">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Total Final Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] ?? 0 }}</h3>
                <p class="text-xs text-green-600 font-medium mt-1">All time approved</p>
            </div>
        </div>
        
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-200 flex items-center group">
            <div class="rounded-lg bg-blue-500 p-3 mr-3 group-hover:scale-105 transition-transform duration-300">
                <i class="fas fa-calendar-day text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Approved Today</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved_today'] ?? 0 }}</h3>
                <p class="text-xs text-blue-600 font-medium mt-1">Today's approvals</p>
            </div>
        </div>
        
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-200 flex items-center group">
            <div class="rounded-lg bg-purple-500 p-3 mr-3 group-hover:scale-105 transition-transform duration-300">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">This Month</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_month'] ?? 0 }}</h3>
                <p class="text-xs text-purple-600 font-medium mt-1">Monthly total</p>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-emerald-600 font-medium">Success Rate</p>
                    <p class="text-base font-bold text-emerald-800">
                        {{ $stats['approved'] > 0 ? round(($stats['approved'] / max(($stats['approved'] + ($stats['rejected'] ?? 0)), 1)) * 100) : 0 }}%
                    </p>
                </div>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-amber-600 font-medium">Pending Review</p>
                    <p class="text-base font-bold text-amber-800">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-lg p-3 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-red-600 font-medium">Rejected</p>
                    <p class="text-base font-bold text-red-800">{{ $stats['rejected'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-indigo-600 font-medium">Total Users</p>
                    <p class="text-base font-bold text-indigo-800">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="glass-effect rounded-xl shadow-lg overflow-hidden">
        <!-- Enhanced Header -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center font-poppins mb-1">
                        <div class="w-8 h-8 bg-maroon-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-list-alt text-grey-800 text-base"></i>
                        </div>
                        Recent Final Approved Reservations
                    </h2>
                    <p class="text-sm text-gray-600 font-medium">Latest reservations that have been approved and finalized</p>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('gsu.reservations.index') }}" 
                       class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-600 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2 text-sm">
                        <i class="fas fa-list mr-1.5"></i>
                        <span>View All</span>
                    </a>
                    <button onclick="refreshData()" 
                            class="p-2 bg-white text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-gray-200 hover:shadow-md">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Reservations List -->
        <div class="p-6">
            @if(($recent ?? collect())->count() > 0)
                <div class="space-y-3">
                    @foreach($recent as $reservation)
                        <div class="reservation-item rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-bold text-base text-gray-800 group-hover:text-maroon-600 transition-colors duration-200">
                                            {{ $reservation->event_title }}
                                        </h4>
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Final Approved
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-maroon-500 mr-1.5 w-3"></i>
                                            <span class="font-medium">User:</span>
                                            <span class="ml-1">{{ $reservation->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-maroon-500 mr-1.5 w-3"></i>
                                            <span class="font-medium">Date:</span>
                                            <span class="ml-1">{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-maroon-500 mr-1.5 w-3"></i>
                                            <span class="font-medium">Time:</span>
                                            <span class="ml-1">{{ $reservation->start_date->format('g:i A') }} - {{ $reservation->end_date->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($reservation->venue)
                                        <div class="mt-2 flex items-center text-xs text-gray-600 bg-blue-50 px-2 py-1 rounded-md w-fit">
                                            <i class="fas fa-map-marker-alt mr-1.5 text-blue-500"></i>
                                            <span class="font-medium">{{ $reservation->venue->name }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($reservation->purpose)
                                        <div class="mt-2 bg-gray-50 p-2 rounded-md border-l-4 border-maroon-500">
                                            <p class="text-xs text-gray-700 leading-relaxed">{{ Str::limit($reservation->purpose, 120) }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col items-end space-y-2 ml-4">
                                    <a href="{{ route('gsu.reservations.show', $reservation->id) }}" 
                                       class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-600 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2 text-sm">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Details</span>
                                    </a>
                                    
                                    <div class="text-xs text-gray-500 text-right">
                                        <div>Approved: {{ $reservation->updated_at->format('M d, Y') }}</div>
                                        <div>ID: #{{ $reservation->id }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- View All Button -->
                <div class="mt-6 text-center">
                    <a href="{{ route('gsu.reservations.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm">
                        <i class="fas fa-arrow-right mr-2"></i>
                        View All Reservations
                    </a>
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No reservations found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">There are currently no final approved reservations to display. New approvals will appear here automatically.</p>
                    <div class="flex items-center justify-center space-x-3">
                        <button onclick="refreshData()" 
                                class="inline-flex items-center px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 font-medium text-sm">
                            <i class="fas fa-sync-alt mr-1.5"></i> Refresh Data
                        </button>
                        <a href="{{ route('gsu.reservations.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-gray-200 font-medium text-sm">
                            <i class="fas fa-list mr-1.5"></i> Browse All
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-chart-bar text-maroon-500 mr-2"></i>
                Weekly Overview
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">This Week</span>
                    <span class="font-semibold text-gray-800">{{ $stats['this_week'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Last Week</span>
                    <span class="font-semibold text-gray-800">{{ $stats['last_week'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Change</span>
                    <span class="font-semibold {{ ($stats['this_week'] ?? 0) >= ($stats['last_week'] ?? 0) ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($stats['this_week'] ?? 0) >= ($stats['last_week'] ?? 0) ? '+' : '' }}{{ ($stats['this_week'] ?? 0) - ($stats['last_week'] ?? 0) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-tasks text-maroon-500 mr-2"></i>
                System Status
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Active Users</span>
                    <span class="font-semibold text-green-600">{{ $stats['active_users'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Pending Reviews</span>
                    <span class="font-semibold text-amber-600">{{ $stats['pending'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">System Health</span>
                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>
                        Optimal
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Auto-refresh data every 5 minutes
        setInterval(refreshData, 300000);
    });

    // Function to refresh dashboard data
    function refreshData() {
        const refreshBtn = document.querySelector('button[onclick="refreshData()"]');
        if (refreshBtn) {
            const originalContent = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Refreshing...';
            refreshBtn.disabled = true;
            
            // Simulate refresh (you can implement actual AJAX call here)
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed bottom-4 right-4 z-50 flex flex-col items-end';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `flex items-center p-4 mb-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out translate-x-full`;
        
        // Set background color based on type
        if (type === 'success') {
            toast.classList.add('bg-green-500', 'text-white');
        } else if (type === 'error') {
            toast.classList.add('bg-red-500', 'text-white');
        } else {
            toast.classList.add('bg-blue-500', 'text-white');
        }
        
        // Set icon based on type
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
        
        toast.innerHTML = `
            <div class="flex-shrink-0 mr-3">
                <i class="fas fa-${icon} text-xl"></i>
            </div>
            <div class="flex-1 font-poppins">
                ${message}
            </div>
            <div class="ml-3 flex-shrink-0">
                <button class="text-white focus:outline-none" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }
</script>
@endsection 