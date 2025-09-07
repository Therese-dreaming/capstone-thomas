@extends('layouts.mhadel')

@section('title', 'Reservation Details - Ms. Mhadel')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'Review reservation information and make approval decision')

@php
use Illuminate\Support\Facades\Storage;
@endphp

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
    
    .discount-btn.selected {
        background-color: #10B981;
        color: white;
        border-color: #10B981;
    }
    
    .discount-btn:hover:not(.selected) {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid #e5e7eb;
        background: white;
    }
    
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 1.25rem;
        width: 2px;
        height: calc(100% - 0.75rem);
        background: #e5e7eb;
    }
    
    .timeline-item:last-child::after {
        display: none;
    }
    
    .timeline-item.completed::before {
        border-color: #10B981;
        background: #10B981;
    }
    
    .timeline-item.pending::before {
        border-color: #F59E0B;
        background: #F59E0B;
    }
    
    .timeline-item.rejected::before {
        border-color: #EF4444;
        background: #EF4444;
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
</style>

@section('header-actions')
    <a href="{{ route('mhadel.reservations.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Back to Reservations
    </a>
@endsection

@section('content')
<div class="space-y-8 font-inter">
    <!-- Status Banner -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-8 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">{{ $reservation->event_title }}</h1>
                    <p class="text-gray-600 text-lg">Submitted by {{ $reservation->user->name }}</p>
                    <div class="text-sm text-gray-500 font-mono mt-2">
                        Reservation ID: {{ $reservation->reservation_id ?? 'N/A' }}
                    </div>
                    <div class="flex items-center mt-3 space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar mr-2 text-maroon"></i>
                            <span>{{ $reservation->start_date->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2 text-maroon"></i>
                            <span>{{ $reservation->start_date->diffInHours($reservation->end_date) }} hours</span>
                        </div>
                        @if($reservation->venue)
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-building mr-2 text-maroon"></i>
                                <span>{{ $reservation->venue->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    @if($reservation->status === 'approved_IOSA')
                        <span class="status-badge bg-yellow-100 text-yellow-800">Pending Review</span>
                        <p class="text-sm text-gray-500 mt-2">Waiting for Ms. Mhadel</p>
                    @elseif($reservation->status === 'approved_mhadel')
                        <span class="status-badge bg-green-100 text-green-800">Approved by Ms. Mhadel</span>
                        <p class="text-sm text-gray-500 mt-2">Forwarded to OTP</p>
                    @elseif($reservation->status === 'rejected_mhadel')
                        <span class="status-badge bg-red-100 text-red-800">Rejected by Ms. Mhadel</span>
                        <p class="text-sm text-gray-500 mt-2">Final Decision</p>
                    @elseif($reservation->status === 'approved_OTP')
                        <span class="status-badge bg-blue-100 text-blue-800">Final Approved</span>
                        <p class="text-sm text-gray-500 mt-2">OTP Confirmed</p>
                    @elseif($reservation->status === 'rejected_OTP')
                        <span class="status-badge bg-red-100 text-red-800">Rejected by OTP</span>
                        <p class="text-sm text-gray-500 mt-2">Final Decision</p>
                    @else
                        <span class="status-badge bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Left Column - Main Content -->
        <div class="xl:col-span-3 space-y-8">
            <!-- Event Details Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-calendar-alt text-maroon mr-3 text-2xl"></i>
                        Event Details
                    </h2>
                </div>
                <div class="category-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
                                <p class="text-gray-900 font-semibold text-lg">{{ $reservation->event_title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Participants</label>
                                <p class="text-gray-900">{{ $reservation->expected_participants }} attendees</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->start_date->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->end_date->format('M d, Y g:i A') }}</p>
                            </div>
                            @if($reservation->venue)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                                    <p class="text-gray-900 font-medium">{{ $reservation->venue->name }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->start_date->diffInHours($reservation->end_date) }} hours</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($reservation->purpose)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Purpose & Description</label>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-900 leading-relaxed">{{ $reservation->purpose }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Grid Category -->
            @if($reservation->activity_grid)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                                <i class="fas fa-table text-maroon mr-3 text-2xl"></i>
                                Activity Grid
                            </h2>
                            <div class="flex space-x-3">
                                @if(Storage::disk('public')->exists($reservation->activity_grid))
                                    <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" 
                                       class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View File
                                    </a>
                                @else
                                    <button onclick="openActivityGridModal()" class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View Text
                                    </button>
                                @endif
                                <a href="{{ route('mhadel.reservations.download-activity-grid', $reservation->id) }}" 
                                   class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="category-content">
                        @if(Storage::disk('public')->exists($reservation->activity_grid))
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
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
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->activity_grid }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Notes & History Category -->
            @if($reservation->notes)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-sticky-note text-maroon mr-3 text-2xl"></i>
                            Notes & History
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->notes }}</pre>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions Category -->
            @if($reservation->status === 'approved_IOSA')
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-bolt text-maroon mr-3 text-2xl"></i>
                            Quick Actions
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="space-y-3">
                            <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="w-full btn-dark-green px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Approve Reservation
                            </button>
                            <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="w-full btn-dark-red px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>Reject Reservation
                            </button>
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-700 text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Approving will forward to OTP for final approval
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($reservation->status === 'approved_mhadel')
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3 text-2xl"></i>
                            Status Information
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-blue-800">Reservation Approved</h3>
                                    <p class="text-sm text-blue-600 mt-1">This reservation has been forwarded to OTP for final approval.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($reservation->status === 'rejected_mhadel')
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-info-circle text-red-500 mr-3 text-2xl"></i>
                            Status Information
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-3 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-red-800">Reservation Rejected</h3>
                                    <p class="text-sm text-red-600 mt-1">This reservation has been rejected and will not proceed further.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-8">
            <!-- Approval Timeline Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-clock text-maroon mr-3 text-2xl"></i>
                        Approval Timeline
                    </h2>
                </div>
                <div class="category-content">
                    <div class="space-y-6">
                        <!-- Submitted -->
                        <div class="timeline-item completed">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Submitted</h4>
                                <p class="text-sm text-gray-600">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                                <p class="text-xs text-gray-500 mt-1">Reservation created by {{ $reservation->user->name }}</p>
                            </div>
                        </div>

                        <!-- IOSA Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_IOSA', 'approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">IOSA Review</h4>
                                @if(in_array($reservation->status, ['approved_IOSA', 'approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP']))
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for IOSA review</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ms. Mhadel Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Ms. Mhadel Review</h4>
                                @if($reservation->status === 'approved_mhadel')
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif($reservation->status === 'rejected_mhadel')
                                    <p class="text-sm text-red-600 font-medium">✗ Rejected</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif(in_array($reservation->status, ['approved_OTP', 'rejected_OTP']))
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-xs text-gray-500">Previously approved</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for your review</p>
                                @endif
                            </div>
                        </div>

                        <!-- OTP Final Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">OTP Final Review</h4>
                                @if($reservation->status === 'approved_OTP')
                                    <p class="text-sm text-green-600 font-medium">✓ Final Approved</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif($reservation->status === 'rejected_OTP')
                                    <p class="text-sm text-red-600 font-medium">✗ Final Rejected</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for OTP review</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requester Information Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-user text-maroon mr-3 text-2xl"></i>
                        Requester Details
                    </h2>
                </div>
                <div class="category-content">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-maroon rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($reservation->user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-800 text-lg">{{ $reservation->user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $reservation->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Submitted</label>
                            <p class="text-sm text-gray-700 font-medium">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Last Updated</label>
                            <p class="text-sm text-gray-700 font-medium">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Details Category -->
            @if($reservation->venue)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-building text-maroon mr-3 text-2xl"></i>
                            Venue Information
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Venue Name</label>
                                <p class="text-sm text-gray-700 font-semibold">{{ $reservation->venue->name }}</p>
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
                            @if($reservation->venue->price_per_hour)
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Rate per Hour</label>
                                    <p class="text-sm text-gray-700 font-semibold text-green-600">₱{{ number_format($reservation->venue->price_per_hour, 2) }}</p>
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

<!-- Activity Grid Modal -->
<div id="activityGridModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-table text-blue-500 mr-2"></i>
                        Activity Grid Preview
                    </h3>
                    <button onclick="closeActivityGridModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <pre id="activityGridContent" class="text-sm text-gray-800 whitespace-pre-wrap font-mono"></pre>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeActivityGridModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
    
    // Approve Modal Functions
    function openApproveModal(reservationId, eventTitle) {
        document.getElementById('approveEventTitle').textContent = eventTitle;
        document.getElementById('approveForm').action = `/mhadel/reservations/${reservationId}/approve`;
        document.getElementById('approveModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Get the reservation data from the page
        const reservation = {
            id: {{ $reservation->id }},
            event_title: '{{ $reservation->event_title }}',
            price_per_hour: {{ $reservation->price_per_hour ?? 0 }},
            base_price: {{ $reservation->base_price ?? 0 }},
            duration_hours: {{ $reservation->start_date->diffInHours($reservation->end_date) }},
            venue: {
                name: '{{ $reservation->venue->name ?? "" }}',
                price_per_hour: {{ $reservation->venue->price_per_hour ?? 0 }}
            }
        };
        
        // Update the venue pricing information display
        const venueRatePerHour = document.getElementById('venueRatePerHour');
        const calculatedBasePrice = document.getElementById('calculatedBasePrice');
        const basePriceCalculation = document.getElementById('basePriceCalculation');
        
        // Display venue rate per hour (if available)
        if (reservation.price_per_hour && parseFloat(reservation.price_per_hour) > 0) {
            const ratePerHour = parseFloat(reservation.price_per_hour);
            venueRatePerHour.textContent = `₱${ratePerHour.toFixed(2)}`;
        } else {
            // Try to get rate from venue data if available
            if (reservation.venue && reservation.venue.price_per_hour) {
                const venueRate = parseFloat(reservation.venue.price_per_hour);
                venueRatePerHour.textContent = `₱${venueRate.toFixed(2)} (from venue)`;
            } else {
                venueRatePerHour.textContent = 'Not available';
            }
        }
        
        // Display calculated base price
        if (reservation.base_price && parseFloat(reservation.base_price) > 0) {
            const userPriceFormatted = parseFloat(reservation.base_price).toFixed(2);
            calculatedBasePrice.textContent = `₱${userPriceFormatted}`;
            
            // Show calculation details if we have both rate and duration
            if (reservation.price_per_hour && reservation.duration_hours) {
                const rate = parseFloat(reservation.price_per_hour);
                const duration = parseInt(reservation.duration_hours);
                basePriceCalculation.textContent = `Rate: ₱${rate.toFixed(2)}/hour × ${duration} hour${duration > 1 ? 's' : ''} = ₱${userPriceFormatted}`;
            } else if (reservation.venue && reservation.venue.price_per_hour && reservation.duration_hours) {
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
        
        // Reset form fields
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

    // Activity Grid Modal Functions
    function openActivityGridModal() {
        const activityGridContent = `{{ $reservation->activity_grid }}`;
        document.getElementById('activityGridContent').textContent = activityGridContent;
        document.getElementById('activityGridModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeActivityGridModal() {
        document.getElementById('activityGridModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
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

        document.getElementById('activityGridModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeActivityGridModal();
            }
        });
    });
</script>
@endsection 