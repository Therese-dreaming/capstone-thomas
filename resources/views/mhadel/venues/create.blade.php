@extends('layouts.mhadel')

@section('title', 'Add New Venue')
@section('page-title', 'Add New Venue')
@section('page-subtitle', 'Create a new venue for your events')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@section('header-actions')
    <div class="flex items-center space-x-3">
        <a href="{{ route('mhadel.venues.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-all duration-300 flex items-center shadow-lg font-bold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Venues
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-maroon rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 text-white">
            <div class="flex items-center">
                <div class="text-red-800 w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold font-poppins mb-2">Add New Venue</h1>
                    <p class="text-lg text-red-100 font-inter">Create a new venue for your events and reservations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <form action="{{ route('mhadel.venues.store') }}" method="POST">
            @csrf

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <!-- Venue Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-gray-800 font-poppins">
                                Venue Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300 @error('name') border-red-500 ring-red-200 @enderror"
                                placeholder="Enter venue name"
                                required
                            >
                            @error('name')
                                <p class="text-sm text-red-600 font-inter flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div class="space-y-2">
                            <label for="capacity" class="block text-sm font-bold text-gray-800 font-poppins">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="capacity" 
                                name="capacity" 
                                value="{{ old('capacity') }}"
                                min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300 @error('capacity') border-red-500 ring-red-200 @enderror"
                                placeholder="Maximum number of people"
                                required
                            >
                            @error('capacity')
                                <p class="text-sm text-red-600 font-inter flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Price per Hour -->
                        <div class="space-y-2">
                            <label for="price_per_hour" class="block text-sm font-bold text-gray-800 font-poppins">
                                Price per Hour (₱) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="price_per_hour" 
                                name="price_per_hour" 
                                value="{{ old('price_per_hour') }}"
                                min="0.01"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300 @error('price_per_hour') border-red-500 ring-red-200 @enderror"
                                placeholder="0.00"
                                required
                            >
                            @error('price_per_hour')
                                <p class="text-sm text-red-600 font-inter flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-5">
                        <!-- Status -->
                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-bold text-gray-800 font-poppins">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300 @error('status') border-red-500 ring-red-200 @enderror"
                                required
                            >
                                <option value="">Select status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-sm text-red-600 font-inter flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Is Available -->
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-gray-800 font-poppins">Availability</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="is_available" 
                                        name="is_available" 
                                        value="1"
                                        {{ old('is_available') ? 'checked' : 'checked' }}
                                        class="h-5 w-5 text-maroon focus:ring-maroon border-gray-300 rounded transition-all duration-300"
                                    >
                                    <label for="is_available" class="ml-3 block text-sm font-medium text-gray-700 font-inter">
                                        Available for reservations
                                    </label>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 font-inter">
                                    <i class="fas fa-info-circle mr-2 text-maroon"></i>
                                    Check this if the venue can be reserved by users
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-bold text-gray-800 font-poppins">
                                Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300 resize-none @error('description') border-red-500 ring-red-200 @enderror"
                                placeholder="Enter venue description (optional)"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 font-inter flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Available Equipment Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-tools text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 font-poppins">Available Equipment</h3>
                        </div>
                        <button type="button" id="add-equipment" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-300 flex items-center shadow-lg font-medium">
                            <i class="fas fa-plus mr-2"></i>Add Equipment
                        </button>
                    </div>
                    
                    <div id="equipment-container" class="space-y-3">
                        <!-- Equipment items will be added here dynamically -->
                    </div>
                    
                    @error('available_equipment')
                        <p class="mt-3 text-sm text-red-600 font-inter flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('mhadel.venues.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300 font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 flex items-center shadow-lg font-bold">
                        <i class="fas fa-save mr-2"></i>Create Venue
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.font-inter {
    font-family: 'Inter', sans-serif;
}

.font-poppins {
    font-family: 'Poppins', sans-serif;
}

/* Form animations */
input, select, textarea {
    transition: all 0.3s ease;
}

input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
}

/* Equipment item hover effects */
.equipment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
* {
    transition: all 0.3s ease;
}

/* Enhanced shadows */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Checkbox styling */
input[type="checkbox"]:checked {
    background-color: #8B0000;
    border-color: #8B0000;
}

/* Button hover effects */
button:hover, a:hover {
    transform: translateY(-1px);
}
</style>

<script>
let equipmentIndex = 0;

document.getElementById('add-equipment').addEventListener('click', function() {
    const container = document.getElementById('equipment-container');
    const equipmentDiv = document.createElement('div');
    equipmentDiv.className = 'equipment-item bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all duration-300';
    equipmentDiv.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="flex-1">
                <input type="text" name="available_equipment[${equipmentIndex}][name]" placeholder="Equipment name" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300" required>
            </div>
            <div class="flex-1">
                <input type="text" name="available_equipment[${equipmentIndex}][category]" placeholder="Category (e.g., Audio, Visual, Furniture)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300" required>
            </div>
            <div class="w-24">
                <input type="number" name="available_equipment[${equipmentIndex}][quantity]" placeholder="Qty" min="1" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-300" required>
            </div>
            <button type="button" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-300" onclick="removeEquipment(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(equipmentDiv);
    equipmentIndex++;
});

function removeEquipment(button) {
    button.closest('.equipment-item').remove();
}
</script>
@endsection
