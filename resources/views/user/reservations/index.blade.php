@extends('layouts.user')

@section('title', 'My Reservations')
@section('page-title', 'My Reservations')

@section('header-actions')
<div class="flex items-center space-x-2 relative">
    <button id="filterToggle" type="button" class="px-3 py-1.5 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2 shadow-sm border border-gray-200 hover:shadow-md text-sm">
        <i class="fas fa-filter text-gray-600"></i>
        <span class="font-medium">Filter</span>
        <i class="fas fa-chevron-down text-xs ml-1 transition-transform duration-200" id="filterArrow"></i>
    </button>
    <a href="{{ route('user.reservations.calendar') }}" class="px-4 py-1.5 bg-gray-800 text-white rounded-lg hover:bg-gray-800 transition-all duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm">
        <i class="fas fa-calendar-alt text-base"></i>
        <span class="font-medium">View Calendar</span>
    </a>

    <form id="filterPanel" action="{{ route('user.reservations.index') }}" method="GET" class="absolute top-12 left-0 w-64 bg-white border border-gray-200 rounded-xl shadow-2xl p-4 hidden z-50 backdrop-blur-sm">
        <div class="space-y-3">
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status Filter</label>
                <select id="status" name="status" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <option value="all" {{ ($currentStatus ?? request('status','all'))=='all' ? 'selected' : '' }}>All Reservations</option>
                    <option value="pending" {{ ($currentStatus ?? request('status'))=='pending' ? 'selected' : '' }}>Pending / In Review</option>
                    <option value="approved" {{ ($currentStatus ?? request('status'))=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ ($currentStatus ?? request('status'))=='completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ ($currentStatus ?? request('status'))=='rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="q" placeholder="Search by event title..." value="{{ request('q') }}" class="w-full border border-gray-300 rounded-lg p-2 pl-8 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <div class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex justify-end space-x-2 pt-3 border-t border-gray-100">
            <a href="{{ route('user.reservations.index') }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors">Reset</a>
            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">Apply Filters</button>
        </div>
    </form>
</div>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-inter { font-family: 'Inter', sans-serif; }
    
    .reservation-card { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    }
    .reservation-card:hover { 
        transform: translateY(-4px) scale(1.01); 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    
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
    .status-rejected { 
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); 
        color: #991B1B; 
        border-color: #EF4444;
    }
    .status-completed { 
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); 
        color: #374151; 
        border-color: #9CA3AF;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>

<div class="space-y-6 font-inter animate-fadeIn">
    <!-- Enhanced Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['all'] ?? $userReservations->total() }}</h3>
                <p class="text-xs text-blue-600 font-medium mt-1">All time</p>
            </div>
        </div>
        
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['approved'] ?? 0 }}</h3>
                <p class="text-xs text-green-600 font-medium mt-1">Successfully booked</p>
            </div>
        </div>
        
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['pending'] ?? 0 }}</h3>
                <p class="text-xs text-amber-600 font-medium mt-1">Under review</p>
            </div>
        </div>
        
        <div class="stats-card rounded-xl shadow-md p-4 border border-gray-100 flex items-center group">
            <div class="rounded-xl bg-gradient-to-br from-gray-500 to-gray-600 p-3 mr-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium mb-1">Completed</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['completed'] ?? 0 }}</h3>
                <p class="text-xs text-gray-600 font-medium mt-1">Successfully finished</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="glass-effect rounded-xl shadow-lg overflow-hidden">
        <!-- Enhanced Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center font-poppins mb-1">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-bookmark text-white text-base"></i>
                        </div>
                        My Reservations
                    </h2>
                    <p class="text-sm text-gray-600 font-medium">Manage and track your venue reservations</p>
                </div>
                
                <!-- Search Bar -->
                <div class="relative">
                    <form method="GET" action="{{ route('user.reservations.index') }}" class="flex items-center space-x-2">
                        <input type="hidden" name="status" value="{{ $currentStatus ?? 'all' }}" />
                        <div class="relative">
                            <input type="text" name="q" placeholder="Search reservations..." value="{{ request('q') }}" 
                                   class="w-64 pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm bg-white/80 backdrop-blur-sm">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Tabs -->
        <div class="flex border-b border-gray-100 overflow-x-auto bg-gray-50/50">
            <a href="{{ route('user.reservations.index', ['status' => 'all'] + request()->except('page','status')) }}" 
               class="px-6 py-3 transition-all duration-200 border-b-2 {{ ($currentStatus ?? 'all')=='all' ? 'border-blue-500 text-blue-600 font-semibold bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-white/50' }} flex items-center space-x-2 text-sm">
                <i class="fas fa-list-ul"></i>
                <span>All</span>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $counts['all'] ?? 0 }}</span>
            </a>
            <a href="{{ route('user.reservations.index', ['status' => 'pending'] + request()->except('page','status')) }}" 
               class="px-6 py-3 transition-all duration-200 border-b-2 {{ ($currentStatus ?? '')=='pending' ? 'border-amber-500 text-amber-600 font-semibold bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-white/50' }} flex items-center space-x-2 text-sm">
                <i class="fas fa-clock"></i>
                <span>Pending</span>
                <span class="bg-amber-200 text-amber-700 text-xs px-2 py-0.5 rounded-full">{{ $counts['pending'] ?? 0 }}</span>
            </a>
            <a href="{{ route('user.reservations.index', ['status' => 'approved'] + request()->except('page','status')) }}" 
               class="px-6 py-3 transition-all duration-200 border-b-2 {{ ($currentStatus ?? '')=='approved' ? 'border-green-500 text-green-600 font-semibold bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-white/50' }} flex items-center space-x-2 text-sm">
                <i class="fas fa-check-circle"></i>
                <span>Approved</span>
                <span class="bg-green-200 text-green-700 text-xs px-2 py-0.5 rounded-full">{{ $counts['approved'] ?? 0 }}</span>
            </a>
            <a href="{{ route('user.reservations.index', ['status' => 'rejected'] + request()->except('page','status')) }}" 
               class="px-6 py-3 transition-all duration-200 border-b-2 {{ ($currentStatus ?? '')=='rejected' ? 'border-red-500 text-red-600 font-semibold bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-white/50' }} flex items-center space-x-2 text-sm">
                <i class="fas fa-times-circle"></i>
                <span>Rejected</span>
                <span class="bg-red-200 text-red-700 text-xs px-2 py-0.5 rounded-full">{{ $counts['rejected'] ?? 0 }}</span>
            </a>
            <a href="{{ route('user.reservations.index', ['status' => 'completed'] + request()->except('page','status')) }}" 
               class="px-6 py-3 transition-all duration-200 border-b-2 {{ ($currentStatus ?? '')=='completed' ? 'border-gray-500 text-gray-600 font-semibold bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-white/50' }} flex items-center space-x-2 text-sm">
                <i class="fas fa-check-double"></i>
                <span>Completed</span>
                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded-full">{{ $counts['completed'] ?? 0 }}</span>
            </a>
        </div>
        
        <!-- Reservations Grid -->
        <div class="p-6">
            @if($userReservations->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($userReservations as $reservation)
                        <div class="reservation-card border border-gray-200 rounded-xl p-4 relative overflow-hidden group">
                            <!-- Enhanced Status Badge -->
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
                                <!-- Event Header -->
                                <div class="mb-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-bold text-gray-800 text-lg leading-tight group-hover:text-blue-600 transition-colors duration-200">
                                            {{ $reservation->event_title }}
                                        </h3>
                                    </div>
                                    
                                    <!-- Venue Info -->
                                    @if($reservation->venue)
                                        <div class="flex items-center text-xs text-gray-600 mb-2 bg-blue-50 px-2 py-1 rounded-md w-fit">
                                            <i class="fas fa-map-marker-alt mr-1.5 text-blue-500"></i>
                                            <span class="font-medium">{{ $reservation->venue->name }}</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Date & Time -->
                                    <div class="flex items-center text-xs text-gray-500 mb-2">
                                        <i class="far fa-calendar mr-1.5 text-gray-400"></i>
                                        <span class="font-medium">{{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'No date' }}</span>
                                        <span class="mx-1.5">•</span>
                                        <i class="far fa-clock mr-1.5 text-gray-400"></i>
                                        <span class="font-medium">{{ $reservation->start_date ? $reservation->start_date->format('g:i A') : 'No time' }} - {{ $reservation->end_date ? $reservation->end_date->format('g:i A') : 'No end time' }}</span>
                                    </div>
                                    
                                    <!-- Duration Badge -->
                                    @if($reservation->start_date && $reservation->end_date)
                                        <div class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            {{ $reservation->start_date->diffInHours($reservation->end_date) }} hours
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Purpose -->
                                @if($reservation->purpose)
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-3 rounded-lg mb-4 border-l-4 border-blue-500">
                                        <p class="text-xs text-gray-700 leading-relaxed">{{ Str::limit($reservation->purpose, 100) }}</p>
                                    </div>
                                @endif
                                
                                <!-- Enhanced Action Buttons -->
                                <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <div class="flex space-x-1.5">
                                        @if($reservation->status === 'pending')
                                            <a href="{{ route('user.reservations.edit', $reservation->id) }}" 
                                               class="p-1.5 bg-amber-50 text-amber-600 rounded-md hover:bg-amber-100 transition-all duration-200 group-hover:scale-105" title="Edit Reservation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="openCancelModal({{ $reservation->id }}, '{{ addslashes($reservation->event_title) }}', '{{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }}')" 
                                                    class="p-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-all duration-200 group-hover:scale-105" title="Cancel Reservation">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif(in_array($reservation->status, ['approved_IOSA','approved_mhadel','approved_OTP']))
                                            <button onclick="openCancelModal({{ $reservation->id }}, '{{ addslashes($reservation->event_title) }}', '{{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }}')" 
                                                    class="p-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-all duration-200 group-hover:scale-105" title="Cancel Reservation">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('user.reservations.show', $reservation->id) }}" 
                                       class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2 text-sm">
                                        <i class="fas fa-eye"></i>
                                        <span>View Details</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Enhanced Pagination -->
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-3">
                        {{ $userReservations->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No reservations found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">It looks like you don't have any reservations matching your current filters. Try adjusting your search or make a new reservation.</p>
                    <div class="flex items-center justify-center space-x-3">
                        <a href="{{ route('user.reservations.calendar') }}" 
                           class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-medium text-sm">
                            <i class="fas fa-calendar-plus mr-2"></i> Make a Reservation
                        </a>
                        <button onclick="document.getElementById('filterPanel').classList.remove('hidden')" 
                                class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-gray-200 font-medium text-sm">
                            <i class="fas fa-filter mr-1.5"></i> Adjust Filters
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Cancel Reservation Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-red-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-red-800 flex items-center font-montserrat">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            Cancel Reservation
                        </h3>
                        <button onclick="closeCancelModal()" class="text-red-400 hover:text-red-600 bg-white rounded-full p-1.5 hover:bg-red-50 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="text-center mb-4">
                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-calendar-times text-red-600 text-xl"></i>
                        </div>
                        <h4 class="text-base font-semibold text-gray-800 mb-1">Are you sure?</h4>
                        <p class="text-gray-600 text-sm">This action cannot be undone.</p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center">
                                <i class="fas fa-heading text-maroon mr-2 w-4"></i>
                                <span class="text-xs font-medium text-gray-700">Event:</span>
                                <span class="text-xs text-gray-900 ml-1" id="cancelEventTitle"></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-maroon mr-2 w-4"></i>
                                <span class="text-xs font-medium text-gray-700">Date:</span>
                                <span class="text-xs text-gray-900 ml-1" id="cancelEventDate"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5"></i>
                            <div class="text-xs text-yellow-800">
                                <p class="font-medium mb-1">What happens when you cancel?</p>
                                <ul class="text-xs space-y-0.5">
                                    <li>• Your reservation will be marked as cancelled</li>
                                    <li>• The venue will become available for other users</li>
                                    <li>• You'll receive a confirmation email</li>
                                    <li>• Admins will be notified of the cancellation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-2">
                        <button onclick="closeCancelModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center text-sm">
                            <i class="fas fa-times mr-1.5"></i> Keep Reservation
                        </button>
                        <button onclick="confirmCancel()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md flex items-center text-sm">
                            <i class="fas fa-check mr-1.5"></i> Yes, Cancel It
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced filter panel toggle
        const toggle = document.getElementById('filterToggle');
        const panel = document.getElementById('filterPanel');
        const arrow = document.getElementById('filterArrow');
        
        if (toggle && panel && arrow) {
            toggle.addEventListener('click', function(e){
                e.stopPropagation();
                panel.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });
            
            // Close panel when clicking outside
            document.addEventListener('click', function(e){
                if (!panel.contains(e.target) && !toggle.contains(e.target)) {
                    panel.classList.add('hidden');
                    arrow.classList.remove('rotate-180');
                }
            });
            
            // Close panel when pressing Escape
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape') {
                    panel.classList.add('hidden');
                    arrow.classList.remove('rotate-180');
                }
            });
        }
        
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Close cancel modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
        
        // Close cancel modal when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('cancelModal').classList.contains('hidden')) {
                closeCancelModal();
            }
        });
    });

    // Global variables for cancel modal
    let currentReservationId = null;
    let currentEventTitle = null;
    let currentEventDate = null;

    // Function to open cancel modal
    function openCancelModal(reservationId, eventTitle, eventDate) {
        currentReservationId = reservationId;
        currentEventTitle = eventTitle;
        currentEventDate = eventDate;
        
        // Update modal content
        document.getElementById('cancelEventTitle').textContent = eventTitle;
        document.getElementById('cancelEventDate').textContent = eventDate;
        
        // Show modal
        document.getElementById('cancelModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Add animation
        const modalContent = document.querySelector('#cancelModal > div > div');
        modalContent.classList.add('animate-fadeIn');
    }

    // Function to close cancel modal
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset variables
        currentReservationId = null;
        currentEventTitle = null;
        currentEventDate = null;
    }

    // Function to confirm cancellation
    function confirmCancel() {
        if (!currentReservationId) return;
        
        // Show loading state
        const confirmBtn = document.querySelector('#cancelModal button[onclick="confirmCancel()"]');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Cancelling...';
        confirmBtn.disabled = true;
        
        fetch(`/user/reservations/${currentReservationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');
                // Close modal
                closeCancelModal();
                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Failed to cancel reservation', 'error');
                // Reset button state
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while cancelling the reservation', 'error');
            // Reset button state
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        });
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
            toast.classList.add('bg-gradient-to-r', 'from-green-500', 'to-green-600', 'text-white');
        } else if (type === 'error') {
            toast.classList.add('bg-gradient-to-r', 'from-red-500', 'to-red-600', 'text-white');
        } else {
            toast.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white');
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