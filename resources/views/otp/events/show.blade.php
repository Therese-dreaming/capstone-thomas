@extends('layouts.drjavier')

@section('title', 'Event Details - OTP')
@section('page-title', 'Event Details')
@section('page-subtitle', 'View complete event information and details')

@section('header-actions')
    <a href="{{ route('drjavier.events.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Back to Events
    </a>
@endsection

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
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .category-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }
    
    .category-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 0.75rem 0.75rem 0 0;
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
        font-weight: 600;
        color: #1F2937;
        word-break: break-word;
    }
    
    .equipment-item {
        background: #F3F4F6;
        border: 1px solid #E5E7EB;
        border-radius: 0.5rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .equipment-icon {
        width: 2rem;
        height: 2rem;
        background: #8B0000;
        color: white;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .equipment-details {
        flex: 1;
        min-width: 0;
    }
    
    .equipment-name {
        font-weight: 600;
        color: #1F2937;
        font-size: 0.875rem;
    }
    
    .equipment-quantity {
        font-size: 0.75rem;
        color: #6B7280;
        margin-top: 0.125rem;
    }
</style>

@section('content')
<div class="space-y-6 font-inter">
    <!-- Status Banner -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800 font-poppins mb-2">{{ $event->title }}</h1>
                    <p class="text-gray-600">Event ID: {{ $event->event_id ?? 'N/A' }}</p>
                    @if($event->description)
                        <p class="text-gray-600 mt-2">{{ $event->description }}</p>
                    @endif
                </div>
                <div class="text-right">
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
                        @case('pending_venue')
                            <span class="status-badge bg-purple-100 text-purple-800">Pending Venue</span>
                            @break
                        @default
                            <span class="status-badge bg-gray-100 text-gray-800">{{ ucfirst($event->status) }}</span>
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Event Details Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-calendar-alt text-maroon mr-3"></i>
                        Event Details
                    </h2>
                </div>
                <div class="category-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon bg-blue-100">
                                <i class="fas fa-calendar text-blue-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Start Date & Time</div>
                                <div class="info-value">{{ $event->start_date->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon bg-green-100">
                                <i class="fas fa-calendar-check text-green-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">End Date & Time</div>
                                <div class="info-value">{{ $event->end_date->format('M d, Y g:i A') }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon bg-purple-100">
                                <i class="fas fa-clock text-purple-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Duration</div>
                                <div class="info-value">{{ $event->start_date->diffInHours($event->end_date) }} hours</div>
                            </div>
                        </div>

                        @if($event->venue)
                        <div class="info-item">
                            <div class="info-icon bg-red-100">
                                <i class="fas fa-map-marker-alt text-red-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Venue</div>
                                <div class="info-value">{{ $event->venue->name }}</div>
                            </div>
                        </div>
                        @endif

                        @if($event->organizer)
                        <div class="info-item">
                            <div class="info-icon bg-yellow-100">
                                <i class="fas fa-user text-yellow-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Organizer</div>
                                <div class="info-value">{{ $event->organizer }}</div>
                            </div>
                        </div>
                        @endif

                        @if($event->department)
                        <div class="info-item">
                            <div class="info-icon bg-indigo-100">
                                <i class="fas fa-building text-indigo-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Department</div>
                                <div class="info-value">{{ $event->department }}</div>
                            </div>
                        </div>
                        @endif

                        @if($event->max_participants)
                        <div class="info-item">
                            <div class="info-icon bg-teal-100">
                                <i class="fas fa-users text-teal-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Max Participants</div>
                                <div class="info-value">{{ $event->max_participants }} people</div>
                            </div>
                        </div>
                        @endif

                        @if($event->contact_person)
                        <div class="info-item">
                            <div class="info-icon bg-pink-100">
                                <i class="fas fa-address-card text-pink-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Contact Person</div>
                                <div class="info-value">{{ $event->contact_person }}</div>
                            </div>
                        </div>
                        @endif

                        @if($event->contact_email)
                        <div class="info-item">
                            <div class="info-icon bg-cyan-100">
                                <i class="fas fa-envelope text-cyan-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Contact Email</div>
                                <div class="info-value">{{ $event->contact_email }}</div>
                            </div>
                        </div>
                        @endif

                        @if($event->contact_phone)
                        <div class="info-item">
                            <div class="info-icon bg-orange-100">
                                <i class="fas fa-phone text-orange-600"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Contact Phone</div>
                                <div class="info-value">{{ $event->contact_phone }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Equipment Details Category -->
            @if($event->equipment_details && count($event->equipment_details) > 0)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-tools text-maroon mr-3"></i>
                        Equipment Requirements
                    </h2>
                </div>
                <div class="category-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($event->equipment_details as $equipment)
                            <div class="equipment-item">
                                <div class="equipment-icon">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <div class="equipment-details">
                                    <div class="equipment-name">{{ $equipment['name'] ?? 'Equipment' }}</div>
                                    <div class="equipment-quantity">Quantity: {{ $equipment['quantity'] ?? 1 }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Notes Category -->
            @if($event->notes)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-sticky-note text-maroon mr-3"></i>
                        Additional Notes
                    </h2>
                </div>
                <div class="category-content">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-800 leading-relaxed">{{ $event->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Event Status Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-info-circle text-maroon mr-3"></i>
                        Event Status
                    </h2>
                </div>
                <div class="category-content">
                    <div class="text-center">
                        @switch($event->status)
                            @case('upcoming')
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clock text-blue-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Upcoming Event</h3>
                                <p class="text-sm text-blue-600">This event is scheduled and ready to begin.</p>
                                @break
                            @case('ongoing')
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-play text-green-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-green-800 mb-2">Event in Progress</h3>
                                <p class="text-sm text-green-600">This event is currently taking place.</p>
                                @break
                            @case('completed')
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-gray-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Event Completed</h3>
                                <p class="text-sm text-gray-600">This event has been successfully completed.</p>
                                @break
                            @case('cancelled')
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-times text-red-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Event Cancelled</h3>
                                <p class="text-sm text-red-600">This event has been cancelled.</p>
                                @break
                            @case('pending_venue')
                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-building text-purple-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-purple-800 mb-2">Pending Venue Assignment</h3>
                                <p class="text-sm text-purple-600">Waiting for venue to be assigned.</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Venue Details Category -->
            @if($event->venue)
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-building text-maroon mr-3"></i>
                        Venue Information
                    </h2>
                </div>
                <div class="category-content">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Venue Name</label>
                            <p class="text-sm text-gray-700 font-semibold">{{ $event->venue->name }}</p>
                        </div>
                        @if($event->venue->description)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Description</label>
                                <p class="text-sm text-gray-700">{{ $event->venue->description }}</p>
                            </div>
                        @endif
                        @if($event->venue->capacity)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Capacity</label>
                                <p class="text-sm text-gray-700">{{ $event->venue->capacity }} people</p>
                            </div>
                        @endif
                        @if($event->venue->price_per_hour)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Rate per Hour</label>
                                <p class="text-sm text-gray-700 font-semibold text-green-600">₱{{ number_format($event->venue->price_per_hour, 2) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Creation Information Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-history text-maroon mr-3"></i>
                        Event History
                    </h2>
                </div>
                <div class="category-content">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Created</label>
                            <p class="text-sm text-gray-700 font-medium">{{ $event->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Last Updated</label>
                            <p class="text-sm text-gray-700 font-medium">{{ $event->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($event->created_at->diffInDays($event->updated_at) > 0)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Days Since Creation</label>
                                <p class="text-sm text-gray-700">{{ $event->created_at->diffInDays(now()) }} days ago</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
