@extends('layouts.drjavier')

@section('title', 'Reservations Management - OTP')
@section('page-title', 'Reservations Management')
@section('page-subtitle', 'Final approval authority for reservations')

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
    .reservation-card {
        transition: all 0.3s ease;
    }
    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
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
    .tab-active {
        border-bottom: 2px solid #800000;
        color: #800000;
        font-weight: 500;
    }
    .calendar-day {
        aspect-ratio: 1/1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 0.9rem;
        padding: 0.25rem;
        min-width: 5.5rem;
        max-width: 6rem;
    }
    .calendar-day:hover:not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .view-toggle-btn.active {
        background-color: white;
        color: #800000;
        font-weight: 500;
    }
    .view-toggle-btn:not(.active) {
        background-color: #f3f4f6;
        color: #6b7280;
    }
</style>

<div class="space-y-6 font-inter">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Awaiting Final Approval</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved by PPGS</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Rejected by PPGS</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['rejected'] }}</h3>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                    <i class="fas fa-calendar-check text-maroon mr-3"></i>
                    Final Approval Management - PPGS (Physical Plan & General Service)
                </h2>
                <div class="flex items-center space-x-2">
                    <form method="GET" action="{{ route('drjavier.reservations.index') }}" class="flex items-center space-x-2">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}" />
                        <div class="relative">
                            <input type="text" name="search" placeholder="Search by title, ID, purpose, or venue..." value="{{ request('search') }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button type="submit" class="px-3 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('drjavier.reservations.index', request()->except('search')) }}" class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                <i class="fas fa-times mr-1"></i>Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200">
            <button onclick="filterByStatus('all')" class="px-6 py-3 text-gray-700 hover:text-maroon transition-colors {{ request('status') == null ? 'tab-active' : '' }}">
                All Pending
            </button>
            <button onclick="filterByStatus('pending')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'pending' ? 'tab-active' : '' }}">
                Awaiting Approval
            </button>
            <button onclick="filterByStatus('approved')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'approved' ? 'tab-active' : '' }}">
                Approved by PPGS
            </button>
            <button onclick="filterByStatus('rejected')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'rejected' ? 'tab-active' : '' }}">
                Rejected by PPGS
            </button>
        </div>
        
        <!-- View Toggle -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button onclick="showListView()" id="listViewBtn" class="view-toggle-btn active px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-list mr-2"></i>List View
                    </button>
                    <button onclick="showCalendarView()" id="calendarViewBtn" class="view-toggle-btn px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        <i class="fas fa-calendar mr-2"></i>Calendar View
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    Showing {{ $reservations->count() }} of {{ $stats['pending'] }} pending reservations
                </div>
            </div>
        </div>
        
        <!-- List View -->
        <div id="listView" class="p-6">
            @if($reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($reservations as $reservation)
                        <div class="reservation-card bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    @if($reservation->status === 'approved_mhadel')
                                        <span class="status-badge status-pending">Awaiting PPGS Approval</span>
                                    @elseif($reservation->status === 'approved_OTP')
                                        <span class="status-badge status-approved">Approved by PPGS</span>
                                    @elseif($reservation->status === 'rejected_OTP')
                                        <span class="status-badge status-rejected">Rejected by PPGS</span>
                                    @else
                                        <span class="status-badge status-pending">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                    @endif
                                    <span class="text-sm text-gray-500">{{ $reservation->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('drjavier.reservations.show', $reservation->id) }}" class="btn-dark-blue px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                    @if($reservation->status === 'approved_mhadel')
                                        <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="btn-dark-green px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-check mr-1"></i>Final Approve
                                        </button>
                                        <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="btn-dark-red px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-times mr-1"></i>Final Reject
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <!-- Event Information -->
                                <div>
                                    <div class="mb-3">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $reservation->event_title }}</h3>
                                        <div class="text-xs text-gray-500 font-mono mt-1">
                                            ID: {{ $reservation->reservation_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->user->email }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-maroon w-4"></i>
                                            <span>{{ \Carbon\Carbon::parse($reservation->start_date)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Venue & Capacity -->
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-3">Venue & Capacity</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->venue->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->venue->capacity }} capacity</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->capacity ?? 'N/A' }} participants</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->purpose }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing Information -->
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-3">Pricing Details</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Base Price:</span>
                                            <span class="font-medium text-green-600">₱{{ number_format($reservation->base_price ?? 0, 2) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Discount:</span>
                                            <span class="font-medium text-blue-600">{{ $reservation->discount_percentage ?? 0 }}%</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Final Price:</span>
                                            <span class="font-medium text-green-800 text-lg">₱{{ number_format($reservation->final_price ?? 0, 2) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Rate/Hour:</span>
                                            <span class="font-medium text-gray-800">₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Equipment Details -->
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-3">Equipment & Duration</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Duration:</span>
                                            <span class="font-medium text-blue-600">{{ $reservation->start_date->diffInHours($reservation->end_date) }} hours</span>
                                        </div>
                                        @php
                                            $hasEquipment = ($reservation->equipment_details && count($reservation->equipment_details) > 0);
                                            $hasCustomEquipment = (!empty($reservation->custom_equipment_requests));
                                        @endphp
                                        @if($hasEquipment || $hasCustomEquipment)
                                            <div class="text-gray-600">Equipment:</div>
                                            <div class="space-y-1">
                                                @if($hasEquipment)
                                                    @foreach($reservation->equipment_details as $equipment)
                                                        <div class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                            <span class="font-medium">{{ $equipment['name'] }}</span>
                                                            <span class="text-gray-500">({{ $equipment['quantity'] }})</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if($hasCustomEquipment)
                                                    @foreach($reservation->custom_equipment_requests as $customEquipment)
                                                        <div class="text-xs bg-blue-100 px-2 py-1 rounded border border-blue-200">
                                                            <span class="font-medium text-blue-800">{{ $customEquipment['name'] ?? 'Custom Equipment' }}</span>
                                                            <span class="text-blue-600">({{ $customEquipment['quantity'] ?? 1 }})</span>
                                                            <span class="text-xs text-blue-500 ml-1">Custom</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-gray-500 text-xs">No equipment requested</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $reservations->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-check text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-4">No Pending PPGS Approvals</h3>
                    <p class="text-gray-500 mb-6">All reservations approved by OTP have been processed.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-blue-800">Workflow Information</h4>
                                <p class="text-blue-700 text-sm mt-1">Reservations approved by OTP will appear here for your final approval.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Calendar View -->
        <div id="calendarView" class="p-6 hidden">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center font-poppins">
                            <i class="fas fa-calendar-alt text-maroon mr-3"></i>
                            Reservation Calendar
                        </h2>
                        <div class="flex items-center space-x-2 bg-white rounded-lg shadow-md p-1.5">
                            <button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="currentMonth" class="font-medium text-gray-700 px-3 font-poppins"></span>
                            <button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Calendar Legend -->
                    <div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-400 rounded-md mr-2"></div>
                            <span class="text-gray-600">Pending PPGS Approval</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div>
                            <span class="text-gray-600">Approved by PPGS</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-400 rounded-md mr-2"></div>
                            <span class="text-gray-600">Rejected by PPGS</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div>
                            <span class="text-gray-600">Today</span>
                        </div>
                    </div>
                    <div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto">
                        <!-- Calendar will be populated by JavaScript -->
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
                        Filter Reservations
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
                
                <!-- Venue Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                    <select id="filterVenue" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                        <option value="">All Venues</option>
                        @foreach($venues as $venue)
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
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="resetFilters()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </button>
                <button onclick="applyFilters()" class="px-4 py-2 bg-red-800 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Final Approval
                    </h3>
                    <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-4">Are you sure you want to give final approval to this reservation?</p>
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold text-gray-800" id="approveEventTitle"></h4>
                    <p class="text-sm text-gray-600 mt-1">This is the final approval step. The reservation will be confirmed.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                    <textarea id="approveNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Add any additional notes for this final approval..."></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeApproveModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <form id="approveForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="approveNotesInput" name="notes">
                    <button type="submit" class="px-4 py-2 btn-dark-green rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Final Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-red-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        Final Rejection
                    </h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-4">Are you sure you want to give final rejection to this reservation?</p>
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold text-gray-800" id="rejectEventTitle"></h4>
                    <p class="text-sm text-gray-600 mt-1">This action cannot be undone.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection (Required)</label>
                    <textarea id="rejectNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please provide a reason for rejecting this reservation..." required></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeRejectModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <form id="rejectForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="rejectNotesInput" name="notes">
                    <button type="submit" class="px-4 py-2 btn-dark-red rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Final Reject
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Details Modal -->
<div id="reservationDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-calendar-check text-maroon mr-2"></i>
                        Reservation Details
                    </h3>
                    <button onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="reservationModalContent">
                <!-- Content populated by JavaScript -->
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeReservationModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Close
                </button>
                <a id="viewFullDetailsLink" href="#" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-colors">
                    View Full Details
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // View toggle functions
    function showListView() {
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('calendarView').classList.add('hidden');
        document.getElementById('listViewBtn').classList.add('active');
        document.getElementById('calendarViewBtn').classList.remove('active');
    }
    
    function showCalendarView() {
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('calendarView').classList.remove('hidden');
        document.getElementById('calendarViewBtn').classList.add('active');
        document.getElementById('listViewBtn').classList.remove('active');
        renderCalendar();
    }
    
    // Calendar functionality
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    const reservationsData = @json($reservations);
    
    function renderCalendar() {
        const calendar = document.getElementById('calendar');
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        const monthNames = ["January", "February", "March", "April", "May", "June",
                           "July", "August", "September", "October", "November", "December"];
        
        document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        let html = '';
        
        // Day headers
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayNames.forEach(day => {
            html += `<div class="text-center py-2 text-sm font-medium text-gray-500 bg-gray-50">${day}</div>`;
        });
        
        // Calendar days
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const isCurrentMonth = date.getMonth() === currentMonth;
            const isToday = date.toDateString() === new Date().toDateString();
            
            let dayClass = 'calendar-day text-center py-3 relative rounded-lg transition-all duration-200 cursor-pointer';
            
            if (!isCurrentMonth) {
                dayClass += ' text-gray-400 bg-gray-50';
            } else if (isToday) {
                dayClass += ' bg-maroon text-white font-bold';
            } else {
                dayClass += ' bg-white hover:bg-gray-50';
            }
            
            // Check for reservations on this date
            const dateString = date.toISOString().split('T')[0];
            const dayReservations = reservationsData.data.filter(reservation => 
                reservation.start_date.startsWith(dateString)
            );
            
            // Add visual indicator if there are reservations
            let reservationIndicator = '';
            if (dayReservations.length > 0) {
                const reservation = dayReservations[0];
                let statusColor = 'bg-yellow-400'; // Default for pending
                
                if (reservation.status === 'approved_OTP') {
                    statusColor = 'bg-green-600'; // Green for final approval
                } else if (reservation.status === 'rejected_OTP') {
                    statusColor = 'bg-red-400'; // Red for rejection
                }
                
                reservationIndicator = `
                    <div class="absolute w-3 h-3 ${statusColor} rounded-full"
                         style="top: 4px; right: 4px;"
                         title="${dayReservations.length} reservation(s) on this date">
                    </div>
                `;
            }
            
            html += `
                <div class="${dayClass}" onclick="showReservationsForDate(new Date('${dateString}'))">
                    <div class="text-sm font-medium">${date.getDate()}</div>
                    ${reservationIndicator}
                </div>
            `;
        }
        
        calendar.innerHTML = html;
    }
    
    function previousMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    }
    
    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    }
    
    // Approve Modal Functions
    function openApproveModal(reservationId, eventTitle) {
        document.getElementById('approveEventTitle').textContent = eventTitle;
        document.getElementById('approveForm').action = `/drjavier/reservations/${reservationId}/approve`;
        document.getElementById('approveModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('approveNotes').value = '';
        document.body.style.overflow = 'auto';
    }
    
    // Reject Modal Functions
    function openRejectModal(reservationId, eventTitle) {
        document.getElementById('rejectEventTitle').textContent = eventTitle;
        document.getElementById('rejectForm').action = `/drjavier/reservations/${reservationId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectNotes').value = '';
        document.body.style.overflow = 'auto';
    }
    
    // Form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Set up event listeners
        document.getElementById('openFilterBtn').addEventListener('click', openFilterModal);
        
        // Handle approve form submission
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            const notes = document.getElementById('approveNotes').value;
            document.getElementById('approveNotesInput').value = notes;
        });
        
        // Handle reject form submission
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            const notes = document.getElementById('rejectNotes').value;
            if (!notes.trim()) {
                e.preventDefault();
                alert('Please provide a reason for rejection.');
                return;
            }
            document.getElementById('rejectNotesInput').value = notes;
        });
        
        // Close modals when clicking outside
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
        
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
        
        // Filter Modal Functions
        document.getElementById('filterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilterModal();
            }
        });
    });
    
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
        document.getElementById('filterVenue').value = '';
        document.getElementById('filterDepartment').value = '';
        applyFilters();
    }
    
    function applyFilters() {
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const venue = document.getElementById('filterVenue').value;
        const department = document.getElementById('filterDepartment').value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (venue) params.append('venue', venue);
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

    function showReservationsForDate(date) {
        const dateString = date.toISOString().split('T')[0];
        const dayReservations = reservationsData.data.filter(r => r.start_date.startsWith(dateString));
        if (dayReservations.length === 0) return;

        const r = dayReservations[0];
        const start = new Date(r.start_date);
        const end = new Date(r.end_date);

        const equipmentHtml = (r.equipment_details && r.equipment_details.length)
            ? r.equipment_details.map(e => `<div class="text-xs bg-gray-100 px-2 py-1 rounded">${e.name} <span class="text-gray-500">(${e.quantity})</span></div>`).join('')
            : '<div class="text-gray-500 text-xs">No equipment requested</div>';

        const pricingRows = `
            <div class="flex items-center justify-between"><span class="text-gray-600">Base Price:</span><span class="font-medium text-green-600">₱${Number(r.base_price || 0).toFixed(2)}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Discount:</span><span class="font-medium text-blue-600">${Number(r.discount_percentage || 0)}%</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Final Price:</span><span class="font-medium text-green-800 text-lg">₱${Number(r.final_price || 0).toFixed(2)}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Rate/Hour:</span><span class="font-medium text-gray-800">₱${Number(r.price_per_hour || 0).toFixed(2)}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-600">Duration:</span><span class="font-medium text-blue-600">${Number(r.duration_hours || 0)} hours</span></div>
        `;

        const content = `
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-800 text-lg mb-2">${r.event_title}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600"><strong>Requester:</strong> ${r.user?.name || ''}</p>
                            <p class="text-gray-600"><strong>Date:</strong> ${start.toLocaleDateString()}</p>
                            <p class="text-gray-600"><strong>Time:</strong> ${start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                        </div>
                        <div>
                            <p class="text-gray-600"><strong>Venue:</strong> ${r.venue?.name || ''}</p>
                            <p class="text-gray-600"><strong>Capacity:</strong> ${r.venue?.capacity || ''}</p>
                            <p class="text-gray-600"><strong>Purpose:</strong> ${r.purpose || ''}</p>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="font-medium text-gray-800 mb-2">Pricing</h5>
                            <div class="space-y-2 text-sm">${pricingRows}</div>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-800 mb-2">Equipment</h5>
                            <div class="space-y-1">${equipmentHtml}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('reservationModalContent').innerHTML = content;
        const link = document.getElementById('viewFullDetailsLink');
        link.href = `/drjavier/reservations/${r.id}`;

        document.getElementById('reservationDetailsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReservationModal() {
        document.getElementById('reservationDetailsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // close when clicking outside
    document.getElementById('reservationDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) closeReservationModal();
    });
</script>
@endsection 