@extends('layouts.mhadel')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')
@section('page-subtitle', 'Modify event details and schedule')

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    
    .form-section {
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .input-group {
        transition: all 0.3s ease;
    }
    
    .input-group:focus-within {
        transform: translateY(-1px);
    }
    
    .conflict-warning {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>

<div class="space-y-6 font-inter">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-poppins flex items-center">
                        <i class="fas fa-edit mr-3 text-maroon"></i>
                        Edit Event
                    </h2>
                    <p class="text-sm text-gray-600 font-medium mt-1">Modify event details, schedule, and venue</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('mhadel.events.show', $event->id) }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                        <i class="fas fa-eye"></i>
                        <span>View Details</span>
                    </a>
                    <a href="{{ route('mhadel.events.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('mhadel.events.update', $event->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-maroon"></i>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                    <input type="text" id="title" name="title" 
                           value="{{ old('title', $event->title) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Organizer *</label>
                    <input type="text" id="organizer" name="organizer" 
                           value="{{ old('organizer', $event->organizer) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('organizer')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" id="department" name="department"
                           value="{{ old('department', $event->department) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('department')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                    <input type="number" id="max_participants" name="max_participants" min="1"
                           value="{{ old('max_participants', $event->max_participants) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('max_participants')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <div class="input-group">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Provide a detailed description of the event..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">{{ old('description', $event->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Schedule Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-maroon"></i>
                Schedule & Venue
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group">
                    <label for="venue_id" class="block text-sm font-medium text-gray-700 mb-2">Venue *</label>
                    <select id="venue_id" name="venue_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                        <option value="">Select a venue</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}" 
                                    {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
                                {{ $venue->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('venue_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" required
                           value="{{ old('start_date', $event->start_date ? $event->start_date->format("Y-m-d\TH:i") : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" required
                           value="{{ old('end_date', $event->end_date ? $event->end_date->format("Y-m-d\TH:i") : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                    <input type="number" id="duration_hours" name="duration_hours" min="0.5" step="0.5" readonly
                           value="{{ old('duration_hours', $event->duration_hours ?? 0) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Automatically calculated from start and end times</p>
                </div>
            </div>
            
            <!-- Conflict Warning -->
            <div id="conflictWarning" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg conflict-warning">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2 mt-0.5"></i>
                    <div class="text-sm text-red-800">
                        <p class="font-medium mb-1">⚠️ Schedule Conflict Detected!</p>
                        <div id="conflictDetails" class="text-xs space-y-1">
                            <!-- Conflict details will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Selection Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-tools mr-2 text-maroon"></i>
                Equipment Selection
            </h3>
            
            <div id="equipment-container">
                @if($event->equipment_details && count($event->equipment_details) > 0)
                    @foreach($event->equipment_details as $index => $equipment)
                        <div class="equipment-item bg-gray-50 p-4 rounded-lg border border-gray-200 mb-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Name</label>
                                    <select name="equipment[{{ $index }}][name]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 equipment-select" data-index="{{ $index }}">
                                        <option value="">Select equipment</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                    <input 
                                        type="number" 
                                        name="equipment[{{ $index }}][quantity]" 
                                        min="1" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 equipment-quantity" 
                                        placeholder="Quantity"
                                        value="{{ $equipment['quantity'] ?? '' }}"
                                    >
                                </div>
                            </div>
                            <button type="button" class="remove-equipment mt-2 text-red-600 hover:text-red-800 text-sm" style="display: {{ count($event->equipment_details) > 1 ? 'block' : 'none' }};">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="equipment-item bg-gray-50 p-4 rounded-lg border border-gray-200 mb-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Name</label>
                                <select name="equipment[0][name]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 equipment-select" data-index="0">
                                    <option value="">Select equipment</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input 
                                    type="number" 
                                    name="equipment[0][quantity]" 
                                    min="1" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200 equipment-quantity" 
                                    placeholder="Quantity"
                                    disabled
                                >
                            </div>
                        </div>
                        <button type="button" class="remove-equipment mt-2 text-red-600 hover:text-red-800 text-sm" style="display: none;">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                @endif
            </div>
            
            <button type="button" id="add-equipment" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Add Equipment</span>
            </button>
            
            <p class="text-xs text-gray-500 mt-2">Select equipment available at the chosen venue. Equipment options will update when you select a venue.</p>
        </div>

        <!-- Status Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clipboard-list mr-2 text-maroon"></i>
                Status & Management
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                        <option value="upcoming" {{ $event->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ $event->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                    <select id="event_type" name="event_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                        <option value="academic" {{ ($event->event_type ?? '') === 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="administrative" {{ ($event->event_type ?? '') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                        <option value="student_activity" {{ ($event->event_type ?? '') === 'student_activity' ? 'selected' : '' }}>Student Activity</option>
                        <option value="community_service" {{ ($event->event_type ?? '') === 'community_service' ? 'selected' : '' }}>Community Service</option>
                        <option value="other" {{ ($event->event_type ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('event_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6">
            <div class="flex items-center space-x-3">
                <button type="button" onclick="checkForConflicts()" 
                        class="px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Check Conflicts</span>
                </button>
                <button type="button" onclick="resetForm()" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-undo"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('mhadel.events.show', $event->id) }}" 
                   class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="px-8 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Event</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Global variables
    let currentVenueId = {{ $event->venue_id }};
    let currentStartDate = '{{ $event->start_date ? $event->start_date->format("Y-m-d\TH:i") : "" }}';
    let currentEndDate = '{{ $event->end_date ? $event->end_date->format("Y-m-d\TH:i") : "" }}';
    
    // Equipment functionality variables
    const equipmentContainer = document.getElementById('equipment-container');
    const addEquipmentBtn = document.getElementById('add-equipment');
    const venueSelect = document.getElementById('venue_id');
    let equipmentIndex = {{ $event->equipment_details ? count($event->equipment_details) - 1 : 0 }};
    
    // Venue data with equipment
    const venues = @json($venues);
    
    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        calculateDuration();
        
        // Add event listeners
        document.getElementById('start_date').addEventListener('change', calculateDuration);
        document.getElementById('end_date').addEventListener('change', calculateDuration);
        
        // Initialize equipment functionality
        initializeEquipment();
    });
    
    // Calculate duration from start and end times
    function calculateDuration() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const durationInput = document.getElementById('duration_hours');
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            if (diffHours > 0) {
                durationInput.value = diffHours.toFixed(1);
            }
        }
    }
    
    // Check for schedule conflicts
    function checkForConflicts() {
        const venueId = document.getElementById('venue_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const eventId = {{ $event->id }};
        
        if (!venueId || !startDate || !endDate) {
            alert('Please fill in venue and schedule details first.');
            return;
        }
        
        // Show loading state
        const checkBtn = document.querySelector('button[onclick="checkForConflicts()"]');
        const originalText = checkBtn.innerHTML;
        checkBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
        checkBtn.disabled = true;
        
        // Send conflict check request
        fetch(`/mhadel/events/${eventId}/check-conflicts`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                venue_id: venueId,
                start_datetime: startDate,
                end_datetime: endDate
            })
        })
        .then(response => response.json())
        .then(data => {
            const conflictsModal = document.getElementById('conflictsModal');
            const conflictsModalTitle = document.getElementById('conflictsModalTitle');
            const conflictsModalContent = document.getElementById('conflictsModalContent');
            
            if (data.conflicts && data.conflicts.length > 0) {
                // Show conflicts
                conflictsModalTitle.textContent = '⚠️ Schedule Conflicts Found';
                let contentHtml = `
                    <div class="space-y-4">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-3 mt-1 text-xl"></i>
                                <div class="text-sm text-red-800">
                                    <p class="font-medium mb-2">The following schedule conflicts were detected:</p>
                                    <div class="space-y-2">`;
                
                data.conflicts.forEach(conflict => {
                    contentHtml += `
                        <div class="bg-white p-3 rounded border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${
                                        conflict.type === 'Event' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'
                                    } mb-1">${conflict.type}</span>
                                    <p class="font-medium text-gray-800">${conflict.title}</p>
                                    <p class="text-sm text-gray-600">${conflict.start} - ${conflict.end}</p>
                                </div>
                            </div>
                        </div>`;
                });
                
                contentHtml += `
                                    </div>
                                    <p class="mt-3 text-xs text-red-700 font-medium">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Please choose a different time or venue to avoid conflicts.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>`;
                
                conflictsModalContent.innerHTML = contentHtml;
            } else {
                // No conflicts - show success message
                conflictsModalTitle.textContent = '✅ No Conflicts Found';
                conflictsModalContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-3xl text-green-600"></i>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">Schedule is Available!</h4>
                        <p class="text-gray-600 mb-4">The selected time slot and venue are free from conflicts.</p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-center space-x-4 text-sm text-green-800">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span>${new Date(startDate).toLocaleDateString()}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span>${new Date(startDate).toLocaleTimeString([], {hour:'numeric',minute:'2-digit'})} - ${new Date(endDate).toLocaleTimeString([], {hour:'numeric',minute:'2-digit'})}</span>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }
            
            // Show the modal
            conflictsModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while checking for conflicts.');
        })
        .finally(() => {
            // Reset button state
            checkBtn.innerHTML = originalText;
            checkBtn.disabled = false;
        });
    }
    
    // Reset form to original values
    function resetForm() {
        // Show reset confirmation modal
        const resetModal = document.getElementById('resetModal');
        resetModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Confirm reset action
    function confirmReset() {
        document.getElementById('title').value = '{{ $event->title }}';
        document.getElementById('organizer').value = '{{ $event->organizer }}';
        document.getElementById('department').value = '{{ $event->department }}';
        document.getElementById('max_participants').value = '{{ $event->max_participants }}';
        document.getElementById('description').value = '{{ $event->description }}';
        document.getElementById('venue_id').value = '{{ $event->venue_id }}';
        document.getElementById('start_date').value = '{{ $event->start_date ? $event->start_date->format("Y-m-d\TH:i") : "" }}';
        document.getElementById('end_date').value = '{{ $event->end_date ? $event->end_date->format("Y-m-d\TH:i") : "" }}';
        document.getElementById('status').value = '{{ $event->status }}';
        document.getElementById('event_type').value = '{{ $event->event_type ?? "" }}';
        
        // Hide conflict warning
        document.getElementById('conflictWarning').classList.add('hidden');
        
        // Update duration
        calculateDuration();
        
        // Close modal and show success message
        closeResetModal();
        
        // Show success notification
        const successNotification = document.createElement('div');
        successNotification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        successNotification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span>Form has been reset to original values!</span>
            </div>
        `;
        document.body.appendChild(successNotification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            successNotification.remove();
        }, 3000);
    }
    
    // Close modals
    function closeConflictsModal() {
        const conflictsModal = document.getElementById('conflictsModal');
        conflictsModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function closeResetModal() {
        const resetModal = document.getElementById('resetModal');
        resetModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end <= start) {
                e.preventDefault();
                alert('End date must be after start date.');
                return false;
            }
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;
    });
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target === document.getElementById('conflictsModal')) {
            closeConflictsModal();
        }
        if (e.target === document.getElementById('resetModal')) {
            closeResetModal();
        }
    });
    
    // Close modals when pressing Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('conflictsModal').classList.contains('hidden')) {
                closeConflictsModal();
            }
            if (!document.getElementById('resetModal').classList.contains('hidden')) {
                closeResetModal();
            }
        }
    });
</script>
@endpush

<!-- Check Conflicts Results Modal -->
<div id="conflictsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full font-poppins">
            <div class="p-6 border-b border-gray-200 bg-maroon">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white font-montserrat">
                        <i class="fas fa-search mr-2 text-white"></i>
                        <span id="conflictsModalTitle">Schedule Conflict Check</span>
                    </h3>
                    <button onclick="closeConflictsModal()" class="text-white hover:text-gray-200 bg-white bg-opacity-20 rounded-full p-2 hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="conflictsModalContent">
                    <!-- Content will be populated here -->
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button onclick="closeConflictsModal()" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins">
            <div class="p-6 border-b border-gray-200 bg-maroon">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white font-montserrat">
                        <i class="fas fa-exclamation-triangle mr-2 text-white"></i>
                        Confirm Reset
                    </h3>
                    <button onclick="closeResetModal()" class="text-amber-400 hover:text-white bg-white rounded-full p-2 hover:bg-amber-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-amber-500 mr-3 mt-1 text-xl"></i>
                    <div>
                        <p class="text-gray-800 font-medium mb-2">Are you sure you want to reset all changes?</p>
                        <p class="text-sm text-gray-600">This will restore all fields to their original values and cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeResetModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmReset()" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-red-800 transition-colors">
                    Reset All Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection 
