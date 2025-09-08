@extends('layouts.mhadel')

@section('title', 'Event Details - Ms. Mhadel')
@section('page-title', 'Event Details')
@section('page-subtitle', 'View complete event information and details')

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
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .category-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .category-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .category-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 1rem 1rem 0 0;
    }
    
    .category-content {
        padding: 1.5rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .info-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .info-content {
        flex: 1;
        min-width: 0;
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 0.875rem;
        color: #111827;
        font-weight: 500;
    }
    
    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background: #8B0000;
        color: white;
    }
    
    .btn-primary:hover {
        background: #7F0000;
    }
    
    .btn-secondary {
        background: #6B7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4B5563;
    }
    
    .btn-danger {
        background: #DC2626;
        color: white;
    }
    
    .btn-danger:hover {
        background: #B91C1C;
    }
</style>

@section('header-actions')
    <div class="flex items-center space-x-3">
        @if($event->status !== 'completed' && $event->status !== 'cancelled')
        <a href="{{ route('mhadel.events.edit', $event) }}" class="action-button btn-secondary">
            <i class="fas fa-edit"></i>Edit Event
        </a>
        @endif
        @if($event->status !== 'completed')
        <button type="button" onclick="openDeleteModal()" class="action-button btn-danger">
            <i class="fas fa-trash"></i>Delete Event
        </button>
        @endif
        <a href="{{ route('mhadel.events.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-8 font-inter">
    <!-- Event Header -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-maroon to-red-800 flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-calendar-alt text-3xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">{{ $event->title }}</h1>
                            <div class="text-sm text-gray-500 font-mono mb-2">
                                Event ID: {{ $event->event_id ?? 'N/A' }}
                            </div>
                            @if($event->description)
                                <p class="text-gray-600 text-lg max-w-2xl">{{ $event->description }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Status and Quick Info -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            @switch($event->status)
                                @case('upcoming')
                                    <span class="status-badge bg-blue-100 text-blue-800">Upcoming</span>
                                    @break
                                @case('ongoing')
                                    <span class="status-badge bg-green-100 text-green-800">Ongoing</span>
                                    @break
                                @case('completed')
                                    <span class="status-badge bg-gray-100 text-gray-800">Completed</span>
                                    @break
                                @case('cancelled')
                                    <span class="status-badge bg-red-100 text-red-800">Cancelled</span>
                                    @break
                            @endswitch
                        </div>
                        
                        <div class="flex items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-maroon"></i>
                                <span>{{ $event->start_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-maroon"></i>
                                <span>{{ $event->start_date->format('g:i A') }} – {{ $event->end_date->format('g:i A') }}</span>
                            </div>
                            @if($event->venue)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-maroon"></i>
                                    <span>{{ $event->venue->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 animate-fadeIn">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-3"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 animate-fadeIn">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column - Event Details -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Event Information -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-info-circle text-maroon mr-3 text-2xl"></i>
                        Event Information
                    </h2>
                </div>
                <div class="category-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Start Date & Time</div>
                                <div class="info-value">{{ $event->start_date->format('F d, Y g:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon bg-green-100 text-green-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">End Date & Time</div>
                                <div class="info-value">{{ $event->end_date->format('F d, Y g:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon bg-purple-100 text-purple-600">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Duration</div>
                                <div class="info-value">{{ $event->start_date->diffInHours($event->end_date) }} hours</div>
                            </div>
                        </div>
                        
                        @if($event->organizer)
                        <div class="info-item">
                            <div class="info-icon bg-yellow-100 text-yellow-600">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Organizer</div>
                                <div class="info-value">{{ $event->organizer }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($event->department)
                        <div class="info-item">
                            <div class="info-icon bg-orange-100 text-orange-600">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Department</div>
                                <div class="info-value">{{ $event->department }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($event->max_participants)
                        <div class="info-item">
                            <div class="info-icon bg-indigo-100 text-indigo-600">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Maximum Participants</div>
                                <div class="info-value">{{ $event->max_participants }} people</div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <div class="info-icon bg-gray-100 text-gray-600">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Created</div>
                                <div class="info-value">{{ $event->created_at->format('F d, Y g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Details -->
            @if($event->venue)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-building text-maroon mr-3 text-2xl"></i>
                        Venue Information
                    </h2>
                </div>
                <div class="category-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Venue Name</div>
                                <div class="info-value">{{ $event->venue->name }}</div>
                            </div>
                        </div>
                        
                        @if($event->venue->description)
                        <div class="info-item">
                            <div class="info-icon bg-green-100 text-green-600">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Description</div>
                                <div class="info-value">{{ $event->venue->description }}</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($event->venue->capacity)
                        <div class="info-item">
                            <div class="info-icon bg-purple-100 text-purple-600">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Capacity</div>
                                <div class="info-value">{{ $event->venue->capacity }} people</div>
                            </div>
                        </div>
                        @endif
                        
                        @if($event->venue->price_per_hour)
                        <div class="info-item">
                            <div class="info-icon bg-yellow-100 text-yellow-600">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Rate per Hour</div>
                                <div class="info-value">₱{{ number_format($event->venue->price_per_hour, 2) }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Equipment Details -->
            @if($event->equipment_details && count($event->equipment_details) > 0)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-tools text-maroon mr-3 text-2xl"></i>
                        Equipment Details
                    </h2>
                </div>
                <div class="category-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($event->equipment_details as $equipment)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cog text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $equipment['name'] }}</h3>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-hashtag text-maroon"></i>
                                <span>Quantity: <span class="font-medium">{{ $equipment['quantity'] }}</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if(count($event->equipment_details) > 0)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2 text-sm text-blue-700">
                            <i class="fas fa-info-circle"></i>
                            <span>Total equipment items: <span class="font-semibold">{{ count($event->equipment_details) }}</span></span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Additional Details -->
            @if($event->purpose || $event->notes)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-sticky-note text-maroon mr-3 text-2xl"></i>
                        Additional Details
                    </h2>
                </div>
                <div class="category-content">
                    @if($event->purpose)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Purpose & Objectives</h3>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <p class="text-gray-800 leading-relaxed">{{ $event->purpose }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($event->notes)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Notes & Additional Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <pre class="text-gray-800 whitespace-pre-wrap font-mono text-sm">{{ $event->notes }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-8">
            <!-- Event Status -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-chart-line text-maroon mr-3 text-2xl"></i>
                        Event Status
                    </h2>
                </div>
                <div class="category-content">
                    <div class="text-center">
                        @switch($event->status)
                            @case('upcoming')
                                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clock text-blue-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Upcoming Event</h3>
                                <p class="text-sm text-blue-600">This event is scheduled and will begin soon.</p>
                                @break
                            @case('ongoing')
                                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-play text-green-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-green-800 mb-2">Event in Progress</h3>
                                <p class="text-sm text-green-600">This event is currently happening.</p>
                                @break
                            @case('completed')
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Event Completed</h3>
                                <p class="text-sm text-gray-600">This event has finished successfully.</p>
                                @break
                            @case('cancelled')
                                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-times text-red-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Event Cancelled</h3>
                                <p class="text-sm text-red-600">This event has been cancelled.</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-bolt text-maroon mr-3 text-2xl"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="category-content">
                    <div class="space-y-3">
                        @if($event->status !== 'completed' && $event->status !== 'cancelled')
                        <a href="{{ route('mhadel.events.edit', $event) }}" class="action-button btn-secondary w-full justify-center">
                            <i class="fas fa-edit"></i>Edit Event
                        </a>
                        @endif
                        
                        @if($event->status !== 'cancelled' && $event->status !== 'completed')
                        <button type="button" onclick="openCancelModal()" class="action-button btn-outline w-full justify-center">
                            <i class="fas fa-ban"></i>Cancel Event
                        </button>
                        @endif
                        
                        @if($event->status !== 'completed')
                        <button type="button" onclick="openDeleteModal()" class="action-button btn-danger w-full justify-center">
                            <i class="fas fa-trash"></i>Delete Event
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event Timeline -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-history text-maroon mr-3 text-2xl"></i>
                        Event Timeline
                    </h2>
                </div>
                <div class="category-content">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Event Created</div>
                                <div class="text-xs text-gray-500">{{ $event->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Event Scheduled</div>
                                <div class="text-xs text-gray-500">{{ $event->start_date->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Event Ends</div>
                                <div class="text-xs text-gray-500">{{ $event->end_date->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Delete Event</h3>
                        <p class="text-gray-600 text-sm">This action cannot be undone</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <p class="text-gray-700 mb-6">
                    Are you sure you want to delete "<span class="font-semibold text-gray-800">{{ $event->title }}</span>"? 
                    This will permanently remove the event and all associated data.
                </p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-red-600 mt-1"></i>
                        <div class="text-sm text-red-700">
                            <p class="font-medium mb-1">What will be deleted:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Event details and information</li>
                                <li>Event schedule and timing</li>
                                <li>All event-related data</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end space-x-3">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <form action="{{ route('mhadel.events.destroy', $event) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Event Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Cancel Event</h3>
                        <p class="text-gray-600 text-sm">This will mark the event as cancelled</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <p class="text-gray-700 mb-6">
                    Are you sure you want to cancel "<span class="font-semibold text-gray-800">{{ $event->title }}</span>"? 
                    This will mark the event as cancelled but preserve all the information.
                </p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium mb-1">What happens when cancelled:</p>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Event status changes to "Cancelled"</li>
                                <li>All event data is preserved</li>
                                <li>Event remains in the system for reference</li>
                                <li>Can be reactivated by editing the status</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <div class="flex justify-end space-x-3">
                    <button onclick="closeCancelModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Keep Event
                    </button>
                    <form action="{{ route('mhadel.events.cancel', $event) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            Cancel Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeCancelModal();
    }
});
</script>
@endsection 