@extends('layouts.mhadel')

@section('title', 'Venues Management')
@section('page-title', 'Venues Management')

@section('header-actions')
    <a href="{{ route('mhadel.venues.create') }}" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-opacity-80 transition">
        <i class="fas fa-plus mr-2"></i>Add New Venue
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Venues</h3>
        <p class="text-gray-600 text-sm">Manage your venue inventory</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Hour</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($venues as $venue)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $venue->name }}</div>
                        @if($venue->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($venue->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $venue->capacity }} people</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">₱{{ number_format($venue->price_per_hour, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($venue->available_equipment && count($venue->available_equipment) > 0)
                            <div class="text-sm text-gray-900">{{ count($venue->available_equipment) }} items</div>
                            <div class="text-xs text-gray-500">
                                @foreach(array_slice($venue->available_equipment, 0, 2) as $equipment)
                                    {{ $equipment['name'] }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                @if(count($venue->available_equipment) > 2)
                                    +{{ count($venue->available_equipment) - 2 }} more
                                @endif
                            </div>
                        @else
                            <div class="text-sm text-gray-500">No equipment</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venue->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($venue->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venue->is_available ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $venue->is_available ? 'Available' : 'Not Available' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $venue->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('mhadel.venues.show', $venue) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('mhadel.venues.edit', $venue) }}" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mhadel.venues.destroy', $venue) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this venue?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-building text-4xl text-gray-300 mb-2"></i>
                            <p>No venues found</p>
                            <a href="{{ route('mhadel.venues.create') }}" class="mt-2 text-maroon hover:underline">Add your first venue</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($venues->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $venues->links() }}
    </div>
    @endif
</div>
@endsection
