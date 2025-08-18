@extends('layouts.mhadel')

@section('title', 'Reservations Management')
@section('page-title', 'Reservations Management')

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
    .status-completed {
        background-color: #6366F1;
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
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(128, 0, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0); }
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
    
    .discount-btn.selected {
        background-color: #10B981;
        color: white;
        border-color: #10B981;
    }
    
    .discount-btn:hover:not(.selected) {
        background-color: #f3f4f6;
        border-color: #d1d5db;
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
                <p class="text-sm text-gray-500 font-medium">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending Review</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Rejected</p>
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
                    Reservation Management - Ms. Mhadel
                </h2>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" placeholder="Search reservations..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
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
                All Reservations
            </button>
            <button onclick="filterByStatus('pending')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'pending' ? 'tab-active' : '' }}">
                Pending Review
            </button>
            <button onclick="filterByStatus('approved')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'approved' ? 'tab-active' : '' }}">
                Approved
            </button>
            <button onclick="filterByStatus('rejected')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'rejected' ? 'tab-active' : '' }}">
                Rejected
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
                    Showing {{ $reservations->count() }} of {{ $stats['total'] }} reservations
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
                                    <span class="status-badge status-pending">
                                        Pending Review
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $reservation->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('mhadel.reservations.show', $reservation->id) }}" class="btn-dark-blue px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                    <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="btn-dark-green px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                    <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="btn-dark-red px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-lg mb-3">{{ $reservation->event_title }}</h3>
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
                                            <i class="fas fa-phone mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->user->phone ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-3">Event Details</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-maroon w-4"></i>
                                            <span>{{ \Carbon\Carbon::parse($reservation->start_date)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('H:i') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->capacity ?? 'N/A' }} participants</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-3">Venue Information</h4>
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
                                            <i class="fas fa-info-circle mr-2 text-maroon w-4"></i>
                                            <span>{{ $reservation->purpose }}</span>
                                        </div>
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
                    <h3 class="text-2xl font-bold text-gray-700 mb-4">No Pending Reservations</h3>
                    <p class="text-gray-500 mb-6">All IOSA approved reservations have been reviewed.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-blue-800">Workflow Information</h4>
                                <p class="text-blue-700 text-sm mt-1">Reservations approved by IOSA will appear here for your review.</p>
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
                            <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">IOSA Approved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-100 border border-green-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">Mhadel Approved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div>
                            <span class="text-gray-600">Final Approval</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-100 border border-red-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">Rejected</span>
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
                <!-- Content will be populated by JavaScript -->
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeReservationModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Close
                </button>
                <button onclick="viewFirstReservation()" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-colors">
                    View Full Details
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Approve Reservation
                    </h3>
                    <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column: Reservation Details -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3 text-lg">Reservation Details</h4>
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <h4 class="font-semibold text-gray-800" id="approveEventTitle"></h4>
                            <p class="text-sm text-gray-600 mt-1">This reservation will be forwarded to OTP for final approval.</p>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <span>Current Status: <span class="font-medium text-blue-600">IOSA Approved</span></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    <i class="fas fa-arrow-right text-green-500 mr-2"></i>
                                    <span>Next Step: <span class="font-medium text-green-600">Ms. Mhadel Review</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea id="approveNotes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Add any additional notes for this approval..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Right Column: Pricing & Discount -->
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3 text-lg">Pricing & Discount</h4>
                        
                        <!-- Venue Rate and Base Price Display -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Pricing Information</label>
                            <div class="space-y-3">
                                <!-- Venue Rate per Hour -->
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-blue-700">Rate per Hour:</span>
                                        <span id="venueRatePerHour" class="text-lg font-bold text-blue-800">₱0.00</span>
                                    </div>
                                </div>
                                
                                <!-- Calculated Base Price -->
                                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-green-700">Calculated Base Price:</span>
                                        <span id="calculatedBasePrice" class="text-lg font-bold text-green-800">₱0.00</span>
                                    </div>
                                    <div class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <span id="basePriceCalculation"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Final Price Setting -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Final Price (₱) <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-600 mb-2">Set the final price for this reservation. Enter 0 for free events.</p>
                            <input type="number" id="basePrice" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Enter final price or 0 for free events" required>
                        </div>
                        
                        <!-- Discount Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount (Optional)</label>
                            <p class="text-xs text-gray-600 mb-2">Select a discount percentage to apply to the final price.</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="selectDiscount(0)" id="discount-0" class="discount-btn px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    No Discount
                                </button>
                                <button type="button" onclick="selectDiscount(20)" id="discount-20" class="discount-btn px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    20% Off
                                </button>
                                <button type="button" onclick="selectDiscount(30)" id="discount-30" class="discount-btn px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    30% Off
                                </button>
                                <button type="button" onclick="selectDiscount(50)" id="discount-50" class="discount-btn px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                    50% Off
                                </button>
                            </div>
                        </div>
                        
                        <!-- Final Price Display -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-green-800">Price After Discount:</span>
                                <span id="finalPrice" class="text-xl font-bold text-green-800">₱0.00</span>
                            </div>
                            <div id="discountInfo" class="text-xs text-green-600 hidden">
                                <span id="discountAmount"></span> discount applied
                            </div>
                        </div>
                        
                        <!-- Summary -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-start">
                                <i class="fas fa-save text-blue-500 mr-2 mt-0.5"></i>
                                <div class="text-xs text-blue-700">
                                    <p class="font-medium">This information will be saved:</p>
                                    <ul class="mt-1 space-y-1">
                                        <li>• Final price set by Ms. Mhadel</li>
                                        <li>• Discount percentage (if applied)</li>
                                        <li>• Approval notes and timestamp</li>
                                        <li>• Status updated to "Ms. Mhadel Approved"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeApproveModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <form id="approveForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="approveNotesInput" name="notes">
                    <input type="hidden" id="approveBasePriceInput" name="base_price">
                    <input type="hidden" id="approveDiscountInput" name="discount">
                    <input type="hidden" id="approveFinalPriceInput" name="final_price">
                    <button type="submit" class="px-4 py-2 btn-dark-green rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Approve Reservation
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
                        Reject Reservation
                    </h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <p class="text-gray-700 mb-4">Are you sure you want to reject this reservation?</p>
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
                        <i class="fas fa-times mr-2"></i>Reject Reservation
                    </button>
                </form>
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
    
    // Debug: Log the entire reservations data structure
    console.log('Full reservations data:', reservationsData);
    console.log('First reservation sample:', reservationsData.data?.[0]);
    if (reservationsData.data && reservationsData.data.length > 0) {
        console.log('First reservation fields:', Object.keys(reservationsData.data[0]));
        console.log('First reservation price_per_hour:', reservationsData.data[0].price_per_hour);
        console.log('First reservation duration_hours:', reservationsData.data[0].duration_hours);
        console.log('First reservation base_price:', reservationsData.data[0].base_price);
        console.log('First reservation venue_id:', reservationsData.data[0].venue_id);
        
        // Check if venue data is loaded
        if (reservationsData.data[0].venue) {
            console.log('First reservation venue data:', reservationsData.data[0].venue);
            console.log('Venue price_per_hour:', reservationsData.data[0].venue.price_per_hour);
        } else {
            console.log('No venue data loaded for first reservation');
        }
    }
    
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
                // Determine the status color based on the new status values
                let statusColor = 'bg-yellow-400'; // Default for pending
                const reservation = dayReservations[0]; // Use first reservation for color
                
                if (reservation.status === 'approved_IOSA') {
                    statusColor = 'bg-blue-400'; // Blue for IOSA approved
                } else if (reservation.status === 'approved_mhadel') {
                    statusColor = 'bg-green-400'; // Green for Mhadel approved
                } else if (reservation.status === 'approved_OTP') {
                    statusColor = 'bg-green-600'; // Darker green for final approval
                } else if (reservation.status === 'rejected_IOSA' || reservation.status === 'rejected_mhadel' || reservation.status === 'rejected_OTP') {
                    statusColor = 'bg-red-400'; // Red for any rejection
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
    
    function showReservationsForDate(date) {
        const dateString = date.toISOString().split('T')[0];
        const dayReservations = reservationsData.data.filter(reservation => 
            reservation.start_date.startsWith(dateString)
        );
        
        if (dayReservations.length > 0) {
            const reservation = dayReservations[0]; // Show first reservation
            const modalContent = document.getElementById('reservationModalContent');
            
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800 text-lg mb-2">${reservation.event_title}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600"><strong>Requester:</strong> ${reservation.user.name}</p>
                                <p class="text-gray-600"><strong>Date:</strong> ${new Date(reservation.start_date).toLocaleDateString()}</p>
                                <p class="text-gray-600"><strong>Time:</strong> ${new Date(reservation.start_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${new Date(reservation.end_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            </div>
                            <div>
                                <p class="text-gray-600"><strong>Venue:</strong> ${reservation.venue.name}</p>
                                <p class="text-gray-600"><strong>Capacity:</strong> ${reservation.venue.capacity}</p>
                                <p class="text-gray-600"><strong>Purpose:</strong> ${reservation.purpose}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                Pending Review
                            </span>
                        </div>
                    </div>
                </div>
            `;
            
            // Store reservation ID for the "View Full Details" button
            window.currentReservationId = reservation.id;
            
            document.getElementById('reservationDetailsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeReservationModal() {
        document.getElementById('reservationDetailsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function viewFirstReservation() {
        if (window.currentReservationId) {
            window.location.href = `/mhadel/reservations/${window.currentReservationId}`;
        }
    }
    
    // Approve Modal Functions
    function openApproveModal(reservationId, eventTitle) {
        document.getElementById('approveEventTitle').textContent = eventTitle;
        document.getElementById('approveForm').action = `/mhadel/reservations/${reservationId}/approve`;
        document.getElementById('approveModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Get the existing price from the reservation data
        const reservation = reservationsData.data.find(r => r.id == reservationId);
        
        // Debug: Log the reservation data to console
        console.log('Reservation data:', reservation);
        console.log('Base price:', reservation?.base_price);
        console.log('Final price:', reservation?.final_price);
        console.log('Price per hour:', reservation?.price_per_hour);
        console.log('Duration hours:', reservation?.duration_hours);
        console.log('All available fields:', Object.keys(reservation || {}));
        
        // Check venue data
        if (reservation && reservation.venue) {
            console.log('Venue data:', reservation.venue);
            console.log('Venue price_per_hour:', reservation.venue.price_per_hour);
        } else {
            console.log('No venue data found in reservation');
        }
        
        // Check for base_price first, then fallback to final_price if base_price is not available
        let userPrice = null;
        let priceSource = '';
        
        if (reservation && reservation.base_price && parseFloat(reservation.base_price) > 0) {
            userPrice = parseFloat(reservation.base_price);
            priceSource = 'base_price';
        } else if (reservation && reservation.final_price && parseFloat(reservation.final_price) > 0) {
            userPrice = parseFloat(reservation.final_price);
            priceSource = 'final_price';
        }
        
        // Update the venue pricing information display
        const venueRatePerHour = document.getElementById('venueRatePerHour');
        const calculatedBasePrice = document.getElementById('calculatedBasePrice');
        const basePriceCalculation = document.getElementById('basePriceCalculation');
        

        
        // Display venue rate per hour (if available)
        if (reservation && reservation.price_per_hour && parseFloat(reservation.price_per_hour) > 0) {
            const ratePerHour = parseFloat(reservation.price_per_hour);
            venueRatePerHour.textContent = `₱${ratePerHour.toFixed(2)}`;
        } else {
            // Try to get rate from venue data if available
            if (reservation && reservation.venue && reservation.venue.price_per_hour) {
                const venueRate = parseFloat(reservation.venue.price_per_hour);
                venueRatePerHour.textContent = `₱${venueRate.toFixed(2)} (from venue)`;
            } else {
                venueRatePerHour.textContent = 'Not available';
            }
        }
        
        // Display calculated base price
        if (userPrice) {
            const userPriceFormatted = userPrice.toFixed(2);
            calculatedBasePrice.textContent = `₱${userPriceFormatted}`;
            
            // Show calculation details if we have both rate and duration
            if (reservation && reservation.price_per_hour && reservation.duration_hours) {
                const rate = parseFloat(reservation.price_per_hour);
                const duration = parseInt(reservation.duration_hours);
                basePriceCalculation.textContent = `Rate: ₱${rate.toFixed(2)}/hour × ${duration} hour${duration > 1 ? 's' : ''} = ₱${userPriceFormatted}`;
            } else if (reservation && reservation.venue && reservation.venue.price_per_hour && reservation.duration_hours) {
                // Fallback to venue rate if reservation rate is not available
                const rate = parseFloat(reservation.venue.price_per_hour);
                const duration = parseInt(reservation.duration_hours);
                const calculatedPrice = rate * duration;
                basePriceCalculation.textContent = `Rate: ₱${rate.toFixed(2)}/hour × ${duration} hour${duration > 1 ? 's' : ''} = ₱${calculatedPrice.toFixed(2)} (calculated from venue rate)`;
            } else {
                basePriceCalculation.textContent = `Base price from reservation data (₱${userPriceFormatted})`;
            }
        } else {
            calculatedBasePrice.textContent = '₱0.00';
            basePriceCalculation.textContent = 'Free event or no pricing data';
        }
        
        // Don't reset the pricing display - we want to show the actual data from the database
        // Only reset the form input fields and discount selection
        document.getElementById('basePrice').value = '';
        selectedDiscount = 0;
        
        // Reset button styles
        document.querySelectorAll('.discount-btn').forEach(btn => {
            btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.add('border-gray-300', 'hover:bg-gray-50');
        });
        
        // Highlight "No Discount" by default
        document.getElementById('discount-0').classList.add('bg-green-500', 'text-white', 'border-green-500');
        document.getElementById('discount-0').classList.remove('border-gray-300', 'hover:bg-gray-50');
        
        // Reset final price display
        document.getElementById('finalPrice').textContent = '₱0.00';
        document.getElementById('discountInfo').classList.add('hidden');
    }
    
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('approveNotes').value = '';
        document.body.style.overflow = 'auto';
    }
    
    // Price and Discount Functions
    let selectedDiscount = 0;
    
    function selectDiscount(discount) {
        selectedDiscount = discount;
        
        // Update button styles
        document.querySelectorAll('.discount-btn').forEach(btn => {
            btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.add('border-gray-300', 'hover:bg-gray-50');
        });
        
        // Highlight selected discount
        if (discount > 0) {
            document.getElementById(`discount-${discount}`).classList.add('bg-green-500', 'text-white', 'border-green-500');
            document.getElementById(`discount-${discount}`).classList.remove('border-gray-300', 'hover:bg-gray-50');
        } else {
            document.getElementById('discount-0').classList.add('bg-green-500', 'text-white', 'border-green-500');
            document.getElementById('discount-0').classList.remove('border-gray-300', 'hover:bg-gray-50');
        }
        
        calculateFinalPrice();
    }
    
    function calculateFinalPrice() {
        const finalPrice = parseFloat(document.getElementById('basePrice').value) || 0;
        const discount = selectedDiscount;
        
        let priceAfterDiscount = finalPrice;
        let discountAmount = 0;
        
        if (discount > 0) {
            discountAmount = (finalPrice * discount) / 100;
            priceAfterDiscount = finalPrice - discountAmount;
        }
        
        // Update display
        document.getElementById('finalPrice').textContent = `₱${priceAfterDiscount.toFixed(2)}`;
        
        if (discount > 0) {
            document.getElementById('discountInfo').classList.remove('hidden');
            document.getElementById('discountAmount').textContent = `${discount}% (₱${discountAmount.toFixed(2)})`;
        } else {
            document.getElementById('discountInfo').classList.add('hidden');
        }
    }
    
    function resetPriceAndDiscount() {
        document.getElementById('basePrice').value = '';
        selectedDiscount = 0;
        
        // Reset button styles
        document.querySelectorAll('.discount-btn').forEach(btn => {
            btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.add('border-gray-300', 'hover:bg-gray-50');
        });
        
        // Highlight "No Discount" by default
        document.getElementById('discount-0').classList.add('bg-green-500', 'text-white', 'border-green-500');
        document.getElementById('discount-0').classList.remove('border-gray-300', 'hover:bg-gray-50');
        
        // Reset final price display
        document.getElementById('finalPrice').textContent = '₱0.00';
        document.getElementById('discountInfo').classList.add('hidden');
        
        // Reset venue pricing information display
        document.getElementById('venueRatePerHour').textContent = '₱0.00';
        document.getElementById('calculatedBasePrice').textContent = '₱0.00';
        document.getElementById('basePriceCalculation').textContent = '';
    }
    
    // Reject Modal Functions
    function openRejectModal(reservationId, eventTitle) {
        document.getElementById('rejectEventTitle').textContent = eventTitle;
        document.getElementById('rejectForm').action = `/mhadel/reservations/${reservationId}/reject`;
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
            const finalPriceInput = document.getElementById('basePrice').value;
            
            // Validate that final price is entered
            if (finalPriceInput === '') {
                e.preventDefault();
                alert('Please enter the final price for this reservation.');
                document.getElementById('basePrice').focus();
                return;
            }
            
            const finalPrice = parseFloat(finalPriceInput) || 0;
            const discount = selectedDiscount;
            let priceAfterDiscount = finalPrice;
            
            if (discount > 0) {
                priceAfterDiscount = finalPrice - (finalPrice * discount / 100);
            }
            
            document.getElementById('approveNotesInput').value = notes;
            document.getElementById('approveBasePriceInput').value = finalPrice;
            document.getElementById('approveDiscountInput').value = discount;
            document.getElementById('approveFinalPriceInput').value = priceAfterDiscount.toFixed(2);
        });
        
        // Add event listener for base price input
        document.getElementById('basePrice').addEventListener('input', calculateFinalPrice);
        
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
        
        document.getElementById('reservationDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReservationModal();
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
</script>
@endsection 