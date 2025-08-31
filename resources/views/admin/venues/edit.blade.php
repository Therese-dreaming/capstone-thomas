@extends('layouts.admin')

@section('title', 'Edit Venue')
@section('page-title', 'Edit Venue')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Edit Venue Details</h3>
            <p class="text-gray-600 text-sm">Update the venue information</p>
        </div>

        <form action="{{ route('admin.venues.update', $venue->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Venue Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $venue->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('name') border-red-500 @enderror"
                        placeholder="Enter venue name"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacity <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="capacity" 
                        name="capacity" 
                        value="{{ old('capacity', $venue->capacity) }}"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('capacity') border-red-500 @enderror"
                        placeholder="Maximum number of people"
                        required
                    >
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price per Hour -->
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 mb-2">
                        Price per Hour (₱) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="price_per_hour" 
                        name="price_per_hour" 
                        value="{{ old('price_per_hour', $venue->price_per_hour) }}"
                        min="0.01"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('price_per_hour') border-red-500 @enderror"
                        placeholder="0.00"
                        required
                    >
                    @error('price_per_hour')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('status') border-red-500 @enderror"
                        required
                    >
                        <option value="">Select status</option>
                        <option value="active" {{ old('status', $venue->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $venue->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Available -->
                <div>
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="is_available" 
                            name="is_available" 
                            value="1"
                            {{ old('is_available', $venue->is_available) ? 'checked' : '' }}
                            class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded"
                        >
                        <label for="is_available" class="ml-2 block text-sm text-gray-700">
                            Available for reservations
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Check this if the venue can be reserved by users</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                        placeholder="Enter venue description (optional)"
                    >{{ old('description', $venue->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Available Equipment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Available Equipment
                    </label>
                    <div id="equipment-container" class="space-y-3">
                        @if($venue->available_equipment)
                            @foreach($venue->available_equipment as $index => $equipment)
                                <div class="flex space-x-3 p-3 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <input type="text" name="available_equipment[{{ $index }}][name]" 
                                               value="{{ $equipment['name'] }}" placeholder="Equipment name" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" name="available_equipment[{{ $index }}][category]" 
                                               value="{{ $equipment['category'] }}" placeholder="Category (e.g., Audio, Visual, Furniture)" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" name="available_equipment[{{ $index }}][quantity]" 
                                               value="{{ $equipment['quantity'] }}" placeholder="Qty" min="1" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
                                    </div>
                                    <button type="button" class="px-2 py-2 text-red-600 hover:bg-red-50 rounded" onclick="this.parentElement.remove()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-equipment" class="mt-2 px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded border hover:bg-gray-200 transition">
                        <i class="fas fa-plus mr-1"></i>Add Equipment
                    </button>
                    @error('available_equipment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-opacity-80 transition">
                    <i class="fas fa-save mr-2"></i>Update Venue
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let equipmentIndex = {{ $venue->available_equipment ? count($venue->available_equipment) : 0 }};

document.getElementById('add-equipment').addEventListener('click', function() {
    const container = document.getElementById('equipment-container');
    const equipmentDiv = document.createElement('div');
    equipmentDiv.className = 'flex space-x-3 p-3 border border-gray-200 rounded-lg';
    equipmentDiv.innerHTML = `
        <div class="flex-1">
            <input type="text" name="available_equipment[${equipmentIndex}][name]" placeholder="Equipment name" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
        </div>
        <div class="flex-1">
            <input type="text" name="available_equipment[${equipmentIndex}][category]" placeholder="Category (e.g., Audio, Visual, Furniture)" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
        </div>
        <div class="w-24">
            <input type="number" name="available_equipment[${equipmentIndex}][quantity]" placeholder="Qty" min="1" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon" required>
        </div>
        <button type="button" class="px-2 py-2 text-red-600 hover:bg-red-50 rounded" onclick="this.parentElement.remove()">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(equipmentDiv);
    equipmentIndex++;
});
</script>
@endsection
