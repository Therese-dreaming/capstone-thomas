@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
    }
    
    .status-pending { 
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
        color: #92400E; 
        border-color: #f59e0b;
    }
    .status-approved { 
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); 
        color: #065F46; 
        border-color: #10B981;
    }
    .status-completed { 
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); 
        color: #374151; 
        border-color: #9CA3AF;
    }
    .status-rejected { 
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); 
        color: #991B1B; 
        border-color: #EF4444;
    }
    
    .metric-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e5e7eb;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .reservation-card { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    }
    .reservation-card:hover { 
        transform: translateY(-4px) scale(1.01); 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h2>
                <p class="text-gray-600">Here's an overview of your reservation activity.</p>
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

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="metric-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</h3>
                <p class="text-xs text-blue-600 font-medium mt-1">All time</p>
            </div>
        </div>
        
        <div class="metric-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] ?? 0 }}</h3>
                <p class="text-xs text-green-600 font-medium mt-1">Successfully booked</p>
            </div>
        </div>
        
        <div class="metric-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] ?? 0 }}</h3>
                <p class="text-xs text-amber-600 font-medium mt-1">Under review</p>
            </div>
        </div>
        
        <div class="metric-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-gray-500 to-gray-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Completed</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['completed'] ?? 0 }}</h3>
                <p class="text-xs text-gray-600 font-medium mt-1">Successfully finished</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                    Status Distribution
                </h3>
            </div>
            <div class="chart-container">
                @if(($stats['total'] ?? 0) > 0)
                    <canvas id="statusChart"></canvas>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-pie text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">No Data Available</h4>
                            <p class="text-sm text-gray-500 mb-4">You haven't made any reservations yet</p>
                            <a href="{{ route('user.reservations.calendar') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-plus mr-2"></i>
                                Make Your First Reservation
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Activity Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-green-600"></i>
                    Monthly Activity
                </h3>
            </div>
            <div class="chart-container">
                @if(($stats['total'] ?? 0) > 0)
                    <canvas id="monthlyChart"></canvas>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-line text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">No Activity Yet</h4>
                            <p class="text-sm text-gray-500 mb-4">Your reservation activity will appear here</p>
                            <a href="{{ route('user.reservations.calendar') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Start Booking
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-bookmark mr-2"></i>
                    Recent Reservations
                </h3>
                <a href="{{ route('user.reservations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            @if(($reservations ?? collect())->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($reservations as $reservation)
                        <div class="reservation-card border border-gray-200 rounded-xl p-5 relative overflow-hidden group">
                            <!-- Status Badge -->
                            @switch($reservation->status)
                                @case('pending')
                                @case('approved_IOSA')
                                @case('approved_mhadel')
                                    <div class="status-badge status-pending">
                                        <i class="fas fa-clock mr-1"></i> In Review
                                    </div>
                                    @break
                                @case('approved')
                                @case('approved_OTP')
                                    <div class="status-badge status-approved">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </div>
                                    @break
                                @case('completed')
                                    <div class="status-badge status-completed">
                                        <i class="fas fa-check-double mr-1"></i> Completed
                                    </div>
                                    @break
                                @case('rejected')
                                @case('rejected_OTP')
                                    <div class="status-badge status-rejected">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </div>
                                    @break
                            @endswitch
                            
                            <div class="flex flex-col h-full">
                                <div class="mb-3">
                                    <h4 class="font-bold text-gray-800 text-lg leading-tight group-hover:text-blue-600 transition-colors duration-200">
                                        {{ $reservation->event_title }}
                                    </h4>
                                    <div class="text-xs text-gray-500 font-mono mt-1">
                                        ID: {{ $reservation->reservation_id ?? 'N/A' }}
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                        {{ $reservation->venue->name ?? 'No venue' }}
                                    </div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <i class="far fa-calendar mr-2 text-gray-400"></i>
                                        {{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}
                                    </div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <i class="far fa-clock mr-2 text-gray-400"></i>
                                        {{ $reservation->start_date ? $reservation->start_date->format('g:i A') : 'No time' }} - 
                                        {{ $reservation->end_date ? $reservation->end_date->format('g:i A') : 'No end time' }}
                                    </div>
                                </div>
                                
                                @if($reservation->purpose)
                                    <div class="bg-gray-50 p-3 rounded-lg mb-4 border-l-4 border-blue-500">
                                        <p class="text-xs text-gray-700 leading-relaxed">{{ Str::limit($reservation->purpose, 80) }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-auto flex justify-end">
                                    <a href="{{ route('user.reservations.show', $reservation->id) }}" 
                                       class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2 text-sm">
                                        <i class="fas fa-eye"></i>
                                        <span>View Details</span>
                                    </a>
                                </div>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize charts if there's data
    @if(($stats['total'] ?? 0) > 0)
        // Status Distribution Chart
        const statusChartElement = document.getElementById('statusChart');
        if (statusChartElement) {
            const statusCtx = statusChartElement.getContext('2d');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: ['Approved', 'Pending', 'Completed', 'Rejected'],
                    datasets: [{
                        label: 'Reservations',
                        data: [
                            {{ $stats['approved'] ?? 0 }},
                            {{ $stats['pending'] ?? 0 }},
                            {{ $stats['completed'] ?? 0 }},
                            {{ $stats['rejected'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#10B981',
                            '#F59E0B',
                            '#6B7280',
                            '#EF4444'
                        ],
                        borderWidth: 2,
                        borderColor: [
                            '#059669',
                            '#D97706',
                            '#4B5563',
                            '#DC2626'
                        ]
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
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Monthly Activity Chart
        const monthlyChartElement = document.getElementById('monthlyChart');
        if (monthlyChartElement) {
            const monthlyCtx = monthlyChartElement.getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Reservations',
                        data: {!! json_encode($monthlyData['data'] ?? []) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
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
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endsection