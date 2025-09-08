@extends('layouts.iosa')

@section('title', 'Reservation Details')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'View complete reservation information and approval timeline')

@php
use Illuminate\Support\Facades\Storage;
@endphp

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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .timeline-item {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.25rem;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #8B1818;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e5e7eb;
    }
    
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 3px;
        top: 1rem;
        width: 2px;
        height: calc(100% - 0.75rem);
        background: #e5e7eb;
    }
    
    .timeline-item.completed::before {
        background: #059669;
        box-shadow: 0 0 0 2px #d1fae5;
    }
    
    .timeline-item.pending::before {
        background: #d97706;
        box-shadow: 0 0 0 2px #fed7aa;
    }
    
    .timeline-item.rejected::before {
        background: #dc2626;
        box-shadow: 0 0 0 2px #fecaca;
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
    
    .btn-primary {
        background: #8B1818;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        background: #7c1515;
    }
    
    .btn-success {
        background: #059669;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .btn-success:hover {
        background: #047857;
    }
    
    .btn-danger {
        background: #dc2626;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .btn-danger:hover {
        background: #b91c1c;
    }
    
    .info-card {
        background: white;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .section-header {
        background: #8B1818;
        color: white;
        padding: 1rem;
        font-weight: 600;
    }
    
    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #8B1818;
    }
    
    .metric-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>

@section('header-actions')
    <a href="{{ route('iosa.reservations.index') }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors flex items-center text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back to Reservations
    </a>
@endsection

@section('content')
<div class="space-y-4 font-inter">
    <!-- Status Banner -->
    <div class="info-card animate-fadeIn">
        <div class="section-header">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold font-poppins">{{ $reservation->event_title }}</h1>
                    <p class="text-gray-200 text-sm mt-1">Submitted by {{ $reservation->user->name }}</p>
                    <div class="text-xs text-gray-300 font-mono mt-1">
                        Reservation ID: {{ $reservation->reservation_id ?? 'N/A' }}
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if($reservation->status === 'pending')
                        <span class="status-badge status-pending">Pending Approval</span>
                    @elseif($reservation->status === 'approved')
                        <span class="status-badge status-approved">Approved by IOSA</span>
                    @elseif($reservation->status === 'rejected')
                        <span class="status-badge status-rejected">Rejected by IOSA</span>
                    @else
                        <span class="status-badge bg-gray-100 text-gray-800">{{ ucfirst($reservation->status) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="info-card p-4 text-center">
            <div class="metric-value">{{ $reservation->capacity ?? 'N/A' }}</div>
            <div class="metric-label">Capacity</div>
        </div>
        <div class="info-card p-4 text-center">
            <div class="metric-value">
                @if($reservation->duration_hours)
                    {{ $reservation->duration_hours }}h
                @else
                    {{ $reservation->start_date->diffInHours($reservation->end_date) }}h
                @endif
            </div>
            <div class="metric-label">Duration</div>
        </div>
        <div class="info-card p-4 text-center">
            <div class="metric-value">
                @if($reservation->final_price)
                    ₱{{ number_format($reservation->final_price, 0) }}
                @else
                    N/A
                @endif
            </div>
            <div class="metric-label">Total Cost</div>
        </div>
        <div class="info-card p-4 text-center">
            <div class="metric-value">
                @if($reservation->equipment_details)
                    {{ count($reservation->equipment_details) }}
                @else
                    0
                @endif
            </div>
            <div class="metric-label">Equipment Items</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column - Event Details -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Event Information -->
            <div class="info-card">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-calendar-alt text-maroon mr-2"></i>
                        Event Information
                    </h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Event Title</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->event_title }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Capacity</label>
                            <p class="text-gray-900">{{ $reservation->capacity }} attendees</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Date & Time</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->start_date->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">End Date & Time</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->end_date->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($reservation->venue)
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Venue</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->venue->name }}</p>
                            </div>
                        @endif
                        @if($reservation->department)
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->department }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Duration</label>
                            <p class="text-gray-900">
                                @if($reservation->duration_hours)
                                    {{ $reservation->duration_hours }} hours
                                @else
                                    {{ $reservation->start_date->diffInHours($reservation->end_date) }} hours
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($reservation->purpose)
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Purpose</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded border-l-2 border-maroon text-sm">{{ $reservation->purpose }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Information -->
            @if($reservation->price_per_hour || $reservation->base_price || $reservation->final_price)
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-tag text-maroon mr-2"></i>
                            Pricing Information
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($reservation->price_per_hour)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Rate per Hour</label>
                                    <p class="text-gray-900 font-medium">₱{{ number_format($reservation->price_per_hour, 2) }}</p>
                                </div>
                            @endif
                            @if($reservation->base_price)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Base Price</label>
                                    <p class="text-gray-900">₱{{ number_format($reservation->base_price, 2) }}</p>
                                </div>
                            @endif
                            @if($reservation->discount_percentage)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Discount</label>
                                    <p class="text-gray-900 text-green-600">{{ $reservation->discount_percentage }}%</p>
                                </div>
                            @endif
                            @if($reservation->final_price)
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Final Total</label>
                                    <p class="text-green-600 font-semibold text-lg">₱{{ number_format($reservation->final_price, 2) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Equipment Details -->
            @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-tools text-maroon mr-2"></i>
                            Equipment Details
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($reservation->equipment_details as $equipment)
                                <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800 text-sm">{{ $equipment['name'] }}</h4>
                                            <p class="text-xs text-gray-600">Quantity: {{ $equipment['quantity'] }}</p>
                                            @if(isset($equipment['price']))
                                                <p class="text-xs text-blue-600 font-medium mt-1">₱{{ number_format($equipment['price'], 2) }} each</p>
                                            @endif
                                        </div>
                                        <div class="text-blue-600">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Grid -->
            @if($reservation->activity_grid)
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                                <i class="fas fa-table text-maroon mr-2"></i>
                                Activity Grid
                            </h2>
                            <div class="flex items-center space-x-2">
                                @if(Storage::disk('public')->exists($reservation->activity_grid))
                                    <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank"
                                       class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View File
                                    </a>
                                @else
                                    <button onclick="openViewActivityGridModal('{{ $reservation->event_title }}', `{{ $reservation->activity_grid }}`)" 
                                            class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View Text
                                    </button>
                                @endif
                                <a href="{{ route('iosa.reservations.download-activity-grid', $reservation->id) }}" 
                                   class="btn-primary flex items-center text-sm">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        @if(Storage::disk('public')->exists($reservation->activity_grid))
                            <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-blue-800 text-sm">File Uploaded</h4>
                                        <p class="text-xs text-blue-600 mt-1">{{ basename($reservation->activity_grid) }}</p>
                                    </div>
                                    <div class="text-blue-600">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-3 rounded border">
                                <pre class="text-xs text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->activity_grid }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @if($reservation->status === 'pending')
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-bolt text-maroon mr-2"></i>
                            Quick Actions
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" 
                                    class="w-full btn-success flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Approve Reservation
                            </button>
                            <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" 
                                    class="w-full btn-danger flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>Reject Reservation
                            </button>
                        </div>
                    </div>
                </div>
            @endif


        </div>

        <!-- Right Column - Timeline & Actions -->
        <div class="space-y-4">
            <!-- Approval Timeline -->
            <div class="info-card">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-clock text-maroon mr-2"></i>
                        Approval Timeline
                    </h2>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <!-- Submitted -->
                        <div class="timeline-item completed">
                            <div class="bg-green-50 p-3 rounded border border-green-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-green-800 text-sm">Reservation Submitted</h4>
                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Completed</span>
                                </div>
                                <p class="text-xs text-green-700">Reservation was submitted by {{ $reservation->user->name }}</p>
                                <p class="text-xs text-green-600 mt-1">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- IOSA Review -->
                        <div class="timeline-item {{ $reservation->status !== 'pending' ? 'completed' : 'pending' }}">
                            <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-blue-800 text-sm">IOSA Review</h4>
                                    @if($reservation->status === 'pending')
                                        <span class="text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded-full">In Progress</span>
                                    @else
                                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Completed</span>
                                    @endif
                                </div>
                                <p class="text-xs text-blue-700">
                                    @if($reservation->status === 'pending')
                                        Currently under review by IOSA
                                    @elseif($reservation->status === 'approved')
                                        Approved by IOSA - Forwarded to Ms. Mhadel
                                    @elseif($reservation->status === 'rejected')
                                        Rejected by IOSA
                                    @endif
                                </p>
                                @if($reservation->status !== 'pending')
                                    <p class="text-xs text-blue-600 mt-1">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ms. Mhadel Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved', 'approved_mhadel', 'approved_OTP']) ? 'completed' : 'pending' }}">
                            <div class="bg-purple-50 p-3 rounded border border-purple-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-purple-800 text-sm">Ms. Mhadel Review</h4>
                                    @if(in_array($reservation->status, ['approved', 'approved_mhadel', 'approved_OTP']))
                                        <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Completed</span>
                                    @else
                                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-full">Pending</span>
                                    @endif
                                </div>
                                <p class="text-xs text-purple-700">
                                    @if(in_array($reservation->status, ['approved', 'approved_mhadel', 'approved_OTP']))
                                        Approved by Ms. Mhadel
                                    @else
                                        Waiting for IOSA approval
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- OTP Final Approval -->
                        <div class="timeline-item {{ $reservation->status === 'approved_OTP' ? 'completed' : 'pending' }}">
                            <div class="bg-indigo-50 p-3 rounded border border-indigo-200">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-indigo-800 text-sm">OTP Final Approval</h4>
                                    @if($reservation->status === 'approved_OTP')
                                        <span class="text-xs text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full">Completed</span>
                                    @else
                                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-full">Pending</span>
                                    @endif
                                </div>
                                <p class="text-xs text-indigo-700">
                                    @if($reservation->status === 'approved_OTP')
                                        Final approval granted by OTP
                                    @else
                                        Waiting for previous approvals
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="info-card">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-user text-maroon mr-2"></i>
                        Requester Information
                    </h2>
                </div>
                <div class="p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-maroon rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($reservation->user->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <h3 class="font-semibold text-gray-800">{{ $reservation->user->name }}</h3>
                            <p class="text-xs text-gray-600">{{ $reservation->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-xs text-gray-600">Submitted</span>
                            <span class="text-xs font-medium text-gray-800">{{ $reservation->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-600">Last Updated</span>
                            <span class="text-xs font-medium text-gray-800">{{ $reservation->updated_at->format('M d, Y g:i A') }}</span>
                        </div>
                        @if($reservation->status !== 'pending')
                            <div class="flex justify-between items-center py-1">
                                <span class="text-xs text-gray-600">Status Changed</span>
                                <span class="text-xs font-medium text-gray-800">{{ $reservation->updated_at->format('M d, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Venue Details -->
            @if($reservation->venue)
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-building text-maroon mr-2"></i>
                            Venue Details
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Venue Name</label>
                                <p class="text-sm font-medium text-gray-700">{{ $reservation->venue->name }}</p>
                            </div>
                            @if($reservation->venue->description)
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Description</label>
                                    <p class="text-sm text-gray-700">{{ $reservation->venue->description }}</p>
                                </div>
                            @endif
                            @if($reservation->venue->capacity)
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Capacity</label>
                                    <p class="text-sm text-gray-700">{{ $reservation->venue->capacity }} people</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rating Information -->
            @if($reservation->status === 'completed' && $reservation->total_ratings > 0)
                <div class="info-card">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-star text-maroon mr-2"></i>
                            User Rating
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($reservation->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($reservation->average_rating, 1) }}/5</p>
                                <p class="text-xs text-gray-600">{{ $reservation->total_ratings }} rating{{ $reservation->total_ratings > 1 ? 's' : '' }}</p>
                            </div>
                            
                            @if($reservation->ratings->count() > 0)
                                <div class="space-y-2">
                                    <h4 class="font-medium text-gray-800 text-sm">Recent Reviews</h4>
                                    @foreach($reservation->ratings->take(1) as $rating)
                                        <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }} text-xs"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rating->created_at->format('M d') }}</span>
                                            </div>
                                            @if($rating->comment)
                                                <p class="text-xs text-gray-700 italic">"{{ Str::limit($rating->comment, 80) }}"</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">by {{ $rating->user->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full font-poppins animate-fadeIn">
            <div class="p-4 border-b border-gray-200 bg-green-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Approve Reservation
                    </h3>
                    <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4">
                <p class="text-gray-700 mb-3 text-sm">Are you sure you want to approve this reservation?</p>
                <div class="bg-green-50 p-3 rounded border border-green-200 mb-3">
                    <h4 class="font-semibold text-green-800 text-sm" id="approveEventTitle"></h4>
                    <p class="text-xs text-green-600 mt-1">This reservation will be forwarded to Ms. Mhadel for final approval.</p>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                    <textarea id="approveNotes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Add any additional notes for this approval..."></textarea>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end space-x-2">
                <button onclick="closeApproveModal()" class="px-3 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors text-sm">
                    Cancel
                </button>
                <form id="approveForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="approveNotesInput" name="notes">
                    <button type="submit" class="btn-success text-sm">
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
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full font-poppins animate-fadeIn">
            <div class="p-4 border-b border-gray-200 bg-red-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        Reject Reservation
                    </h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4">
                <p class="text-gray-700 mb-3 text-sm">Are you sure you want to reject this reservation?</p>
                <div class="bg-red-50 p-3 rounded border border-red-200 mb-3">
                    <h4 class="font-semibold text-red-800 text-sm" id="rejectEventTitle"></h4>
                    <p class="text-xs text-red-600 mt-1">This action cannot be undone.</p>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection (Required)</label>
                    <textarea id="rejectNotes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Please provide a reason for rejecting this reservation..." required></textarea>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 flex justify-end space-x-2">
                <button onclick="closeRejectModal()" class="px-3 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors text-sm">
                    Cancel
                </button>
                <form id="rejectForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="rejectNotesInput" name="notes">
                    <button type="submit" class="btn-danger text-sm">
                        <i class="fas fa-times mr-2"></i>Reject Reservation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Activity Grid Modal -->
<div id="viewActivityGridModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full font-poppins animate-fadeIn">
            <div class="p-4 border-b border-gray-200 bg-blue-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <i class="fas fa-table text-blue-500 mr-2"></i>
                        Activity Grid - {{ $reservation->event_title }}
                    </h3>
                    <button onclick="closeViewActivityGridModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 overflow-auto max-h-full">
                <pre class="text-xs text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->activity_grid }}</pre>
            </div>
        </div>
    </div>
</div>

<script>
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

    // View Activity Grid Modal Functions
    function openViewActivityGridModal(eventTitle, activityGridContent) {
        document.getElementById('viewActivityGridModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('viewActivityGridModal').querySelector('h3').textContent = `Activity Grid - ${eventTitle}`;
        document.getElementById('viewActivityGridModal').querySelector('pre').textContent = activityGridContent;
    }

    function closeViewActivityGridModal() {
        document.getElementById('viewActivityGridModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
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

        document.getElementById('viewActivityGridModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewActivityGridModal();
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
</script>
@endsection