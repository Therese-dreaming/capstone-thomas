@extends('layouts.mhadel')

@section('title', 'Reports & Analytics - Ms. Mhadel')
@section('page-title', 'Reports & Analytics')

@section('header-actions')
    <button id="openFilterBtn" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm mr-2 flex items-center">
        <i class="fas fa-filter mr-2 text-maroon"></i>Filter
    </button>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .font-inter {
        font-family: 'Inter', sans-serif;
    }
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
    .btn-dark-green {
        background-color: #166534;
        color: white;
    }
    .btn-dark-green:hover {
        background-color: #15803d;
    }
    .btn-dark-red {
        background-color: #991b1b;
        color: white;
    }
    .btn-dark-red:hover {
        background-color: #dc2626;
    }
    .btn-dark-blue {
        background-color: #1e40af;
        color: white;
    }
    .btn-dark-blue:hover {
        background-color: #2563eb;
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        white-space: nowrap;
        display: inline-block;
        min-width: 60px;
        text-align: center;
    }
    .status-pending {
        background-color: #F59E0B;
        color: #1F2937;
    }
    .status-approved {
        background-color: #10B981;
        color: #1F2937;
    }
    .status-rejected {
        background-color: #EF4444;
        color: #1F2937;
    }
    .status-completed {
        background-color: #6366F1;
        color: #1F2937;
    }
    .tab-active {
        border-bottom: 2px solid #800000;
        color: #800000;
        font-weight: 500;
    }
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(128, 0, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0); }
    }
    
    .view-toggle-btn {
        background-color: #f3f4f6;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }
    
    .view-toggle-btn.active {
        background-color: #800000;
        color: white;
        border-color: #800000;
    }
    
    .view-toggle-btn:hover:not(.active) {
        background-color: #e5e7eb;
        color: #374151;
    }
</style>

<div class="space-y-6 font-inter">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($kpis['total'] ?? 0) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($kpis['approved'] ?? 0) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Rejected</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($kpis['rejected'] ?? 0) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-purple-50 p-3 mr-4">
                <i class="fas fa-peso-sign text-purple-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
                <h3 class="text-2xl font-bold text-gray-800">₱{{ number_format($kpis['revenue'] ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                    <i class="fas fa-chart-bar text-maroon mr-3"></i>
                    Reports & Analytics - Ms. Mhadel
                </h2>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" placeholder="Search reports..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200">
            <button onclick="filterByStatus('all')" class="px-6 py-3 text-gray-700 hover:text-maroon transition-colors {{ request('status') == null ? 'tab-active' : '' }}">
                All Reports
            </button>
            <button onclick="filterByStatus('pending')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'pending' ? 'tab-active' : '' }}">
                Pending Review
            </button>
            <button onclick="filterByStatus('approved_mhadel')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'approved_mhadel' ? 'tab-active' : '' }}">
                Mhadel Approved
            </button>
            <button onclick="filterByStatus('approved_OTP')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'approved_OTP' ? 'tab-active' : '' }}">
                OTP Approved
            </button>
            <button onclick="filterByStatus('rejected_mhadel')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'rejected_mhadel' ? 'tab-active' : '' }}">
                Mhadel Rejected
            </button>
            <button onclick="filterByStatus('rejected_OTP')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'rejected_OTP' ? 'tab-active' : '' }}">
                OTP Rejected
            </button>
            <button onclick="filterByStatus('completed')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'completed' ? 'tab-active' : '' }}">
                Completed
            </button>
        </div>
        
        <!-- View Toggle -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button onclick="showListView()" id="listViewBtn" class="view-toggle-btn active px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-list mr-2"></i>List View
                    </button>
                    <button onclick="showChartView()" id="chartViewBtn" class="view-toggle-btn px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-chart-pie mr-2"></i>Chart View
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        Showing {{ $results->count() }} of {{ $stats['total'] ?? $kpis['total'] ?? 0 }} results
                    </span>
                    <button onclick="openExportModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                        <i class="fas fa-file-excel mr-2"></i>Export to Excel
                    </button>
                </div>
            </div>
        </div>
        
        <!-- List View -->
        <div id="listView" class="p-0">
            @if($results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Details</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Financial</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($results as $r)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $r->event_title ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-600">{{ $r->purpose ?? 'No purpose specified' }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-users mr-1"></i>{{ $r->capacity ?? 'N/A' }} participants
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ optional($r->user)->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-600">{{ optional($r->user)->email ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-building mr-1"></i>{{ $r->department ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ optional($r->venue)->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ optional($r->venue)->capacity ?? 'N/A' }} capacity
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm text-gray-900">{{ optional($r->start_date)->format('M d, Y') ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($r->start_date && $r->end_date)
                                                    {{ \Carbon\Carbon::parse($r->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($r->end_date)->format('g:i A') }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $st = $r->status;
                                            $badge = 'status-pending';
                                            if ($st === 'approved_IOSA' || $st === 'approved_mhadel' || $st === 'approved_OTP' || $st === 'approved') { 
                                                $badge = 'status-approved'; 
                                            }
                                            if (str_starts_with($st, 'rejected') || $st === 'rejected') { 
                                                $badge = 'status-rejected'; 
                                            }
                                            if ($st === 'completed') { 
                                                $badge = 'status-completed'; 
                                            }
                                        @endphp
                                        <span class="status-badge {{ $badge }}">
                                            {{ str_replace('_',' ', $r->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-semibold text-green-600">
                                                {{ is_null($r->final_price) ? '₱0.00' : ('₱'.number_format((float)$r->final_price, 2)) }}
                                            </div>
                                            @if($r->discount_percentage && $r->discount_percentage > 0)
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-percent mr-1"></i>{{ $r->discount_percentage }}% discount
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('mhadel.reports.show', $r->id) }}" class="btn-dark-blue px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                                            <i class="fas fa-eye mr-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-chart-bar text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-4">No Reports Found</h3>
                    <p class="text-gray-500 mb-6">There are no reports matching your current filters.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-blue-800">Reports Information</h4>
                                <p class="text-blue-700 text-sm mt-1">Reports are generated based on your filter criteria and data availability.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Chart View -->
        <div id="chartView" class="p-6 hidden">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center font-poppins">
                            <i class="fas fa-chart-pie text-maroon mr-3"></i>
                            Analytics Dashboard
                        </h2>
                        <div class="flex items-center space-x-2">
                            <select id="chartPeriod" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Revenue Chart -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue Trend</h3>
                            <div class="h-64 bg-white rounded-lg border border-gray-200 p-4">
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                        
                        <!-- Status Distribution -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Distribution</h3>
                            <div class="h-64 bg-white rounded-lg border border-gray-200 p-4">
                                <canvas id="statusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Analytics -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                        <!-- Venue Usage -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Venues</h3>
                            <div class="space-y-3">
                                @php
                                    $venueStats = $results->groupBy('venue_id')->map(function($group) {
                                        return $group->count();
                                    })->sortDesc()->take(5);
                                @endphp
                                @foreach($venueStats as $venueId => $count)
                                    @php
                                        $venue = $venues->firstWhere('id', $venueId);
                                        $venueName = $venue ? $venue->name : 'Unknown Venue';
                                        $percentage = $results->count() > 0 ? round(($count / $results->count()) * 100, 1) : 0;
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700 truncate">{{ $venueName }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-maroon h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Department Distribution -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Activity</h3>
                            <div class="space-y-3">
                                @php
                                    $deptStats = $results->groupBy('department')->map(function($group) {
                                        return $group->count();
                                    })->sortDesc()->take(5);
                                @endphp
                                @foreach($deptStats as $dept => $count)
                                    @php
                                        $percentage = $results->count() > 0 ? round(($count / $results->count()) * 100, 1) : 0;
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700 truncate">{{ $dept ?: 'No Department' }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Monthly Trends -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Trends</h3>
                            <div class="space-y-3">
                                @php
                                    $monthlyStats = $results->groupBy(function($item) {
                                        return $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('M Y') : 'Unknown';
                                    })->map(function($group) {
                                        return $group->count();
                                    })->sortKeys()->take(6);
                                @endphp
                                @foreach($monthlyStats as $month => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700">{{ $month }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $count > 0 ? min(100, ($count / max($monthlyStats->max(), 1)) * 100) : 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-filter text-maroon mr-2"></i>
                        Filter Reports
                    </h3>
                    <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="date" id="filterDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="date" id="filterDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">All Statuses</option>
                        @foreach(['pending','approved_IOSA','approved_mhadel','approved_OTP','rejected_mhadel','rejected_OTP'] as $s)
                            <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Venue Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                    <select id="filterVenue" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">All Venues</option>
                        @foreach($venues ?? [] as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Department Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="filterDepartment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">All Departments</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Business Administration">Business Administration</option>
                        <option value="Student Affairs">Student Affairs</option>
                        <option value="Faculty Development">Faculty Development</option>
                    </select>
                </div>
                
                <!-- Quick Date Ranges -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Ranges</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setQuickRange('this_week')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            This Week
                        </button>
                        <button type="button" onclick="setQuickRange('this_month')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            This Month
                        </button>
                        <button type="button" onclick="setQuickRange('ytd')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            YTD
                        </button>
                        <button type="button" onclick="setQuickRange('clear')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="resetFilters()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </button>
                <button onclick="applyFilters()" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                        Export to Excel
                    </h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Date Range Filter for Export -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Date Range</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="date" id="exportDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="date" id="exportDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                </div>
                
                <!-- Quick Date Ranges for Export -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Ranges</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setExportQuickRange('this_week')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            This Week
                        </button>
                        <button type="button" onclick="setExportQuickRange('this_month')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            This Month
                        </button>
                        <button type="button" onclick="setExportQuickRange('ytd')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            YTD
                        </button>
                        <button type="button" onclick="setExportQuickRange('clear')" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>
                
                <!-- Status Filter for Export -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Include Statuses</label>
                    
                    <!-- Quick Status Presets -->
                    <div class="mb-3">
                        <button type="button" onclick="setStatusPreset('active')" class="px-3 py-1 text-xs border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2" title="Pending + Mhadel Approved + OTP Approved + Completed">
                            Active Only
                        </button>
                        <button type="button" onclick="setStatusPreset('all')" class="px-3 py-1 text-xs border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mr-2">
                            All Statuses
                        </button>
                        <button type="button" onclick="setStatusPreset('clear')" class="px-3 py-1 text-xs border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Clear All
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusPending" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Pending</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusApprovedIOSA" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">IOSA Approved</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusApprovedMhadel" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Mhadel Approved</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusApprovedOTP" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">OTP Approved</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusRejectedMhadel" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Mhadel Rejected</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusRejectedOTP" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">OTP Rejected</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="exportStatusCompleted" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Completed</span>
                        </label>
                    </div>
                </div>
                
                <!-- Export Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="includeFilters" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Include current page filters</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="includeSummary" class="mr-2 text-maroon focus:ring-maroon" checked>
                            <span class="text-sm text-gray-700">Include summary sheet</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeExportModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button onclick="executeExport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 shadow-md">
                    <i class="fas fa-download mr-2"></i>Export Now
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // View toggle functions
    function showListView() {
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('chartView').classList.add('hidden');
        document.getElementById('listViewBtn').classList.add('active');
        document.getElementById('chartViewBtn').classList.remove('active');
    }
    
    function showChartView() {
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('chartView').classList.remove('hidden');
        document.getElementById('chartViewBtn').classList.add('active');
        document.getElementById('listViewBtn').classList.remove('active');
    }
    
    // Filter Modal Functions
    function openFilterModal() {
        document.getElementById('filterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeFilterModal() {
        document.getElementById('filterModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function resetFilters() {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterVenue').value = '';
        document.getElementById('filterDepartment').value = '';
    }
    
    function setQuickRange(type) {
        const dateFrom = document.getElementById('filterDateFrom');
        const dateTo = document.getElementById('filterDateTo');
        const now = new Date();
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        switch(type) {
            case 'this_week':
                const monday = new Date(now);
                monday.setDate(now.getDate() - now.getDay() + 1);
                const sunday = new Date(monday);
                sunday.setDate(monday.getDate() + 6);
                dateFrom.value = formatDate(monday);
                dateTo.value = formatDate(sunday);
                break;
            case 'this_month':
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                dateFrom.value = formatDate(firstDay);
                dateTo.value = formatDate(lastDay);
                break;
            case 'ytd':
                const yearStart = new Date(now.getFullYear(), 0, 1);
                dateFrom.value = formatDate(yearStart);
                dateTo.value = formatDate(now);
                break;
            case 'clear':
                dateFrom.value = '';
                dateTo.value = '';
                break;
        }
    }
    
    function applyFilters() {
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const status = document.getElementById('filterStatus').value;
        const venue = document.getElementById('filterVenue').value;
        const department = document.getElementById('filterDepartment').value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (dateFrom) params.append('start_date', dateFrom);
        if (dateTo) params.append('end_date', dateTo);
        if (status) params.append('status', status);
        if (venue) params.append('venue_id', venue);
        if (department) params.append('department', department);
        
        // Redirect with filters
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
    
    // Tab functionality
    function filterByStatus(status) {
        const params = new URLSearchParams(window.location.search);
        if (status === 'all') {
            params.delete('status');
        } else {
            params.set('status', status);
        }
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
    
    // Export Modal Functions
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Pre-fill with current filter dates if available
        const currentFrom = document.getElementById('filterDateFrom')?.value;
        const currentTo = document.getElementById('filterDateTo')?.value;
        if (currentFrom) document.getElementById('exportDateFrom').value = currentFrom;
        if (currentTo) document.getElementById('exportDateTo').value = currentTo;
    }
    
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function setExportQuickRange(type) {
        const dateFrom = document.getElementById('exportDateFrom');
        const dateTo = document.getElementById('exportDateTo');
        const now = new Date();
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        switch(type) {
            case 'this_week':
                const monday = new Date(now);
                monday.setDate(now.getDate() - now.getDay() + 1);
                const sunday = new Date(monday);
                sunday.setDate(monday.getDate() + 6);
                dateFrom.value = formatDate(monday);
                dateTo.value = formatDate(sunday);
                break;
            case 'this_month':
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                dateFrom.value = formatDate(firstDay);
                dateTo.value = formatDate(lastDay);
                break;
            case 'ytd':
                const yearStart = new Date(now.getFullYear(), 0, 1);
                dateFrom.value = formatDate(yearStart);
                dateTo.value = formatDate(now);
                break;
            case 'clear':
                dateFrom.value = '';
                dateTo.value = '';
                break;
        }
    }
    
    function executeExport() {
        const dateFrom = document.getElementById('exportDateFrom').value;
        const dateTo = document.getElementById('exportDateTo').value;
        const includeFilters = document.getElementById('includeFilters').checked;
        const includeSummary = document.getElementById('includeSummary').checked;
        
        // Collect selected statuses
        const selectedStatuses = [];
        if (document.getElementById('exportStatusPending').checked) selectedStatuses.push('pending');
        if (document.getElementById('exportStatusApprovedIOSA').checked) selectedStatuses.push('approved_IOSA');
        if (document.getElementById('exportStatusApprovedMhadel').checked) selectedStatuses.push('approved_mhadel');
        if (document.getElementById('exportStatusApprovedOTP').checked) selectedStatuses.push('approved_OTP');
        if (document.getElementById('exportStatusRejectedMhadel').checked) selectedStatuses.push('rejected_mhadel');
        if (document.getElementById('exportStatusRejectedOTP').checked) selectedStatuses.push('rejected_OTP');
        if (document.getElementById('exportStatusCompleted').checked) selectedStatuses.push('completed');
        
        // Build export URL
        let exportUrl = '{{ route("mhadel.reports.export") }}?';
        const params = new URLSearchParams();
        
        // Add export date range
        if (dateFrom) params.append('export_start_date', dateFrom);
        if (dateTo) params.append('export_end_date', dateTo);
        
        // Add selected statuses
        if (selectedStatuses.length > 0) {
            params.append('export_statuses', selectedStatuses.join(','));
        }
        
        // Add current page filters if requested
        if (includeFilters) {
            const currentParams = new URLSearchParams(window.location.search);
            params.append('include_filters', '1');
            // Copy current filters
            ['start_date', 'end_date', 'status', 'venue_id', 'department'].forEach(key => {
                if (currentParams.has(key)) {
                    params.append(key, currentParams.get(key));
                }
            });
        }
        
        // Add export options
        params.append('include_summary', includeSummary ? '1' : '0');
        
        // Execute export
        window.location.href = exportUrl + params.toString();
        closeExportModal();
    }
    
    function setStatusPreset(type) {
        const checkboxes = {
            pending: document.getElementById('exportStatusPending'),
            approved_IOSA: document.getElementById('exportStatusApprovedIOSA'),
            approved_mhadel: document.getElementById('exportStatusApprovedMhadel'),
            approved_OTP: document.getElementById('exportStatusApprovedOTP'),
            rejected_mhadel: document.getElementById('exportStatusRejectedMhadel'),
            rejected_OTP: document.getElementById('exportStatusRejectedOTP'),
            completed: document.getElementById('exportStatusCompleted')
        };
        
        switch(type) {
            case 'active':
                // Your preferred selection: approved OTP, approved mhadel, pending, completed
                checkboxes.pending.checked = true;
                checkboxes.approved_IOSA.checked = false;
                checkboxes.approved_mhadel.checked = true;
                checkboxes.approved_OTP.checked = true;
                checkboxes.rejected_mhadel.checked = false;
                checkboxes.rejected_OTP.checked = false;
                checkboxes.completed.checked = true;
                break;
            case 'all':
                // Check all statuses
                Object.values(checkboxes).forEach(cb => cb.checked = true);
                break;
            case 'clear':
                // Uncheck all statuses
                Object.values(checkboxes).forEach(cb => cb.checked = false);
                break;
        }
    }
    
    // Chart variables
    let revenueChart = null;
    let statusChart = null;
    
    // Initialize charts
    function initializeCharts() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#800000',
                        backgroundColor: 'rgba(128, 0, 0, 0.1)',
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
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Mhadel Approved', 'OTP Approved', 'Mhadel Rejected', 'OTP Rejected', 'Completed'],
                    datasets: [{
                        data: [30, 25, 20, 10, 5, 10],
                        backgroundColor: [
                            '#F59E0B',
                            '#10B981',
                            '#3B82F6',
                            '#EF4444',
                            '#DC2626',
                            '#6366F1'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
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
        }
    }
    
    // Update charts with real data
    function updateCharts() {
        // Get data from the current results
        const results = @json($results);
        
        // Update status distribution
        if (statusChart) {
            const statusCounts = {
                'pending': 0,
                'approved_mhadel': 0,
                'approved_OTP': 0,
                'rejected_mhadel': 0,
                'rejected_OTP': 0,
                'completed': 0
            };
            
            results.forEach(item => {
                const status = item.status;
                if (statusCounts.hasOwnProperty(status)) {
                    statusCounts[status]++;
                }
            });
            
            statusChart.data.datasets[0].data = [
                statusCounts.pending,
                statusCounts.approved_mhadel,
                statusCounts.approved_OTP,
                statusCounts.rejected_mhadel,
                statusCounts.rejected_OTP,
                statusCounts.completed
            ];
            
            statusChart.update();
        }
        
        // Update revenue chart with monthly data
        if (revenueChart) {
            const monthlyRevenue = {};
            results.forEach(item => {
                if (item.start_date) {
                    const month = new Date(item.start_date).toLocaleDateString('en-US', { month: 'short' });
                    const revenue = parseFloat(item.final_price) || 0;
                    monthlyRevenue[month] = (monthlyRevenue[month] || 0) + revenue;
                }
            });
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const revenueData = months.map(month => monthlyRevenue[month] || 0);
            
            revenueChart.data.datasets[0].data = revenueData;
            revenueChart.update();
        }
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Set up event listeners
        document.getElementById('openFilterBtn').addEventListener('click', openFilterModal);
        
        // Close modals when clicking outside
        document.getElementById('filterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilterModal();
            }
        });
        
        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportModal();
            }
        });
        
        // Initialize view toggle buttons
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Initialize charts
        initializeCharts();
        
        // Update charts when switching to chart view
        document.getElementById('chartViewBtn').addEventListener('click', function() {
            setTimeout(updateCharts, 100);
        });
    });
</script>
@endsection 