@extends('layouts.user')

@section('title', 'Reservation Details')
@section('page-title', 'Reservation Details')

@section('styles')
<style>
    .status-badge {
        transition: all 0.2s ease;
    }
    .status-badge:hover {
        transform: translateY(-1px);
    }
    .detail-card {
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }
    .detail-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .metric-card {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    .metric-card:hover {
        background: #f1f5f9;
        transform: translateY(-1px);
    }
    .timeline-dot {
        transition: all 0.2s ease;
    }
    .timeline-dot:hover {
        transform: scale(1.1);
    }
    .tab-button {
        transition: all 0.2s ease;
        position: relative;
    }
    .tab-button::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: #8b0000;
        transition: width 0.2s ease;
    }
    .tab-button.active::before {
        width: 100%;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .progress-ring {
        transform: rotate(-90deg);
    }
    .progress-ring-circle {
        transition: stroke-dasharray 0.5s ease;
    }
    .action-btn {
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('header-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('user.reservations.index') }}" class="action-btn px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 flex items-center space-x-2 shadow-sm border border-gray-200">
        <i class="fas fa-arrow-left"></i>
        <span>Back to List</span>
    </a>
    <a href="{{ route('user.reservations.calendar') }}" class="action-btn px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 flex items-center space-x-2 shadow-sm">
        <i class="fas fa-calendar-alt"></i>
        <span>Calendar</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    @php
        $status = $reservation->status;
        $badge = ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'icon' => 'fa-clock', 'label' => 'In Review'];
        if (in_array($status, ['approved','approved_OTP'])) { 
            $badge = ['class' => 'bg-green-100 text-green-800 border-green-300', 'icon' => 'fa-check-circle', 'label' => 'Approved']; 
        }
        if ($status === 'completed') { 
            $badge = ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'icon' => 'fa-check-double', 'label' => 'Completed']; 
        }
        if (in_array($status, ['rejected','rejected_OTP'])) { 
            $badge = ['class' => 'bg-red-100 text-red-800 border-red-300', 'icon' => 'fa-times-circle', 'label' => 'Rejected']; 
        }
        if ($status === 'cancelled') { 
            $badge = ['class' => 'bg-gray-100 text-gray-800 border-gray-300', 'icon' => 'fa-ban', 'label' => 'Cancelled']; 
        }
        $duration = $reservation->duration_hours;
    @endphp

    <!-- Header Section -->
    <div class="bg-maroon rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl text-white font-black text-gray-800">{{ $reservation->event_title }}</h1>
                <div class="text-sm text-white mt-1">
                    <div>Requested on {{ $reservation->created_at?->format('M d, Y g:i A') }}</div>
                    <div class="font-mono text-xs mt-1">Reservation ID: {{ $reservation->reservation_id ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="mt-3 md:mt-0">
                <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badge['class'] }} border">
                    <i class="fas {{ $badge['icon'] }} mr-2"></i>
                    {{ $badge['label'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="metric-card rounded-lg p-3 text-center">
            <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-blue-600"></i>
            </div>
            <h3 class="text-xs font-medium text-gray-500 mb-1">Venue</h3>
            <p class="text-sm font-semibold text-gray-800">{{ $reservation->venue->name ?? 'N/A' }}</p>
        </div>
        
        <div class="metric-card rounded-lg p-3 text-center">
            <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-purple-100 flex items-center justify-center">
                <i class="far fa-calendar text-purple-600"></i>
            </div>
            <h3 class="text-xs font-medium text-gray-500 mb-1">Date</h3>
            <p class="text-sm font-semibold text-gray-800">{{ $reservation->start_date?->format('M d, Y') }}</p>
        </div>
        
        <div class="metric-card rounded-lg p-3 text-center">
            <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-green-100 flex items-center justify-center">
                <i class="far fa-clock text-green-600"></i>
            </div>
            <h3 class="text-xs font-medium text-gray-500 mb-1">Time</h3>
            <p class="text-sm font-semibold text-gray-800">{{ $reservation->start_date?->format('g:i A') }} – {{ $reservation->end_date?->format('g:i A') }}</p>
        </div>
        
        <div class="metric-card rounded-lg p-3 text-center">
            <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="fas fa-hourglass-half text-orange-600"></i>
            </div>
            <h3 class="text-xs font-medium text-gray-500 mb-1">Duration</h3>
            <p class="text-sm font-semibold text-gray-800">{{ $duration ? $duration . ' hr' . ($duration > 1 ? 's' : '') : '—' }}</p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex overflow-x-auto">
            <button class="tab-button active flex-1 py-3 px-4 text-center font-medium text-maroon-600 focus:outline-none" data-tab="details">
                <i class="fas fa-info-circle mr-2"></i>Details
            </button>
            <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="timeline">
                <i class="fas fa-history mr-2"></i>Timeline
            </button>
            <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="pricing">
                <i class="fas fa-money-bill-wave mr-2"></i>Pricing
            </button>
            @if(!empty($reservation->equipment_details) || !empty($reservation->custom_equipment_requests))
            <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="equipment">
                <i class="fas fa-toolbox mr-2"></i>Equipment
            </button>
            @endif
            @if(!empty($reservation->activity_grid))
            <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="attachments">
                <i class="fas fa-paperclip mr-2"></i>Attachments
            </button>
            @endif
            @if($reservation->status === 'completed')
            <button class="tab-button flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="rating">
                <i class="fas fa-star mr-2"></i>Rating
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content active" id="details-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-maroon-100 flex items-center justify-center mr-3">
                    <i class="fas fa-bullseye text-maroon-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Reservation Details</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-bullseye text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-blue-600 mb-1">Purpose</div>
                            <div class="text-gray-800 text-sm">{{ $reservation->purpose }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i class="fas fa-users text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-purple-600 mb-1">Capacity</div>
                            <div class="text-gray-800 text-sm">{{ $reservation->capacity }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @if($reservation->status === 'cancelled' && !empty($reservation->cancellation_reason))
                    <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i class="fas fa-ban text-red-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-medium text-red-600 mb-2">Cancellation Reason</div>
                                <div class="text-gray-800 text-sm leading-relaxed">{{ $reservation->cancellation_reason }}</div>
                                @if($reservation->cancelled_at)
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="far fa-clock mr-1"></i>Cancelled on {{ $reservation->cancelled_at->format('M d, Y g:i A') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($reservation->notes))
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                <i class="fas fa-sticky-note text-amber-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-amber-600 mb-2">Additional Notes</div>
                                <div class="text-gray-800 text-sm leading-relaxed">
                                    @php
                                        $notes = $reservation->notes;
                                        // Remove the system-generated content and show only user notes
                                        if (strpos($notes, 'Notes:') !== false) {
                                            $notesParts = explode('Notes:', $notes);
                                            if (count($notesParts) > 1) {
                                                $userNotes = trim($notesParts[1]);
                                                // Remove pricing review section if it exists
                                                if (strpos($userNotes, 'Pricing Review:') !== false) {
                                                    $userNotes = explode('Pricing Review:', $userNotes)[0];
                                                }
                                                $userNotes = trim($userNotes);
                                                echo $userNotes === 'None.' ? 'No additional notes provided.' : $userNotes;
                                            } else {
                                                echo 'No additional notes provided.';
                                            }
                                        } else {
                                            echo $notes;
                                        }
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="tab-content" id="timeline-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                    <i class="fas fa-history text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Status Timeline</h3>
            </div>
            
            @php
                $steps = [
                    ['key' => 'submitted', 'label' => 'Submitted', 'icon' => 'fa-paper-plane'],
                    ['key' => 'iosa', 'label' => 'IOSA Review', 'icon' => 'fa-eye'],
                    ['key' => 'mhadel', 'label' => 'OTP Review', 'icon' => 'fa-user-check'],
                    ['key' => 'otp', 'label' => 'Final Approval (PPGS)', 'icon' => 'fa-shield-alt'],
                    ['key' => 'gsu', 'label' => 'GSU Completion', 'icon' => 'fa-tools'],
                ];
                $currentIndex = 0; $failed = false; $doneSteps = 1;
                switch ($reservation->status) {
                    case 'pending':
                        $currentIndex = 1; $doneSteps = 1; break;
                    case 'approved_IOSA':
                        $currentIndex = 2; $doneSteps = 2; break;
                    case 'approved_mhadel':
                        $currentIndex = 3; $doneSteps = 3; break;
                    case 'approved':
                    case 'approved_OTP':
                        $currentIndex = 4; $doneSteps = 4; break;
                    case 'completed':
                        $currentIndex = 4; $doneSteps = 5; break;
                    case 'rejected':
                    case 'rejected_OTP':
                        $currentIndex = 3; $doneSteps = 3; $failed = true; break;
                    default:
                        $currentIndex = 0; $doneSteps = 1; break;
                }
                $segments = max(1, count($steps) - 1);
                $progressSegments = max(0, min($segments, $doneSteps - 1));
                $progressPercent = intval(($progressSegments / $segments) * 100);
            @endphp

            <!-- Progress Ring -->
            <div class="flex justify-center mb-4">
                <div class="relative">
                    <svg class="w-20 h-20 progress-ring">
                        <circle
                            class="progress-ring-circle"
                            stroke="#e5e7eb"
                            stroke-width="4"
                            fill="transparent"
                            r="32"
                            cx="40"
                            cy="40"
                        />
                        <circle
                            class="progress-ring-circle"
                            stroke="{{ $failed ? '#ef4444' : '#10b981' }}"
                            stroke-width="4"
                            fill="transparent"
                            r="32"
                            cx="40"
                            cy="40"
                            stroke-dasharray="{{ 2 * 3.14159 * 32 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 32 * (1 - $progressPercent / 100) }}"
                        />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-800">{{ $progressPercent }}%</div>
                            <div class="text-xs text-gray-500">Complete</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative pl-8">
                <div class="absolute left-4 top-1 bottom-1 w-0.5 bg-gray-300 rounded-full"></div>
                <div class="absolute left-4 top-1 w-0.5 rounded-full transition-all duration-500 ease-out" 
                     style="height: {{ $progressPercent }}%; background: {{ $failed ? '#ef4444' : '#10b981' }};"></div>

                @foreach($steps as $i => $step)
                    @php
                        $state = 'todo';
                        if ($failed && $i === $currentIndex) { $state = 'failed'; }
                        elseif ($i < $doneSteps) { $state = 'done'; }
                        elseif ($i === $currentIndex) { $state = 'current'; }
                    @endphp
                    <div class="relative flex items-start mb-6">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 timeline-dot
                            {{ $state === 'done' ? 'bg-green-500 border-green-500 text-white' : '' }}
                            {{ $state === 'current' ? 'bg-blue-600 border-blue-600 text-white' : '' }}
                            {{ $state === 'failed' ? 'bg-red-500 border-red-500 text-white' : '' }}
                            {{ $state === 'todo' ? 'bg-gray-300 border-gray-300 text-white' : '' }}
                        ">
                            <i class="fas {{ $step['icon'] }} text-xs"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-sm font-medium text-gray-800 mb-1">{{ $step['label'] }}</div>
                            @if($i === 0 && $reservation->created_at)
                                <div class="text-xs text-gray-500 mb-2">{{ $reservation->created_at->format('M d, Y g:i A') }}</div>
                            @endif
                            @if($i === 4 && $reservation->status === 'completed' && $reservation->completion_date)
                                <div class="text-xs text-gray-500 mb-2">Completed on {{ $reservation->completion_date->format('M d, Y g:i A') }}</div>
                            @endif
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $state === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $state === 'current' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $state === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $state === 'todo' ? 'bg-gray-100 text-gray-600' : '' }}
                            ">
                                @if($state === 'done')
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                @elseif($state === 'current')
                                    <i class="fas fa-spinner fa-spin mr-1"></i> In Progress
                                @elseif($state === 'failed')
                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                @else
                                    <i class="fas fa-hourglass-start mr-1"></i> Pending
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @if($reservation->status === 'completed' && $reservation->reports && $reservation->reports->count() > 0)
                    <div class="relative flex items-start mb-6">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 timeline-dot bg-orange-500 border-orange-500 text-white">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="text-sm font-medium text-gray-800 mb-1">Reported Issues</div>
                            <div class="text-xs text-gray-500 mb-2">{{ $reservation->reports->count() }} issue(s) reported</div>
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Issues Found
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-content" id="pricing-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Pricing Summary</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 text-sm font-medium flex items-center">
                            <i class="fas fa-money-bill-wave mr-2 text-blue-500"></i>Rate per Hour
                        </span>
                        <span class="text-lg font-bold text-gray-800">₱{{ number_format((float)($reservation->price_per_hour ?? 0), 2) }}</span>
                    </div>
                </div>
                
                <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 text-sm font-medium flex items-center">
                            <i class="fas fa-hourglass-half mr-2 text-purple-500"></i>Duration
                        </span>
                        <span class="text-lg font-bold text-gray-800">{{ !is_null($duration) ? $duration . ' hr' : '—' }}</span>
                    </div>
                </div>
                
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 text-sm font-medium flex items-center">
                            <i class="fas fa-calculator mr-2 text-green-500"></i>Base Price
                        </span>
                        <span class="text-lg font-bold text-gray-800">₱{{ number_format((float)($reservation->base_price ?? 0), 2) }}</span>
                    </div>
                </div>
                
                <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600 text-sm font-medium flex items-center">
                            <i class="fas fa-percent mr-2 text-orange-500"></i>Discount
                        </span>
                        <span class="text-lg font-bold text-gray-800">{{ is_null($reservation->discount_percentage) ? '—' : $reservation->discount_percentage . '%' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-maroon-50 rounded-lg border-2 border-maroon-200">
                <div class="flex items-center justify-between">
                    <span class="text-maroon-800 font-bold text-lg flex items-center">
                        <i class="fas fa-tag mr-2"></i>Final Price
                    </span>
                    <span class="text-maroon-900 font-bold text-2xl">
                        {{ is_null($reservation->final_price) ? '—' : '₱' . number_format((float)$reservation->final_price, 2) }}
                    </span>
                </div>
            </div>
            
            @if(is_null($reservation->final_price))
                <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200 text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Final pricing will be set during the approval process.
                </div>
            @endif
        </div>
    </div>

    @if(!empty($reservation->equipment_details) || !empty($reservation->custom_equipment_requests))
    <div class="tab-content" id="equipment-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                    <i class="fas fa-toolbox text-orange-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Equipment & Resources</h3>
            </div>
            
            @if(!empty($reservation->equipment_details))
            <!-- Standard Equipment Section -->
            <div class="mb-6">
                <div class="flex items-center mb-3">
                    <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center mr-2">
                        <i class="fas fa-toolbox text-orange-600 text-xs"></i>
                    </div>
                    <h4 class="text-md font-semibold text-gray-800">Standard Equipment</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach((array)$reservation->equipment_details as $eq)
                        @php 
                            $name = is_array($eq) ? ($eq['name'] ?? 'Item') : (string)$eq; 
                            $qty = is_array($eq) ? ($eq['quantity'] ?? 1) : 1; 
                        @endphp
                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-toolbox text-orange-600 text-sm"></i>
                                    </div>
                                    <span class="text-gray-800 font-medium text-sm">{{ $name }}</span>
                                </div>
                                <span class="text-orange-600 bg-white px-2 py-1 rounded-full border border-orange-200 text-xs font-medium">× {{ $qty }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if(!empty($reservation->custom_equipment_requests))
            <!-- Custom Equipment Requests Section -->
            <div class="{{ !empty($reservation->equipment_details) ? 'pt-6 border-t border-gray-200' : '' }}">
                <div class="flex items-center mb-3">
                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                        <i class="fas fa-plus-circle text-blue-600 text-xs"></i>
                    </div>
                    <h4 class="text-md font-semibold text-gray-800">Custom Equipment Requests</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    @foreach($reservation->custom_equipment_requests as $customEquipment)
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-wrench text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="text-gray-800 font-medium text-sm">{{ $customEquipment['name'] ?? 'Custom Equipment' }}</span>
                                        <div class="text-xs text-blue-600 mt-1">Custom Request</div>
                                    </div>
                                </div>
                                <span class="text-blue-600 bg-white px-2 py-1 rounded-full border border-blue-200 text-xs font-medium">× {{ $customEquipment['quantity'] ?? 1 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                            <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                        </div>
                        <div class="text-sm text-blue-800">
                            <strong>Note:</strong> Custom equipment requests are subject to availability and admin approval. Additional charges may apply.
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(!empty($reservation->activity_grid))
    <div class="tab-content" id="attachments-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-paperclip text-blue-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Attachments</h3>
            </div>
            
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" 
                   class="flex items-center justify-between hover:bg-blue-100 p-3 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                        <div>
                            <span class="text-gray-800 font-semibold text-sm">Activity Grid</span>
                            <p class="text-blue-600 text-xs mt-1">Click to view or download</p>
                        </div>
                    </div>
                    <div class="text-blue-500">
                        <i class="fas fa-download"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Rating Tab -->
    @if($reservation->status === 'completed')
    <div class="tab-content" id="rating-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 detail-card">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Reservation Rating</h3>
            </div>
            
            @if($reservation->hasUserRated(auth()->id()))
                <!-- Show existing rating -->
                @php $userRating = $reservation->getUserRating(auth()->id()); @endphp
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-yellow-800">Your Rating</h4>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $userRating->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                            <span class="ml-2 text-sm font-medium text-yellow-700">{{ $userRating->rating }}/5</span>
                        </div>
                    </div>
                    @if($userRating->comment)
                        <div class="mt-3">
                            <p class="text-sm text-yellow-700 bg-white p-3 rounded border border-yellow-200">
                                <i class="fas fa-quote-left text-yellow-500 mr-2"></i>
                                {{ $userRating->comment }}
                            </p>
                        </div>
                    @endif
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-clock mr-1"></i>
                        Rated on {{ $userRating->created_at->format('M d, Y g:i A') }}
                    </p>
                </div>
            @else
                <!-- Show rating form -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-gray-700 mb-4">How was your experience with this reservation?</p>
                    <button onclick="openRatingModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" 
                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center">
                        <i class="fas fa-star mr-2"></i>
                        Rate This Reservation
                    </button>
                </div>
            @endif
            
            <!-- Show average rating if there are ratings -->
            @if($reservation->total_ratings > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-800">Overall Rating</h4>
                            <p class="text-sm text-gray-600">{{ $reservation->total_ratings }} rating{{ $reservation->total_ratings > 1 ? 's' : '' }}</p>
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($reservation->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ number_format($reservation->average_rating, 1) }}/5</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-maroon-600');
                    btn.classList.add('text-gray-500');
                });
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active', 'text-maroon-600');
                button.classList.remove('text-gray-500');
                
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-content`).classList.add('active');
            });
        });
        
        
        // Simple progress bar animation
        setTimeout(() => {
            const progressBar = document.querySelector('.timeline-content .absolute.left-4.top-1.w-0\\.5.rounded-full:not(.bg-gray-300)');
            if (progressBar) {
                progressBar.style.transition = 'height 0.5s ease';
            }
        }, 100);
    });
</script>

<!-- Rating Modal -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins animate-fadeIn">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Rate Reservation
                    </h3>
                    <button onclick="closeRatingModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2" id="ratingEventTitle"></h4>
                    <p class="text-sm text-gray-600">How was your experience with this reservation?</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Your Rating</label>
                    <div class="flex items-center space-x-2" id="starRating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-500 mt-2" id="ratingText">Click a star to rate</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
                    <textarea id="ratingComment" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Share your experience..."></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeRatingModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button onclick="submitRating()" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-star mr-2"></i>Submit Rating
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentRating = 0;
    let currentReservationId = null;

    function openRatingModal(reservationId, eventTitle) {
        currentReservationId = reservationId;
        document.getElementById('ratingEventTitle').textContent = eventTitle;
        document.getElementById('ratingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Reset form
        currentRating = 0;
        document.getElementById('ratingComment').value = '';
        updateStarDisplay();
    }

    function closeRatingModal() {
        document.getElementById('ratingModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentReservationId = null;
    }

    function setRating(rating) {
        currentRating = rating;
        updateStarDisplay();
    }

    function updateStarDisplay() {
        const stars = document.querySelectorAll('.star-btn');
        const ratingText = document.getElementById('ratingText');
        
        stars.forEach((star, index) => {
            if (index < currentRating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        
        const ratingTexts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        ratingText.textContent = currentRating > 0 ? ratingTexts[currentRating] : 'Click a star to rate';
    }

    function submitRating() {
        if (currentRating === 0) {
            alert('Please select a rating before submitting.');
            return;
        }

        const comment = document.getElementById('ratingComment').value;
        
        fetch(`/user/reservations/${currentReservationId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                rating: currentRating,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Rating submitted successfully!', 'success');
                closeRatingModal();
                // Reload the page to show the new rating
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Error submitting rating', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error submitting rating. Please try again.', 'error');
        });
    }

    function showNotification(message, type) {
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
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 5000);
    }

    // Close modal when clicking outside
    document.getElementById('ratingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRatingModal();
        }
    });
</script>
@endsection