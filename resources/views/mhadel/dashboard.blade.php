@extends('layouts.mhadel')

@section('title', 'Ms. Mhadel Dashboard')
@section('page-title', 'Ms. Mhadel Dashboard')
@section('page-subtitle', 'Second Level Approval - Reservation Management')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@section('content')
<div class="space-y-6 font-poppins">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="px-6 pt-6">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button onclick="showTab('overview')" id="tab-overview" class="tab-button active px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
                <button onclick="showTab('finance')" id="tab-finance" class="tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-dollar-sign mr-2"></i>Finance
                </button>
                <button onclick="showTab('trends')" id="tab-trends" class="tab-button px-6 py-3 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="fas fa-trending-up mr-2"></i>Trends
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
                            <p class="text-red-100 font-inter">Review IOSA-approved reservations and make final decisions.</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold font-poppins">{{ $stats['pending'] }}</div>
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
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['pending'] }}</h3>
                                <p class="text-xs text-gray-500 font-inter">IOSA Approved</p>
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
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['approved_today'] }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Forwarded to OTP</p>
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
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['rejected_today'] }}</h3>
                                <p class="text-xs text-gray-500 font-inter">Final Rejection</p>
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
                                <h3 class="text-2xl font-bold text-gray-800 font-poppins">{{ $stats['total_month'] }}</h3>
                                <p class="text-xs text-gray-500 font-inter">All Reservations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <a href="{{ route('mhadel.reservations.index') }}" class="bg-maroon text-white p-6 rounded-xl hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Review Reservations</h3>
                                <p class="text-red-100 font-inter">View and manage pending reservations</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('mhadel.venues.index') }}" class="bg-blue-600 text-white p-6 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-building text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Manage Venues</h3>
                                <p class="text-blue-100 font-inter">Create and manage venues</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('mhadel.events.index') }}" class="bg-green-600 text-white p-6 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-3xl mr-4"></i>
                            <div>
                                <h3 class="text-lg font-bold font-poppins">Manage Events</h3>
                                <p class="text-green-100 font-inter">Create and manage events</p>
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
                        <a href="{{ route('mhadel.reservations.index') }}" class="text-maroon hover:text-red-800 text-sm font-bold font-inter transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($recent_reservations->count() > 0)
                            <div class="space-y-4">
                                @foreach($recent_reservations as $reservation)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-100">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3">
                                                @php
                                                    $status = $reservation->status;
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Pending Review';
                                                    
                                                    if ($status === 'approved_IOSA') {
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        $statusText = 'IOSA Approved';
                                                    } elseif ($status === 'approved_mhadel') {
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        $statusText = 'Mhadel Approved';
                                                    } elseif ($status === 'approved_OTP') {
                                                        $statusClass = 'bg-purple-100 text-purple-800';
                                                        $statusText = 'OTP Approved';
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
                                            <a href="{{ route('mhadel.reservations.show', $reservation->id) }}" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 font-medium" title="View Details">
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

            <!-- Finance Tab -->
            <div id="content-finance" class="tab-content hidden">
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
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Expected Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button onclick="updateExpectedRevenueChart('monthly')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">Monthly</button>
                                <button onclick="updateExpectedRevenueChart('quarterly')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Quarterly</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartExpectedRevenue"></canvas></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Revenue vs Expected</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-xs text-gray-600">Actual</span>
                                    <div class="w-3 h-3 bg-blue-500 rounded-full ml-3"></div>
                                    <span class="text-xs text-gray-600">Expected</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="updateRevenueComparisonChart('monthly')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">Monthly</button>
                                    <button onclick="updateRevenueComparisonChart('quarterly')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">Quarterly</button>
                                </div>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartRevenueComparison"></canvas></div>
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

            <!-- Trends Tab -->
            <div id="content-trends" class="tab-content hidden">
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

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Department Distribution</h3>
                            <div class="flex space-x-2">
                                <button onclick="updateDepartmentChart('count')" class="px-3 py-1 text-xs font-medium bg-maroon text-white rounded-lg">By Count</button>
                                <button onclick="updateDepartmentChart('revenue')" class="px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg">By Revenue</button>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartDepartments"></canvas></div>
                        <div id="departmentsTotal" class="mt-2 text-xs text-gray-600"></div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Venue Utilization</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Hours Used</span>
                            </div>
                        </div>
                        <div style="height: 280px;"><canvas id="chartUtilization"></canvas></div>
                    </div>
                </div>

                <!-- New Analytics -->
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
                    <h3 class="text-lg font-bold text-gray-800 font-poppins mb-6">Monthly Comparison</h3>
                    <div style="height: 320px;"><canvas id="chartMonthlyComparison"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for Finance and Trends tabs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<style>
.font-inter {
    font-family: 'Inter', sans-serif;
}

.font-poppins {
    font-family: 'Poppins', sans-serif;
}

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

/* Hover Effects */
.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth Transitions */
* {
    transition: all 0.3s ease;
}
</style>

<script>
// Global data variables
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
var totalRevenue = @json($totalRevenue ?? 0);
var expectedRevenue = @json($expectedRevenue ?? 0);
var averageRevenue = @json($averageRevenue ?? 0);
var revenueGrowth = @json($revenueGrowth ?? 0);

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
    if (tabName === 'finance' || tabName === 'trends') {
        initializeCharts();
    }
}

// Chart Initialization
function initializeCharts() {
    // Check if charts are already initialized
    if (window.chartsInitialized) return;
    
    // Initialize Finance Charts
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
    
    // Initialize Expected Revenue Chart
    var expRev = prepareCanvas('chartExpectedRevenue', 280);
    if (expRev) {
        console.log('Initializing Expected Revenue Chart with data:', expectedRevenueSeries);
        new Chart(expRev, { 
            type: 'line', 
            data: { 
                labels: labelsMonths, 
                datasets: [{ 
                    label: 'Expected Revenue', 
                    data: expectedRevenueSeries, 
                    borderColor: '#2563EB', 
                    backgroundColor: 'rgba(37,99,235,0.1)', 
                    fill: true, 
                    tension: 0.4, 
                    pointRadius: 4, 
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }] 
            }, 
            options: (function(){ 
                var o = enhancedOptions(); 
                o.scales.y.ticks.callback = function(v){ return peso(v); }; 
                o.plugins.tooltip.callbacks = {
                    label: function(context) {
                        return 'Expected Revenue: ' + peso(context.parsed.y);
                    }
                };
                return o; 
            })() 
        });
    }
    
    // Initialize Revenue Comparison Chart
    var revComp = prepareCanvas('chartRevenueComparison', 280);
    if (revComp) {
        new Chart(revComp, { 
            type: 'line', 
            data: { 
                labels: labelsMonths, 
                datasets: [{ 
                    label: 'Actual Revenue', 
                    data: revenueSeries, 
                    borderColor: '#10B981', 
                    backgroundColor: 'rgba(16,185,129,0.1)', 
                    fill: false, 
                    tension: 0.4, 
                    pointRadius: 4, 
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }, {
                    label: 'Expected Revenue', 
                    data: expectedRevenueSeries, 
                    borderColor: '#2563EB', 
                    backgroundColor: 'rgba(37,99,235,0.1)', 
                    fill: false, 
                    tension: 0.4, 
                    pointRadius: 4, 
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }] 
            }, 
            options: (function(){ 
                var o = enhancedOptions(); 
                o.scales.y.ticks.callback = function(v){ return peso(v); }; 
                o.plugins.legend = {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    }
                };
                o.plugins.tooltip.callbacks = {
                    label: function(context) {
                        return context.dataset.label + ': ' + peso(context.parsed.y);
                    }
                };
                return o; 
            })() 
        });
    }
    
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
    
    var tv = prepareCanvas('chartTopVenues', 320);
    if (tv) { 
        // Check if we have top venues data
        if (!topVenues || topVenues.length === 0) {
            tv.style.display = 'none';
            const emptyDiv = document.createElement('div');
            emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No venue revenue data available</p></div>';
            emptyDiv.className = 'flex items-center justify-center h-full';
            tv.parentNode.appendChild(emptyDiv);
        } else {
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
    }

    // Initialize Trends Charts
    var dept = prepareCanvas('chartDepartments', 280);
    if (dept) {
        // Check if we have department data
        if (!byDepartment || byDepartment.length === 0) {
            dept.style.display = 'none';
            const emptyDiv = document.createElement('div');
            emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-building text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No department data available</p></div>';
            emptyDiv.className = 'flex items-center justify-center h-full';
            dept.parentNode.appendChild(emptyDiv);
        } else {
            new Chart(dept, { 
                type: 'bar', 
                data: { 
                    labels: byDepartment.map(function(d){ return d.department; }), 
                    datasets: [{ 
                        data: byDepartment.map(function(d){ return d.count; }), 
                        backgroundColor: '#1E3A8A',
                        borderColor: '#1E40AF',
                        borderWidth: 2
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
                            callbacks: {
                                label: function(context) { return 'Count: ' + context.parsed.x; }
                            }
                        }
                    }, 
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 } },
                        y: { ticks: { font: { size: 11 } } }
                    }
                } 
            }); 
        }
    }
    
    var util = prepareCanvas('chartUtilization', 280);
    if (util) {
        // Check if we have utilization data
        if (!utilizationWeeks || utilizationWeeks.length === 0) {
            util.style.display = 'none';
            const emptyDiv = document.createElement('div');
            emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-chart-line text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No utilization data available</p></div>';
            emptyDiv.className = 'flex items-center justify-center h-full';
            util.parentNode.appendChild(emptyDiv);
        } else {
            new Chart(util, { 
                type: 'line', 
                data: { 
                    labels: utilizationWeeks.map(function(u){ return (u.label || ('W'+u.week)); }), 
                    datasets: [{ 
                        data: utilizationWeeks.map(function(u){ return u.hours; }), 
                        borderColor: '#2563EB', 
                        backgroundColor: 'rgba(37,99,235,0.1)', 
                        fill: true, 
                        tension: 0.4, 
                        pointRadius: 4, 
                        pointHoverRadius: 6,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }] 
                }, 
                options: enhancedOptions() 
            }); 
        }
    }

    // Initialize new charts
    initializeStatusFlowChart();
    initializePeakHoursChart();
    initializeMonthlyComparisonChart();
    
    // Debug logging
    console.log('Chart Data:', {
        topVenues: topVenues,
        topVenuesByBookings: topVenuesByBookings,
        byDepartment: byDepartment,
        byDepartmentRevenue: byDepartmentRevenue,
        utilizationWeeks: utilizationWeeks,
        statusFlow: statusFlow,
        peakHours: peakHours,
        monthlyComparison: monthlyComparison,
        revenueSeries: revenueSeries,
        expectedRevenueSeries: expectedRevenueSeries,
        totalRevenue: totalRevenue,
        expectedRevenue: expectedRevenue
    });
    
    // Additional debugging for expected revenue
    console.log('Expected Revenue Debug Details:', {
        'expectedRevenueSeries length': expectedRevenueSeries.length,
        'expectedRevenueSeries data': expectedRevenueSeries,
        'expectedRevenueSeries type': typeof expectedRevenueSeries,
        'expectedRevenueSeries is array': Array.isArray(expectedRevenueSeries),
        'has any non-zero values': expectedRevenueSeries.some(val => val > 0),
        'all values': expectedRevenueSeries
    });
    
    // Additional debugging for department data
    console.log('Department Data Debug Details:', {
        'byDepartment length': byDepartment.length,
        'byDepartment data': byDepartment,
        'byDepartmentRevenue length': byDepartmentRevenue.length,
        'byDepartmentRevenue data': byDepartmentRevenue,
        'byDepartment is array': Array.isArray(byDepartment),
        'byDepartmentRevenue is array': Array.isArray(byDepartmentRevenue)
    });
    
    window.chartsInitialized = true;
}

// New Chart Functions
function initializeStatusFlowChart() {
    var ctx = prepareCanvas('chartStatusFlow', 280);
    if (!ctx) return;
    
    // Use actual data from controller with fallbacks
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
    
    // Check if we have any data
    if (statusData.datasets[0].data.every(val => val === 0)) {
        // Show empty state
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
                legend: {
                    display: false
                },
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
    
    // Check if we have peak hours data
    if (!peakHours || peakHours.length === 0) {
        // Show empty state
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-clock text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No peak hours data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    // Use actual data from controller and format hours properly
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

function initializeMonthlyComparisonChart() {
    var ctx = prepareCanvas('chartMonthlyComparison', 320);
    if (!ctx) return;
    
    // Check if we have monthly comparison data
    if (!monthlyComparison || monthlyComparison.length === 0) {
        // Show empty state
        ctx.style.display = 'none';
        const emptyDiv = document.createElement('div');
        emptyDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-chart-line text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-600">No monthly comparison data available</p></div>';
        emptyDiv.className = 'flex items-center justify-center h-full';
        ctx.parentNode.appendChild(emptyDiv);
        return;
    }
    
    // Use actual data from controller
    const comparisonData = {
        labels: monthlyComparison.map(m => m.month),
        datasets: [{
            label: 'Reservations',
            data: monthlyComparison.map(m => m.reservations),
            borderColor: '#8B1818',
            backgroundColor: 'rgba(139,24,24,0.1)',
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 3,
            yAxisID: 'y'
        }, {
            label: 'Revenue',
            data: monthlyComparison.map(m => m.revenue),
            borderColor: '#10B981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 3,
            yAxisID: 'y1'
        }]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: comparisonData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17,24,39,0.95)',
                    titleColor: '#E5E7EB',
                    bodyColor: '#E5E7EB',
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(17,24,39,0.06)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(17,24,39,0.06)' },
                    ticks: { color: '#6B7280', font: { size: 11 } }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { 
                        color: '#6B7280', 
                        font: { size: 11 },
                        callback: function(value) { return peso(value); }
                    }
                }
            }
        }
    });
}

// Chart Update Functions
function updateRevenueChart(period) {
    // Update button states
    document.querySelectorAll('[onclick*="updateRevenueChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    // Get the chart instance
    const chart = Chart.getChart('chartRevenue');
    if (!chart) return;
    
    // Update chart data based on period
    if (period === 'monthly') {
        chart.data.labels = labelsMonths;
        chart.data.datasets[0].data = revenueSeries;
    } else if (period === 'quarterly') {
        chart.data.labels = labelsQuarters;
        chart.data.datasets[0].data = revenueQuarterly;
    }
    
    chart.update();
}

function updateExpectedRevenueChart(period) {
    // Update button states
    document.querySelectorAll('[onclick*="updateExpectedRevenueChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    // Get the chart instance
    const chart = Chart.getChart('chartExpectedRevenue');
    if (!chart) return;
    
    // Update chart data based on period
    if (period === 'monthly') {
        chart.data.labels = labelsMonths;
        chart.data.datasets[0].data = expectedRevenueSeries;
    } else if (period === 'quarterly') {
        chart.data.labels = labelsQuarters;
        chart.data.datasets[0].data = expectedRevenueQuarterly;
    }
    
    chart.update();
}

function updateRevenueComparisonChart(period) {
    // Update button states
    document.querySelectorAll('[onclick*="updateRevenueComparisonChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');

    // Get the chart instance
    const chart = Chart.getChart('chartRevenueComparison');
    if (!chart) return;

    // Update chart data based on period
    if (period === 'monthly') {
        chart.data.labels = labelsMonths;
        chart.data.datasets[0].data = revenueSeries; // Actual Revenue
        chart.data.datasets[1].data = expectedRevenueSeries; // Expected Revenue
    } else if (period === 'quarterly') {
        chart.data.labels = labelsQuarters;
        chart.data.datasets[0].data = revenueQuarterly; // Actual Revenue
        chart.data.datasets[1].data = expectedRevenueQuarterly; // Expected Revenue
    }
    
    chart.update();
}

function updateVenueChart(metric) {
    // Update button states
    document.querySelectorAll('[onclick*="updateVenueChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    // Get the chart instance
    const chart = Chart.getChart('chartTopVenues');
    if (!chart) return;
    
    // Update chart data based on metric
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

function updateDepartmentChart(metric) {
    console.log('updateDepartmentChart called with metric:', metric);
    console.log('byDepartment data:', byDepartment);
    console.log('byDepartmentRevenue data:', byDepartmentRevenue);
    
    // Update button states
    document.querySelectorAll('[onclick*="updateDepartmentChart"]').forEach(btn => {
        btn.classList.remove('bg-maroon', 'text-white');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
    event.target.classList.add('bg-maroon', 'text-white');
    
    // Get the chart instance
    const chart = Chart.getChart('chartDepartments');
    if (!chart) {
        console.error('Chart not found: chartDepartments');
        return;
    }
    
    console.log('Found chart:', chart);
    
    // Update chart data based on metric
    if (metric === 'count') {
        console.log('Updating to count view');
        chart.data.labels = byDepartment.map(d => d.department);
        chart.data.datasets[0].data = byDepartment.map(d => d.count);
        chart.options.plugins.tooltip.callbacks.label = function(context) {
            return 'Count: ' + context.parsed;
        };
        
        // Show the chart
        chart.canvas.style.display = 'block';
        chart.update();
        
    } else if (metric === 'revenue') {
        console.log('Updating to revenue view');
        
        // Check if we have revenue data
        if (!byDepartmentRevenue || byDepartmentRevenue.length === 0 || byDepartmentRevenue.every(d => d.revenue === 0)) {
            console.log('No revenue data available, showing empty state');
            
            // Hide the chart and show empty state
            chart.canvas.style.display = 'none';
            
            // Remove existing empty state if any
            const existingEmptyState = chart.canvas.parentNode.querySelector('.empty-state-revenue');
            if (existingEmptyState) {
                existingEmptyState.remove();
            }
            
            // Create empty state for revenue
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-state-revenue flex items-center justify-center h-full';
            emptyDiv.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-blue-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-600 font-poppins mb-2">No Revenue Data Yet</h3>
                    <p class="text-sm text-gray-500 font-inter mb-3">Revenue will appear once reservations are Final Approved</p>
                    <div class="inline-flex items-center px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-xs text-blue-700 font-medium">Pending → IOSA → Mhadel → Final Approved</span>
                    </div>
                </div>
            `;
            
            chart.canvas.parentNode.appendChild(emptyDiv);
            
        } else {
            console.log('Revenue data available, updating chart');
            
            // Show the chart and remove empty state
            chart.canvas.style.display = 'block';
            const existingEmptyState = chart.canvas.parentNode.querySelector('.empty-state-revenue');
            if (existingEmptyState) {
                existingEmptyState.remove();
            }
            
            // Update chart with revenue data
            chart.data.labels = byDepartmentRevenue.map(d => d.department);
            chart.data.datasets[0].data = byDepartmentRevenue.map(d => d.revenue);
            chart.options.plugins.tooltip.callbacks.label = function(context) {
                return 'Revenue: ' + peso(context.parsed);
            };
            
            chart.update();
        }
    }
    
    console.log('Updated chart data:', chart.data);
}

// Initialize overview tab by default
document.addEventListener('DOMContentLoaded', function() {
    showTab('overview');
});
</script>
@endsection 