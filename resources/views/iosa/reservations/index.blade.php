@extends('layouts.iosa')

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
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved'] }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
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
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-calendar-check text-maroon mr-3"></i>
                    Reservation Management - IOSA
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
                Pending
            </button>
            <button onclick="filterByStatus('approved')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'approved' ? 'tab-active' : '' }}">
                Approved
            </button>
            <button onclick="filterByStatus('rejected')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'rejected' ? 'tab-active' : '' }}">
                Rejected
            </button>
            <button onclick="filterByStatus('completed')" class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors {{ request('status') == 'completed' ? 'tab-active' : '' }}">
                Completed
            </button>
        </div>
        
        <!-- Calendar View Toggle -->
        <div class="flex justify-end p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                <button id="listViewBtn" class="px-4 py-2 bg-white text-gray-700 border-r border-gray-300 flex items-center view-toggle-btn active">
                    <i class="fas fa-list mr-2 text-maroon"></i>List View
                </button>
                <button id="calendarViewBtn" class="px-4 py-2 bg-gray-100 text-gray-500 flex items-center view-toggle-btn">
                    <i class="fas fa-calendar-alt mr-2"></i>Calendar View
                </button>
            </div>
        </div>
        
        <!-- List View -->
        <div id="listView" class="p-6">
            @if($reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($reservations as $reservation)
                        <div class="reservation-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        @if($reservation->status === 'pending')
                                            <span class="status-badge status-pending mr-3">Pending</span>
                                        @elseif($reservation->status === 'approved')
                                            <span class="status-badge status-approved mr-3">Approved</span>
                                        @elseif($reservation->status === 'rejected')
                                            <span class="status-badge status-rejected mr-3">Rejected</span>
                                        @else
                                            <span class="status-badge status-completed mr-3">{{ ucfirst($reservation->status) }}</span>
                                        @endif
                                        <span class="text-sm text-gray-500">Submitted {{ $reservation->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $reservation->event_title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-user mr-2 text-maroon"></i>
                                        <span>{{ $reservation->user->name }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-maroon"></i>
                                            <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-maroon"></i>
                                            <span>{{ $reservation->start_date->format('g:i A') }} - {{ $reservation->end_date->format('g:i A') }}</span>
                                        </div>
                                        @if($reservation->venue)
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                                                <span>{{ $reservation->venue->name }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-maroon"></i>
                                            <span>{{ $reservation->capacity }} attendees</span>
                                        </div>
                                        @if($reservation->final_price)
                                            <div class="flex items-center">
                                                <i class="fas fa-tag mr-2 text-maroon"></i>
                                                <span>₱{{ number_format($reservation->final_price, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($reservation->purpose)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>Purpose:</strong> {{ $reservation->purpose }}
                                        </div>
                                    @endif
                                    
                                    @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>Equipment:</strong> 
                                            @foreach($reservation->equipment_details as $equipment)
                                                <span class="inline-block bg-gray-100 px-2 py-1 rounded mr-2 mb-1">
                                                    {{ $equipment['name'] }} ({{ $equipment['quantity'] }})
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                                    <a href="{{ route('iosa.reservations.show', $reservation->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($reservation->status === 'pending')
                                        <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="p-2 btn-dark-green rounded-lg transition-colors" title="Approve Reservation">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        
                                        <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="p-2 btn-dark-red rounded-lg transition-colors" title="Reject Reservation">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($reservation->status === 'approved')
                                        <span class="text-sm text-green-600 font-medium">✓ Approved by IOSA</span>
                                    @elseif($reservation->status === 'rejected')
                                        <span class="text-sm text-red-600 font-medium">✗ Rejected by IOSA</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-6">
                    <div class="text-sm text-gray-600">
                        Showing {{ $reservations->firstItem() }}-{{ $reservations->lastItem() }} of {{ $reservations->total() }} reservations
                    </div>
                    <div class="flex space-x-1">
                        @if($reservations->onFirstPage())
                            <button class="px-3 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        @else
                            <a href="{{ $reservations->previousPageUrl() }}" class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif
                        
                        @foreach($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                            @if($page == $reservations->currentPage())
                                <span class="px-3 py-1 rounded border border-gray-300 bg-maroon text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($reservations->hasMorePages())
                            <a href="{{ $reservations->nextPageUrl() }}" class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <button class="px-3 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-check text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No Reservations Found</h3>
                    <p class="text-gray-500">There are no reservations to display at the moment.</p>
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
                            <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">Pending Review</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-100 border border-green-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">IOSA Approved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-100 border border-red-300 rounded-md mr-2"></div>
                            <span class="text-gray-600">IOSA Rejected</span>
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
    
    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto font-poppins animate-fadeIn">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
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
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Approve Reservation
                        </h3>
                        <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Are you sure you want to approve this reservation?</p>
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h4 class="font-semibold text-gray-800" id="approveEventTitle"></h4>
                        <p class="text-sm text-gray-600 mt-1">This reservation will be forwarded to Ms. Mhadel for final approval.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                        <textarea id="approveNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Add any additional notes for this approval..."></textarea>
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
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
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
</div>

<!-- Reservation Details Modal -->
<div id="reservationDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-calendar-day text-maroon mr-2"></i>
                        <span id="modalDateTitle">Reservation Details</span>
                    </h3>
                    <button onclick="closeReservationDetailsModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div id="reservationDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeReservationDetailsModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Close
                </button>
                <button id="viewFirstReservationBtn" onclick="viewFirstReservation()" class="px-4 py-2 btn-dark-blue rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>View Details
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
    
    // Approve Modal Functions
    function openApproveModal(reservationId, eventTitle) {
        document.getElementById('approveEventTitle').textContent = eventTitle;
        document.getElementById('approveForm').action = `/iosa/reservations/${reservationId}/approve`;
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
        document.getElementById('rejectForm').action = `/iosa/reservations/${reservationId}/reject`;
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
        
        // Close modals when clicking outside
        document.getElementById('filterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilterModal();
            }
        });
        
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
        
        // Reservation Details Modal
        document.getElementById('reservationDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReservationDetailsModal();
            }
        });
        
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
    });
    
    // Calendar functionality
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    const reservationsData = @json($reservations);
    
    // View toggle functionality
    document.getElementById('listViewBtn').addEventListener('click', function() {
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('calendarView').classList.add('hidden');
        this.classList.add('active');
        document.getElementById('calendarViewBtn').classList.remove('active');
    });
    
    document.getElementById('calendarViewBtn').addEventListener('click', function() {
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('calendarView').classList.remove('hidden');
        this.classList.add('active');
        document.getElementById('listViewBtn').classList.remove('active');
        renderCalendar();
    });
    
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
                
                if (reservation.status === 'pending') {
                    statusColor = 'bg-yellow-400'; // Yellow for pending
                } else if (reservation.status === 'approved_IOSA') {
                    statusColor = 'bg-green-400'; // Green for IOSA approved
                } else if (reservation.status === 'rejected_IOSA') {
                    statusColor = 'bg-red-400'; // Red for IOSA rejected
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
    
    function updateCurrentMonth() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('currentMonth').textContent = 
            `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
    
    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
        updateCurrentMonth();
    }
    
    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
        updateCurrentMonth();
    }
    
    function showReservationsForDate(date) {
        openReservationDetailsModal(date);
    }
    
    // Reservation Details Modal Functions
    let selectedDateForModal = null;
    
    function openReservationDetailsModal(date) {
        selectedDateForModal = date;
        const dayReservations = reservationsData.data.filter(reservation => {
            const startDate = new Date(reservation.start_date);
            const endDate = new Date(reservation.end_date);
            const currentDate = new Date(date);
            return currentDate >= startDate && currentDate <= endDate;
        });
        
        if (dayReservations.length === 0) {
            alert('No reservations for this date.');
            return;
        }
        
        const modalContent = document.getElementById('reservationDetailsContent');
        modalContent.innerHTML = ''; // Clear previous content
        
        dayReservations.forEach((reservation, index) => {
            const statusColor = reservation.status === 'pending' ? '🟡' : 
                              reservation.status === 'approved_IOSA' ? '🟢' : '🔴';
            const statusText = reservation.status.toUpperCase();
            const statusClass = reservation.status === 'pending' ? 'text-yellow-600' : 
                              reservation.status === 'approved_IOSA' ? 'text-green-600' : 'text-red-600';
            
            const reservationHtml = `
                <div class="bg-gray-50 p-4 rounded-lg mb-4 border-l-4 border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 text-lg">${reservation.event_title}</h4>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <i class="fas fa-user mr-2 text-maroon"></i>
                                <span>${reservation.user}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <i class="fas fa-clock mr-2 text-maroon"></i>
                                <span>${reservation.start_date} ${reservation.start_time} - ${reservation.end_date} ${reservation.end_time}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                                <span>${reservation.venue}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <i class="fas fa-users mr-2 text-maroon"></i>
                                <span>${reservation.capacity} attendees</span>
                            </div>
                            ${reservation.purpose ? `
                            <div class="flex items-start text-sm text-gray-600 mt-1">
                                <i class="fas fa-align-left mr-2 text-maroon mt-0.5"></i>
                                <span>${reservation.purpose}</span>
                            </div>
                            ` : ''}
                            ${reservation.final_price ? `
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <i class="fas fa-tag mr-2 text-maroon"></i>
                                <span>₱${parseFloat(reservation.final_price).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            </div>
                            ` : ''}
                            ${reservation.equipment_details && reservation.equipment_details.length > 0 ? `
                            <div class="flex items-start text-sm text-gray-600 mt-1">
                                <i class="fas fa-tools mr-2 text-maroon mt-0.5"></i>
                                <span>${reservation.equipment_details.map(eq => eq.name + ' (' + eq.quantity + ')').join(', ')}</span>
                            </div>
                            ` : ''}
                        </div>
                        <div class="ml-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass} bg-gray-100">
                                ${statusColor} ${statusText}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            modalContent.innerHTML += reservationHtml;
        });
        
        document.getElementById('modalDateTitle').textContent = `Reservations for ${new Date(date).toLocaleDateString()}`;
        document.getElementById('reservationDetailsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeReservationDetailsModal() {
        document.getElementById('reservationDetailsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        selectedDateForModal = null;
    }
    
    function viewFirstReservation() {
        if (!selectedDateForModal) {
            alert('No date selected.');
            return;
        }
        
        const dayReservations = reservationsData.data.filter(reservation => {
            const startDate = new Date(reservation.start_date);
            const endDate = new Date(reservation.end_date);
            const currentDate = new Date(selectedDateForModal);
            return currentDate >= startDate && currentDate <= endDate;
        });
        
        if (dayReservations.length === 0) {
            alert('No reservations for this date.');
            return;
        }
        
        const firstReservation = dayReservations[0];
        window.location.href = `/iosa/reservations/${firstReservation.id}`;
    }
</script>
@endsection 