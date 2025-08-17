@extends('layouts.user')

@section('title', 'My Reservations')
@section('page-title', 'My Reservations')

@section('header-actions')
<div class="flex space-x-3">
    <a href="#" class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center space-x-2 shadow-sm border border-gray-200">
        <i class="fas fa-filter text-maroon"></i>
        <span>Filter</span>
    </a>
    <a href="{{ route('user.reservations.calendar') }}" class="px-4 py-2 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 flex items-center space-x-2 shadow-md">
        <i class="fas fa-calendar-alt text-lg"></i>
        <span>View Calendar</span>
    </a>
</div>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
    .font-montserrat {
        font-family: 'Montserrat', sans-serif;
    }
    .reservation-card {
        transition: all 0.3s ease;
    }
    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
        border: 1px solid #F59E0B;
    }
    .status-approved {
        background-color: #D1FAE5;
        color: #065F46;
        border: 1px solid #10B981;
    }
    .status-rejected {
        background-color: #FEE2E2;
        color: #991B1B;
        border: 1px solid #EF4444;
    }
</style>

<div class="space-y-8 font-poppins">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-calendar-check text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $userReservations->total() }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ App\Models\Reservation::where('user_id', Auth::id())->where('status', 'approved')->count() }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ App\Models\Reservation::where('user_id', Auth::id())->where('status', 'pending')->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-bookmark text-maroon mr-3"></i>
                    My Reservations
                </h2>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" placeholder="Search reservations..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <button class="px-6 py-3 text-gray-700 hover:text-maroon transition-colors border-b-2 border-maroon font-medium">
                All Reservations
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Pending
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Approved
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Rejected
            </button>
        </div>
        
        <div class="p-6">
            @if($userReservations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($userReservations as $reservation)
                        <div class="reservation-card border border-gray-200 rounded-xl p-5 bg-white relative overflow-hidden">
                            <!-- Status Badge -->
                            @switch($reservation->status)
                                @case('pending')
                                    <div class="status-badge status-pending">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </div>
                                    @break
                                @case('approved')
                                    <div class="status-badge status-approved">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </div>
                                    @break
                                @case('rejected')
                                    <div class="status-badge status-rejected">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </div>
                                    @break
                            @endswitch
                            
                            <div class="flex flex-col h-full">
                                <div class="mb-4">
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $reservation->event_title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 mt-2">
                                        <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                                        {{ $reservation->venue->name ?? 'No venue' }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mt-1">
                                        <i class="far fa-clock text-maroon mr-2"></i>
                                        {{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }} - 
                                        {{ $reservation->end_date ? $reservation->end_date->format('g:i A') : 'No end time' }}
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                                    <p class="text-sm text-gray-600">{{ Str::limit($reservation->purpose, 100) }}</p>
                                </div>
                                

                                
                                <!-- Action Buttons -->
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
                                    <a href="#" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($reservation->status == 'pending')
                                        <a href="#" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors" title="Edit Reservation">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Cancel Reservation">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex justify-center">
                    {{ $userReservations->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600 mb-4">You don't have any reservations yet</p>
                    <a href="{{ route('user.reservations.calendar') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md">
                        <i class="fas fa-calendar-plus mr-2"></i> Make Your First Reservation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabs = document.querySelectorAll('.flex.border-b.border-gray-200 button');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-b-2', 'border-maroon', 'font-medium');
                    t.classList.add('text-gray-500');
                });
                
                // Add active class to clicked tab
                this.classList.add('border-b-2', 'border-maroon', 'font-medium');
                this.classList.remove('text-gray-500');
                this.classList.add('text-gray-700');
                
                // Here you would typically filter the reservations based on the selected tab
                // For now, we'll just log which tab was clicked
                console.log('Tab clicked:', this.textContent.trim());
            });
        });
    });
</script>
@endsection