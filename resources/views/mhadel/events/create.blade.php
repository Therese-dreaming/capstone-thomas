@extends('layouts.mhadel')

@section('title', 'Create New Event')
@section('page-title', 'Create New Event')
@section('page-subtitle', 'Add a new event to your calendar')

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
    
    .required {
        color: #DC2626;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    
    .form-input.error {
        border-color: #DC2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
        cursor: pointer;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
        resize: vertical;
        min-height: 120px;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: #8B0000;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    
    .help-text {
        font-size: 0.75rem;
        color: #6B7280;
        margin-top: 0.25rem;
    }
    
    .error-text {
        font-size: 0.75rem;
        color: #DC2626;
        margin-top: 0.25rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: #8B0000;
        color: white;
    }
    
    .btn-primary:hover {
        background: #7F0000;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(139, 0, 0, 0.2);
    }
    
    .btn-secondary {
        background: #6B7280;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #4B5563;
        transform: translateY(-1px);
    }
    
    .btn-outline {
        background: transparent;
        color: #6B7280;
        border: 2px solid #e5e7eb;
    }
    
    .btn-outline:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        margin: 2rem 0;
    }
    
    .info-box {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-box-title {
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-box-text {
        font-size: 0.875rem;
        color: #1e40af;
        line-height: 1.5;
    }
</style>

@section('header-actions')
    <a href="{{ route('mhadel.events.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition shadow-sm flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>Back to Events
    </a>
@endsection

@section('content')
<div class="space-y-8 font-inter">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-fadeIn">
        <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-maroon to-red-800 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">Create New Event</h1>
                    <p class="text-gray-600 text-lg">Add a new event to your calendar with all the necessary details</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="form-card animate-fadeIn">
        <div class="form-header">
            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                <i class="fas fa-calendar-plus text-maroon mr-3 text-2xl"></i>
                Event Details
            </h2>
            <p class="text-gray-600 mt-2">Fill in the information below to create your event</p>
        </div>

        <form action="{{ route('mhadel.events.store') }}" method="POST" class="form-content">
            @csrf

            @if ($errors->any())
            <div class="info-box bg-red-50 border-red-200 mb-6">
                <div class="info-box-title text-red-800">
                    <i class="fas fa-exclamation-triangle"></i>
                    Please fix the following issues:
                </div>
                <ul class="list-disc pl-5 space-y-1 text-red-700">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Basic Information Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="form-group">
                    <label for="title" class="form-label">
                        Event Title <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title') }}"
                        class="form-input @error('title') error @enderror"
                        placeholder="Enter event title"
                        required
                    >
                    @error('title')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="organizer" class="form-label">
                        Organizer <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="organizer" 
                        name="organizer" 
                        value="{{ old('organizer') }}"
                        class="form-input @error('organizer') error @enderror"
                        placeholder="Enter organizer name"
                        required
                    >
                    @error('organizer')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">
                        Department
                    </label>
                    <input 
                        type="text" 
                        id="department" 
                        name="department" 
                        value="{{ old('department') }}"
                        class="form-input @error('department') error @enderror"
                        placeholder="Enter department name"
                    >
                    <p class="help-text">Optional. Specify which department this event belongs to.</p>
                    @error('department')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Venue Selection -->
            <div class="form-group">
                <label for="venue_id" class="form-label">
                    Venue <span class="required">*</span>
                </label>
                <select 
                    id="venue_id" 
                    name="venue_id" 
                    class="form-select @error('venue_id') error @enderror"
                    required
                >
                    <option value="">Select a venue</option>
                    @foreach(\App\Models\Venue::where('is_available', true)->get() as $venue)
                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                        {{ $venue->name }} - Capacity: {{ $venue->capacity }} people
                        @if($venue->price_per_hour)
                            (₱{{ number_format($venue->price_per_hour, 2) }}/hour)
                        @endif
                    </option>
                    @endforeach
                </select>
                <p class="help-text">Only currently available venues are listed. Venue pricing is shown when available.</p>
                @error('venue_id')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="section-divider"></div>

            <!-- Date and Time Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="start_date" class="form-label">
                        Start Date & Time <span class="required">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="start_date" 
                        name="start_date" 
                        value="{{ old('start_date') }}"
                        class="form-input @error('start_date') error @enderror"
                        required
                    >
                    @error('start_date')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date" class="form-label">
                        End Date & Time <span class="required">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="end_date" 
                        name="end_date" 
                        value="{{ old('end_date') }}"
                        class="form-input @error('end_date') error @enderror"
                        required
                    >
                    @error('end_date')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Event Configuration Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="max_participants" class="form-label">
                        Maximum Participants
                    </label>
                    <input 
                        type="number" 
                        id="max_participants" 
                        name="max_participants" 
                        value="{{ old('max_participants') }}"
                        min="1"
                        class="form-input @error('max_participants') error @enderror"
                        placeholder="Leave empty for no limit"
                    >
                    <p class="help-text">Optional. Leave blank to allow unlimited attendees.</p>
                    @error('max_participants')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Equipment Selection Section -->
            <div class="form-group">
                <label class="form-label">
                    Equipment Selection
                </label>
                <div id="equipment-container">
                    <div class="equipment-item bg-gray-50 p-4 rounded-lg border border-gray-200 mb-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-sm">Equipment Name</label>
                                <select name="equipment[0][name]" class="form-select equipment-select" data-index="0">
                                    <option value="">Select equipment</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label text-sm">Quantity</label>
                                <input 
                                    type="number" 
                                    name="equipment[0][quantity]" 
                                    min="1" 
                                    class="form-input equipment-quantity" 
                                    placeholder="Quantity"
                                    disabled
                                >
                            </div>
                        </div>
                        <button type="button" class="remove-equipment mt-2 text-red-600 hover:text-red-800 text-sm" style="display: none;">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                <button type="button" id="add-equipment" class="btn btn-outline text-sm">
                    <i class="fas fa-plus mr-2"></i>Add Equipment
                </button>
                <p class="help-text">Select equipment available at the chosen venue. Equipment options will update when you select a venue.</p>
            </div>

            <!-- Description Section -->
            <div class="form-group">
                <label for="description" class="form-label">
                    Event Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-textarea @error('description') error @enderror"
                    placeholder="Provide a detailed description of your event (optional)"
                >{{ old('description') }}</textarea>
                <p class="help-text">Describe what attendees can expect, the agenda, or any special requirements.</p>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Help Information -->
            <div class="info-box">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb"></i>
                    Tips for creating a great event
                </div>
                <div class="info-box-text">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Choose a descriptive title that clearly explains what the event is about</li>
                        <li>Set realistic start and end times to avoid scheduling conflicts</li>
                        <li>Select a venue that can accommodate your expected number of participants</li>
                        <li>Provide a clear description to help attendees understand what to expect</li>
                    </ul>
                </div>
            </div>

            <!-- Event Status Information -->
            <div class="info-box bg-blue-50 border-blue-200">
                <div class="info-box-title text-blue-800">
                    <i class="fas fa-info-circle"></i>
                    Event Status Information
                </div>
                <div class="info-box-text text-blue-700">
                    <p class="mb-2"><strong>Event status is automatically determined based on your scheduled date and time:</strong></p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Upcoming:</strong> Events scheduled for the future</li>
                        <li><strong>Ongoing:</strong> Events currently happening (between start and end time)</li>
                        <li><strong>Completed:</strong> Events that have finished</li>
                        <li><strong>Cancelled:</strong> Events that have been cancelled (can be set manually)</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-info-circle text-maroon"></i>
                    <span>Fields marked with <span class="required">*</span> are required</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('mhadel.events.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>Create Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate end date when start date changes
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        if (this.value && !endDateInput.value) {
            // Set end date to 2 hours after start date by default
            const startDate = new Date(this.value);
            const endDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000)); // Add 2 hours
            
            // Format for datetime-local input
            const endDateString = endDate.toISOString().slice(0, 16);
            endDateInput.value = endDateString;
        }
    });
    
    // Validate that end date is after start date
    endDateInput.addEventListener('change', function() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(this.value);
        
        if (endDate <= startDate) {
            alert('End date must be after start date');
            this.value = '';
        }
    });

    // Equipment selection functionality
    const venueSelect = document.getElementById('venue_id');
    const equipmentContainer = document.getElementById('equipment-container');
    const addEquipmentBtn = document.getElementById('add-equipment');
    let equipmentIndex = 0;

    // Venue data with equipment
    const venues = @json($venues);

    // Update equipment options when venue changes
    venueSelect.addEventListener('change', function() {
        const selectedVenueId = this.value;
        const selectedVenue = venues.find(venue => venue.id == selectedVenueId);
        
        // Clear existing equipment selections
        clearEquipmentSelections();
        
        if (selectedVenue && selectedVenue.available_equipment) {
            updateEquipmentOptions(selectedVenue.available_equipment);
        } else {
            updateEquipmentOptions([]);
        }
    });

    function clearEquipmentSelections() {
        const equipmentSelects = document.querySelectorAll('.equipment-select');
        const equipmentQuantities = document.querySelectorAll('.equipment-quantity');
        
        equipmentSelects.forEach(select => {
            select.innerHTML = '<option value="">Select equipment</option>';
            select.disabled = true;
        });
        
        equipmentQuantities.forEach(input => {
            input.value = '';
            input.disabled = true;
        });
    }

    function updateEquipmentOptions(equipment) {
        const equipmentSelects = document.querySelectorAll('.equipment-select');
        
        equipmentSelects.forEach(select => {
            select.innerHTML = '<option value="">Select equipment</option>';
            
            if (equipment && equipment.length > 0) {
                select.disabled = false;
                equipment.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.maxQuantity = item.quantity;
                    select.appendChild(option);
                });
            } else {
                select.disabled = true;
            }
        });
    }

    function updateEquipmentOptionsForNewSelect(selectElement) {
        const selectedVenueId = venueSelect.value;
        const selectedVenue = venues.find(venue => venue.id == selectedVenueId);
        
        selectElement.innerHTML = '<option value="">Select equipment</option>';
        
        if (selectedVenue && selectedVenue.available_equipment) {
            selectElement.disabled = false;
            
            // Get already selected equipment names
            const selectedEquipment = getSelectedEquipmentNames();
            
            selectedVenue.available_equipment.forEach(item => {
                // Only add equipment that hasn't been selected yet
                if (!selectedEquipment.includes(item.name)) {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.maxQuantity = item.quantity;
                    selectElement.appendChild(option);
                }
            });
        } else {
            selectElement.disabled = true;
        }
    }

    function getSelectedEquipmentNames() {
        const selectedEquipment = [];
        const equipmentSelects = document.querySelectorAll('.equipment-select');
        
        equipmentSelects.forEach(select => {
            if (select.value && select.value !== '') {
                selectedEquipment.push(select.value);
            }
        });
        
        return selectedEquipment;
    }

    function updateAllEquipmentDropdowns() {
        const selectedVenueId = venueSelect.value;
        const selectedVenue = venues.find(venue => venue.id == selectedVenueId);
        const selectedEquipment = getSelectedEquipmentNames();
        
        if (!selectedVenue || !selectedVenue.available_equipment) {
            return;
        }
        
        const equipmentSelects = document.querySelectorAll('.equipment-select');
        
        equipmentSelects.forEach(select => {
            const currentValue = select.value;
            const currentIndex = select.dataset.index;
            
            // Clear and rebuild options
            select.innerHTML = '<option value="">Select equipment</option>';
            
            selectedVenue.available_equipment.forEach(item => {
                // Check if this equipment is selected in other dropdowns
                const isSelectedElsewhere = selectedEquipment.includes(item.name) && item.name !== currentValue;
                
                if (!isSelectedElsewhere) {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = `${item.name} (Available: ${item.quantity})`;
                    option.dataset.maxQuantity = item.quantity;
                    
                    // Restore the current selection
                    if (item.name === currentValue) {
                        option.selected = true;
                    }
                    
                    select.appendChild(option);
                }
            });
        });
    }

    // Handle equipment selection change
    equipmentContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('equipment-select')) {
            const quantityInput = e.target.closest('.equipment-item').querySelector('.equipment-quantity');
            const maxQuantity = e.target.selectedOptions[0]?.dataset.maxQuantity;
            
            if (e.target.value && maxQuantity) {
                quantityInput.disabled = false;
                quantityInput.max = maxQuantity;
                quantityInput.placeholder = `Max: ${maxQuantity}`;
                
                // Clear any invalid quantity
                if (parseInt(quantityInput.value) > parseInt(maxQuantity)) {
                    quantityInput.value = '';
                }
            } else {
                quantityInput.disabled = true;
                quantityInput.value = '';
                quantityInput.placeholder = 'Quantity';
            }
            
            // Update other equipment dropdowns to remove/restore the selected equipment
            updateAllEquipmentDropdowns();
        }
    });

    // Handle quantity input validation
    equipmentContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('equipment-quantity')) {
            const equipmentSelect = e.target.closest('.equipment-item').querySelector('.equipment-select');
            const maxQuantity = equipmentSelect.selectedOptions[0]?.dataset.maxQuantity;
            const currentValue = parseInt(e.target.value);
            
            if (maxQuantity && currentValue > parseInt(maxQuantity)) {
                e.target.value = maxQuantity;
                showQuantityWarning(`Maximum quantity for ${equipmentSelect.value} is ${maxQuantity}`);
            }
        }
    });

    // Handle quantity input on blur (when user finishes typing)
    equipmentContainer.addEventListener('blur', function(e) {
        if (e.target.classList.contains('equipment-quantity')) {
            const equipmentSelect = e.target.closest('.equipment-item').querySelector('.equipment-select');
            const maxQuantity = equipmentSelect.selectedOptions[0]?.dataset.maxQuantity;
            const currentValue = parseInt(e.target.value);
            
            if (maxQuantity && currentValue > parseInt(maxQuantity)) {
                e.target.value = maxQuantity;
                showQuantityWarning(`Maximum quantity for ${equipmentSelect.value} is ${maxQuantity}`);
            }
        }
    });

    function showQuantityWarning(message) {
        // Remove any existing warning
        const existingWarning = document.querySelector('.quantity-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        // Create warning message
        const warning = document.createElement('div');
        warning.className = 'quantity-warning bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-2 rounded text-sm mt-2';
        warning.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${message}`;
        
        // Add warning to the equipment container
        equipmentContainer.appendChild(warning);
        
        // Remove warning after 3 seconds
        setTimeout(() => {
            if (warning.parentNode) {
                warning.remove();
            }
        }, 3000);
    }

    // Add new equipment row
    addEquipmentBtn.addEventListener('click', function() {
        equipmentIndex++;
        const equipmentItem = document.createElement('div');
        equipmentItem.className = 'equipment-item bg-gray-50 p-4 rounded-lg border border-gray-200 mb-3';
        equipmentItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label text-sm">Equipment Name</label>
                    <select name="equipment[${equipmentIndex}][name]" class="form-select equipment-select" data-index="${equipmentIndex}">
                        <option value="">Select equipment</option>
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm">Quantity</label>
                    <input 
                        type="number" 
                        name="equipment[${equipmentIndex}][quantity]" 
                        min="1" 
                        class="form-input equipment-quantity" 
                        placeholder="Quantity"
                        disabled
                    >
                </div>
            </div>
            <button type="button" class="remove-equipment mt-2 text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        `;
        
        equipmentContainer.appendChild(equipmentItem);
        
        // Update equipment options for the new select only (preserve existing selections)
        updateEquipmentOptionsForNewSelect(equipmentItem.querySelector('.equipment-select'));
        
        // Show remove buttons if there are multiple equipment items
        updateRemoveButtons();
    });

    // Remove equipment row
    equipmentContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-equipment')) {
            e.target.closest('.equipment-item').remove();
            updateRemoveButtons();
            
            // Update all equipment dropdowns to restore removed equipment options
            updateAllEquipmentDropdowns();
        }
    });

    function updateRemoveButtons() {
        const equipmentItems = document.querySelectorAll('.equipment-item');
        const removeButtons = document.querySelectorAll('.remove-equipment');
        
        removeButtons.forEach(button => {
            button.style.display = equipmentItems.length > 1 ? 'block' : 'none';
        });
    }

    // Initialize remove buttons visibility
    updateRemoveButtons();
});
</script>
@endsection
