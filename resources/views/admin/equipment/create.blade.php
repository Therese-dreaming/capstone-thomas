@extends('layouts.admin')

@section('title', 'Add New Equipment')
@section('page-title', 'Add New Equipment')

@section('header-actions')
    <a href="{{ route('admin.equipment.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
        <i class="fas fa-arrow-left mr-2"></i>Back to Equipment
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Equipment Information</h3>
        <p class="text-gray-600 text-sm">Fill in the details for the new equipment</p>
    </div>

    <form action="{{ route('admin.equipment.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Equipment Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Enter equipment name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" 
                        id="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-transparent @error('category') border-red-500 @enderror"
                        required>
                    <option value="">Select a category</option>
                    <option value="Audio" {{ old('category') == 'Audio' ? 'selected' : '' }}>Audio</option>
                    <option value="Visual" {{ old('category') == 'Visual' ? 'selected' : '' }}>Visual</option>
                    <option value="Lighting" {{ old('category') == 'Lighting' ? 'selected' : '' }}>Lighting</option>
                    <option value="Furniture" {{ old('category') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                    <option value="Technology" {{ old('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Safety" {{ old('category') == 'Safety' ? 'selected' : '' }}>Safety</option>
                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Quantity -->
            <div>
                <label for="total_quantity" class="block text-sm font-medium text-gray-700 mb-2">Total Quantity</label>
                <input type="number" 
                       name="total_quantity" 
                       id="total_quantity" 
                       value="{{ old('total_quantity') }}"
                       min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-transparent @error('total_quantity') border-red-500 @enderror"
                       placeholder="Enter total quantity"
                       required>
                @error('total_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.equipment.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-maroon text-white rounded-lg hover:bg-opacity-80 transition">
                <i class="fas fa-save mr-2"></i>Save Equipment
            </button>
        </div>
    </form>
</div>
@endsection
