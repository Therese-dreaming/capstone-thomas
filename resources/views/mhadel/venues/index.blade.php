@extends('layouts.mhadel')

@section('title', 'Venues Management')
@section('page-title', 'Venues Management')
@section('page-subtitle', 'Manage your venue inventory and availability')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@section('header-actions')
    <form method="GET" action="{{ route('mhadel.venues.index') }}" class="flex items-center space-x-3">
        <!-- Search Bar -->
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search venues..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors w-64 font-inter">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-maroon">
                <i class="fas fa-search"></i>
            </div>
        </div>
        
        <!-- Filter Dropdown -->
        <div class="relative">
            <button type="button" id="filterBtn" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-all duration-300 flex items-center shadow-lg">
                <i class="fas fa-filter mr-2"></i>Filter
                <i class="fas fa-chevron-down ml-2 text-xs"></i>
            </button>
            <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
                <div class="p-3 border-b border-gray-100 bg-maroon">
                    <h4 class="text-sm font-bold text-white font-poppins">Filter by Status</h4>
                </div>
                <div class="p-2">
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                        <input type="checkbox" name="status[]" value="active" {{ in_array('active', request('status', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 font-medium">Active</span>
                    </label>
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                        <input type="checkbox" name="status[]" value="inactive" {{ in_array('inactive', request('status', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 font-medium">Inactive</span>
                    </label>
                </div>
                <div class="p-3 border-t border-gray-100 bg-gray-100">
                    <h4 class="text-sm font-bold text-gray-800 font-poppins">Filter by Availability</h4>
                </div>
                <div class="p-2">
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                        <input type="checkbox" name="availability[]" value="available" {{ in_array('available', request('availability', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 font-medium">Available</span>
                    </label>
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                        <input type="checkbox" name="availability[]" value="unavailable" {{ in_array('unavailable', request('availability', [])) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 font-medium">Not Available</span>
                    </label>
                </div>
                <div class="p-3 border-t border-gray-100">
                    <button type="submit" class="w-full bg-maroon text-white py-2 rounded-lg hover:bg-red-800 transition-colors font-medium">
                        Apply Filters
                    </button>
                    <a href="{{ route('mhadel.venues.index') }}" class="block w-full text-center text-gray-600 py-2 mt-2 hover:text-gray-800 transition-colors font-medium">
                        Clear All
                    </a>
                </div>
            </div>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-300 flex items-center shadow-lg">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        
        <a href="{{ route('mhadel.venues.create') }}" class="bg-maroon text-white px-6 py-2 rounded-lg hover:bg-red-800 transition-all duration-300 flex items-center shadow-lg font-bold">
            <i class="fas fa-plus mr-2"></i>Add New Venue
        </a>
    </form>
@endsection

@section('content')

<!-- Venues Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="venuesGrid">
    @forelse($venues as $venue)
    <div class="venue-card bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105" 
         data-name="{{ strtolower($venue->name) }}" 
         data-status="{{ $venue->status }}" 
         data-availability="{{ $venue->is_available ? 'available' : 'unavailable' }}">
        
        <!-- Venue Header -->
        <div class="p-6 bg-maroon text-white">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-white mb-2 font-poppins">{{ $venue->name }}</h3>
                    @if($venue->description)
                        <p class="text-sm text-red-100 line-clamp-2 font-inter">{{ $venue->description }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end space-y-2 ml-4">
                    <!-- Status Badge -->
                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $venue->status === 'active' ? 'bg-green-600 text-white shadow-lg' : 'bg-red-600 text-white shadow-lg' }}">
                        {{ ucfirst($venue->status) }}
                    </span>
                    <!-- Availability Badge -->
                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $venue->is_available ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-600 text-white shadow-lg' }}">
                        {{ $venue->is_available ? 'Available' : 'Busy' }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Venue Details -->
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="text-3xl font-bold text-blue-800 font-poppins">{{ $venue->capacity }}</div>
                    <div class="text-sm text-blue-600 font-medium font-inter">Capacity</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="text-3xl font-bold text-green-800 font-poppins">₱{{ number_format($venue->price_per_hour) }}</div>
                    <div class="text-sm text-green-600 font-medium font-inter">Per Hour</div>
                </div>
            </div>
            
            <!-- Equipment -->
            @if($venue->available_equipment && count($venue->available_equipment) > 0)
            <div class="mb-6">
                <h4 class="text-sm font-bold text-gray-800 mb-3 font-poppins flex items-center">
                    <i class="fas fa-tools mr-2 text-maroon"></i>Available Equipment
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(array_slice($venue->available_equipment, 0, 3) as $equipment)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-red-50 text-red-800 font-medium border border-red-200">
                            <i class="fas fa-tools mr-1"></i>{{ $equipment['name'] }}
                        </span>
                    @endforeach
                    @if(count($venue->available_equipment) > 3)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700 font-medium border border-gray-300">
                            +{{ count($venue->available_equipment) - 3 }} more
                        </span>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Created Date -->
            <div class="text-xs text-gray-500 mb-6 flex items-center font-inter">
                <i class="fas fa-calendar mr-2 text-maroon"></i>Created {{ $venue->created_at->format('M d, Y') }}
            </div>
        </div>
        
        <!-- Action Buttons - Fixed Height for Alignment -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 h-16 flex items-center justify-between">
            <div class="flex space-x-3">
                <a href="{{ route('mhadel.venues.show', $venue) }}" 
                   class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-all duration-300 shadow-lg" 
                   title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('mhadel.venues.edit', $venue) }}" 
                   class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-all duration-300 shadow-lg" 
                   title="Edit Venue">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <button type="button" 
                    onclick="openDeleteModal('{{ $venue->id }}', '{{ $venue->name }}')"
                    class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-all duration-300 shadow-lg" 
                    title="Delete Venue">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-gray-50 rounded-xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="mb-6">
                <div class="w-24 h-24 bg-maroon rounded-full flex items-center justify-center mx-auto shadow-lg">
                    <i class="fas fa-building text-4xl text-white"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3 font-poppins">No Venues Found</h3>
            <p class="text-gray-600 mb-8 font-inter">Get started by adding your first venue to the system.</p>
            <a href="{{ route('mhadel.venues.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 shadow-lg font-bold">
                <i class="fas fa-plus mr-2"></i>Add Your First Venue
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($venues->hasPages())
<div class="mt-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Results Info (Left) -->
            <div class="text-sm text-gray-700 font-inter">
                <span class="font-medium">Showing</span>
                <span class="font-bold text-maroon">{{ $venues->firstItem() ?? 0 }}</span>
                <span class="font-medium">to</span>
                <span class="font-bold text-maroon">{{ $venues->lastItem() ?? 0 }}</span>
                <span class="font-medium">of</span>
                <span class="font-bold text-maroon">{{ $venues->total() }}</span>
                <span class="font-medium">results</span>
                @if(request('search'))
                    <span class="font-medium">for</span>
                    <span class="font-bold text-maroon">"{{ request('search') }}"</span>
                @endif
            </div>
            
            <!-- Pagination Navigation (Right) -->
            <div class="flex items-center space-x-2">
                {{ $venues->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-poppins">Delete Venue</h3>
                        <p class="text-sm text-gray-600 font-inter">This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-gray-700 mb-4 font-inter">
                    Are you sure you want to delete <span class="font-bold text-gray-900" id="venueNameToDelete"></span>?
                </p>
                <p class="text-sm text-red-600 font-medium font-inter">
                    <i class="fas fa-info-circle mr-2"></i>
                    This will permanently remove the venue and all associated data.
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-200 flex space-x-3">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Delete Venue
                    </button>
                </form>
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.venue-card {
    transition: all 0.3s ease;
}

.venue-card:hover {
    transform: translateY(-2px);
}

/* Animation for cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.venue-card {
    animation: fadeInUp 0.3s ease-out;
}

/* Enhanced hover effects */
.venue-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Gradient text effect */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Pulse animation for stats */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.stats-card:hover {
    animation: pulse 0.6s ease-in-out;
}

/* Modal animations */
#deleteModal {
    transition: opacity 0.3s ease;
}

#deleteModal .bg-white {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter dropdown functionality
    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    
    // Toggle filter dropdown
    filterBtn.addEventListener('click', function() {
        filterDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!filterBtn.contains(e.target) && !filterDropdown.contains(e.target)) {
            filterDropdown.classList.add('hidden');
        }
    });
});

// Delete Modal Functions
function openDeleteModal(venueId, venueName) {
    document.getElementById('venueNameToDelete').textContent = venueName;
    document.getElementById('deleteForm').action = `/mhadel/venues/${venueId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endsection
