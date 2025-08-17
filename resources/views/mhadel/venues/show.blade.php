@extends('layouts.mhadel')

@section('title', 'Venue Details')
@section('page-title', 'Venue Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $venue->name }}</h3>
                    <p class="text-gray-600 text-sm">Venue information and details</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('mhadel.venues.edit', $venue) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('mhadel.venues.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Basic Information</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Venue Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $venue->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacity</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $venue->capacity }} people</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price per Hour</label>
                        <p class="mt-1 text-sm text-gray-900">₱{{ number_format($venue->price_per_hour, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venue->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($venue->status) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Availability</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venue->is_available ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $venue->is_available ? 'Available for Reservations' : 'Not Available for Reservations' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $venue->description ?: 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Equipment Information -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Available Equipment</h4>
                    
                    @if($venue->available_equipment && count($venue->available_equipment) > 0)
                        <div class="space-y-3">
                            @foreach($venue->available_equipment as $equipment)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">{{ $equipment['name'] }}</h5>
                                            <p class="text-sm text-gray-600">{{ $equipment['category'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                                Qty: {{ $equipment['quantity'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-tools text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No equipment available for this venue</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timestamps -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <label class="block font-medium text-gray-700">Created</label>
                        <p>{{ $venue->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">Last Updated</label>
                        <p>{{ $venue->updated_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
