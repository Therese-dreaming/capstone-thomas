@extends('layouts.admin')

@section('title', 'Equipment Management')
@section('page-title', 'Equipment List')

@section('header-actions')
    <a href="{{ route('admin.equipment.create') }}" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-opacity-80 transition">
        <i class="fas fa-plus mr-2"></i>Add New Equipment
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Equipment</h3>
        <p class="text-gray-600 text-sm">Manage your equipment inventory</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($equipment as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $item->category }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $item->total_quantity }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.equipment.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.equipment.edit', $item) }}" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this equipment?')">
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
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-tools text-4xl text-gray-300 mb-2"></i>
                            <p>No equipment found</p>
                            <a href="{{ route('admin.equipment.create') }}" class="mt-2 text-maroon hover:underline">Add your first equipment</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($equipment->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $equipment->links() }}
    </div>
    @endif
</div>
@endsection
