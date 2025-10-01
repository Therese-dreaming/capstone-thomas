@extends('layouts.mhadel')

@section('title', 'Reservation Details - OTP')
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
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
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
    
    .fee-selection-radio {
        transition: all 0.2s ease;
    }
    
    .fee-selection-radio:checked + label {
        background-color: #f0f9ff;
        border-color: #0ea5e9;
    }
    
    .fee-selection-label {
        border: 2px solid transparent;
        border-radius: 0.5rem;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .fee-selection-label:hover {
        background-color: #f9fafb;
        border-color: #e5e7eb;
    }
    
    #pricingSection, #discountSection {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
</style>

@section('header-actions')
    <a href="{{ route('mhadel.reservations.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Back to Reservations
    </a>
@endsection

@section('content')
<div class="space-y-6 font-inter">
    <!-- Status Banner -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800 font-poppins mb-2">{{ $reservation->event_title }}</h1>
                    <p class="text-gray-600">Submitted by {{ $reservation->user->name }}</p>
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
                        <p class="text-sm text-gray-500 mt-2">Waiting for OTP</p>
                    @elseif($reservation->status === 'approved_mhadel')
                        <span class="status-badge bg-green-100 text-green-800">Approved by OTP</span>
                        <p class="text-sm text-gray-500 mt-2">Forwarded to PPGS</p>
                    @elseif($reservation->status === 'rejected_mhadel')
                        <span class="status-badge bg-red-100 text-red-800">Rejected by OTP</span>
                        <p class="text-sm text-gray-500 mt-2">Final Decision</p>
                    @elseif($reservation->status === 'approved_OTP')
                        <span class="status-badge bg-blue-100 text-blue-800">Approved by PPGS</span>
                        <p class="text-sm text-gray-500 mt-2">PPGS Confirmed</p>
                    @elseif($reservation->status === 'rejected_OTP')
                        <span class="status-badge bg-red-100 text-red-800">Rejected by PPGS</span>
                        <p class="text-sm text-gray-500 mt-2">Final Decision</p>
                    @elseif($reservation->status === 'cancelled')
                        <span class="status-badge bg-red-100 text-red-800">Cancelled by User</span>
                        <p class="text-sm text-gray-500 mt-2">User Cancelled</p>
                    @else
                        <span class="status-badge bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Left Column - Main Content -->
        <div class="xl:col-span-3 space-y-6">
            <!-- Event Details Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-calendar-alt text-maroon mr-3"></i>
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

            <!-- Cancellation Information -->
            @if($reservation->status === 'cancelled' && $reservation->cancellation_reason)
                <div class="category-card animate-fadeIn">
                    <div class="category-header bg-red-50 border-b border-red-200">
                        <h2 class="text-lg font-bold text-red-800 font-poppins flex items-center">
                            <i class="fas fa-times-circle text-red-600 mr-3"></i>
                            Cancellation Information
                        </h2>
                    </div>
                    <div class="category-content bg-red-50">
                        <div class="bg-white p-6 rounded-lg border border-red-200">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation text-red-600 text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-red-800 mb-3">Reason for Cancellation</h4>
                                    <p class="text-red-700 leading-relaxed mb-4">{{ $reservation->cancellation_reason }}</p>
                                    @if($reservation->cancelled_at)
                                        <div class="pt-4 border-t border-red-200">
                                            <div class="flex items-center text-sm text-red-600">
                                                <i class="fas fa-clock mr-2"></i>
                                                <span class="font-medium">Cancelled on {{ $reservation->cancelled_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Custom Equipment Requests Category -->
            @if($reservation && !empty($reservation->custom_equipment_requests))
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-plus-circle text-maroon mr-3"></i>
                        Custom Equipment Requests
                    </h2>
                </div>
                <div class="category-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($reservation->custom_equipment_requests as $customEquipment)
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-wrench text-blue-600"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-800 font-medium">{{ $customEquipment['name'] ?? 'Custom Equipment' }}</span>
                                            <div class="text-xs text-blue-600 mt-1">Custom Request</div>
                                        </div>
                                    </div>
                                    <span class="text-blue-600 bg-white px-3 py-1 rounded-full border border-blue-200 font-medium">× {{ $customEquipment['quantity'] ?? 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div class="text-sm text-blue-800">
                                <strong>Note:</strong> Custom equipment requests are subject to availability and admin approval. Additional charges may apply.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes & History Category -->
            @if($reservation->notes)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-sticky-note text-maroon mr-3"></i>
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
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-bolt text-maroon mr-3"></i>
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
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
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
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-info-circle text-red-500 mr-3"></i>
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
        <div class="space-y-6">
            <!-- Approval Timeline Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-clock text-maroon mr-3"></i>
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
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_IOSA', 'approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP', 'completed']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">IOSA Review</h4>
                                @if(in_array($reservation->status, ['approved_IOSA', 'approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP', 'completed']))
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for IOSA review</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ms. Mhadel Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_mhadel', 'approved_OTP', 'rejected_mhadel', 'rejected_OTP', 'completed']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">Ms. Mhadel Review</h4>
                                @if($reservation->status === 'approved_mhadel')
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif($reservation->status === 'rejected_mhadel')
                                    <p class="text-sm text-red-600 font-medium">✗ Rejected</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif(in_array($reservation->status, ['approved_OTP', 'rejected_OTP', 'completed']))
                                    <p class="text-sm text-green-600 font-medium">✓ Approved</p>
                                    <p class="text-xs text-gray-500">Previously approved</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for your review</p>
                                @endif
                            </div>
                        </div>

                        <!-- OTP Final Review -->
                        <div class="timeline-item {{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP', 'completed']) ? 'completed' : 'pending' }}">
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800">OTP Final Review</h4>
                                @if($reservation->status === 'approved_OTP')
                                    <p class="text-sm text-green-600 font-medium">✓ Approved by PPGS</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif($reservation->status === 'rejected_OTP')
                                    <p class="text-sm text-red-600 font-medium">✗ Rejected by PPGS</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                @elseif($reservation->status === 'completed')
                                    <p class="text-sm text-green-600 font-medium">✓ Approved by PPGS</p>
                                    <p class="text-xs text-gray-500">Previously approved</p>
                                @else
                                    <p class="text-sm text-yellow-600 font-medium">⏳ Pending</p>
                                    <p class="text-xs text-gray-500">Waiting for OTP review</p>
                                @endif
                            </div>
                        </div>

                        <!-- Event Completed -->
                        @if($reservation->status === 'completed')
                            <div class="timeline-item completed">
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-800">Event Completed</h4>
                                    <p class="text-sm text-green-600 font-medium">✓ Event Completed</p>
                                    <p class="text-sm text-gray-600">{{ $reservation->completed_at ? $reservation->completed_at->format('M d, Y g:i A') : $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                    @if($reservation->completion_notes)
                                        <div class="mt-2 p-2 bg-green-50 rounded border border-green-200">
                                            <p class="text-xs text-gray-600"><strong>Completion Notes:</strong> {{ $reservation->completion_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Requester Information Category -->
            <div class="category-card animate-fadeIn">
                <div class="category-header">
                    <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-user text-maroon mr-3"></i>
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
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-building text-maroon mr-3"></i>
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

            <!-- Rating Information Category -->
            @if($reservation->status === 'completed' && $reservation->total_ratings > 0)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <h2 class="text-lg font-bold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-star text-maroon mr-3"></i>
                            User Rating
                        </h2>
                    </div>
                    <div class="category-content">
                        <div class="space-y-4">
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($reservation->average_rating) ? 'text-yellow-400' : 'text-gray-300' }} text-2xl"></i>
                                    @endfor
                                </div>
                                <p class="text-3xl font-bold text-gray-800">{{ number_format($reservation->average_rating, 1) }}/5</p>
                                <p class="text-sm text-gray-600">{{ $reservation->total_ratings }} rating{{ $reservation->total_ratings > 1 ? 's' : '' }}</p>
                            </div>
                            
                            @if($reservation->ratings->count() > 0)
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800">Recent Reviews</h4>
                                    @foreach($reservation->ratings->take(2) as $rating)
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $rating->created_at->format('M d, Y') }}</span>
                                            </div>
                                            @if($rating->comment)
                                                <p class="text-sm text-gray-700 italic">"{{ Str::limit($rating->comment, 120) }}"</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-2">by {{ $rating->user->name }}</p>
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

<!-- Modals -->
<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-7xl w-full font-poppins animate-fadeIn">
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
                <div id="approvalGrid" class="grid grid-cols-1 gap-6">
                    <!-- Left Column: Reservation Details -->
                    <div id="reservationDetailsColumn">
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
                                    <span>Next Step: <span class="font-medium text-green-600">OTP Review</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Fee Selection <span class="text-red-500">*</span></label>
                            <div class="space-y-3">
                                <div>
                                    <input type="radio" id="feeTypeFree" name="feeType" value="free" class="fee-selection-radio h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300" checked>
                                    <label for="feeTypeFree" class="fee-selection-label block text-sm font-medium text-gray-700 ml-6">
                                        <span class="flex items-center">
                                            <i class="fas fa-gift text-green-500 mr-2"></i>
                                            Free Reservation
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1 block">No charges will be applied to this reservation</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" id="feeTypeWithFee" name="feeType" value="with_fee" class="fee-selection-radio h-4 w-4 text-maroon focus:ring-maroon border-gray-300">
                                    <label for="feeTypeWithFee" class="fee-selection-label block text-sm font-medium text-gray-700 ml-6">
                                        <span class="flex items-center">
                                            <i class="fas fa-money-bill-wave text-maroon mr-2"></i>
                                            With Fee (Charged)
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1 block">Pricing will be applied based on venue rates and duration</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea id="approveNotes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Add any additional notes for this approval..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Middle Column: Pricing Information -->
                    <div id="pricingSection" class="hidden">
                        <h4 class="font-medium text-gray-800 mb-3 text-lg">Pricing Information</h4>
                        
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
                        
                        <!-- Summary -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-start">  
                                <i class="fas fa-save text-blue-500 mr-2"></i>
                                <div class="text-xs text-blue-700">
                                    <p class="font-medium">This information will be saved:</p>
                                    <ul class="mt-1 space-y-1">
                                        <li>• Final price set by OTP</li>
                                        <li>• Discount percentage (if applied)</li>
                                        <li>• Approval notes and timestamp</li>
                                        <li>• Status updated to "OTP Approved"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Discount & Final Price -->
                    <div id="discountSection" class="hidden">
                        <h4 class="font-medium text-gray-800 mb-3 text-lg">Discount & Final Price</h4>
                        
                        <!-- Discount Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Selection</label>
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
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-green-800">Price After Discount:</span>
                                <span id="finalPrice" class="text-xl font-bold text-green-800">₱0.00</span>
                            </div>
                            <div id="discountInfo" class="text-xs text-green-600 hidden">
                                <span id="discountAmount"></span> discount applied
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                <div class="text-xs text-yellow-700">
                                    <p class="font-medium">Discount Information:</p>
                                    <ul class="mt-1 space-y-1">
                                        <li>• Discounts are applied to the final price</li>
                                        <li>• Cannot be combined with other discounts</li>
                                        <li>• Applied immediately upon selection</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeApproveModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <form id="approveForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" id="approveNotesInput" name="notes">
                    <input type="hidden" id="approveFeeTypeInput" name="fee_type">
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
    
    // Fee Selection Functions
    function handleFeeTypeChange() {
        const feeTypeFree = document.getElementById('feeTypeFree');
        const feeTypeWithFee = document.getElementById('feeTypeWithFee');
        const pricingSection = document.getElementById('pricingSection');
        const discountSection = document.getElementById('discountSection');
        const approvalGrid = document.getElementById('approvalGrid');
        const basePrice = document.getElementById('basePrice');
        
        if (feeTypeFree && feeTypeFree.checked) {
            // Single column layout for free reservations
            if (approvalGrid) {
                approvalGrid.className = 'grid grid-cols-1 gap-6';
            }
            
            // Hide pricing and discount sections for free reservations
            if (pricingSection) {
                pricingSection.style.display = 'none';
                pricingSection.classList.add('hidden');
            }
            if (discountSection) {
                discountSection.style.display = 'none';
                discountSection.classList.add('hidden');
            }
            
            // Set base price to 0 for free reservations
            if (basePrice) {
                basePrice.value = '0';
                basePrice.removeAttribute('required');
            }
            
            // Reset discount
            selectedDiscount = 0;
            calculateFinalPrice();
        } else if (feeTypeWithFee && feeTypeWithFee.checked) {
            // Three column layout for paid reservations
            if (approvalGrid) {
                approvalGrid.className = 'grid grid-cols-1 lg:grid-cols-3 gap-6';
            }
            
            // Show pricing and discount sections for paid reservations
            if (pricingSection) {
                pricingSection.style.display = 'block';
                pricingSection.classList.remove('hidden');
            }
            if (discountSection) {
                discountSection.style.display = 'block';
                discountSection.classList.remove('hidden');
            }
            
            // Make base price required for paid reservations
            if (basePrice) {
                basePrice.setAttribute('required', 'required');
                basePrice.value = '';
            }
        }
    }
    
    // Approve Modal Functions
    function openApproveModal(reservationId, eventTitle) {
        const approveEventTitle = document.getElementById('approveEventTitle');
        const approveForm = document.getElementById('approveForm');
        const approveModal = document.getElementById('approveModal');

        if (approveEventTitle) {
            approveEventTitle.textContent = eventTitle;
        }

        if (approveForm) {
            approveForm.setAttribute('action', `/mhadel/reservations/${reservationId}/approve`);
        }

        if (approveModal) {
            approveModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
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
        
        // Reset fee type to free by default
        const feeTypeFree = document.getElementById('feeTypeFree');
        const feeTypeWithFee = document.getElementById('feeTypeWithFee');
        if (feeTypeFree) feeTypeFree.checked = true;
        if (feeTypeWithFee) feeTypeWithFee.checked = false;
        
        // Handle initial fee type state
        handleFeeTypeChange();
        
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


    
    // Form submission handlers
    document.addEventListener('DOMContentLoaded', function() {
        const approveForm = document.getElementById('approveForm');
        const basePrice = document.getElementById('basePrice');
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');

        // Handle approve form submission
        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                const notes = document.getElementById('approveNotes')?.value || '';
                const feeTypeFree = document.getElementById('feeTypeFree');
                const feeTypeWithFee = document.getElementById('feeTypeWithFee');
                const finalPriceInput = basePrice?.value || '';
                
                // Get fee type
                let feeType = 'free';
                if (feeTypeWithFee && feeTypeWithFee.checked) {
                    feeType = 'with_fee';
                }
                
                // Validate based on fee type
                if (feeType === 'with_fee' && finalPriceInput === '') {
                    e.preventDefault();
                    alert('Please enter the final price for this reservation.');
                    basePrice?.focus();
                    return;
                }
                
                const finalPrice = parseFloat(finalPriceInput) || 0;
                const discount = selectedDiscount;
                let priceAfterDiscount = finalPrice;
                
                if (discount > 0 && feeType === 'with_fee') {
                    priceAfterDiscount = finalPrice - (finalPrice * discount / 100);
                }
                
                const approveNotesInput = document.getElementById('approveNotesInput');
                const approveFeeTypeInput = document.getElementById('approveFeeTypeInput');
                const approveBasePriceInput = document.getElementById('approveBasePriceInput');
                const approveDiscountInput = document.getElementById('approveDiscountInput');
                const approveFinalPriceInput = document.getElementById('approveFinalPriceInput');

                if (approveNotesInput) approveNotesInput.value = notes;
                if (approveFeeTypeInput) approveFeeTypeInput.value = feeType;
                if (approveBasePriceInput) approveBasePriceInput.value = feeType === 'free' ? 0 : finalPrice;
                if (approveDiscountInput) approveDiscountInput.value = feeType === 'free' ? 0 : discount;
                if (approveFinalPriceInput) approveFinalPriceInput.value = feeType === 'free' ? 0 : priceAfterDiscount.toFixed(2);
            });
        }
        
        // Add event listener for base price input
        if (basePrice) {
            basePrice.addEventListener('input', calculateFinalPrice);
        }
        
        // Add event listeners for fee type radio buttons
        const feeTypeFree = document.getElementById('feeTypeFree');
        const feeTypeWithFee = document.getElementById('feeTypeWithFee');
        
        if (feeTypeFree) {
            feeTypeFree.addEventListener('change', handleFeeTypeChange);
        }
        
        if (feeTypeWithFee) {
            feeTypeWithFee.addEventListener('change', handleFeeTypeChange);
        }
        
        // Handle reject form submission
        const rejectForm = document.getElementById('rejectForm');
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                const rejectNotes = document.getElementById('rejectNotes');
                const notes = rejectNotes?.value || '';
                if (!notes.trim()) {
                    e.preventDefault();
                    alert('Please provide a reason for rejection.');
                    return;
                }
                const rejectNotesInput = document.getElementById('rejectNotesInput');
                if (rejectNotesInput) rejectNotesInput.value = notes;
            });
        }
        
        // Close modals when clicking outside
        if (approveModal) {
            approveModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeApproveModal();
                }
            });
        }
        
        if (rejectModal) {
            rejectModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRejectModal();
                }
            });
        }

        // Remove the activityGridModal event listener since it doesn't exist in this view
    });
</script>
@endsection 