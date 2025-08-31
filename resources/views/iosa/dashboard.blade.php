@extends('layouts.iosa')

@section('title', 'IOSA Dashboard')
@section('page-title', 'IOSA Dashboard')
@section('page-subtitle', 'Reservation Approval Management & Analytics')

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('styles')
<style>
    .stat-card { 
        transition: all 0.3s ease; 
        background: white;
        border: 1px solid #e5e7eb;
    }
    .stat-card:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: #8B1818;
    }
    .action-card { 
        transition: all 0.3s ease; 
        background: white;
        border: 1px solid #e5e7eb;
        color: #374151;
    }
    .action-card:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: #8B1818;
        background-color: #fef2f2;
    }
    .upcoming-card {
        background: white;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .upcoming-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: #8B1818;
    }
    .chart-card {
        background: white;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .chart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: #8B1818;
    }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(20px);} 
        to { opacity: 1; transform: translateY(0);} 
    }
    .animate-fade-in { animation: fadeIn 0.6s ease-in-out; }
    
    .gradient-text {
        background: linear-gradient(135deg, #8B1818 0%, #a52a2a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-upcoming { background: #dbeafe; color: #1e40af; }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #8B1818;
    }
    
    .metric-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .bg-maroon { background-color: #8B1818; }
    .text-maroon { color: #8B1818; }
    .border-maroon { border-color: #8B1818; }
</style>
@endsection

@section('content')
<div class="space-y-6 font-poppins animate-fade-in">
    <!-- Welcome Section -->
    <div class="bg-maroon rounded-xl shadow-lg border border-gray-100 p-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2 font-montserrat text-white">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-white text-lg">Here's what's happening with venue reservations today</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-white">{{ date('M d') }}</div>
                <div class="text-white text-lg">{{ date('l, Y') }}</div>
                <div class="text-white text-sm">{{ date('g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="metric-label">Pending Approvals</p>
                    <h3 class="metric-value">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
                <div class="rounded-full bg-maroon bg-opacity-10 p-4">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="metric-label">Approved Today</p>
                    <h3 class="metric-value text-green-600">{{ $stats['approved_today'] ?? 0 }}</h3>
                </div>
                <div class="rounded-full bg-green-100 p-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="metric-label">Rejected Today</p>
                    <h3 class="metric-value text-red-600">{{ $stats['rejected_today'] ?? 0 }}</h3>
                </div>
                <div class="rounded-full bg-red-100 p-4">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="metric-label">Total This Month</p>
                    <h3 class="metric-value text-purple-600">{{ $stats['total_month'] ?? 0 }}</h3>
                </div>
                <div class="rounded-full bg-purple-100 p-4">
                    <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Reservation Status Chart -->
        <div class="chart-card rounded-xl shadow-lg p-6">
            <div class="p-4 bg-maroon rounded-t-lg mb-6">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-chart-pie text-white mr-2"></i>
                    Reservation Status Distribution
                </h3>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="chart-card rounded-xl shadow-lg p-6">
            <div class="p-4 bg-maroon rounded-t-lg mb-6">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-chart-line text-white mr-2"></i>
                    Monthly Reservation Trends
                </h3>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Events Status Chart -->
        <div class="chart-card rounded-xl shadow-lg p-6">
            <div class="p-4 bg-maroon rounded-t-lg mb-6">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-calendar-alt text-white mr-2"></i>
                    Events Status Distribution
                </h3>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="eventsChart"></canvas>
            </div>
        </div>

        <!-- Departments Chart -->
        <div class="chart-card rounded-xl shadow-lg p-6">
            <div class="p-4 bg-maroon rounded-t-lg mb-6">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-building text-white mr-2"></i>
                    Top Departments by Reservations
                </h3>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="departmentsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Events Trend Chart -->
    <div class="chart-card rounded-xl shadow-lg p-6">
        <div class="p-4 bg-maroon rounded-t-lg mb-6">
            <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                <i class="fas fa-chart-area text-white mr-2"></i>
                Monthly Events Trends
            </h3>
        </div>
        <div class="relative" style="height: 300px;">
            <canvas id="eventsTrendChart"></canvas>
        </div>
    </div>

    <!-- Upcoming Reservations & Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Reservations -->
        <div class="upcoming-card rounded-xl shadow-lg">
            <div class="p-6 bg-maroon rounded-t-lg">
                <h3 class="text-xl font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-calendar-check text-white mr-3"></i>
                    Upcoming Reservations
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($upcoming_reservations ?? [] as $reservation)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-lg">{{ $reservation->event_title }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $reservation->user->name }}</p>
                                    <p class="text-gray-500 text-xs">
                                        {{ $reservation->start_date->format('M d, Y') }} • 
                                        {{ $reservation->start_date->format('g:i A') }} - {{ $reservation->end_date->format('g:i A') }}
                                    </p>
                                    <p class="text-gray-500 text-xs">{{ $reservation->venue->name ?? 'Venue TBD' }}</p>
                                </div>
                                <div class="text-right">
                                    @if($reservation->status === 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($reservation->status === 'approved_IOSA')
                                        <span class="status-badge status-approved">IOSA Approved</span>
                                    @elseif($reservation->status === 'rejected_IOSA')
                                        <span class="status-badge status-rejected">IOSA Rejected</span>
                                    @else
                                        <span class="status-badge status-upcoming">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No upcoming reservations</p>
                            <p class="text-gray-400 text-sm">All caught up!</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-6 text-center">
                    <a href="{{ route('iosa.reservations.index') }}" class="bg-maroon text-white px-6 py-3 rounded-xl hover:bg-red-800 transition-all duration-300 font-medium">
                        View All Reservations →
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="upcoming-card rounded-xl shadow-lg">
            <div class="p-6 bg-maroon rounded-t-lg">
                <h3 class="text-xl font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-star text-white mr-3"></i>
                    Upcoming Events
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($upcoming_events ?? [] as $event)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-lg">{{ $event->title }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $event->organizer }}</p>
                                    <p class="text-gray-500 text-xs">
                                        {{ $event->start_date->format('M d, Y') }} • 
                                        {{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                    </p>
                                    <p class="text-gray-500 text-xs">{{ $event->venue->name ?? 'Venue TBD' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="status-badge status-upcoming">{{ ucfirst($event->status) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-plus text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No upcoming events</p>
                            <p class="text-gray-400 text-sm">Check back later!</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-6 text-center">
                    <a href="#" class="bg-maroon text-white px-6 py-3 rounded-xl hover:bg-red-800 transition-all duration-300 font-medium">
                        View All Events →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 bg-maroon rounded-t-lg">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-history text-white mr-2"></i>
                    Recent Activity
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recent_reservations ?? [] as $reservation)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-maroon bg-opacity-10 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar text-maroon"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $reservation->event_title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $reservation->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $reservation->start_date->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                            @if($reservation->status === 'pending')
                                <span class="status-badge status-pending">Pending</span>
                            @elseif($reservation->status === 'approved_IOSA')
                                <span class="status-badge status-approved">IOSA Approved</span>
                            @elseif($reservation->status === 'rejected_IOSA')
                                <span class="status-badge status-rejected">IOSA Rejected</span>
                            @else
                                <span class="status-badge status-upcoming">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No recent activity</p>
                            <p class="text-gray-400 text-sm">All reservations are up to date</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 bg-maroon rounded-t-lg">
                <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                    <i class="fas fa-bolt text-white mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('iosa.reservations.index') }}" class="action-card flex flex-col items-center p-6 rounded-xl hover:scale-105 transition-all duration-300">
                        <i class="fas fa-calendar-check text-maroon text-3xl mb-3"></i>
                        <span class="text-sm font-medium">Review Pending</span>
                        <span class="text-xs text-gray-500 mt-1">Manage approvals</span>
                    </a>
                    
                    <a href="#" class="action-card flex flex-col items-center p-6 rounded-xl hover:scale-105 transition-all duration-300">
                        <i class="fas fa-chart-bar text-green-600 text-3xl mb-3"></i>
                        <span class="text-sm font-medium">Analytics</span>
                        <span class="text-xs text-gray-500 mt-1">View reports</span>
                    </a>
                    
                    <a href="#" class="action-card flex flex-col items-center p-6 rounded-xl hover:scale-105 transition-all duration-300">
                        <i class="fas fa-cog text-purple-600 text-3xl mb-3"></i>
                        <span class="text-sm font-medium">Settings</span>
                        <span class="text-xs text-gray-500 mt-1">Configure</span>
                    </a>
                    
                    <a href="#" class="action-card flex flex-col items-center p-6 rounded-xl hover:scale-105 transition-all duration-300">
                        <i class="fas fa-user text-blue-600 text-3xl mb-3"></i>
                        <span class="text-sm font-medium">Profile</span>
                        <span class="text-xs text-gray-500 mt-1">Account</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 bg-maroon rounded-t-lg">
            <h3 class="text-lg font-bold text-white flex items-center font-montserrat">
                <i class="fas fa-info-circle text-white mr-2"></i>
                System Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-maroon rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Role</h4>
                    <p class="text-maroon">IOSA Administrator</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-key text-white text-2xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Permissions</h4>
                    <p class="text-green-600">Reservation Approval</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Last Login</h4>
                    <p class="text-blue-600">{{ date('M d, Y H:i') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-server text-white text-2xl"></i>
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">System Status</h4>
                    <p class="text-purple-600">All Systems Operational</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'IOSA Approved', 'IOSA Rejected', 'Mhadel Approved', 'OTP Approved', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusDistribution['pending'] ?? 0 }}, 
                    {{ $statusDistribution['approved_IOSA'] ?? 0 }}, 
                    {{ $statusDistribution['rejected_IOSA'] ?? 0 }}, 
                    {{ $statusDistribution['approved_mhadel'] ?? 0 }},
                    {{ $statusDistribution['approved_OTP'] ?? 0 }},
                    {{ $statusDistribution['cancelled'] ?? 0 }}
                ],
                backgroundColor: [
                    '#f59e0b',
                    '#10b981',
                    '#ef4444',
                    '#3b82f6',
                    '#8b5cf6',
                    '#6b7280'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Monthly Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Reservations',
                data: {!! json_encode($monthlyTrends ?? [0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#8B1818',
                backgroundColor: 'rgba(139, 24, 24, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });

    // Events Status Chart
    const eventsCtx = document.getElementById('eventsChart').getContext('2d');
    new Chart(eventsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $eventsData['upcoming'] ?? 0 }}, 
                    {{ $eventsData['ongoing'] ?? 0 }}, 
                    {{ $eventsData['completed'] ?? 0 }}, 
                    {{ $eventsData['cancelled'] ?? 0 }}
                ],
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#8b5cf6',
                    '#ef4444'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Departments Chart
    const departmentsCtx = document.getElementById('departmentsChart').getContext('2d');
    const departmentsLabels = {!! json_encode(array_keys($departmentsData ?? [])) !!};
    const departmentsValues = {!! json_encode(array_values($departmentsData ?? [])) !!};
    
    if (departmentsLabels.length > 0) {
        new Chart(departmentsCtx, {
            type: 'bar',
            data: {
                labels: departmentsLabels,
                datasets: [{
                    label: 'Reservations',
                    data: departmentsValues,
                    backgroundColor: '#8B1818',
                    borderColor: '#8B1818',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    } else {
        // Empty state design for departments chart
        const departmentsContainer = document.getElementById('departmentsChart').parentElement;
        departmentsContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-center py-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-building text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">No Department Data</h4>
                <p class="text-gray-500 text-sm">Department information will appear here once reservations are made</p>
            </div>
        `;
    }

    // Monthly Events Trend Chart
    const eventsTrendCtx = document.getElementById('eventsTrendChart').getContext('2d');
    const eventsTrendData = {!! json_encode($monthlyEventsTrends ?? [0, 0, 0, 0, 0, 0]) !!};
    
    if (eventsTrendData.some(value => value > 0)) {
        new Chart(eventsTrendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
                datasets: [{
                    label: 'Events',
                    data: eventsTrendData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    } else {
        // Empty state design for events trend chart
        const eventsTrendContainer = document.getElementById('eventsTrendChart').parentElement;
        eventsTrendContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-center py-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-chart-area text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">No Events Data</h4>
                <p class="text-gray-500 text-sm">Event trends will appear here once events are scheduled</p>
            </div>
        `;
    }
});
</script>
@endsection
    