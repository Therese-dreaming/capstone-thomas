@extends('layouts.user')

@section('title', 'My Reservations')
@section('page-title', 'My Reservations')

@section('header-actions')
<div class="flex items-center space-x-3 relative">
    <button id="filterToggle" type="button" class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors flex items-center space-x-2 shadow-sm border border-gray-200">
        <i class="fas fa-filter text-gray-600"></i>
        <span>Filter</span>
    </button>
    <a href="{{ route('user.reservations.calendar') }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors flex items-center space-x-2 shadow-sm">
        <i class="fas fa-calendar-alt text-lg"></i>
        <span>View Calendar</span>
    </a>

    <form id="filterPanel" action="{{ route('user.reservations.index') }}" method="GET" class="absolute top-12 left-0 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-4 hidden">
        <label for="status" class="block text-sm text-gray-700 mb-2">Status</label>
        <select id="status" name="status" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            <option value="all" {{ ($currentStatus ?? request('status','all'))=='all' ? 'selected' : '' }}>All</option>
            <option value="pending" {{ ($currentStatus ?? request('status'))=='pending' ? 'selected' : '' }}>Pending / In Review</option>
            <option value="approved" {{ ($currentStatus ?? request('status'))=='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ ($currentStatus ?? request('status'))=='rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <div class="mt-3 flex justify-end space-x-2">
            <a href="{{ route('user.reservations.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Reset</a>
            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-sm rounded">Apply</button>
        </div>
    </form>
</div>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-montserrat { font-family: 'Montserrat', sans-serif; }
    .reservation-card { transition: all 0.3s ease; }
    .reservation-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); }
    .status-badge { position: absolute; top: 12px; right: 12px; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
    .status-pending { background-color: #FEF3C7; color: #92400E; border: 1px solid #F59E0B; }
    .status-approved { background-color: #D1FAE5; color: #065F46; border: 1px solid #10B981; }
    .status-rejected { background-color: #FEE2E2; color: #991B1B; border: 1px solid #EF4444; }
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
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['all'] ?? $userReservations->total() }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['approved'] ?? 0 }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $counts['pending'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-bookmark mr-3"></i>
                    My Reservations
                </h2>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <form method="GET" action="{{ route('user.reservations.index') }}">
                            <input type="hidden" name="status" value="{{ $currentStatus ?? 'all' }}" />
                            <input type="text" name="q" placeholder="Search reservations..." value="{{ request('q') }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 transition-colors">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <a href="{{ route('user.reservations.index', ['status' => 'all'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ ($currentStatus ?? 'all')=='all' ? 'border-gray-800 text-gray-800 font-medium' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">All</a>
            <a href="{{ route('user.reservations.index', ['status' => 'pending'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ ($currentStatus ?? '')=='pending' ? 'border-gray-800 text-gray-800 font-medium' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Pending</a>
            <a href="{{ route('user.reservations.index', ['status' => 'approved'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ ($currentStatus ?? '')=='approved' ? 'border-gray-800 text-gray-800 font-medium' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Approved</a>
            <a href="{{ route('user.reservations.index', ['status' => 'rejected'] + request()->except('page','status')) }}" class="px-6 py-3 transition-colors border-b-2 {{ ($currentStatus ?? '')=='rejected' ? 'border-gray-800 text-gray-800 font-medium' : 'text-gray-500 hover:text-gray-800 border-transparent' }}">Rejected</a>
        </div>
        
        <div class="p-6">
            @if($userReservations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($userReservations as $reservation)
                        <div class="reservation-card border border-gray-200 rounded-xl p-5 bg-white relative overflow-hidden">
                            <!-- Status Badge -->
                            @switch($reservation->status)
                                @case('pending')
                                @case('approved_IOSA')
                                @case('approved_mhadel')
                                    <div class="status-badge status-pending">
                                        <i class="fas fa-clock mr-1"></i> In Review
                                    </div>
                                    @break
                                @case('approved')
                                @case('approved_OTP')
                                    <div class="status-badge status-approved">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </div>
                                    @break
                                @case('rejected')
                                @case('rejected_OTP')
                                    <div class="status-badge status-rejected">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </div>
                                    @break
                            @endswitch
                            
                            <div class="flex flex-col h-full">
                                <div class="mb-4">
                                    <h3 class="font-bold text-gray-800 text-lg">{{ $reservation->event_title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 mt-2">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        {{ $reservation->venue->name ?? 'No venue' }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mt-1">
                                        <i class="far fa-clock mr-2"></i>
                                        {{ $reservation->start_date ? $reservation->start_date->format('M d, Y g:i A') : 'No date' }} - 
                                        {{ $reservation->end_date ? $reservation->end_date->format('g:i A') : 'No end time' }}
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                                    <p class="text-sm text-gray-600">{{ Str::limit($reservation->purpose, 100) }}</p>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
                                    <a href="{{ route('user.reservations.show', $reservation->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($reservation->status, ['pending','approved_IOSA','approved_mhadel']))
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
                    {{ $userReservations->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600 mb-4">No reservations found for this filter</p>
                    <a href="{{ route('user.reservations.calendar') }}" class="inline-block px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-md">
                        <i class="fas fa-calendar-plus mr-2"></i> Make a Reservation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle filter panel
        const toggle = document.getElementById('filterToggle');
        const panel = document.getElementById('filterPanel');
        if (toggle && panel) {
            toggle.addEventListener('click', function(){
                panel.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e){
                if (!panel.contains(e.target) && !toggle.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection