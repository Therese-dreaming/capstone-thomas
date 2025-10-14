@extends('layouts.gsu')

@section('title', 'Reservation Details - GSU')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'Final Approved Reservation')

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
    
    .status-badge { 
        padding: 0.5rem 1rem; 
        border-radius: 9999px; 
        font-size: 0.75rem; 
        font-weight: 600; 
        text-transform: uppercase; 
        letter-spacing: 0.05em;
        background-color: #10B981;
        color: #ffffff;
    }
    
    .info-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }
    
    .glass-effect {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    
    .highlight-box {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-left: 4px solid #800000;
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
                        {{ $reservation->event_title }}
                    </h1>
                    <p class="text-gray-600 font-medium">Final approved reservation information and details</p>
                    <div class="text-sm text-gray-500 font-mono mt-2">
                        Reservation ID: {{ $reservation->reservation_id ?? 'N/A' }}
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('gsu.reservations.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-arrow-left mr-1.5"></i>
                        <span>Back to List</span>
                    </a>
                    <span class="status-badge">Final Approved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Event Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Details Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Event Information</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-user text-maroon mr-3 w-4"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Requester</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $reservation->user->name }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-maroon mr-3 w-4"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Date</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $reservation->start_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-maroon mr-3 w-4"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Time</p>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($reservation->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_date)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($reservation->start_date && $reservation->end_date)
                                <div class="flex items-center">
                                    <i class="fas fa-hourglass-half text-maroon mr-3 w-4"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Duration</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($reservation->start_date)->diffInHours($reservation->end_date) }} hours
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($reservation->purpose)
                        <div class="highlight-box p-4 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-maroon mr-3 mt-0.5"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium mb-1">Purpose</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $reservation->purpose }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Venue & Capacity Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Venue & Capacity</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-maroon mr-3 w-4"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Venue Name</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $reservation->venue->name }}</p>
                            </div>
                        </div>
                        
                        @if($reservation->venue->description)
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-maroon mr-3 mt-0.5 w-4"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Description</p>
                                    <p class="text-sm text-gray-700">{{ $reservation->venue->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-users text-maroon mr-3 w-4"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Expected Participants</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $reservation->capacity ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        @if($reservation->venue->capacity)
                            <div class="flex items-center">
                                <i class="fas fa-building text-maroon mr-3 w-4"></i>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Venue Capacity</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $reservation->venue->capacity }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Equipment Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tools text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Equipment Requested</h3>
                </div>
                
                @if(($reservation->equipment_details && count($reservation->equipment_details) > 0) || (!empty($reservation->custom_equipment_requests)))
                    <div class="space-y-4">
                        @if($reservation->equipment_details && count($reservation->equipment_details) > 0)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-toolbox text-blue-600 mr-2"></i>
                                    Standard Equipment
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($reservation->equipment_details as $eq)
                                        <div class="bg-blue-50 text-blue-800 px-3 py-2 rounded-lg border border-blue-200 flex items-center justify-between">
                                            <span class="font-medium text-sm">{{ $eq['name'] }}</span>
                                            <span class="bg-blue-200 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">
                                                {{ $eq['quantity'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($reservation->custom_equipment_requests))
                            <div class="{{ ($reservation->equipment_details && count($reservation->equipment_details) > 0) ? 'pt-4 border-t border-gray-200' : '' }}">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-plus-circle text-orange-600 mr-2"></i>
                                    Custom Equipment Requests
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    @foreach($reservation->custom_equipment_requests as $customEquipment)
                                        <div class="bg-orange-50 text-orange-800 px-3 py-2 rounded-lg border border-orange-200 flex items-center justify-between">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-sm">{{ $customEquipment['name'] ?? 'Custom Equipment' }}</span>
                                                <span class="text-xs text-orange-600">Custom Request</span>
                                            </div>
                                            <span class="bg-orange-200 text-orange-800 text-xs px-2 py-1 rounded-full font-semibold">
                                                {{ $customEquipment['quantity'] ?? 1 }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle text-orange-600 mr-2 mt-0.5"></i>
                                        <p class="text-xs text-orange-800">
                                            <strong>Note:</strong> Custom equipment requests are subject to availability and admin approval. Additional charges may apply.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="fas fa-tools text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">No equipment requested for this reservation</p>
                    </div>
                @endif
            </div>

            <!-- Issues Reported Card (Only show if reservation is completed and has reports) -->
            @if($reservation->status === 'completed' && $reservation->reports && $reservation->reports->count() > 0)
                <div class="info-card rounded-xl p-6 border-l-4 border-red-500 bg-red-50">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Issues Reported</h3>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($reservation->reports as $report)
                            <div class="bg-white border border-red-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold text-white
                                        @if($report->severity === 'low') bg-blue-500
                                        @elseif($report->severity === 'medium') bg-yellow-500
                                        @elseif($report->severity === 'high') bg-red-500
                                        @elseif($report->severity === 'critical') bg-red-800
                                        @else bg-gray-500
                                        @endif">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ ucfirst($report->severity) }} - {{ ucfirst($report->type) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $report->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium mb-1">Description</p>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $report->description }}</p>
                                    </div>
                                    
                                    @if($report->actions_taken)
                                        <div>
                                            <p class="text-xs text-gray-500 font-medium mb-1">Actions Taken</p>
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $report->actions_taken }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <div class="flex items-center space-x-4">
                                            <span class="text-xs text-gray-500">
                                                <strong>Status:</strong> 
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                    @if($report->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($report->status === 'resolved') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <strong>Reported by:</strong> {{ $report->reporter->name ?? 'GSU' }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 font-mono">Report #{{ $report->id }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Pricing & Actions -->
        <div class="space-y-6">
            <!-- Pricing Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tag text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Pricing Details</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="text-center">
                            <p class="text-xs text-green-600 font-medium mb-1">Final Price</p>
                            <p class="text-2xl font-bold text-green-800">₱{{ number_format($reservation->final_price ?? 0, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Rate per Hour</span>
                            <span class="font-semibold text-gray-800">₱{{ number_format($reservation->price_per_hour ?? 0, 2) }}</span>
                        </div>
                        
                        @if($reservation->start_date && $reservation->end_date)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Duration</span>
                                <span class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($reservation->start_date)->diffInHours($reservation->end_date) }} hours
                                </span>
                            </div>
                        @endif
                        
                        @php
                            $totalEquipmentCount = 0;
                            if($reservation->equipment_details && count($reservation->equipment_details) > 0) {
                                $totalEquipmentCount += count($reservation->equipment_details);
                            }
                            if(!empty($reservation->custom_equipment_requests)) {
                                $totalEquipmentCount += count($reservation->custom_equipment_requests);
                            }
                        @endphp
                        @if($totalEquipmentCount > 0)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Equipment</span>
                                <span class="font-semibold text-gray-800">{{ $totalEquipmentCount }} items</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reservation Status Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Reservation Status</h3>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="status-badge text-xs">Final Approved</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Reservation ID</span>
                        <span class="font-mono text-sm font-semibold text-gray-800">#{{ $reservation->id }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Created</span>
                        <span class="text-sm font-medium text-gray-800">{{ $reservation->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Last Updated</span>
                        <span class="text-sm font-medium text-gray-800">{{ $reservation->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Rating Information Card -->
            @if($reservation->status === 'completed' && $reservation->total_ratings > 0)
                <div class="info-card rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">User Rating</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($reservation->average_rating) ? 'text-yellow-400' : 'text-gray-300' }} text-xl"></i>
                                @endfor
                            </div>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($reservation->average_rating, 1) }}/5</p>
                            <p class="text-sm text-gray-600">{{ $reservation->total_ratings }} rating{{ $reservation->total_ratings > 1 ? 's' : '' }}</p>
                        </div>
                        
                        @if($reservation->ratings->count() > 0)
                            <div class="space-y-3">
                                <h4 class="font-semibold text-gray-800 text-sm">Recent Reviews</h4>
                                @foreach($reservation->ratings->take(1) as $rating)
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $rating->created_at->format('M d, Y') }}</span>
                                        </div>
                                        @if($rating->comment)
                                            <p class="text-sm text-gray-700 italic">"{{ Str::limit($rating->comment, 100) }}"</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">by {{ $rating->user->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Quick Actions Card -->
            <div class="info-card rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('gsu.reservations.index') }}" 
                       class="w-full px-4 py-3 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2 text-sm">
                        <i class="fas fa-list mr-1.5"></i>
                        <span>View All Reservations</span>
                    </a>
                    
                    @if($reservation->status !== 'completed')
                    <button onclick="openCompleteModal({{ $reservation->id }}, '{{ $reservation->event_title }}', 'reservation')" 
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2 text-sm">
                        <i class="fas fa-check-circle mr-1.5"></i>
                        <span>Mark as Complete</span>
                    </button>
                    @else
                    <div class="w-full px-4 py-3 bg-green-100 text-green-800 rounded-lg flex items-center justify-center space-x-2 text-sm font-medium">
                        <i class="fas fa-check-circle mr-1.5"></i>
                        <span>Already Completed</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add print styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .glass-effect, .info-card { box-shadow: none !important; }
            .bg-gray-50 { background-color: #f9fafb !important; }
            .animate-fadeIn { animation: none !important; }
        }
    `;
    document.head.appendChild(style);
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