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
    
    /* Category card styles to match Ms. Mhadel's design */
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
    .category-content { padding: 1.5rem; }

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

    /* Timeline styles aligned with Mhadel design */
    .timeline-item { position: relative; padding-left: 2rem; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0; top: 0.5rem;
        width: 12px; height: 12px; border-radius: 50%;
        border: 3px solid #e5e7eb; background: white;
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 5px; top: 1.25rem; width: 2px; height: calc(100% - 0.75rem);
        background: #e5e7eb;
    }
    .timeline-item:last-child::after { display: none; }
    .timeline-item.completed::before { border-color: #10B981; background: #10B981; }
    .timeline-item.pending::before { border-color: #F59E0B; background: #F59E0B; }
    .timeline-item.rejected::before { border-color: #EF4444; background: #EF4444; }
    .status-badge-inline {
        display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 9999px;
        font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
    }
</style>

@section('header-actions')
    <a href="{{ route('drjavier.reservations.index') }}" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center text-sm">
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
                    @if($reservation->status === 'approved_mhadel')
                        <span class="status-badge-inline bg-yellow-100 text-yellow-800">Pending OTP Final Review</span>
                        <p class="text-sm text-gray-500 mt-2">Waiting for OTP</p>
                    @elseif($reservation->status === 'approved_OTP')
                        <span class="status-badge-inline bg-blue-100 text-blue-800">Final Approved</span>
                        <p class="text-sm text-gray-500 mt-2">OTP Confirmed</p>
                    @elseif($reservation->status === 'rejected_OTP')
                        <span class="status-badge-inline bg-red-100 text-red-800">Final Rejected</span>
                        <p class="text-sm text-gray-500 mt-2">Decision Complete</p>
                    @else
                        <span class="status-badge-inline bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-3 space-y-8">
            <!-- Event Details -->
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

            <!-- Activity Grid -->
            @if($reservation->activity_grid)
                <div class="category-card animate-fadeIn">
                    <div class="category-header">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                                <i class="fas fa-table text-maroon mr-3 text-2xl"></i>
                                Activity Grid
                            </h2>
                            <div class="flex items-center space-x-2">
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
                                <a href="{{ route('drjavier.reservations.download-activity-grid', $reservation->id) }}" 
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

            <!-- Notes & History -->
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

            <!-- Quick Actions -->
            @if($reservation->status === 'approved_mhadel')
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
                                This is the final step. Your decision will complete the process.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($reservation->status === 'approved_OTP')
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
                                    <h3 class="font-semibold text-blue-800">Reservation Final Approved by OTP</h3>
                                    <p class="text-sm text-blue-600 mt-1">This reservation has been fully approved and confirmed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($reservation->status === 'rejected_OTP')
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
                                    <h3 class="font-semibold text-red-800">Reservation Final Rejected by OTP</h3>
                                    <p class="text-sm text-red-600 mt-1">This reservation has been rejected and will not proceed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-8">
            <!-- Approval Timeline -->
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
                                    <p class="text-xs text-gray-500">Waiting for Ms. Mhadel review</p>
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
                                    <p class="text-xs text-gray-500">Waiting for your review</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requester Details -->
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

            <!-- Venue Information -->
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
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
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
                <p class="text-gray-700 mb-4">Are you sure you want to approve this reservation?</p>
                <div class="bg-green-50 p-4 rounded-lg mb-4 border border-green-200">
                    <h4 class="font-semibold text-gray-800" id="approveEventTitle"></h4>
                    <p class="text-sm text-green-600 mt-1">This is the final approval. The reservation will be confirmed.</p>
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
    
    // Activity Grid Modal Functions
    function openActivityGridModal() {
        const activityGridContent = `{{ $reservation->activity_grid }}`;
        const el = document.getElementById('activityGridContent');
        if (el) el.textContent = activityGridContent;
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

        const activityGridModal = document.getElementById('activityGridModal');
        if (activityGridModal) {
            activityGridModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeActivityGridModal();
                }
            });
        }
    });
</script>
@endsection