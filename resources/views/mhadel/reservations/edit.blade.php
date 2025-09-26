@extends('layouts.mhadel')

@section('title', 'Edit Reservation')
@section('page-title', 'Edit Reservation')
@section('page-subtitle', 'Modify reservation details and schedule')

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
    
    .capacity-warning {
        animation: capacityPulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    @keyframes capacityPulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    
    .capacity-info {
        animation: fadeIn 0.3s ease-out;
    }
    
    .venue-suggestions {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .suggestion-item:hover {
        background-color: #f0fdf4 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
                        Edit Reservation
                    </h2>
                    <p class="text-sm text-gray-600 font-medium mt-1">Modify reservation details, schedule, and pricing</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('mhadel.reservations.show', $reservation->id) }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                        <i class="fas fa-eye"></i>
                        <span>View Details</span>
                    </a>
                    <a href="{{ route('mhadel.reservations.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('mhadel.reservations.update', $reservation->id) }}" method="POST" class="space-y-6">
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
                    <label for="event_title" class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                    <input type="text" id="event_title" name="event_title" 
                           value="{{ old('event_title', $reservation->event_title) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('event_title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                    <textarea id="purpose" name="purpose" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">{{ old('purpose', $reservation->purpose) }}</textarea>
                    @error('purpose')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Expected Attendees</label>
                    <input type="number" id="capacity" name="capacity" min="1"
                           value="{{ old('capacity', $reservation->capacity) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('capacity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Capacity Warning -->
                    <div id="capacityWarning" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-lg capacity-warning">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-red-800">
                                <p class="font-medium mb-1">⚠️ Capacity Exceeded!</p>
                                <p id="capacityWarningText" class="text-xs"><!-- Warning text will be populated here --></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Capacity Info -->
                    <div id="capacityInfo" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg capacity-info">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p id="capacityInfoText" class="text-xs"><!-- Capacity info will be populated here --></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" id="department" name="department"
                           value="{{ old('department', $reservation->department) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('department')
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
                                    {{ old('venue_id', $reservation->venue_id) == $venue->id ? 'selected' : '' }}
                                    data-price="{{ $venue->price_per_hour }}"
                                    data-capacity="{{ $venue->capacity }}">
                                {{ $venue->name }} - ₱{{ number_format($venue->price_per_hour, 2) }}/hour ({{ $venue->capacity }} capacity)
                            </option>
                        @endforeach
                    </select>
                    @error('venue_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Venue Suggestions -->
                    <div id="venueSuggestions" class="hidden mt-2 p-3 bg-green-50 border border-green-200 rounded-lg venue-suggestions">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-green-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-green-800">
                                <p class="font-medium mb-1">💡 Recommended Venues</p>
                                <p class="text-xs mb-2">Based on your expected attendees, these venues are suitable:</p>
                                <div id="suggestedVenuesList" class="space-y-1">
                                    <!-- Suggested venues will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" required
                           value="{{ old('start_date', $reservation->start_date ? $reservation->start_date->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" required
                           value="{{ old('end_date', $reservation->end_date ? $reservation->end_date->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                    <input type="number" id="duration_hours" name="duration_hours" min="0.5" step="0.5" readonly
                           value="{{ old('duration_hours', $reservation->duration_hours) }}"
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

        <!-- Pricing Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-tag mr-2 text-maroon"></i>
                Pricing & Discounts
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="input-group">
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 mb-2">Price per Hour</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                        <input type="number" id="price_per_hour" name="price_per_hour" min="0" step="0.01" readonly
                               value="{{ old('price_per_hour', $reservation->price_per_hour) }}"
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Based on selected venue</p>
                </div>
                
                <div class="input-group">
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                    <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01"
                           value="{{ old('discount_percentage', $reservation->discount_percentage ?? 0) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                    @error('discount_percentage')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="final_price" class="block text-sm font-medium text-gray-700 mb-2">Final Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                        <input type="number" id="final_price" name="final_price" min="0" step="0.01" readonly
                               value="{{ old('final_price', $reservation->final_price) }}"
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Automatically calculated</p>
                </div>
            </div>
        </div>

        <!-- Status & Notes Section -->
        <div class="form-section rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-clipboard-list mr-2 text-maroon"></i>
                Status & Notes
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">
                        <option value="pending" {{ $reservation->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_IOSA" {{ $reservation->status === 'approved_IOSA' ? 'selected' : '' }}>IOSA Approved</option>
                        <option value="approved_mhadel" {{ $reservation->status === 'approved_mhadel' ? 'selected' : '' }}>Approved by OTP</option>
                        <option value="approved_OTP" {{ $reservation->status === 'approved_OTP' ? 'selected' : '' }}>Approved by PPGS</option>
                        <option value="completed" {{ $reservation->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected_IOSA" {{ $reservation->status === 'rejected_IOSA' ? 'selected' : '' }}>Rejected by IOSA</option>
                        <option value="rejected_mhadel" {{ $reservation->status === 'rejected_mhadel' ? 'selected' : '' }}>Rejected by OTP</option>
                        <option value="rejected_OTP" {{ $reservation->status === 'rejected_OTP' ? 'selected' : '' }}>Rejected by PPGS</option>
                        <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              placeholder="Add any administrative notes or comments..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-all duration-200">{{ old('notes', $reservation->notes) }}</textarea>
                    @error('notes')
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
                <a href="{{ route('mhadel.reservations.show', $reservation->id) }}" 
                   class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="px-8 py-3 bg-maroon text-white rounded-lg hover:bg-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Reservation</span>
                </button>
            </div>
        </div>
    </form>
</div>

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

@push('scripts')
<script>
    // Global variables
    let currentVenueId = {{ $reservation->venue_id }};
    let currentStartDate = '{{ $reservation->start_date ? $reservation->start_date->format("Y-m-d\TH:i") : "" }}';
    let currentEndDate = '{{ $reservation->end_date ? $reservation->end_date->format("Y-m-d\TH:i") : "" }}';
    
    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        updatePricing();
        calculateDuration();
        
        // Add event listeners
        document.getElementById('venue_id').addEventListener('change', function() {
            updatePricing();
            validateCapacity();
        });
        document.getElementById('capacity').addEventListener('input', function() {
            validateCapacity();
            showVenueSuggestions();
        });
        document.getElementById('start_date').addEventListener('change', calculateDuration);
        document.getElementById('end_date').addEventListener('change', calculateDuration);
        document.getElementById('discount_percentage').addEventListener('input', updatePricing);
        
        // Initial capacity validation
        validateCapacity();
    });
    
    // Update pricing when venue or discount changes
    function updatePricing() {
        const venueSelect = document.getElementById('venue_id');
        const discountInput = document.getElementById('discount_percentage');
        const pricePerHourInput = document.getElementById('price_per_hour');
        const finalPriceInput = document.getElementById('final_price');
        
        if (venueSelect.value) {
            const selectedOption = venueSelect.options[venueSelect.selectedIndex];
            const pricePerHour = parseFloat(selectedOption.dataset.price);
            pricePerHourInput.value = pricePerHour.toFixed(2);
            
            // Calculate final price
            const duration = parseFloat(document.getElementById('duration_hours').value) || 0;
            const basePrice = pricePerHour * duration;
            const discount = parseFloat(discountInput.value) || 0;
            const finalPrice = basePrice * (1 - discount / 100);
            
            finalPriceInput.value = finalPrice.toFixed(2);
        } else {
            pricePerHourInput.value = '0.00';
            finalPriceInput.value = '0.00';
        }
    }
    
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
                updatePricing();
            }
        }
    }
    
    // Show venue suggestions based on capacity
    function showVenueSuggestions() {
        const capacityInput = document.getElementById('capacity');
        const venueSuggestions = document.getElementById('venueSuggestions');
        const suggestedVenuesList = document.getElementById('suggestedVenuesList');
        const venueSelect = document.getElementById('venue_id');
        
        const requestedCapacity = parseInt(capacityInput.value) || 0;
        
        if (requestedCapacity > 0) {
            // Get all venue options and filter suitable ones
            const venueOptions = Array.from(venueSelect.options).slice(1); // Skip first "Select a venue" option
            const suitableVenues = venueOptions.filter(option => {
                const venueCapacity = parseInt(option.dataset.capacity) || 0;
                return venueCapacity >= requestedCapacity;
            });
            
            if (suitableVenues.length > 0) {
                // Sort by capacity (ascending) to show most appropriate venues first
                suitableVenues.sort((a, b) => {
                    const capacityA = parseInt(a.dataset.capacity) || 0;
                    const capacityB = parseInt(b.dataset.capacity) || 0;
                    return capacityA - capacityB;
                });
                
                // Generate suggestion list
                let suggestionsHtml = '';
                suitableVenues.slice(0, 3).forEach(option => { // Show top 3 suggestions
                    const venueName = option.text.split(' - ')[0];
                    const venueCapacity = parseInt(option.dataset.capacity) || 0;
                    const pricePerHour = parseFloat(option.dataset.price) || 0;
                    const remainingCapacity = venueCapacity - requestedCapacity;
                    
                    suggestionsHtml += `
                        <div class="suggestion-item flex items-center justify-between p-2 bg-white rounded border border-green-200 transition-all duration-200 cursor-pointer" 
                             onclick="selectSuggestedVenue('${option.value}')">
                            <div class="flex-1">
                                <div class="font-medium text-green-800 text-xs">${venueName}</div>
                                <div class="text-xs text-green-600">
                                    Capacity: ${venueCapacity} | Price: ₱${pricePerHour.toFixed(2)}/hr | +${remainingCapacity} extra spaces
                                </div>
                            </div>
                            <button type="button" class="ml-2 px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition-colors">
                                Select
                            </button>
                        </div>
                    `;
                });
                
                suggestedVenuesList.innerHTML = suggestionsHtml;
                venueSuggestions.classList.remove('hidden');
            } else {
                // No suitable venues found
                suggestedVenuesList.innerHTML = `
                    <div class="p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        No venues can accommodate ${requestedCapacity} attendees. Consider reducing the number of attendees.
                    </div>
                `;
                venueSuggestions.classList.remove('hidden');
            }
        } else {
            // Hide suggestions when no capacity entered
            venueSuggestions.classList.add('hidden');
        }
    }
    
    // Select a suggested venue
    function selectSuggestedVenue(venueId) {
        const venueSelect = document.getElementById('venue_id');
        venueSelect.value = venueId;
        
        // Trigger change events to update pricing and validation
        venueSelect.dispatchEvent(new Event('change'));
        
        // Hide suggestions after selection
        document.getElementById('venueSuggestions').classList.add('hidden');
        
        // Show success feedback
        const successNotification = document.createElement('div');
        successNotification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300';
        successNotification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm">Venue selected!</span>
            </div>
        `;
        document.body.appendChild(successNotification);
        
        // Remove notification after 2 seconds
        setTimeout(() => {
            successNotification.remove();
        }, 2000);
    }
    
    // Validate capacity against venue capacity
    function validateCapacity() {
        const venueSelect = document.getElementById('venue_id');
        const capacityInput = document.getElementById('capacity');
        const capacityWarning = document.getElementById('capacityWarning');
        const capacityWarningText = document.getElementById('capacityWarningText');
        const capacityInfo = document.getElementById('capacityInfo');
        const capacityInfoText = document.getElementById('capacityInfoText');
        
        const requestedCapacity = parseInt(capacityInput.value) || 0;
        
        if (venueSelect.value && requestedCapacity > 0) {
            const selectedOption = venueSelect.options[venueSelect.selectedIndex];
            const venueCapacity = parseInt(selectedOption.dataset.capacity) || 0;
            const venueName = selectedOption.text.split(' - ')[0]; // Extract venue name
            
            // Hide both info boxes first
            capacityWarning.classList.add('hidden');
            capacityInfo.classList.add('hidden');
            
            if (requestedCapacity > venueCapacity) {
                // Show warning for overcapacity
                capacityWarningText.textContent = `You are requesting ${requestedCapacity} attendees, but ${venueName} can only accommodate ${venueCapacity} people. Please reduce the number of attendees or select a larger venue.`;
                capacityWarning.classList.remove('hidden');
                
                // Add red border to capacity input
                capacityInput.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                capacityInput.classList.remove('border-gray-300', 'focus:ring-maroon', 'focus:border-maroon');
            } else {
                // Show info for acceptable capacity
                const remainingCapacity = venueCapacity - requestedCapacity;
                capacityInfoText.textContent = `${venueName} can accommodate ${venueCapacity} people. You have ${remainingCapacity} additional spaces available.`;
                capacityInfo.classList.remove('hidden');
                
                // Reset input styling
                capacityInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                capacityInput.classList.add('border-gray-300', 'focus:ring-maroon', 'focus:border-maroon');
            }
        } else {
            // Hide both info boxes when no venue selected or no capacity entered
            capacityWarning.classList.add('hidden');
            capacityInfo.classList.add('hidden');
            
            // Reset input styling
            capacityInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            capacityInput.classList.add('border-gray-300', 'focus:ring-maroon', 'focus:border-maroon');
        }
    }
    
    // Check for schedule conflicts
    function checkForConflicts() {
        const venueId = document.getElementById('venue_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const reservationId = {{ $reservation->id }};
        
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
        fetch(`/mhadel/reservations/${reservationId}/check-conflicts`, {
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
        document.getElementById('event_title').value = '{{ $reservation->event_title }}';
        document.getElementById('purpose').value = '{{ $reservation->purpose }}';
        document.getElementById('capacity').value = '{{ $reservation->capacity }}';
        document.getElementById('department').value = '{{ $reservation->department }}';
        document.getElementById('venue_id').value = '{{ $reservation->venue_id }}';
        document.getElementById('start_date').value = '{{ $reservation->start_date ? $reservation->start_date->format("Y-m-d\TH:i") : "" }}';
        document.getElementById('end_date').value = '{{ $reservation->end_date ? $reservation->end_date->format("Y-m-d\TH:i") : "" }}';
        document.getElementById('discount_percentage').value = '{{ $reservation->discount_percentage ?? 0 }}';
        document.getElementById('status').value = '{{ $reservation->status }}';
        document.getElementById('notes').value = '{{ $reservation->notes }}';
        
        // Hide conflict warning
        document.getElementById('conflictWarning').classList.add('hidden');
        
        // Update pricing and duration
        updatePricing();
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
        
        // Check capacity validation
        const venueSelect = document.getElementById('venue_id');
        const capacityInput = document.getElementById('capacity');
        const requestedCapacity = parseInt(capacityInput.value) || 0;
        
        if (venueSelect.value && requestedCapacity > 0) {
            const selectedOption = venueSelect.options[venueSelect.selectedIndex];
            const venueCapacity = parseInt(selectedOption.dataset.capacity) || 0;
            
            if (requestedCapacity > venueCapacity) {
                // Show warning but allow submission
                const proceed = confirm(`⚠️ Warning: The requested capacity (${requestedCapacity}) exceeds the venue capacity (${venueCapacity}). This reservation will be flagged as overcapacity. Do you want to proceed?`);
                if (!proceed) {
                    e.preventDefault();
                    capacityInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    capacityInput.focus();
                    return false;
                }
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
@endsection 
