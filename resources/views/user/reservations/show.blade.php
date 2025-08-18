@extends('layouts.user')

@section('title', 'Reservation Details')
@section('page-title', 'Reservation Details')

@section('styles')
<style>
    .status-badge {
        transition: all 0.3s ease;
    }
    .status-badge:hover {
        transform: translateY(-2px);
    }
    .detail-card {
        transition: all 0.3s ease;
    }
    .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .timeline-dot {
        transition: all 0.3s ease;
    }
    .timeline-dot:hover {
        transform: scale(1.2);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('header-actions')
<div class="flex space-x-3">
    <a href="{{ route('user.reservations.index') }}" class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center space-x-2 shadow-sm border border-gray-200">
        <i class="fas fa-arrow-left"></i>
        <span>Back to List</span>
    </a>
    <a href="{{ route('user.reservations.calendar') }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors flex items-center space-x-2 shadow-sm">
        <i class="fas fa-calendar-alt"></i>
        <span>Calendar</span>
    </a>
    @if(!empty($reservation->activity_grid))
        <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center space-x-2 shadow-sm border border-gray-200">
            <i class="fas fa-file-download"></i>
            <span>Activity Grid</span>
        </a>
    @endif
    <button id="printButton" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2 shadow-sm">
        <i class="fas fa-print"></i>
        <span>Print</span>
    </button>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @php
        $status = $reservation->status;
        $badge = ['class' => 'bg-yellow-100 text-yellow-700 border border-yellow-300', 'icon' => 'fa-clock', 'label' => 'In Review'];
        if (in_array($status, ['approved','approved_OTP'])) { $badge = ['class' => 'bg-green-100 text-green-700 border border-green-300', 'icon' => 'fa-check-circle', 'label' => 'Approved']; }
        if (in_array($status, ['rejected','rejected_OTP'])) { $badge = ['class' => 'bg-red-100 text-red-700 border border-red-300', 'icon' => 'fa-times-circle', 'label' => 'Rejected']; }
        // Use only the persisted DB value for duration to avoid inaccurate display when NULL
        $duration = $reservation->duration_hours;
    @endphp

    <!-- Overview Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $reservation->event_title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Requested on {{ $reservation->created_at?->format('M d, Y g:i A') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2 md:mt-0 status-badge {{ $badge['class'] }}">
                <i class="fas {{ $badge['icon'] }} mr-2"></i>{{ $badge['label'] }}
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors detail-card">
                <i class="fas fa-map-marker-alt text-gray-600 mr-3"></i>
                <div>
                    <div class="text-xs text-gray-500">Venue</div>
                    <div class="text-gray-800 font-medium">{{ $reservation->venue->name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors detail-card">
                <i class="far fa-calendar text-gray-600 mr-3"></i>
                <div>
                    <div class="text-xs text-gray-500">Date</div>
                    <div class="text-gray-800 font-medium">{{ $reservation->start_date?->format('M d, Y') }}</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors detail-card">
                <i class="far fa-clock text-gray-600 mr-3"></i>
                <div>
                    <div class="text-xs text-gray-500">Time</div>
                    <div class="text-gray-800 font-medium">{{ $reservation->start_date?->format('g:i A') }} – {{ $reservation->end_date?->format('g:i A') }}</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors detail-card">
                <i class="fas fa-hourglass-half text-gray-600 mr-3"></i>
                <div>
                    <div class="text-xs text-gray-500">Duration</div>
                    <div class="text-gray-800 font-medium">{{ $duration ? $duration . ' hr' . ($duration > 1 ? 's' : '') : '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button class="tab-button active flex-1 py-4 px-6 text-center font-medium border-b-2 border-indigo-500 text-indigo-600 focus:outline-none" data-tab="details">
                <i class="fas fa-info-circle mr-2"></i>Details
            </button>
            <button class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="timeline">
                <i class="fas fa-history mr-2"></i>Timeline
            </button>
            <button class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="pricing">
                <i class="fas fa-money-bill-wave mr-2"></i>Pricing
            </button>
            @if(!empty($reservation->equipment_details))
            <button class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="equipment">
                <i class="fas fa-toolbox mr-2"></i>Equipment
            </button>
            @endif
            @if(!empty($reservation->activity_grid))
            <button class="tab-button flex-1 py-4 px-6 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="attachments">
                <i class="fas fa-paperclip mr-2"></i>Attachments
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content active" id="details-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-bullseye text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Purpose</div>
                        <div class="text-gray-800 mt-1">{{ $reservation->purpose }}</div>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Capacity</div>
                        <div class="text-gray-800 mt-1">{{ $reservation->capacity }}</div>
                    </div>
                </div>
            </div>

            @if(!empty($reservation->notes))
                <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-sticky-note text-gray-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-600">Notes</div>
                            <div class="mt-1 text-gray-800">{{ $reservation->notes }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="tab-content" id="timeline-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Timeline</h3>
            @php
                $steps = [
                    ['key' => 'submitted', 'label' => 'Submitted'],
                    ['key' => 'iosa', 'label' => 'IOSA Review'],
                    ['key' => 'mhadel', 'label' => 'Ms. Mhadel Review'],
                    ['key' => 'otp', 'label' => 'Final Approval (OTP)'],
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
                        $currentIndex = 3; $doneSteps = 4; break;
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

            <div class="relative pl-8">
                <div class="absolute left-3 top-1 bottom-1 w-1.5 bg-gray-200 rounded"></div>
                <div class="absolute left-3 top-1 w-1.5 rounded transition-all duration-1000" style="height: {{ $progressPercent }}%; background-color: {{ $failed ? '#EF4444' : '#10B981' }};"></div>

                @foreach($steps as $i => $step)
                    @php
                        $state = 'todo';
                        if ($failed && $i === $currentIndex) { $state = 'failed'; }
                        elseif ($i < $doneSteps) { $state = 'done'; }
                        elseif ($i === $currentIndex) { $state = 'current'; }
                    @endphp
                    <div class="relative flex items-start mb-8">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 timeline-dot
                            {{ $state === 'done' ? 'bg-green-500 border-green-500 text-white' : '' }}
                            {{ $state === 'current' ? 'bg-blue-600 border-blue-600 text-white' : '' }}
                            {{ $state === 'failed' ? 'bg-red-500 border-red-500 text-white' : '' }}
                            {{ $state === 'todo' ? 'bg-gray-300 border-gray-300 text-white' : '' }}
                        ">
                            <i class="fas {{ $state === 'done' ? 'fa-check' : ($state === 'failed' ? 'fa-times' : 'fa-circle') }} text-[10px]"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-800">{{ $step['label'] }}</div>
                            @if($i === 0 && $reservation->created_at)
                                <div class="text-xs text-gray-500">{{ $reservation->created_at->format('M d, Y g:i A') }}</div>
                            @endif
                            <div class="mt-2 text-sm text-gray-600">
                                @if($state === 'done')
                                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i> Completed</span>
                                @elseif($state === 'current')
                                    <span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i> In Progress</span>
                                @elseif($state === 'failed')
                                    <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                                @else
                                    <span class="text-gray-500"><i class="fas fa-hourglass-start mr-1"></i> Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="tab-content" id="pricing-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Summary</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600 flex items-center"><i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i>Rate per Hour</span>
                    <span class="text-gray-800 font-medium">₱{{ number_format((float)($reservation->price_per_hour ?? 0), 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600 flex items-center"><i class="fas fa-hourglass-half mr-2 text-indigo-500"></i>Duration</span>
                    <span class="text-gray-800 font-medium">{{ !is_null($duration) ? $duration . ' hr' : '—' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600 flex items-center"><i class="fas fa-calculator mr-2 text-indigo-500"></i>Base Price</span>
                    <span class="text-gray-800 font-medium">₱{{ number_format((float)($reservation->base_price ?? 0), 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-600 flex items-center"><i class="fas fa-percent mr-2 text-indigo-500"></i>Discount</span>
                    <span class="text-gray-800 font-medium">{{ is_null($reservation->discount_percentage) ? '—' : $reservation->discount_percentage . '%' }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg border border-indigo-100 mt-6">
                    <span class="text-indigo-800 font-semibold flex items-center"><i class="fas fa-tag mr-2"></i>Final Price</span>
                    <span class="text-indigo-900 font-bold text-xl">{{ is_null($reservation->final_price) ? '—' : '₱' . number_format((float)$reservation->final_price, 2) }}</span>
                </div>
            </div>
            @if(is_null($reservation->final_price))
                <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200 text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Final pricing will be set during the approval process.
                </div>
            @endif
        </div>
    </div>

    @if(!empty($reservation->equipment_details))
    <div class="tab-content" id="equipment-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Equipment</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach((array)$reservation->equipment_details as $eq)
                    @php $name = is_array($eq) ? ($eq['name'] ?? 'Item') : (string)$eq; $qty = is_array($eq) ? ($eq['quantity'] ?? 1) : 1; @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                <i class="fas fa-toolbox text-indigo-600"></i>
                            </div>
                            <span class="text-gray-800 font-medium">{{ $name }}</span>
                        </div>
                        <span class="text-gray-600 bg-white px-3 py-1 rounded-full border border-gray-200">× {{ $qty }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(!empty($reservation->activity_grid))
    <div class="tab-content" id="attachments-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 detail-card">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Attachments</h3>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" class="flex items-center justify-between hover:bg-gray-100 p-3 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                        <div>
                            <span class="text-gray-800 font-medium">Activity Grid</span>
                            <p class="text-xs text-gray-500 mt-1">Click to view or download</p>
                        </div>
                    </div>
                    <i class="fas fa-download text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Floating Action Button for Mobile -->
<div class="fixed bottom-6 right-6 md:hidden">
    <div class="relative group">
        <button id="mobileActionBtn" class="w-14 h-14 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg focus:outline-none">
            <i class="fas fa-ellipsis-h text-xl"></i>
        </button>
        <div id="mobileActionMenu" class="absolute bottom-16 right-0 bg-white rounded-lg shadow-xl border border-gray-200 w-48 hidden">
            <div class="py-2">
                <a href="{{ route('user.reservations.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                <a href="{{ route('user.reservations.calendar') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-calendar-alt mr-2"></i> Calendar
                </a>
                @if(!empty($reservation->activity_grid))
                <a href="{{ asset('storage/' . $reservation->activity_grid) }}" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-file-download mr-2"></i> Activity Grid
                </a>
                @endif
                <button id="mobilePrintBtn" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>
    </div>
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
                tabButtons.forEach(btn => btn.classList.remove('active', 'border-indigo-500', 'text-indigo-600'));
                tabButtons.forEach(btn => btn.classList.add('border-transparent', 'text-gray-500'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                button.classList.add('active', 'border-indigo-500', 'text-indigo-600');
                button.classList.remove('border-transparent', 'text-gray-500');
                
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-content`).classList.add('active');
            });
        });
        
        // Print functionality
        document.getElementById('printButton').addEventListener('click', function() {
            window.print();
        });
        
        if (document.getElementById('mobilePrintBtn')) {
            document.getElementById('mobilePrintBtn').addEventListener('click', function() {
                window.print();
            });
        }
        
        // Mobile action menu toggle
        const mobileActionBtn = document.getElementById('mobileActionBtn');
        const mobileActionMenu = document.getElementById('mobileActionMenu');
        
        if (mobileActionBtn && mobileActionMenu) {
            mobileActionBtn.addEventListener('click', function() {
                mobileActionMenu.classList.toggle('hidden');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileActionBtn.contains(event.target) && !mobileActionMenu.contains(event.target)) {
                    mobileActionMenu.classList.add('hidden');
                }
            });
        }
        
        // Add animation to timeline progress bar
        setTimeout(() => {
            const progressBar = document.querySelector('.timeline-content .absolute.left-3.top-1.w-1\\.5.rounded:not(.bg-gray-200)');
            if (progressBar) {
                progressBar.style.transition = 'height 1s ease-in-out';
            }
        }, 100);
    });
</script>
@endsection