@extends('layouts.mhadel')

@section('title', 'Ms. Mhadel Dashboard')
@section('page-title', 'Ms. Mhadel Dashboard')
@section('page-subtitle', 'Second Level Approval - Reservation Management')

@section('content')
<div class="space-y-6 font-inter">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-maroon to-red-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold font-poppins mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                <p class="text-white text-opacity-90">You are the second level of approval for reservations. Review IOSA-approved reservations and make final decisions.</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold">{{ $stats['pending'] }}</div>
                <div class="text-white text-opacity-80">Pending Review</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending Review</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
                <p class="text-xs text-gray-500">IOSA Approved</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved Today</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['approved_today'] }}</h3>
                <p class="text-xs text-gray-500">Forwarded to Dr. Javier</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Rejected Today</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['rejected_today'] }}</h3>
                <p class="text-xs text-gray-500">Final Rejection</p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total This Month</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_month'] }}</h3>
                <p class="text-xs text-gray-500">All Reservations</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                <i class="fas fa-bolt text-maroon mr-3"></i>
                Quick Actions
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('mhadel.reservations.index') }}" class="bg-red-800 text-white p-6 rounded-xl hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-check text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold">Review Reservations</h3>
                            <p class="text-white text-opacity-80 text-sm">View and manage pending reservations</p>
                        </div>
                    </div>
                </a>
                
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-3xl text-gray-400 mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Analytics</h3>
                            <p class="text-gray-600 text-sm">View approval statistics</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-cog text-3xl text-gray-400 mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Settings</h3>
                            <p class="text-gray-600 text-sm">Manage your preferences</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                    <i class="fas fa-history text-maroon mr-3"></i>
                    Recent IOSA Approved Reservations
                </h2>
                <a href="{{ route('mhadel.reservations.index') }}" class="text-maroon hover:text-red-700 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($recent_reservations->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_reservations as $reservation)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium mr-3">
                                        Pending Review
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $reservation->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-800">{{ $reservation->event_title }}</h3>
                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                    <i class="fas fa-user mr-2 text-maroon"></i>
                                    <span>{{ $reservation->user->name }}</span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-2 text-maroon"></i>
                                    <span>{{ $reservation->start_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('mhadel.reservations.show', $reservation->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="openApproveModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="p-2 btn-dark-green rounded-lg transition-colors" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="openRejectModal({{ $reservation->id }}, '{{ $reservation->event_title }}')" class="p-2 btn-dark-red rounded-lg transition-colors" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-check text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No Pending Reservations</h3>
                    <p class="text-gray-500">All IOSA approved reservations have been reviewed.</p>
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
                    <p class="text-sm text-gray-600 mt-1">This reservation will be forwarded to Dr. Javier for final approval.</p>
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
        document.getElementById('approveForm').action = `/mhadel/reservations/${reservationId}/approve`;
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