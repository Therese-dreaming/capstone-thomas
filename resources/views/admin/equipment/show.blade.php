@extends('layouts.admin')

@section('title', 'Equipment Details')
@section('page-title', 'Equipment Details')

@section('header-actions')
    <div class="flex space-x-2">
        <a href="{{ route('admin.equipment.edit', $equipment) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <a href="{{ route('admin.equipment.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Equipment
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">{{ $equipment->name }}</h3>
                <p class="text-gray-600 text-sm mt-1">Equipment Information</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Created</p>
                <p class="text-sm font-medium text-gray-700">{{ $equipment->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Name -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-tag text-maroon mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Equipment Name</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $equipment->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-list-alt text-maroon mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Category</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $equipment->category }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Quantity -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-boxes text-maroon mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Quantity</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $equipment->total_quantity }}</p>
                    </div>
                </div>
            </div>

            <!-- Created Date -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-plus text-maroon mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Created</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $equipment->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Last Updated -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-edit text-maroon mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Last Updated</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $equipment->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
            <form action="{{ route('admin.equipment.destroy', $equipment) }}" 
                  method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this equipment? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>Delete Equipment
                </button>
            </form>

            <div class="flex space-x-2">
                <a href="{{ route('admin.equipment.edit', $equipment) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit Equipment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
