@extends('layouts.mhadel')

@section('title', 'Venue Details')
@section('page-title', 'Venue Details')
@section('page-subtitle', 'Complete information about {{ $venue->name }}')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@section('header-actions')
    <div class="flex items-center space-x-3">
        <a href="{{ route('mhadel.venues.edit', $venue) }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center shadow-lg font-bold">
            <i class="fas fa-edit mr-2"></i>Edit Venue
        </a>
        <a href="{{ route('mhadel.venues.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all duration-300 flex items-center shadow-lg font-bold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Venues
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <!-- Venue Header Card -->
    <div class="bg-maroon rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <i class="text-red-800 fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold font-poppins mb-2">{{ $venue->name }}</h1>
                            <p class="text-lg text-red-100 font-inter">{{ $venue->description ?: 'A professional venue for your events' }}</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="text-red-800 bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-center">
                                <div class="text-2xl font-bold font-poppins mb-1">{{ number_format($venue->capacity) }}</div>
                                <div class="text-sm text-red-800 font-medium font-inter">Capacity</div>
                            </div>
                        </div>
                        <div class="text-red-800 bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-center">
                                <div class="text-2xl font-bold font-poppins mb-1">₱{{ number_format($venue->price_per_hour) }}</div>
                                <div class="text-sm text-red-800 font-medium font-inter">Per Hour</div>
                            </div>
                        </div>
                        <div class="text-red-800 bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-center">
                                <div class="text-2xl font-bold font-poppins mb-1">{{ $venue->available_equipment ? count($venue->available_equipment) : 0 }}</div>
                                <div class="text-sm text-red-800 font-medium font-inter">Equipment Items</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status and Availability Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-maroon rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 font-poppins">Venue Status</h3>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 font-inter">Operational Status</span>
                    <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full {{ $venue->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        <i class="fas {{ $venue->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                        {{ ucfirst($venue->status) }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 font-inter">Reservation Status</span>
                    <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full {{ $venue->is_available ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                        <i class="fas {{ $venue->is_available ? 'fa-check' : 'fa-clock' }} mr-2"></i>
                        {{ $venue->is_available ? 'Available' : 'Busy' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Timestamps Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
            <div class="flex items-center mb-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 font-poppins">Timeline</h3>
            </div>
            
            <div class="space-y-3">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-800 font-inter mb-1">Created</div>
                    <div class="text-base font-bold text-blue-900 font-poppins">{{ $venue->created_at->format('F d, Y') }}</div>
                    <div class="text-sm text-blue-600 font-inter">{{ $venue->created_at->format('g:i A') }}</div>
                </div>
                
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-800 font-inter mb-1">Last Updated</div>
                    <div class="text-base font-bold text-green-900 font-poppins">{{ $venue->updated_at->format('F d, Y') }}</div>
                    <div class="text-sm text-green-600 font-inter">{{ $venue->updated_at->format('g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Information Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
        <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-tools text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 font-poppins">Available Equipment</h3>
        </div>
        
        @if($venue->available_equipment && count($venue->available_equipment) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($venue->available_equipment as $equipment)
                    <div class="bg-green-50 rounded-lg border border-green-200 p-3 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h5 class="font-bold text-green-900 font-poppins mb-1">{{ $equipment['name'] }}</h5>
                                <p class="text-sm text-green-700 font-inter mb-2">{{ $equipment['category'] }}</p>
                            </div>
                            <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-tools text-white text-xs"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="inline-flex px-2 py-1 text-sm font-bold bg-green-600 text-white rounded-full">
                                Qty: {{ $equipment['quantity'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-tools text-2xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-600 font-poppins mb-2">No Equipment Available</h4>
                <p class="text-gray-500 font-inter">This venue doesn't have any equipment listed yet.</p>
                <a href="{{ route('mhadel.venues.edit', $venue) }}" 
                   class="inline-flex items-center px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 mt-3 font-medium">
                    <i class="fas fa-plus mr-2"></i>Add Equipment
                </a>
            </div>
        @endif
    </div>

    <!-- Detailed Information Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
        <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-file-alt text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 font-poppins">Detailed Information</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="text-sm font-medium text-purple-800 font-inter mb-1">Venue Name</div>
                    <div class="text-base font-bold text-purple-900 font-poppins">{{ $venue->name }}</div>
                </div>
                
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-sm font-medium text-blue-800 font-inter mb-1">Capacity</div>
                    <div class="text-base font-bold text-blue-900 font-poppins">{{ number_format($venue->capacity) }} people</div>
                </div>
                
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-sm font-medium text-green-800 font-inter mb-1">Price per Hour</div>
                    <div class="text-base font-bold text-green-900 font-poppins">₱{{ number_format($venue->price_per_hour, 2) }}</div>
                </div>
            </div>
            
            <div class="space-y-3">
                @if($venue->description)
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="text-sm font-medium text-amber-800 font-inter mb-1">Description</div>
                        <div class="text-sm text-amber-900 font-inter leading-relaxed">{{ $venue->description }}</div>
                    </div>
                @endif
                
                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="text-sm font-medium text-indigo-800 font-inter mb-1">Venue ID</div>
                    <div class="text-sm text-indigo-900 font-inter font-mono">#{{ $venue->id }}</div>
                </div>
                
                <div class="p-3 bg-teal-50 rounded-lg border border-teal-200">
                    <div class="text-sm font-medium text-teal-800 font-inter mb-1">Database Status</div>
                    <div class="text-sm text-teal-900 font-inter">Active Record</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.font-inter {
    font-family: 'Inter', sans-serif;
}

.font-poppins {
    font-family: 'Poppins', sans-serif;
}

/* Card animations */
.bg-white {
    transition: all 0.3s ease;
}

.bg-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Equipment card hover effects */
.equipment-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Smooth transitions */
* {
    transition: all 0.3s ease;
}

/* Enhanced shadows */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
@endsection
