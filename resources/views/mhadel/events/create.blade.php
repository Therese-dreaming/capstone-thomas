@extends('layouts.mhadel')

@section('title', 'Create New Event')
@section('page-title', 'Create New Event')
@section('page-subtitle', 'Add a new event for venue assignment')

<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-inter {
        font-family: 'Inter', sans-serif;
    }
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .form-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .form-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .form-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 1rem 1rem 0 0;
    }
    
    .form-content {
        padding: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #800000;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
    }
    
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
        resize: vertical;
        min-height: 100px;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: #800000;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
    }
    
    .btn-primary {
        background-color: #800000;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        background-color: #660000;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .btn-secondary {
        background-color: #6b7280;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-secondary:hover {
        background-color: #4b5563;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .equipment-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }
    
    .remove-equipment {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .remove-equipment:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    .info-box {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-box h4 {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .info-box p {
        color: #92400e;
        font-size: 0.875rem;
        margin: 0;
    }
    
    .venue-suggestion {
        font-size: 0.875rem;
        color: #800000;
        margin-top: 0.5rem;
    }
    
    .warning {
        color: #ef4444;
    }
    
    .checking-badge {
        background-color: #f7f7f7;
        color: #333;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: inline-block;
        margin-left: 0.5rem;
    }
    
    .conflict-badge {
        background-color: #ef4444;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: inline-block;
        margin-left: 0.5rem;
    }
    
    .success-badge {
        background-color: #34c759;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        display: inline-block;
        margin-left: 0.5rem;
    }
    
    .conflict-indicator {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .success-indicator {
        border-color: #34c759;
        box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.1);
    }
</style>

@section('content')
<div class="space-y-6 font-inter">
    <!-- Info Box -->
    <div class="info-box">
        <h4 class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Event Creation Process
        </h4>
        <p>Create your event by filling in all the details including venue selection. Your event will be created immediately and appear in the calendar once submitted.</p>
    </div>

    <div class="form-card animate-fadeIn">
        <div class="form-header">
            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                <i class="fas fa-calendar-plus mr-3 text-maroon"></i>
                Create New Event
            </h2>
            <p class="text-gray-600 mt-1">Fill in the details below to create a new event</p>
        </div>
        
        <div class="form-content">
            <form id="eventForm" action="{{ route('mhadel.events.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading mr-2 text-maroon"></i>
                            Event Title *
                        </label>
                        <input type="text" id="title" name="title" class="form-input" 
                               value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="organizer" class="form-label">
                            <i class="fas fa-user mr-2 text-maroon"></i>
                            Organizer *
                        </label>
                        <input type="text" id="organizer" name="organizer" class="form-input" 
                               value="{{ old('organizer') }}" required>
                        @error('organizer')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left mr-2 text-maroon"></i>
                        Description
                    </label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Describe the event...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="start_date" class="form-label">
                            <i class="fas fa-calendar mr-2 text-maroon"></i>
                            Start Date & Time *
                        </label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-input" 
                               value="{{ old('start_date') }}" required>
                        <span id="startDateBadge" class="checking-badge" style="display: none;"></span>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date" class="form-label">
                            <i class="fas fa-calendar mr-2 text-maroon"></i>
                            End Date & Time *
                        </label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-input" 
                               value="{{ old('end_date') }}" required>
                        <span id="endDateBadge" class="checking-badge" style="display: none;"></span>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="department" class="form-label">
                            <i class="fas fa-building mr-2 text-maroon"></i>
                            Department
                        </label>
                        <input type="text" id="department" name="department" class="form-input" 
                               value="{{ old('department') }}" placeholder="e.g., BSIT, BSCS">
                        @error('department')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="max_participants" class="form-label">
                            <i class="fas fa-users mr-2 text-maroon"></i>
                            Maximum Participants
                        </label>
                        <input type="number" id="max_participants" name="max_participants" class="form-input" 
                               value="{{ old('max_participants') }}" min="1" placeholder="e.g., 100">
                        @error('max_participants')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Venue Selection -->
                <div class="form-group">
                    <label for="venue_id" class="form-label">
                        <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                        Venue *
                    </label>
                    <select id="venue_id" name="venue_id" class="form-input" required>
                        <option value="">Select a venue</option>
                        @foreach(\App\Models\Venue::where('is_available', true)->get() as $venue)
                            <option value="{{ $venue->id }}" data-capacity="{{ $venue->capacity }}" class="venue-option" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                {{ $venue->name }} (Capacity: {{ $venue->capacity }})
                            </option>
                        @endforeach
                    </select>
                    <span id="venueBadge" class="checking-badge" style="display: none;"></span>
                    <div id="venueSuggestion" class="venue-suggestion" style="display: none;">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <span id="suggestionText"></span>
                    </div>
                    <div id="conflictWarning" class="venue-suggestion warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span id="conflictText"></span>
                    </div>
                    @error('venue_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Equipment Section -->
                <div class="form-group">
                    <div class="flex items-center justify-between mb-4">
                        <label class="form-label">
                            <i class="fas fa-tools mr-2 text-maroon"></i>
                            Equipment Needed
                        </label>
                        <button type="button" id="addEquipment" class="btn-secondary text-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Add Equipment
                        </button>
                    </div>
                    
                    <div id="equipmentContainer">
                        <!-- Equipment items will be added here dynamically -->
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('mhadel.events.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let equipmentCount = 0;

document.getElementById('addEquipment').addEventListener('click', function() {
    addEquipment();
});

function addEquipment() {
    const container = document.getElementById('equipmentContainer');
    const equipmentItem = document.createElement('div');
    equipmentItem.className = 'equipment-item';
    
    equipmentItem.innerHTML = `
        <button type="button" class="remove-equipment" onclick="removeEquipment(this)">
            <i class="fas fa-times"></i>
        </button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Equipment Name</label>
                <input type="text" name="equipment[${equipmentCount}][name]" class="form-input" 
                       placeholder="e.g., Microphone">
            </div>
            <div>
                <label class="form-label">Quantity</label>
                <input type="number" name="equipment[${equipmentCount}][quantity]" class="form-input" 
                       min="1" placeholder="e.g., 2">
            </div>
        </div>
    `;
    
    container.appendChild(equipmentItem);
    equipmentCount++;
}

function removeEquipment(button) {
    const equipmentItem = button.closest('.equipment-item');
    equipmentItem.remove();
}

// Set minimum date to today
const today = new Date().toISOString().slice(0, 16);
document.getElementById('start_date').min = today;
document.getElementById('end_date').min = today;

// Update end date minimum when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('end_date').min = this.value;
});

// Venue suggestion and conflict checking
document.getElementById('max_participants').addEventListener('input', function() {
    const maxParticipants = parseInt(this.value);
    const venueSelect = document.getElementById('venue_id');
    const venueOptions = venueSelect.querySelectorAll('.venue-option');
    const suggestionText = document.getElementById('suggestionText');
    const venueSuggestion = document.getElementById('venueSuggestion');
    
    if (maxParticipants > 0) {
        // Filter venues that can accommodate the participants (capacity >= maxParticipants)
        const suitableVenues = Array.from(venueOptions).filter(option => 
            parseInt(option.getAttribute('data-capacity')) >= maxParticipants
        );
        
        if (suitableVenues.length > 0) {
            // Sort by capacity ascending to get the venue with the closest (smallest) capacity
            suitableVenues.sort((a, b) => 
                parseInt(a.getAttribute('data-capacity')) - parseInt(b.getAttribute('data-capacity'))
            );
            
            // Get the venue with the smallest capacity that can accommodate all participants
            const bestVenue = suitableVenues[0];
            const venueName = bestVenue.textContent;
            const venueCapacity = parseInt(bestVenue.getAttribute('data-capacity'));
            
            suggestionText.textContent = `Consider selecting ${venueName} as it can accommodate ${maxParticipants} participants (closest capacity match).`;
            venueSuggestion.style.display = 'block';
        } else {
            suggestionText.textContent = 'No venues can accommodate the selected number of participants.';
            venueSuggestion.style.display = 'block';
        }
    } else {
        venueSuggestion.style.display = 'none';
    }
});

// Conflict checking function with enhanced visual indicators
let conflictCheckTimeout;

function showCheckingState() {
    const startDateBadge = document.getElementById('startDateBadge');
    const endDateBadge = document.getElementById('endDateBadge');
    const venueBadge = document.getElementById('venueBadge');
    
    [startDateBadge, endDateBadge, venueBadge].forEach(badge => {
        badge.className = 'checking-badge';
        badge.innerHTML = '<span class="spinner"></span>Checking...';
        badge.style.display = 'inline-block';
    });
}

function clearAllIndicators() {
    const venueSelect = document.getElementById('venue_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const conflictWarning = document.getElementById('conflictWarning');
    const startDateBadge = document.getElementById('startDateBadge');
    const endDateBadge = document.getElementById('endDateBadge');
    const venueBadge = document.getElementById('venueBadge');
    
    // Remove visual indicators from inputs
    [venueSelect, startDateInput, endDateInput].forEach(input => {
        input.classList.remove('conflict-indicator', 'success-indicator');
    });
    
    // Hide badges and warning
    conflictWarning.style.display = 'none';
    [startDateBadge, endDateBadge, venueBadge].forEach(badge => {
        badge.style.display = 'none';
    });
}

function checkVenueConflicts() {
    // Clear any existing timeout
    clearTimeout(conflictCheckTimeout);
    
    const venueId = document.getElementById('venue_id').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const conflictWarning = document.getElementById('conflictWarning');
    const conflictText = document.getElementById('conflictText');
    
    if (!venueId || !startDate || !endDate) {
        clearAllIndicators();
        return;
    }
    
    // Show checking state immediately
    showCheckingState();
    
    // Debounce the API call
    conflictCheckTimeout = setTimeout(() => {
        console.log('Starting conflict check...', {
            venue_id: venueId,
            start_date: startDate,
            end_date: endDate
        });
        
        // Get CSRF token with fallback
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '{{ csrf_token() }}';
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            clearAllIndicators();
            
            // Show error state
            conflictText.innerHTML = '<strong>Error:</strong> Security token not found. Please refresh the page.';
            conflictWarning.className = 'venue-suggestion warning';
            conflictWarning.style.display = 'block';
            return;
        }
        
        // Make AJAX request to check conflicts
        fetch('{{ route("mhadel.events.check-conflicts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                venue_id: venueId,
                start_date: startDate,
                end_date: endDate
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Conflict check response:', data);
            
            const venueSelect = document.getElementById('venue_id');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const startDateBadge = document.getElementById('startDateBadge');
            const endDateBadge = document.getElementById('endDateBadge');
            const venueBadge = document.getElementById('venueBadge');
            
            if (data.hasConflicts) {
                console.log('Conflicts found:', data.conflicts);
                const conflictList = data.conflicts.map(conflict => 
                    `"${conflict.title}" (${conflict.start_date} - ${conflict.end_date})`
                ).join(', ');
                
                conflictText.innerHTML = `<strong>Scheduling Conflict Detected!</strong><br>The selected venue has conflicts with: ${conflictList}`;
                conflictWarning.style.display = 'block';
                
                // Add conflict indicators to inputs
                [venueSelect, startDateInput, endDateInput].forEach(input => {
                    input.classList.add('conflict-indicator');
                    input.classList.remove('success-indicator');
                });
                
                // Show conflict badges
                [startDateBadge, endDateBadge, venueBadge].forEach(badge => {
                    badge.className = 'conflict-badge';
                    badge.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Conflict';
                    badge.style.display = 'inline-block';
                });
                
            } else {
                console.log('No conflicts found');
                conflictWarning.style.display = 'none';
                
                // Add success indicators to inputs
                [venueSelect, startDateInput, endDateInput].forEach(input => {
                    input.classList.add('success-indicator');
                    input.classList.remove('conflict-indicator');
                });
                
                // Show success badges
                [startDateBadge, endDateBadge, venueBadge].forEach(badge => {
                    badge.className = 'success-badge';
                    badge.innerHTML = '<i class="fas fa-check mr-1"></i>Available';
                    badge.style.display = 'inline-block';
                });
            }
        })
        .catch(error => {
            console.error('Error checking conflicts:', error);
            clearAllIndicators();
            
            // Show error state
            conflictText.innerHTML = '<strong>Error:</strong> Unable to check for conflicts. Please try again.';
            conflictWarning.className = 'venue-suggestion warning';
            conflictWarning.style.display = 'block';
        });
    }, 500); // 500ms debounce delay
}

// Form validation
document.getElementById('eventForm').addEventListener('submit', function(e) {
    const equipmentItems = document.querySelectorAll('.equipment-item');
    let isValid = true;
    
    equipmentItems.forEach((item, index) => {
        const nameInput = item.querySelector('input[name*="[name]"]');
        const quantityInput = item.querySelector('input[name*="[quantity]"]');
        
        if ((nameInput.value.trim() && !quantityInput.value.trim()) || 
            (!nameInput.value.trim() && quantityInput.value.trim())) {
            e.preventDefault();
            alert(`Equipment ${index + 1}: Both name and quantity are required if either is provided.`);
            isValid = false;
            return false;
        }
    });
    
    if (!isValid) {
        return false;
    }
});

// Event listeners for conflict checking
document.getElementById('venue_id').addEventListener('change', checkVenueConflicts);
document.getElementById('start_date').addEventListener('change', checkVenueConflicts);
document.getElementById('end_date').addEventListener('change', checkVenueConflicts);
</script>
@endsection
