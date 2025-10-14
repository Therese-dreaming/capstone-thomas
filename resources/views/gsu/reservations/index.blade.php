@extends('layouts.gsu')

@section('title', 'GSU Reservations')
@section('page-title', 'Final Approved Reservations')
@section('page-subtitle', 'View and track reservations approved by OTP')

@section('header-actions')
<div class="flex items-center space-x-3">
                        <a href="{{ route('gsu.reservations.export', request()->query()) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-file-excel mr-2"></i>
                        <span>Export to Excel</span>
                    </a>
</div>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    
    .animate-fadeIn { 
        animation: fadeIn 0.3s ease-out; 
    }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .reservation-card { 
        transition: all 0.3s ease; 
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    
    .reservation-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }
    
    .status-badge { 
        padding: 0.25rem 0.75rem; 
        border-radius: 9999px; 
        font-size: 0.75rem; 
        font-weight: 500; 
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
    }
    
    .status-approved { 
        background-color: #10B981; 
        color: #ffffff; 
    }
    
    .status-rejected { 
        background-color: #EF4444; 
        color: #ffffff; 
    }
    
    .status-pending { 
        background-color: #F59E0B; 
        color: #ffffff; 
    }
    
    .status-completed { 
        background-color: #10B981; 
        color: #ffffff; 
    }
    
    .issue-indicator {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    
    .issue-badge {
        background: linear-gradient(135deg, #EF4444, #DC2626);
        color: white;
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        animation: pulse 2s infinite;
    }
    
    .issue-badge.severity-low { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    .issue-badge.severity-medium { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .issue-badge.severity-high { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .issue-badge.severity-critical { 
        background: linear-gradient(135deg, #7C2D12, #991B1B);
        animation: pulse 1s infinite;
    }
    
    .view-toggle-btn { 
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .view-toggle-btn.active { 
        background-color: #800000; 
        color: #ffffff; 
        font-weight: 500;
        border-color: #800000;
    }
    
    .view-toggle-btn:not(.active) { 
        background-color: #f8fafc; 
        color: #6b7280; 
        border-color: #e2e8f0;
    }
    
    .view-toggle-btn:hover:not(.active) {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    .calendar-day { 
        aspect-ratio: 1/1; 
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
        font-size: 0.9rem; 
        padding: 0.25rem; 
        min-width: 4rem; 
        max-width: 5rem; 
        transition: all 0.2s ease;
    }
    
    .calendar-day:hover:not(.disabled) { 
        transform: translateY(-1px); 
        box-shadow: 0 4px 12px -2px rgba(0,0,0,0.1);
    }
    
    .glass-effect {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="space-y-6 font-inter animate-fadeIn">
    <!-- Header Section -->
    <div class="glass-effect rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center font-poppins mb-2">
                        <div class="w-10 h-10 bg-maroon rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-check text-white text-lg"></i>
                        </div>
                        Final Approved Reservations
                    </h1>
                    <p class="text-gray-600 font-medium">Manage and monitor all approved reservations</p>
                </div>
                
                <!-- Search and Actions -->
                <div class="flex items-center space-x-3">
                    <form method="GET" action="{{ route('gsu.reservations.index') }}" class="flex items-center space-x-2">
                        <div class="relative">
                            <input type="text" 
                                   name="search"
                                   placeholder="Search by title, ID, purpose, or venue..." 
                                   value="{{ request('search') }}"
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm w-64">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button type="submit" class="px-3 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-200 text-sm">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('gsu.reservations.index', request()->except('search')) }}" class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 text-sm">
                                <i class="fas fa-times mr-1"></i>Clear
                            </a>
                        @endif
                    </form>
                    
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('gsu.reservations.index') }}" class="flex items-center space-x-2">
                        <input type="date" 
                               id="startDate" 
                               name="start_date" 
                               value="{{ request('start_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm">
                        <span class="text-gray-500 text-sm">to</span>
                        <input type="date" 
                               id="endDate" 
                               name="end_date" 
                               value="{{ request('end_date') }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-sm">
                        <button type="submit" 
                                class="px-3 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-200 text-sm">
                            <i class="fas fa-filter mr-1"></i>
                            Filter
                        </button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('gsu.reservations.index') }}" 
                               class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 text-sm">
                                <i class="fas fa-times mr-1"></i>
                                Clear
                            </a>
                        @endif
                    </form>
                    
                    <a href="{{ route('gsu.reservations.export', request()->query()) }}" 
                       id="exportBtn"
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-file-excel mr-2"></i>
                        <span>Export</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button onclick="showListView()" 
                            id="listViewBtn" 
                            class="view-toggle-btn active px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm">
                        <i class="fas fa-list mr-2"></i>List View
                    </button>
                    <button onclick="showCalendarView()" 
                            id="calendarViewBtn" 
                            class="view-toggle-btn px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm">
                        <i class="fas fa-calendar mr-2"></i>Calendar View
                    </button>
                    <button onclick="showCompletedView()" 
                            id="completedViewBtn" 
                            class="view-toggle-btn px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm">
                        <i class="fas fa-check-circle mr-2"></i>Completed
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="viewCounter">Showing {{ $reservations->count() }} final approved reservations</span>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div id="listView" class="p-6">
            @if($reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($reservations as $reservation)
                        <div class="reservation-card rounded-lg p-5 hover:shadow-lg transition-all duration-300">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="status-badge status-approved">Final Approved</span>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ $reservation->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('gsu.reservations.show', $reservation->id) }}" 
                                       class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg flex items-center space-x-2">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Details</span>
                                    </a>
                                    
                                    @if($reservation->status !== 'completed')
                                    <button onclick="openCompleteModal({{ $reservation->id }}, '{{ $reservation->event_title }}', 'reservation')" 
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg flex items-center space-x-2">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        <span>Complete</span>
                                    </button>
                                    @else
                                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-medium flex items-center space-x-2">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        <span>Completed</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Content Grid -->
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <!-- Event Details -->
                                <div class="space-y-3">
                                    <div class="mb-3">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $reservation->event_title }}</h3>
                                        <div class="text-xs text-gray-500 font-mono mt-1">
                                            ID: {{ $reservation->reservation_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">User:</span>
                                            <span class="ml-1">{{ $reservation->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Date:</span>
                                            <span class="ml-1">{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Time:</span>
                                            <span class="ml-1">{{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Venue & Capacity -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                                        Venue & Capacity
                                    </h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Venue:</span>
                                            <span class="ml-1">{{ $reservation->venue->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Capacity:</span>
                                            <span class="ml-1">{{ $reservation->capacity ?? 'N/A' }} participants</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-tag text-maroon mr-2"></i>
                                        Pricing
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded-lg">
                                            <span class="text-gray-600 font-medium">Final Price:</span>
                                            <span class="font-bold text-green-800 text-lg">₱{{ number_format($reservation->final_price ?? 0, 2) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Rate/Hour:</span>
                                            <span class="font-medium text-gray-800">₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Equipment -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-tools text-maroon mr-2"></i>
                                        Equipment
                                    </h4>
                                    @if(($reservation->equipment_details && count($reservation->equipment_details) > 0) || (!empty($reservation->custom_equipment_requests)))
                                        <div class="space-y-1 text-sm">
                                            @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
                                                @foreach($reservation->equipment_details as $eq)
                                                    <div class="text-xs bg-blue-50 text-blue-800 px-2 py-1 rounded-md border border-blue-200">
                                                        <span class="font-medium">{{ $eq['name'] }}</span> 
                                                        <span class="text-blue-600">({{ $eq['quantity'] }})</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if(!empty($reservation->custom_equipment_requests))
                                                @foreach($reservation->custom_equipment_requests as $customEquipment)
                                                    <div class="text-xs bg-orange-50 text-orange-800 px-2 py-1 rounded-md border border-orange-200">
                                                        <span class="font-medium">{{ $customEquipment['name'] ?? 'Custom Equipment' }}</span> 
                                                        <span class="text-orange-600">({{ $customEquipment['quantity'] ?? 1 }})</span>
                                                        <span class="text-orange-500 ml-1">[Custom]</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-gray-500 text-xs bg-gray-50 px-2 py-1 rounded-md">No equipment requested</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-3">
                        {{ $reservations->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Final Approved Reservations</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">Approved reservations will appear here once they are finalized.</p>
                </div>
            @endif
        </div>
        
        <!-- Calendar View -->
        <div id="calendarView" class="p-6 hidden">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-calendar-alt text-maroon mr-3"></i>
                            Reservation Calendar
                        </h2>
                        <div class="flex items-center space-x-2 bg-white rounded-lg shadow-md p-1.5">
                            <button onclick="previousMonth()" 
                                    class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="currentMonth" class="font-medium text-gray-700 px-3"></span>
                            <button onclick="nextMonth()" 
                                    class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Legend -->
                    <div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-600 text-white rounded-md mr-2"></div>
                            <span class="text-gray-600">Final Approved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div>
                            <span class="text-gray-600">Today</span>
                        </div>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto"></div>
                </div>
            </div>
        </div>

        <!-- Completed Reservations View -->
        <div id="completedView" class="p-6 hidden">
            @if($completedReservations->count() > 0)
                <div class="space-y-4">
                    @foreach($completedReservations as $reservation)
                        <div class="reservation-card rounded-lg p-5 hover:shadow-lg transition-all duration-300">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="status-badge status-completed">Completed</span>
                                    @if($reservation->reports->count() > 0)
                                        @php
                                            $highestSeverityReport = $reservation->reports->sortByDesc(function($report) {
                                                $severityOrder = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
                                                return $severityOrder[$report->severity] ?? 0;
                                            })->first();
                                        @endphp
                                        <div class="issue-indicator">
                                            <span class="issue-badge severity-{{ $highestSeverityReport->severity }}">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ ucfirst($highestSeverityReport->severity) }} Issue
                                            </span>
                                        </div>
                                    @endif
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        Completed: {{ $reservation->completion_date ? \Carbon\Carbon::parse($reservation->completion_date)->format('M d, Y H:i') : 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('gsu.reservations.show', $reservation->id) }}" 
                                       class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg flex items-center space-x-2">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Details</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Content Grid -->
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <!-- Event Details -->
                                <div class="space-y-3">
                                    <div class="mb-3">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $reservation->event_title }}</h3>
                                        <div class="text-xs text-gray-500 font-mono mt-1">
                                            ID: {{ $reservation->reservation_id ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">User:</span>
                                            <span class="ml-1">{{ $reservation->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Date:</span>
                                            <span class="ml-1">{{ $reservation->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Time:</span>
                                            <span class="ml-1">{{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Venue & Capacity -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                                        Venue & Capacity
                                    </h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Venue:</span>
                                            <span class="ml-1">{{ $reservation->venue->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-maroon w-4"></i>
                                            <span class="font-medium">Capacity:</span>
                                            <span class="ml-1">{{ $reservation->capacity ?? 'N/A' }} participants</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Completion Info -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                        Completion Info
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-gray-600 font-medium">Completed By:</span>
                                                <span class="font-bold text-green-800">{{ $reservation->completed_by ?? 'GSU' }}</span>
                                            </div>
                                            @if($reservation->completion_notes)
                                                <div class="text-xs text-gray-600 mt-2">
                                                    <strong>Notes:</strong> {{ $reservation->completion_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Issues & Reports -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                                        Issues & Reports
                                    </h4>
                                    @if($reservation->reports->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($reservation->reports as $report)
                                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="issue-badge severity-{{ $report->severity }}">
                                                            {{ ucfirst($report->severity) }}
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $report->created_at->format('M d, Y') }}
                                                        </span>
                                                    </div>
                                                    <div class="text-xs text-gray-700">
                                                        <strong>Type:</strong> {{ ucfirst($report->type) }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 mt-1">
                                                        {{ Str::limit($report->description, 100) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-gray-500 text-xs bg-gray-50 px-3 py-2 rounded-md border border-gray-200">
                                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                            No issues reported
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination for Completed Reservations -->
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-3">
                        {{ $completedReservations->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State for Completed -->
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-green-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Completed Reservations</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">Completed reservations will appear here once they are marked as finished by GSU.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reservation Details Modal -->
<div id="reservationDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-calendar-check text-maroon mr-2"></i>
                        Reservation Details
                    </h3>
                    <button onclick="closeReservationModal()" 
                            class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6" id="reservationModalContent"></div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeReservationModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                    Close
                </button>
                <a id="viewFullDetailsLink" href="#" 
                   class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    View Full Details
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showListView() {
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('calendarView').classList.add('hidden');
    document.getElementById('completedView').classList.add('hidden');
    document.getElementById('listViewBtn').classList.add('active');
    document.getElementById('calendarViewBtn').classList.remove('active');
    document.getElementById('completedViewBtn').classList.remove('active');
    
    // Update counter
    document.getElementById('viewCounter').textContent = 'Showing {{ $reservations->count() }} final approved reservations';
}

function showCalendarView() {
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('calendarView').classList.remove('hidden');
    document.getElementById('completedView').classList.add('hidden');
    document.getElementById('calendarViewBtn').classList.add('active');
    document.getElementById('listViewBtn').classList.remove('active');
    document.getElementById('completedViewBtn').classList.remove('active');
    renderCalendar();
    
    // Update counter
    document.getElementById('viewCounter').textContent = 'Showing {{ $reservations->count() }} final approved reservations';
}

function showCompletedView() {
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('calendarView').classList.add('hidden');
    document.getElementById('completedView').classList.remove('hidden');
    document.getElementById('completedViewBtn').classList.add('active');
    document.getElementById('listViewBtn').classList.remove('active');
    document.getElementById('calendarViewBtn').classList.remove('active');
    
    // Update counter
    document.getElementById('viewCounter').textContent = 'Showing {{ $completedReservations->count() }} completed reservations';
}

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
const reservationsData = @json($reservations);

function formatDateLocal(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

function renderCalendar() {
    const calendar = document.getElementById('calendar');
    const firstDay = new Date(currentYear, currentMonth, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const monthNames = ["January", "February", "March", "April", "May", "June", 
                       "July", "August", "September", "October", "November", "December"];
    document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    let html = '';
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    // Header row
    dayNames.forEach(d => {
        html += `<div class="text-center py-2 text-sm font-medium text-gray-500 bg-gray-50">${d}</div>`;
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
        
        const dateStringLocal = formatDateLocal(date);
        const dayReservations = reservationsData.data.filter(r => {
            const rLocal = formatDateLocal(new Date(r.start_date));
            return rLocal === dateStringLocal;
        });
        
        let reservationIndicator = '';
        if (dayReservations.length > 0) {
            reservationIndicator = `<div class="absolute w-3 h-3 bg-green-600 rounded-full" style="top:4px;right:4px;" title="${dayReservations.length} reservation(s) on this date"></div>`;
        }
        
        html += `<div class="${dayClass}" onclick="showReservationsForDate('${dateStringLocal}')">
                    <div class="text-sm font-medium">${date.getDate()}</div>
                    ${reservationIndicator}
                </div>`;
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

function showReservationsForDate(dateOrString) {
    const dateString = typeof dateOrString === 'string' ? dateOrString : formatDateLocal(dateOrString);
    const dayReservations = reservationsData.data.filter(r => {
        const rLocal = formatDateLocal(new Date(r.start_date));
        return rLocal === dateString;
    });
    
    if (dayReservations.length === 0) return;
    
    const r = dayReservations[0];
    const start = new Date(r.start_date);
    const end = new Date(r.end_date);
    
    const equipmentHtml = (r.equipment_details && r.equipment_details.length) 
        ? r.equipment_details.map(e => 
            `<div class="text-xs bg-blue-50 text-blue-800 px-2 py-1 rounded-md border border-blue-200">
                <span class="font-medium">${e.name}</span> 
                <span class="text-blue-600">(${e.quantity})</span>
            </div>`
        ).join('') 
        : '<div class="text-gray-500 text-xs bg-gray-50 px-2 py-1 rounded-md">No equipment requested</div>';
    
    const pricingRows = `
        <div class="flex items-center justify-between">
            <span class="text-gray-600">Final Price:</span>
            <span class="font-medium text-green-800 text-lg">₱${Number(r.final_price || 0).toFixed(2)}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-gray-600">Rate/Hour:</span>
            <span class="font-medium text-gray-800">₱${Number(r.price_per_hour || 0).toFixed(2)}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-gray-600">Duration:</span>
            <span class="font-medium text-blue-600">${Number(r.duration_hours || 0)} hours</span>
        </div>
    `;
    
    const content = `
        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-800 text-lg mb-3">${r.event_title}</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-600 mb-2"><strong>Requester:</strong> ${r.user?.name || ''}</p>
                        <p class="text-gray-600 mb-2"><strong>Date:</strong> ${start.toLocaleDateString()}</p>
                        <p class="text-gray-600"><strong>Time:</strong> ${start.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})} - ${end.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-2"><strong>Venue:</strong> ${r.venue?.name || ''}</p>
                        <p class="text-gray-600 mb-2"><strong>Capacity:</strong> ${r.capacity || ''}</p>
                        <p class="text-gray-600"><strong>Purpose:</strong> ${r.purpose || ''}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
    link.href = `/gsu/reservations/${r.id}`;
    
    document.getElementById('reservationDetailsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReservationModal() {
    document.getElementById('reservationDetailsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('reservationDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReservationModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('reservationDetailsModal').classList.contains('hidden')) {
        closeReservationModal();
    }
});

// Date Filter Functionality
function updateExportUrl() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const exportBtn = document.getElementById('exportBtn');
    
    let url = '{{ route("gsu.reservations.export") }}';
    const params = new URLSearchParams();
    
    // Add date parameters if selected
    if (startDate) {
        params.append('start_date', startDate);
    }
    if (endDate) {
        params.append('end_date', endDate);
    }
    
    // Add current search parameters
    const searchParams = new URLSearchParams(window.location.search);
    for (const [key, value] of searchParams) {
        if (key !== 'start_date' && key !== 'end_date') {
            params.append(key, value);
        }
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    exportBtn.href = url;
}

// Add event listeners for date inputs
document.getElementById('startDate').addEventListener('change', updateExportUrl);
document.getElementById('endDate').addEventListener('change', updateExportUrl);

// Initialize export URL on page load
document.addEventListener('DOMContentLoaded', function() {
    updateExportUrl();
});

// Complete modal functions
let currentCompleteItem = null;

function openCompleteModal(id, title, type) {
    currentCompleteItem = { id: id, title: title, type: type };
    
    const itemDetails = document.getElementById('completeItemDetails');
    itemDetails.innerHTML = `
        <div class="font-medium text-gray-800">${title}</div>
        <div class="text-gray-600">${type === 'event' ? 'Event' : 'Reservation'}</div>
    `;
    
    document.getElementById('completionNotes').value = '';
    document.getElementById('completeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentCompleteItem = null;
}

function confirmComplete() {
	if (!currentCompleteItem) return;
	
	const notes = document.getElementById('completionNotes').value;
	const { id, type } = currentCompleteItem;
	
	// Check if report section is visible and has data
	const reportSection = document.getElementById('reportSection');
	const hasReport = !reportSection.classList.contains('hidden') && 
		document.getElementById('reportType').value && 
		document.getElementById('reportSeverity').value && 
		document.getElementById('reportDescription').value;
	
	// Create form data
	const formData = new FormData();
	formData.append('_token', '{{ csrf_token() }}');
	formData.append('completion_notes', notes);
	
	// Add report data if present
	if (hasReport) {
		formData.append('type', document.getElementById('reportType').value);
		formData.append('severity', document.getElementById('reportSeverity').value);
		formData.append('description', document.getElementById('reportDescription').value);
		formData.append('actions_taken', document.getElementById('reportActions').value);
	}
	
	// Determine the route based on type
	const route = type === 'event' 
		? `{{ route('gsu.events.complete', ':id') }}`.replace(':id', id)
		: `{{ route('gsu.reservations.complete', ':id') }}`.replace(':id', id);
	
	// Send request
	fetch(route, {
		method: 'POST',
		body: formData,
		headers: {
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'application/json'
		}
	})
	.then(response => {
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		return response.json();
	})
	.then(data => {
		if (data.success) {
			// Show success message
			let message = 'Item marked as completed successfully!';
			if (hasReport) {
				message += ' Issue has also been reported.';
			}
			showNotification(message, 'success');
			closeCompleteModal();
			
			// Reload the page to reflect changes
			setTimeout(() => {
				window.location.reload();
			}, 1500);
		} else {
			showNotification(data.message || 'Error marking item as complete', 'error');
		}
	})
	.catch(error => {
		console.error('Error:', error);
		// Check if it's a JSON parsing error
		if (error.message.includes('JSON')) {
			showNotification('Server returned an invalid response. Please try again.', 'error');
		} else {
			showNotification('Error marking item as complete. Please try again.', 'error');
		}
	});
}

function toggleReportSection() {
	const rightColumn = document.getElementById('rightColumn');
	const modalContent = document.getElementById('modalContent');
	const modalContainer = document.getElementById('completeModalContainer');
	const toggleText = document.getElementById('reportToggleText');
	
	if (rightColumn.classList.contains('hidden')) {
		// Show report section - switch to 2-column layout
		rightColumn.classList.remove('hidden');
		modalContent.classList.add('grid', 'grid-cols-2', 'gap-6');
		modalContainer.classList.remove('max-w-md');
		modalContainer.classList.add('max-w-4xl');
		toggleText.textContent = 'Hide Report';
	} else {
		// Hide report section - switch back to single column
		rightColumn.classList.add('hidden');
		modalContent.classList.remove('grid', 'grid-cols-2', 'gap-6');
		modalContainer.classList.remove('max-w-4xl');
		modalContainer.classList.add('max-w-md');
		toggleText.textContent = 'Report Issue';
		// Clear form fields when hiding
		document.getElementById('reportType').value = '';
		document.getElementById('reportSeverity').value = '';
		document.getElementById('reportDescription').value = '';
		document.getElementById('reportActions').value = '';
	}
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}
</script>

<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div id="completeModalContainer" class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[85vh] flex flex-col font-poppins transition-all duration-300">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 font-montserrat">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        Mark as Complete
                    </h3>
                    <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 overflow-y-auto flex-1">
                <!-- Item Details Section -->
                <div class="mb-3">
                    <p class="text-sm text-gray-700 mb-2">Are you sure you want to mark this item as completed?</p>
                    <div id="completeItemDetails" class="bg-gray-50 p-2 rounded-lg text-xs text-gray-600"></div>
                </div>
                
                <!-- Two Column Layout Container -->
                <div id="modalContent" class="space-y-3">
                    <!-- Left Column (or full width when no report) -->
                    <div id="leftColumn" class="space-y-3">
                        <div>
                            <label for="completionNotes" class="block text-sm font-medium text-gray-700 mb-1">Completion Notes (Optional)</label>
                            <textarea id="completionNotes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200" placeholder="Add any notes about the completion..."></textarea>
                        </div>
                        
                        <!-- Report Issue Toggle -->
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-700">Report Issue (Optional)</h4>
                                <button type="button" onclick="toggleReportSection()" class="text-xs text-maroon hover:text-red-700 font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <span id="reportToggleText">Report Issue</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-600">
                            <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                            This will notify IOSA, OTP, and PPGS about the completion.
                        </div>
                    </div>
                    
                    <!-- Right Column (Report Section - appears when toggled) -->
                    <div id="rightColumn" class="hidden">
                        <div id="reportSection" class="space-y-3 bg-red-50 p-3 rounded-lg border border-red-200">
                            <h5 class="font-medium text-red-800 text-sm mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Issue Report Details
                            </h5>
                            
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <label for="reportType" class="block text-xs font-medium text-gray-700 mb-1">Issue Type</label>
                                    <select id="reportType" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-xs">
                                        <option value="">Select type...</option>
                                        <option value="accident">Accident</option>
                                        <option value="problem">Problem</option>
                                        <option value="violation">Violation</option>
                                        <option value="damage">Damage</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="reportSeverity" class="block text-xs font-medium text-gray-700 mb-1">Severity</label>
                                    <select id="reportSeverity" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-xs">
                                        <option value="">Select severity...</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label for="reportDescription" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="reportDescription" rows="2" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-xs" placeholder="Describe what happened..."></textarea>
                            </div>
                            
                            <div>
                                <label for="reportActions" class="block text-xs font-medium text-gray-700 mb-1">Actions Taken (Optional)</label>
                                <textarea id="reportActions" rows="2" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 text-xs" placeholder="What actions were taken to address the issue..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end space-x-3 flex-shrink-0 bg-white">
                <button onclick="closeCompleteModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button onclick="confirmComplete()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <i class="fas fa-check mr-2"></i>Mark Complete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 