@extends('layouts.admin')

@section('title', 'Add New Venue')
@section('page-title', 'Add New Venue')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Venue Details</h3>
            <p class="text-gray-600 text-sm">Fill in the information for the new venue</p>
        </div>

        <form action="{{ route('admin.venues.store') }}" method="POST" class="p-6">
            @csrf

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
                        value="{{ old('name') }}"
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
                        value="{{ old('capacity') }}"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('capacity') border-red-500 @enderror"
                        placeholder="Maximum number of people"
                        required
                    >
                    @error('capacity')
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
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            {{ old('is_available') ? 'checked' : 'checked' }}
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
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-opacity-80 transition">
                    <i class="fas fa-save mr-2"></i>Save Venue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
