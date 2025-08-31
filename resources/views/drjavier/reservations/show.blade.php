@extends('layouts.drjavier')

@section('title', 'Reservation Details - OTP')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'Final approval authority for reservations')

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
    
    .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .btn-dark-green {
        background-color: #166534;
        color: white;
    }
    .btn-dark-green:hover { background-color: #15803d; }
    
    .btn-dark-red {
        background-color: #991b1b;
        color: white;
    }
    .btn-dark-red:hover { background-color: #dc2626; }
    
    .btn-dark-blue {
        background-color: #1e40af;
        color: white;
    }
    .btn-dark-blue:hover { background-color: #2563eb; }
    
    .compact-card {
        background: white;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .compact-header {
        background: #8B1818;
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .compact-content { padding: 1rem; }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .info-value {
        font-size: 0.875rem;
        color: #111827;
        font-weight: 500;
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
</style>

@section('header-actions')
    <a href="{{ route('drjavier.reservations.index') }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back to Reservations
    </a>
@endsection

@section('content')
<div class="space-y-6 font-inter">
    <!-- Status Banner -->
    <div class="compact-card animate-fadeIn">
        <div class="compact-header">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold font-poppins">{{ $reservation->event_title }}</h1>
                    <p class="text-gray-200 text-sm mt-1">Submitted by {{ $reservation->user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($reservation->status === 'approved_mhadel')
                        <span class="status-badge status-pending">Pending OTP's Final Review</span>
                    @elseif($reservation->status === 'approved_OTP')
                        <span class="status-badge status-approved">Final Approved by OTP</span>
                    @elseif($reservation->status === 'rejected_OTP')
                        <span class="status-badge status-rejected">Final Rejected by OTP</span>
                    @else
                        <span class="status-badge bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Event Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Information -->
            <div class="compact-card">
                <div class="compact-header">
                    <h2 class="text-lg font-semibold font-poppins flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>Event Information
                    </h2>
                </div>
                <div class="compact-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->event_title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Participants</label>
                            <p class="text-gray-900">{{ $reservation->expected_participants }} attendees</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->start_date->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                            <p class="text-gray-900 font-medium">{{ $reservation->end_date->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($reservation->venue)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                                <p class="text-gray-900">{{ $reservation->venue->name }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                            <p class="text-gray-900">{{ $reservation->start_date->diffInHours($reservation->end_date) }} hours</p>
                        </div>
                    </div>
                    
                    @if($reservation->purpose)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                            <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $reservation->purpose }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Grid -->
            @if($reservation->activity_grid)
                <div class="compact-card">
                    <div class="compact-header">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold font-poppins flex items-center">
                                <i class="fas fa-table mr-2"></i>Activity Grid
                            </h2>
                            <div class="flex items-center space-x-2">
                                @if(Storage::disk('public')->exists($reservation->activity_grid))
                                    <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" 
                                       class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View File
                                    </a>
                                @endif
                                <a href="{{ route('drjavier.reservations.download-activity-grid', $reservation->id) }}" 
                                   class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="compact-content">
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
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->activity_grid }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($reservation->notes)
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-sticky-note mr-2"></i>Notes & History
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->notes }}</pre>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Equipment Details -->
            @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-tools mr-2"></i>Equipment Requested
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($reservation->equipment_details as $equipment)
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $equipment['name'] }}</h4>
                                            @if(isset($equipment['category']))
                                                <p class="text-sm text-gray-600">{{ $equipment['category'] }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-blue-600">{{ $equipment['quantity'] }}</span>
                                            <p class="text-xs text-gray-500">Quantity</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @if($reservation->status === 'approved_mhadel')
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-bolt mr-2"></i>Quick Actions
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="space-y-3">
                            <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" 
                                    class="w-full btn-dark-green px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Approve Reservation
                            </button>
                            <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" 
                                    class="w-full btn-dark-red px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>Reject Reservation
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            This is the final approval. The reservation will be confirmed.
                        </p>
                    </div>
                </div>
            @elseif($reservation->status === 'approved_OTP')
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-2"></i>Status Information
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                                <div>
                                    <h3 class="font-medium text-green-800">Reservation Final Approved by OTP</h3>
                                    <p class="text-sm text-green-600 mt-1">This reservation has been fully approved and confirmed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($reservation->status === 'rejected_OTP')
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-info-circle text-red-500 mr-2"></i>Status Information
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-3 text-xl"></i>
                                <div>
                                    <h3 class="font-medium text-red-800">Reservation Final Rejected by OTP</h3>
                                    <p class="text-sm text-red-600 mt-1">This reservation has been rejected and will not proceed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - User Info & Actions -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="compact-card">
                <div class="compact-header">
                    <h2 class="text-lg font-semibold font-poppins flex items-center">
                        <i class="fas fa-user mr-2"></i>Requester Information
                    </h2>
                </div>
                <div class="compact-content">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-maroon rounded-full flex items-center justify-center text-white font-medium text-lg">
                            {{ substr($reservation->user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium text-gray-800">{{ $reservation->user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $reservation->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Submitted</label>
                            <p class="text-sm text-gray-700">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Last Updated</label>
                            <p class="text-sm text-gray-700">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">IOSA Approval</label>
                            <p class="text-sm text-gray-700 font-medium text-green-600">✓ Approved</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Ms. Mhadel Approval</label>
                            <p class="text-sm text-gray-700 font-medium text-green-600">✓ Approved</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Details -->
            @if($reservation->venue)
                <div class="compact-card">
                    <div class="compact-header">
                        <h2 class="text-lg font-semibold font-poppins flex items-center">
                            <i class="fas fa-building mr-2"></i>Venue Details
                        </h2>
                    </div>
                    <div class="compact-content">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Venue Name</label>
                                <p class="text-sm text-gray-700 font-medium">{{ $reservation->venue->name }}</p>
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

            <!-- Pricing Information -->
            <div class="compact-card">
                <div class="compact-header">
                    <h2 class="text-lg font-semibold font-poppins flex items-center">
                        <i class="fas fa-dollar-sign mr-2"></i>Pricing Information
                    </h2>
                </div>
                <div class="compact-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base Price</label>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($reservation->base_price ?? 0, 2) }}</p>
                            <p class="text-sm text-gray-500">Set by Ms. Mhadel</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Applied</label>
                            <p class="text-xl font-semibold text-blue-600">{{ $reservation->discount_percentage ?? 0 }}%</p>
                            @if($reservation->discount_percentage && $reservation->discount_percentage > 0)
                                <p class="text-sm text-gray-500">Discount amount: ₱{{ number_format(($reservation->base_price * $reservation->discount_percentage / 100), 2) }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Final Price</label>
                            <p class="text-2xl font-bold text-green-800">₱{{ number_format($reservation->final_price ?? 0, 2) }}</p>
                            <p class="text-sm text-gray-500">Price after discount</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rate per Hour</label>
                            <p class="text-lg font-semibold text-gray-800">₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</p>
                            <p class="text-sm text-gray-500">Venue hourly rate</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Timeline -->
            <div class="compact-card">
                <div class="compact-header">
                    <h2 class="text-lg font-semibold font-poppins flex items-center">
                        <i class="fas fa-clock mr-2"></i>Approval Timeline
                    </h2>
                </div>
                <div class="compact-content">
                    <div class="relative">
                        <div class="space-y-4">
                            <!-- Submitted -->
                            <div class="relative flex items-start">
                                <div class="relative z-10 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="bg-green-50 p-2 rounded border border-green-200">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-green-800 text-xs">Reservation Submitted</h4>
                                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Completed</span>
                                        </div>
                                        <p class="text-xs text-green-700">Submitted by {{ $reservation->user->name }}</p>
                                        <p class="text-xs text-green-600 mt-1">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- IOSA Review -->
                            <div class="relative flex items-start">
                                <div class="relative z-10 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="bg-blue-50 p-2 rounded border border-blue-200">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-blue-800 text-xs">IOSA Review</h4>
                                            <span class="text-xs text-blue-600 bg-green-100 px-2 py-1 rounded-full">Completed</span>
                                        </div>
                                        <p class="text-xs text-blue-700">Approved by IOSA</p>
                                        <p class="text-xs text-blue-600 mt-1">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ms. Mhadel Review -->
                            <div class="relative flex items-start">
                                <div class="relative z-10 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="bg-purple-50 p-2 rounded border border-purple-200">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-purple-800 text-xs">Ms. Mhadel Review</h4>
                                            <span class="text-xs text-purple-600 bg-green-100 px-2 py-1 rounded-full">Completed</span>
                                        </div>
                                        <p class="text-xs text-purple-700">Approved by Ms. Mhadel</p>
                                        <p class="text-xs text-purple-600 mt-1">{{ $reservation->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- OTP Final Review -->
                            <div class="relative flex items-start">
                                <div class="relative z-10 w-8 h-8 {{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP']) ? 'bg-indigo-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    @if(in_array($reservation->status, ['approved_OTP', 'rejected_OTP']))
                                        <i class="fas fa-check text-xs"></i>
                                    @else
                                        <i class="fas fa-clock text-xs"></i>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="{{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP']) ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50 border-gray-200' }} p-2 rounded border">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold {{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP']) ? 'text-indigo-800' : 'text-gray-600' }} text-xs">OTP Final Review</h4>
                                            @if(in_array($reservation->status, ['approved_OTP', 'rejected_OTP']))
                                                <span class="text-xs {{ $reservation->status === 'approved_OTP' ? 'text-indigo-600 bg-indigo-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full">
                                                    {{ $reservation->status === 'approved_OTP' ? 'Completed' : 'Rejected' }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-600 bg-yellow-100 px-2 py-1 rounded-full">Current Position</span>
                                            @endif
                                        </div>
                                        <p class="text-xs {{ in_array($reservation->status, ['approved_OTP', 'rejected_OTP']) ? 'text-indigo-700' : 'text-gray-600' }}">
                                            @if($reservation->status === 'approved_OTP')
                                                Final approval granted by OTP
                                            @elseif($reservation->status === 'rejected_OTP')
                                                Final rejection by OTP
                                            @else
                                                Waiting for OTP final review
                                            @endif
                                        </p>
                                        @if(in_array($reservation->status, ['approved_OTP', 'rejected_OTP']))
                                            <p class="text-xs {{ $reservation->status === 'approved_OTP' ? 'text-indigo-600' : 'text-red-600' }} mt-1">{{ $reservation->updated_at->format('M d, Y g:i A') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Position Indicator -->
                        @if($reservation->status === 'approved_mhadel')
                            <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-yellow-600 mr-2 text-xs"></i>
                                    <div>
                                        <p class="text-xs font-medium text-yellow-800">Current Position</p>
                                        <p class="text-xs text-yellow-700">Reservation is waiting for your final review</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full font-poppins animate-fadeIn">
            <div class="p-4 border-b border-gray-200 bg-green-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>Approve Reservation
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
                    <p class="text-xs text-green-600 mt-1">This is the final approval. The reservation will be confirmed.</p>
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
                    <button type="submit" class="btn-dark-green text-sm px-3 py-2 rounded">
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
                    <h3 class="text-lg font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>Reject Reservation
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
                    <button type="submit" class="btn-dark-red text-sm px-3 py-2 rounded">
                        <i class="fas fa-times mr-2"></i>Reject Reservation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
    });
</script>
@endsection