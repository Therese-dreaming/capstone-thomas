@extends('layouts.drjavier')

@section('title', 'Reservation Details - Dr. Javier')
@section('page-title', 'Reservation Details')
@section('page-subtitle', 'Final approval authority for reservations')

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
</style>

@section('header-actions')
    <a href="{{ route('drjavier.reservations.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Back to Reservations
    </a>
@endsection

@section('content')
<div class="space-y-6 font-inter">
    <!-- Status Banner -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 font-poppins">{{ $reservation->event_title }}</h1>
                    <p class="text-gray-600">Submitted by {{ $reservation->user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($reservation->status === 'mhadel_approved')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-medium">Pending Dr. Javier's Final Review</span>
                    @elseif($reservation->status === 'dr_javier_approved')
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-medium">Approved by Dr. Javier (OTP)</span>
                    @elseif($reservation->status === 'dr_javier_rejected')
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium">Rejected by Dr. Javier (OTP)</span>
                    @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full font-medium">{{ ucfirst($reservation->status) }}</span>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-calendar-alt text-maroon mr-2"></i>
                        Event Information
                    </h2>
                </div>
                <div class="p-6">
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
                            <p class="text-gray-900">{{ $reservation->start_date->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                            <p class="text-gray-900">{{ $reservation->end_date->format('M d, Y g:i A') }}</p>
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

            <!-- Equipment Required -->
            @if($reservation->equipment && $reservation->equipment->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-tools text-maroon mr-2"></i>
                            Equipment Required
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($reservation->equipment as $equipment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-tools text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $equipment->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $equipment->description }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-gray-700">Qty: {{ $equipment->pivot->quantity }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Grid -->
            @if($reservation->activity_grid)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                                <i class="fas fa-table text-maroon mr-2"></i>
                                Activity Grid
                            </h2>
                            <a href="{{ route('drjavier.reservations.download-activity-grid', $reservation->id) }}" 
                               class="btn-dark-blue px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->activity_grid }}</pre>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($reservation->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-sticky-note text-maroon mr-2"></i>
                            Notes & History
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono">{{ $reservation->notes }}</pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - User Info & Actions -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-user text-maroon mr-2"></i>
                        Requester Information
                    </h2>
                </div>
                <div class="p-6">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-building text-maroon mr-2"></i>
                            Venue Details
                        </h2>
                    </div>
                    <div class="p-6">
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

            <!-- Quick Actions -->
            @if($reservation->status === 'mhadel_approved')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 animate-fadeIn">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 font-poppins flex items-center">
                            <i class="fas fa-bolt text-maroon mr-2"></i>
                            Quick Actions
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="w-full btn-dark-green px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Approve Reservation
                            </button>
                            <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="w-full btn-dark-red px-4 py-3 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i>Reject Reservation
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            This is the final approval. The reservation will be confirmed.
                        </p>
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
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold text-gray-800" id="approveEventTitle"></h4>
                    <p class="text-sm text-gray-600 mt-1">This is the final approval. The reservation will be confirmed.</p>
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