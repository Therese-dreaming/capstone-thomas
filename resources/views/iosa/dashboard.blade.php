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
    
    /* Tab Styles */
    .tab-button {
        color: #6B7280;
        background: transparent;
    }

    .tab-button.active {
        color: #8B0000;
        background: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .tab-button:hover:not(.active) {
        color: #374151;
        background: rgba(139, 0, 0, 0.05);
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('content')
<div class="space-y-6 font-poppins">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="px-6 pt-6">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button onclick="showTab('overview')" id="tab-overview" class="tab-button active px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
                <button onclick="showTab('analytics')" id="tab-analytics" class="tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-chart-line mr-2"></i>Analytics
                </button>
                <button onclick="showTab('reports')" id="tab-reports" class="tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-file-alt mr-2"></i>Reports
                </button>
                <button onclick="showTab('ratings')" id="tab-ratings" class="tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-star mr-2"></i>Ratings
                </button>
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content active">
                <!-- Hero Section -->
                <div class="bg-maroon rounded-xl p-6 text-white mb-8">
                    <div class="flex items-start justify-between">
                <div>
                            <h1 class="text-2xl font-bold font-poppins mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                            <p class="text-red-100 font-inter">Review reservation requests and make approval decisions.</p>
                </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold font-poppins">{{ $stats['pending'] ?? 0 }}</div>
                            <div class="text-sm text-red-100 font-inter">Pending Review</div>
                </div>
            </div>
        </div>
        
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
        </div>
                <div>
                                <p class="text-sm text-gray-600 font-inter">Pending Review</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['pending'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Awaiting Approval</p>
            </div>
        </div>
    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Approved Today</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['approved_today'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Forwarded to Mhadel</p>
            </div>
        </div>
    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Rejected Today</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['rejected_today'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">IOSA Rejected</p>
            </div>
        </div>
    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
        </div>
                            <div>
                                <p class="text-sm text-gray-600 font-inter">Total This Month</p>
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['total_month'] ?? 0 }}</h3>
                                <p class="text-xs text-gray-500 font-inter">All Reservations</p>
                            </div>
                        </div>
                        </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('iosa.reservations.index') }}" class="bg-maroon text-white p-6 rounded-xl hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Review Reservations</h3>
                                <p class="text-red-100 font-inter">View and manage pending reservations</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('iosa.reservations.index') }}" class="bg-blue-600 text-white p-6 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-list text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">All Reservations</h3>
                                <p class="text-blue-100 font-inter">View all reservation requests</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('iosa.reservation-reports') }}" class="bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Reservation Reports</h3>
                                <p class="text-green-100 font-inter">View and export reservation data</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Recent Reservations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-history text-maroon mr-3"></i>
                            Recent Reservations
                        </h2>
                        <a href="{{ route('iosa.reservations.index') }}" class="text-maroon hover:text-red-800 text-sm font-bold font-inter transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
            </div>
            <div class="p-6">
                        @if(isset($recent_reservations) && $recent_reservations->count() > 0)
                <div class="space-y-4">
                                @foreach($recent_reservations as $reservation)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-100">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3">
                                                @php
                                                    $status = $reservation->status;
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Pending Review';
                                                    
                                                    if ($status === 'pending') {
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        $statusText = 'Pending Review';
                                                    } elseif ($status === 'approved_IOSA') {
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        $statusText = 'IOSA Approved';
                                                    } elseif ($status === 'approved_mhadel') {
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        $statusText = 'Mhadel Approved';
                                                    } elseif ($status === 'approved_OTP') {
                                                        $statusClass = 'bg-purple-100 text-purple-800';
                                                        $statusText = 'OTP Approved';
                                                    } elseif ($status === 'rejected_IOSA') {
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        $statusText = 'IOSA Rejected';
                                                    } elseif ($status === 'rejected_mhadel') {
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        $statusText = 'Mhadel Rejected';
                                                    } elseif ($status === 'rejected_OTP') {
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                        $statusText = 'OTP Rejected';
                                                    } elseif ($status === 'completed') {
                                                        $statusClass = 'bg-indigo-100 text-indigo-800';
                                                        $statusText = 'Completed';
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs font-bold mr-3">{{ $statusText }}</span>
                                                <span class="text-sm text-gray-500 font-inter">{{ $reservation->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-base font-bold text-gray-800 font-poppins mb-2">{{ $reservation->event_title }}</div>
                                            <div class="flex items-center text-sm text-gray-600 font-inter">
                                                <i class="fas fa-user mr-2 text-maroon"></i>
                                                <span>{{ $reservation->user->name }}</span>
                                                <span class="mx-3 text-gray-400">•</span>
                                                <i class="fas fa-calendar mr-2 text-maroon"></i>
                                                <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                            </div>
                                </div>
                                <div>
                                            <a href="{{ route('iosa.reservations.show', $reservation->id) }}" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 font-medium" title="View Details">
                                                <i class="fas fa-eye mr-2"></i>View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-check text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-600 font-poppins mb-2">No Recent Reservations</h3>
                                <p class="text-gray-500 font-inter">No reservations found in the recent activity</p>
                            </div>
                            @endif
                        </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div id="content-analytics" class="tab-content hidden">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalUsers ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Total Users</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-building text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalVenues ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Active Venues</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-check text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $totalReservations ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Total Reservations</p>
                    </div>
                </div>

                <!-- Revenue Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-inter font-medium">Total Revenue</p>
                                <h3 class="text-2xl font-bold text-green-800 font-poppins">₱{{ number_format($totalRevenue ?? 0) }}</h3>
                                <p class="text-xs text-green-600 font-inter">This month</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600 font-inter font-medium">Expected Revenue</p>
                                <h3 class="text-2xl font-bold text-blue-800 font-poppins">₱{{ number_format($expectedRevenue ?? 0) }}</h3>
                                <p class="text-xs text-blue-600 font-inter">IOSA & Mhadel approved</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-700 font-inter font-medium">Avg Price / Reservation</p>
                                <h3 class="text-2xl font-bold text-yellow-900 font-poppins">₱{{ number_format($averageRevenue ?? 0) }}</h3>
                                <p class="text-xs text-yellow-700 font-inter">Final Approved</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calculator text-yellow-700 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl p-6 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-700 font-inter font-medium">Revenue Growth</p>
                                <h3 class="text-2xl font-bold text-purple-900 font-poppins">{{ number_format($revenueGrowth ?? 0, 1) }}%</h3>
                                <p class="text-xs text-purple-700 font-inter">vs last month</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-purple-700 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts will be added in next step -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                        <i class="fas fa-chart-line text-maroon mr-2"></i>
                        Monthly Reservation Trends
                    </h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                        <i class="fas fa-building text-maroon mr-2"></i>
                        Top Departments by Reservations
                    </h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="departmentsChart"></canvas>
                    </div>
                </div>

                <!-- Additional Charts from Ms. Mhadel's Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Monthly Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button onclick="updateRevenueChart('monthly')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">Monthly</button>
                                <button onclick="updateRevenueChart('quarterly')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Quarterly</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartRevenue"></canvas></div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Approval Performance</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Approved</span>
                                <div class="w-3 h-3 bg-red-500 rounded-full ml-3"></div>
                                <span class="text-xs text-gray-600">Rejected</span>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartApprovals"></canvas></div>
                        <div id="approvalsTotal" class="mt-2 text-xs text-gray-600"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6">Reservation Status Flow</h3>
                        <div style="height: 280px;"><canvas id="chartStatusFlow"></canvas></div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6">Peak Booking Hours</h3>
                        <div style="height: 280px;"><canvas id="chartPeakHours"></canvas></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins">Top Performing Venues</h3>
                        <div class="flex space-x-2">
                            <button onclick="updateVenueChart('revenue')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">By Revenue</button>
                            <button onclick="updateVenueChart('bookings')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">By Bookings</button>
                        </div>
                    </div>
                    <div style="height: 320px;"><canvas id="chartTopVenues"></canvas></div>
                </div>
            </div>

            <!-- Reports Tab -->
            <div id="content-reports" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-4 flex items-center">
                            <i class="fas fa-file-alt text-maroon mr-2"></i>
                            Generate Reports
                        </h3>
                        <p class="text-gray-600 font-inter mb-4">Create comprehensive reports for reservation data analysis.</p>
                        <a href="{{ route('iosa.reservation-reports') }}" class="inline-flex items-center px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 font-medium">
                            <i class="fas fa-download mr-2"></i>View Reports
                        </a>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-4 flex items-center">
                            <i class="fas fa-chart-line text-maroon mr-2"></i>
                            Analytics Dashboard
                        </h3>
                        <p class="text-gray-600 font-inter mb-4">View detailed analytics and trends for reservation data.</p>
                        <button onclick="showTab('analytics')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-medium">
                            <i class="fas fa-chart-line mr-2"></i>View Analytics
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ratings Tab -->
            <div id="content-ratings" class="tab-content hidden">
                <!-- Rating Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $ratingsData['total_ratings'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">Total Ratings</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $ratingsData['average_rating'] ?? 0 }}/5</h3>
                        <p class="text-sm text-gray-600 font-inter">Average Rating</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $ratingsData['ratings_this_month'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 font-inter">This Month</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-trending-up text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $ratingsData['rating_growth'] ?? 0 }}%</h3>
                        <p class="text-sm text-gray-600 font-inter">Growth Rate</p>
                    </div>
                </div>

                <!-- Rating Distribution Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                            <i class="fas fa-chart-pie text-maroon mr-2"></i>
                            Rating Distribution
                        </h3>
                        <div style="height: 300px;"><canvas id="ratingDistributionChart"></canvas></div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                            <i class="fas fa-chart-line text-maroon mr-2"></i>
                            Monthly Ratings Trend
                        </h3>
                        <div style="height: 300px;"><canvas id="monthlyRatingsChart"></canvas></div>
                    </div>
                </div>

                <!-- Ratings by Venue and Department -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                            <i class="fas fa-building text-maroon mr-2"></i>
                            Top Venues by Ratings
                        </h3>
                        <div style="height: 300px;"><canvas id="ratingsByVenueChart"></canvas></div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                            <i class="fas fa-users text-maroon mr-2"></i>
                            Ratings by Department
                        </h3>
                        <div style="height: 300px;"><canvas id="ratingsByDepartmentChart"></canvas></div>
                    </div>
                </div>

                <!-- Recent Ratings with Comments -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6 flex items-center">
                        <i class="fas fa-comments text-maroon mr-2"></i>
                        Recent Ratings with Comments
                    </h3>
                    @if(isset($ratingsData['recent_ratings']) && count($ratingsData['recent_ratings']) > 0)
                        <div class="space-y-4">
                            @foreach($ratingsData['recent_ratings'] as $rating)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="flex items-center mr-3">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $rating['rating'] ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $rating['rating'] }}/5</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $rating['created_at'] }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <p class="text-sm text-gray-800 font-medium">{{ $rating['event_title'] }}</p>
                                        <p class="text-xs text-gray-600">{{ $rating['venue_name'] }} • {{ $rating['user_name'] }}</p>
                                    </div>
                                    <p class="text-sm text-gray-700 italic">"{{ $rating['comment'] }}"</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-comments text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 font-poppins mb-2">No Recent Comments</h3>
                            <p class="text-gray-500 font-inter">No ratings with comments found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab Management
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(`content-${tabName}`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('active');
    }
    
    // Activate selected tab button
    const selectedButton = document.getElementById(`tab-${tabName}`);
    if (selectedButton) {
        selectedButton.classList.add('active');
    }
    
    // Initialize charts if needed
    if (tabName === 'analytics' || tabName === 'ratings') {
        initializeCharts();
    }
}

// Global data variables (from Ms. Mhadel's dashboard)
var labelsMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var labelsQuarters = ['Jan-Mar', 'Apr-Jun', 'Jul-Sep', 'Oct-Dec'];
var revenueSeries = @json($revenueSeries ?? []);
var revenueQuarterly = @json($revenueQuarterly ?? []);
var expectedRevenueSeries = @json($expectedRevenueSeries ?? []);
var expectedRevenueQuarterly = @json($expectedRevenueQuarterly ?? []);
var approvals = @json($approvalsVsRejections['approved'] ?? 0);
var rejections = @json($approvalsVsRejections['rejected'] ?? 0);
var topVenues = @json($topVenues ?? []);
var topVenuesByBookings = @json($topVenuesByBookings ?? []);
var byDepartment = @json($byDepartment ?? []);
var byDepartmentRevenue = @json($byDepartmentRevenue ?? []);
var utilizationWeeks = @json($utilizationWeeks ?? []);
var statusFlow = @json($statusFlow ?? []);
var peakHours = @json($peakHours ?? []);
var monthlyComparison = @json($monthlyComparison ?? []);

// Ratings data
var ratingsData = @json($ratingsData ?? []);
var ratingDistribution = @json($ratingsData['rating_distribution'] ?? []);
var monthlyRatingsData = @json($ratingsData['monthly_ratings_data'] ?? []);
var monthlyAverageRatings = @json($ratingsData['monthly_average_ratings'] ?? []);
var ratingsByVenue = @json($ratingsData['ratings_by_venue'] ?? []);
var ratingsByDepartment = @json($ratingsData['ratings_by_department'] ?? []);

// Utility functions
function peso(v){ 
    try { 
        return '\u20B1' + Number(v||0).toLocaleString(); 
    } catch(e){ 
        return 'PHP ' + (v||0); 
    } 
}

function prepareCanvas(id, height){
    var c = document.getElementById(id);
    if (!c) return null;
    c.style.height = height + 'px';
    c.style.maxHeight = height + 'px';
    if (Chart && Chart.getChart) { 
        var inst = Chart.getChart(c); 
        if (inst) inst.destroy(); 
    }
    return c;
}

function enhancedOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                display: false 
            }, 
            tooltip: { 
                backgroundColor: 'rgba(17,24,39,0.95)', 
                borderColor: 'rgba(255,255,255,0.08)', 
                borderWidth: 1, 
                titleColor: '#E5E7EB', 
                bodyColor: '#E5E7EB', 
                padding: 12,
                cornerRadius: 8,
                displayColors: true
            } 
        },
        scales: { 
            x: { 
                grid: { 
                    color: 'rgba(17,24,39,0.06)',
                    drawBorder: false
                }, 
                ticks: { 
                    color: '#6B7280',
                    font: { size: 11 }
                } 
            }, 
            y: { 
                grid: { 
                    color: 'rgba(17,24,39,0.06)',
                    drawBorder: false
                }, 
                ticks: { 
                    color: '#6B7280',
                    font: { size: 11 }
                } 
            } 
        },
        elements: {
            point: {
                radius: 4,
                hoverRadius: 6
            },
            line: {
                tension: 0.4
            }
        }
    };
}

// Chart Initialization
function initializeCharts() {
    // Check if charts are already initialized
    if (window.chartsInitialized) return;
    
    // Initialize all charts from Ms. Mhadel's dashboard
    initializeRevenueChart();
    initializeApprovalsChart();
    initializeStatusFlowChart();
    initializePeakHoursChart();
    initializeTopVenuesChart();
    initializeDepartmentsChart();
    initializeTrendChart();
    
    // Initialize ratings charts
    initializeRatingDistributionChart();
    initializeMonthlyRatingsChart();
    initializeRatingsByVenueChart();
    initializeRatingsByDepartmentChart();
    
    window.chartsInitialized = true;
}

// Individual chart initialization functions
function initializeRevenueChart() {
    var rev = prepareCanvas('chartRevenue', 280);
    if (rev) {
        new Chart(rev, { 
            type: 'line', 
            data: { 
                labels: labelsMonths, 
                datasets: [{ 
                    label: 'Revenue', 
                    data: revenueSeries, 
                    borderColor: '#8B1818', 
                    backgroundColor: 'rgba(139,24,24,0.1)', 
                    fill: true, 
                    tension: 0.4, 
                    pointRadius: 4, 
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    pointBackgroundColor: '#8B1818',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }] 
            }, 
            options: (function(){ 
                var o = enhancedOptions(); 
                o.scales.y.ticks.callback = function(v){ return peso(v); }; 
                o.plugins.tooltip.callbacks = {
                    label: function(context) {
                        return 'Revenue: ' + peso(context.parsed.y);
                    }
                };
                return o; 
            })() 
        });
    }
}

function initializeApprovalsChart() {
    var appr = prepareCanvas('chartApprovals', 280);
    if (appr) {
        new Chart(appr, { 
            type: 'bar', 
            data: { 
                labels: ['Approved','Rejected'], 
                datasets: [{ 
                    data: [approvals, rejections], 
                    backgroundColor: ['#065F46','#991B1B'],
                    borderColor: ['#064E3B','#7F1D1D'],
                    borderWidth: 2
                }] 
            }, 
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                indexAxis: 'y',
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,0.95)',
                        titleColor: '#E5E7EB',
                        bodyColor: '#E5E7EB',
                        padding: 12
                    }
                }, 
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { ticks: { font: { size: 12 } } }
                }
            } 
        });
    }
}

function initializeStatusFlowChart() {
    var ctx = prepareCanvas('chartStatusFlow', 280);
    if (!ctx) return;
    
    const statusData = {
        labels: ['Submitted', 'IOSA Approved', 'Mhadel Approved', 'Final Approved', 'Rejected'],
        datasets: [{
            label: 'Reservations',
            data: [
                statusFlow.submitted || 0,
                statusFlow.iosa_approved || 0,
                statusFlow.mhadel_approved || 0,
                statusFlow.final_approved || 0,
                statusFlow.rejected || 0
            ],
            backgroundColor: ['#F59E0B', '#3B82F6', '#8B1818', '#10B981', '#EF4444'],
            borderColor: '#ffffff',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
        }]
    };
    
    if (statusData.datasets[0].data.every(val => val === 0)) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-chart-bar text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No status data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    new Chart(ctx, {
        type: 'bar',
        data: statusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(17,24,39,0.06)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                }
            }
        }
    });
}

function initializePeakHoursChart() {
    var ctx = prepareCanvas('chartPeakHours', 280);
    if (!ctx) return;
    
    if (!peakHours || peakHours.length === 0) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-clock text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No peak hours data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    const hoursLabels = peakHours.map(h => {
        const hour = h.hour;
        if (hour === 0) return '12 AM';
        if (hour < 12) return hour + ' AM';
        if (hour === 12) return '12 PM';
        return (hour - 12) + ' PM';
    });
    
    const hoursData = {
        labels: hoursLabels,
        datasets: [{
            label: 'Bookings',
            data: peakHours.map(h => h.count),
            backgroundColor: 'rgba(139,24,24,0.8)',
            borderColor: '#8B1818',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
        }]
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: hoursData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(17,24,39,0.06)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                }
            }
        }
    });
}

function initializeTopVenuesChart() {
    var tv = prepareCanvas('chartTopVenues', 320);
    if (!tv) return;
    
    if (!topVenues || topVenues.length === 0) {
        tv.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No venue data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        tv.parentNode.appendChild(emptyDiv);
        return;
    }
    
    var vl = topVenues.map(function(v){ return v.venue; }); 
    var vt = topVenues.map(function(v){ return v.total; }); 
    new Chart(tv, { 
        type: 'bar', 
        data: { 
            labels: vl, 
            datasets: [{ 
                data: vt, 
                backgroundColor: 'rgba(139,24,24,0.8)', 
                borderRadius: 8, 
                barPercentage: 0.7, 
                categoryPercentage: 0.7,
                hoverBackgroundColor: 'rgba(139,24,24,1)'
            }] 
        }, 
        options: (function(){ 
            var o = enhancedOptions(); 
            o.indexAxis = 'y'; 
            o.scales.x.ticks.callback = function(v){ return peso(v); }; 
            o.plugins.tooltip.callbacks = {
                label: function(context) {
                    return 'Revenue: ' + peso(context.parsed.x);
                }
            };
            return o; 
        })() 
    }); 
}

function initializeDepartmentsChart() {
    var dept = prepareCanvas('departmentsChart', 300);
    const departmentsLabels = {!! json_encode(array_keys($departmentsData ?? [])) !!};
    const departmentsValues = {!! json_encode(array_values($departmentsData ?? [])) !!};
    
    if (dept) {
        if (departmentsLabels.length > 0) {
            new Chart(dept, {
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
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17,24,39,0.95)',
                            titleColor: '#E5E7EB',
                            bodyColor: '#E5E7EB',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            ticks: { color: '#6B7280', font: { size: 11 } }
                        },
                        x: {
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            ticks: { color: '#6B7280', font: { size: 11 } }
                        }
                    }
                }
            });
        } else {
            dept.style.display = 'none';
            const emptyDiv = document.createElement('div');
            emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No department data available</p></div>';
            emptyDiv.className = 'flex items-center justify-center h-full';
            dept.parentNode.appendChild(emptyDiv);
        }
    }
}

function initializeTrendChart() {
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
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
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    pointBackgroundColor: '#8B1818',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,0.95)',
                        titleColor: '#E5E7EB',
                        bodyColor: '#E5E7EB',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { color: '#6B7280', font: { size: 11 } }
                    },
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { color: '#6B7280', font: { size: 11 } }
                    }
                }
            }
        });
    }
}

// Chart update functions
function updateRevenueChart(period) {
    document.querySelectorAll('[onclick*="updateRevenueChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    const chart = Chart.getChart('chartRevenue');
    if (!chart) return;
    
    if (period === 'monthly') {
        chart.data.labels = labelsMonths;
        chart.data.datasets[0].data = revenueSeries;
    } else if (period === 'quarterly') {
        chart.data.labels = labelsQuarters;
        chart.data.datasets[0].data = revenueQuarterly;
    }
    
    chart.update();
}

function updateVenueChart(metric) {
    document.querySelectorAll('[onclick*="updateVenueChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    const chart = Chart.getChart('chartTopVenues');
    if (!chart) return;
    
    if (metric === 'revenue') {
        chart.data.labels = topVenues.map(v => v.venue);
        chart.data.datasets[0].data = topVenues.map(v => v.total);
        chart.options.scales.x.ticks.callback = function(v) { return peso(v); };
        chart.options.plugins.tooltip.callbacks.label = function(context) {
            return 'Revenue: ' + peso(context.parsed.x);
        };
    } else if (metric === 'bookings') {
        chart.data.labels = topVenuesByBookings.map(v => v.venue);
        chart.data.datasets[0].data = topVenuesByBookings.map(v => v.total);
        chart.options.scales.x.ticks.callback = function(v) { return v; };
        chart.options.plugins.tooltip.callbacks.label = function(context) {
            return 'Bookings: ' + context.parsed.x;
        };
    }
    
    chart.update();
}

// Ratings Chart Initialization Functions
function initializeRatingDistributionChart() {
    var ctx = prepareCanvas('ratingDistributionChart', 300);
    if (!ctx) return;
    
    if (!ratingDistribution || Object.keys(ratingDistribution).length === 0) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-star text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No rating data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    const distributionData = {
        labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
        datasets: [{
            data: [
                ratingDistribution[1] || 0,
                ratingDistribution[2] || 0,
                ratingDistribution[3] || 0,
                ratingDistribution[4] || 0,
                ratingDistribution[5] || 0
            ],
            backgroundColor: ['#EF4444', '#F97316', '#EAB308', '#22C55E', '#10B981'],
            borderColor: '#ffffff',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: distributionData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });
}

function initializeMonthlyRatingsChart() {
    var ctx = prepareCanvas('monthlyRatingsChart', 300);
    if (!ctx) return;
    
    if (!monthlyRatingsData || monthlyRatingsData.length === 0) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-chart-line text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No monthly rating data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsMonths,
            datasets: [{
                label: 'Number of Ratings',
                data: monthlyRatingsData,
                borderColor: '#8B1818',
                backgroundColor: 'rgba(139, 24, 24, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 3,
                pointBackgroundColor: '#8B1818',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }, {
                label: 'Average Rating',
                data: monthlyAverageRatings,
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                fill: false,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 3,
                pointBackgroundColor: '#F59E0B',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    min: 0,
                    max: 5,
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                x: {
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                }
            }
        }
    });
}

function initializeRatingsByVenueChart() {
    var ctx = prepareCanvas('ratingsByVenueChart', 300);
    if (!ctx) return;
    
    if (!ratingsByVenue || ratingsByVenue.length === 0) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No venue rating data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    const venueLabels = ratingsByVenue.map(v => v.venue);
    const venueRatings = ratingsByVenue.map(v => v.avg_rating);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: venueLabels,
            datasets: [{
                label: 'Average Rating',
                data: venueRatings,
                backgroundColor: 'rgba(139, 24, 24, 0.8)',
                borderColor: '#8B1818',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Average Rating: ' + context.parsed.x + '/5';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: 0,
                    max: 5,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { 
                        color: '#6B7280', 
                        font: { size: 11 },
                        callback: function(value) {
                            return value + '/5';
                        }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                }
            }
        }
    });
}

function initializeRatingsByDepartmentChart() {
    var ctx = prepareCanvas('ratingsByDepartmentChart', 300);
    if (!ctx) return;
    
    if (!ratingsByDepartment || ratingsByDepartment.length === 0) {
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-users text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No department rating data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    const deptLabels = ratingsByDepartment.map(d => d.department);
    const deptRatings = ratingsByDepartment.map(d => d.avg_rating);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Average Rating',
                data: deptRatings,
                backgroundColor: 'rgba(139, 24, 24, 0.8)',
                borderColor: '#8B1818',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Average Rating: ' + context.parsed.x + '/5';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: 0,
                    max: 5,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { 
                        color: '#6B7280', 
                        font: { size: 11 },
                        callback: function(value) {
                            return value + '/5';
                        }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                }
            }
        }
    });
}

// Initialize overview tab by default
document.addEventListener('DOMContentLoaded', function() {
    showTab('overview');
});
</script>
@endsection
    