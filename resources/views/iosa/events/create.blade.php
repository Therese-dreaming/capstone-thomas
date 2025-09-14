@extends('layouts.iosa')

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
</style>

@section('content')
<div class="space-y-6 font-inter">
    <!-- Info Box -->
    <div class="info-box">
        <h4 class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Event Creation Process
        </h4>
        <p>When you create an event, it will be sent to Ms. Mhadel for venue assignment. The event will appear in your calendar once a venue has been assigned.</p>
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
            <form id="eventForm" action="{{ route('iosa.events.store') }}" method="POST">
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
                        @if(old('equipment'))
                            @foreach(old('equipment') as $index => $equipment)
                                <div class="equipment-item">
                                    <button type="button" class="remove-equipment" onclick="removeEquipment(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="form-label">Equipment Name</label>
                                            <input type="text" name="equipment[{{ $index }}][name]" class="form-input" 
                                                   value="{{ $equipment['name'] ?? '' }}" placeholder="e.g., Microphone">
                                        </div>
                                        <div>
                                            <label class="form-label">Quantity</label>
                                            <input type="number" name="equipment[{{ $index }}][quantity]" class="form-input" 
                                                   value="{{ $equipment['quantity'] ?? '' }}" min="1" placeholder="e.g., 2">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('iosa.events.index') }}" class="btn-secondary">
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
let equipmentCount = {{ count(old('equipment', [])) }};

document.getElementById('addEquipment').addEventListener('click', function() {
    addEquipment();
});

function addEquipment() {
    const container = document.getElementById('equipmentContainer');
    const equipmentItem = document.createElement('div');
    equipmentItem.className = 'equipment-item';
    
    // Calculate the next sequential number
    const currentCount = container.children.length;
    const nextNumber = currentCount + 1;
    
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
                       min="1" placeholder="e.g., 2" required>
            </div>
        </div>
    `;
    
    container.appendChild(equipmentItem);
    equipmentCount++;
}

function removeEquipment(button) {
    const equipmentItem = button.closest('.equipment-item');
    equipmentItem.remove();
    renumberEquipment();
}

function renumberEquipment() {
    const container = document.getElementById('equipmentContainer');
    const items = container.querySelectorAll('.equipment-item');
    
    items.forEach((item, index) => {
        const inputs = item.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.name;
            if (name.includes('[name]')) {
                input.name = `equipment[${index}][name]`;
            } else if (name.includes('[quantity]')) {
                input.name = `equipment[${index}][quantity]`;
            }
        });
    });
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

// Set minimum date to today
const today = new Date().toISOString().slice(0, 16);
document.getElementById('start_date').min = today;
document.getElementById('end_date').min = today;

// Update end date minimum when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('end_date').min = this.value;
});
</script>
@endsection
