@extends('layouts.user')

@section('title', 'Edit Reservation')
@section('page-title', 'Edit Reservation')

@section('header-actions')
<a href="{{ route('user.reservations.index') }}" class="font-montserrat font-bold px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all duration-300 flex items-center space-x-2 shadow-md">
    <i class="fas fa-arrow-left text-lg"></i>
    <span>Back to Reservations</span>
</a>
@endsection

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
    .font-montserrat {
        font-family: 'Montserrat', sans-serif;
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="font-poppins animate-fadeIn">
    <!-- Edit Reservation Form -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-edit text-maroon mr-3"></i>
                    Edit Reservation: {{ $reservation->event_title }}
                </h2>
                <div class="text-sm text-gray-600 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Status: {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                </div>
            </div>
        </div>
        
        <form action="{{ route('user.reservations.update', $reservation->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Event Title -->
                    <div>
                        <label for="event_title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-heading text-maroon mr-2"></i>
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="event_title" name="event_title" value="{{ old('event_title', $reservation->event_title) }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        @error('event_title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-align-left text-maroon mr-2"></i>
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <textarea id="purpose" name="purpose" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">{{ old('purpose', $reservation->purpose) }}</textarea>
                        @error('purpose')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-building text-maroon mr-2"></i>
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select id="department" name="department" required onchange="toggleOtherDepartment()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            <option value="">Select Department</option>
                            <option value="ECE" {{ (old('department', $reservation->department) == 'ECE') ? 'selected' : '' }}>ECE</option>
                            <option value="JHS" {{ (old('department', $reservation->department) == 'JHS') ? 'selected' : '' }}>JHS</option>
                            <option value="SHS" {{ (old('department', $reservation->department) == 'SHS') ? 'selected' : '' }}>SHS</option>
                            <option value="BSIT" {{ (old('department', $reservation->department) == 'BSIT') ? 'selected' : '' }}>BSIT</option>
                            <option value="BSENT" {{ (old('department', $reservation->department) == 'BSENT') ? 'selected' : '' }}>BSENT</option>
                            <option value="BSP" {{ (old('department', $reservation->department) == 'BSP') ? 'selected' : '' }}>BSP</option>
                            <option value="BSBA" {{ (old('department', $reservation->department) == 'BSBA') ? 'selected' : '' }}>BSBA</option>
                            <option value="BSA" {{ (old('department', $reservation->department) == 'BSA') ? 'selected' : '' }}>BSA</option>
                            <option value="TED" {{ (old('department', $reservation->department) == 'TED') ? 'selected' : '' }}>TED</option>
                            <option value="Other" {{ (old('department', $reservation->department) == 'Other' || !in_array($reservation->department, ['ECE', 'JHS', 'SHS', 'BSIT', 'BSENT', 'BSP', 'BSBA', 'BSA', 'TED'])) ? 'selected' : '' }}>Other</option>
                        </select>
                        
                        <!-- Other Department Input -->
                        <div id="other_department_container" class="mt-3 {{ (old('department', $reservation->department) == 'Other' || !in_array($reservation->department, ['ECE', 'JHS', 'SHS', 'BSIT', 'BSENT', 'BSP', 'BSBA', 'BSA', 'TED'])) ? '' : 'hidden' }}">
                            <label for="other_department" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-edit text-maroon mr-2"></i>
                                Specify Other Department <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="other_department" name="other_department" 
                                   value="{{ (old('department', $reservation->department) == 'Other' || !in_array($reservation->department, ['ECE', 'JHS', 'SHS', 'BSIT', 'BSENT', 'BSP', 'BSBA', 'BSA', 'TED'])) ? old('other_department', $reservation->department) : '' }}"
                                   placeholder="Enter department name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        </div>
                        @error('department')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-users text-maroon mr-2"></i>
                            Expected Capacity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="capacity" name="capacity" min="1" value="{{ old('capacity', $reservation->capacity) }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        @error('capacity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-hourglass-start text-maroon mr-2"></i>
                                Start Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="start_date" name="start_date" required
                                   value="{{ old('start_date', $reservation->start_date ? $reservation->start_date->format('Y-m-d\TH:i') : '') }}"
                                   min="{{ now()->addDays(3)->format('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-hourglass-end text-maroon mr-2"></i>
                                End Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="end_date" name="end_date" required
                                   value="{{ old('end_date', $reservation->end_date ? $reservation->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Venue (Auto-selected based on capacity) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                            Venue <span class="text-red-500">*</span>
                        </label>
                        <div id="venue_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                            @if($reservation->venue)
                                <i class="fas fa-check-circle mr-2 text-green-500"></i> {{ $reservation->venue->name }} <span class="text-sm text-gray-500">(Capacity: {{ $reservation->venue->capacity }})</span>
                            @else
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first
                            @endif
                        </div>
                        <input type="hidden" id="venue_id" name="venue_id" value="{{ old('venue_id', $reservation->venue_id) }}">
                        <p class="text-xs text-gray-500 mt-1">Venue will be automatically selected based on your capacity requirement</p>
                    </div>

                    <!-- Price Rate (Auto-calculated) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag text-maroon mr-2"></i>
                            Rate per Hour <span class="text-red-500">*</span>
                        </label>
                        <div id="price_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                            @if($reservation->venue)
                                <i class="fas fa-check-circle mr-2 text-green-500"></i> ₱{{ number_format($reservation->venue->price_per_hour ?? 0) }} <span class="text-sm text-gray-500">per hour</span>
                            @else
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first
                            @endif
                        </div>
                        <input type="hidden" id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour', $reservation->venue->price_per_hour ?? 0) }}">
                        <p class="text-xs text-gray-500 mt-1">Rate will be automatically calculated based on venue selection</p>
                    </div>

                    <!-- Equipment Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tools text-maroon mr-2"></i>
                            Equipment to Borrow
                        </label>
                        <div id="equipment_container" class="space-y-4">
                            @if($reservation->venue && $reservation->venue->available_equipment)
                                @foreach($reservation->venue->available_equipment as $equipment)
                                    @php
                                        $equipmentId = strtolower(str_replace(' ', '_', $equipment['name']));
                                        $maxQuantity = $equipment['quantity'] ?? 1;
                                        $currentQuantity = $reservation->equipment[$equipment['name']] ?? 0;
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="equipment_{{ $equipmentId }}" name="equipment[]" value="{{ $equipment['name'] }}" 
                                                       {{ $currentQuantity > 0 ? 'checked' : '' }}
                                                       class="w-4 h-4 text-maroon border-gray-300 rounded focus:ring-maroon">
                                                <label for="equipment_{{ $equipmentId }}" class="ml-2 text-sm font-medium text-gray-700">{{ $equipment['name'] }}</label>
                                            </div>
                                            <span class="text-xs text-gray-500">Available: <span id="available_{{ $equipmentId }}">{{ $maxQuantity }}</span></span>
                                        </div>
                                        <div id="{{ $equipmentId }}_quantity_container" class="{{ $currentQuantity > 0 ? '' : 'hidden' }} ml-6">
                                            <div class="flex items-center space-x-2">
                                                <label class="text-xs text-gray-600">Quantity:</label>
                                                <input type="number" id="{{ $equipmentId }}_quantity" name="equipment_quantity[{{ $equipment['name'] }}]" 
                                                       min="1" max="{{ $maxQuantity }}" value="{{ $currentQuantity ?: 1 }}" 
                                                       class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-maroon focus:border-maroon">
                                                <span class="text-xs text-gray-500">/ {{ $maxQuantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-gray-500 py-4">No equipment available for this venue</div>
                            @endif
                        </div>
                        
                        <!-- No Equipment Needed -->
                        <div class="border border-gray-200 rounded-lg p-3 mt-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="equipment_none" name="equipment[]" value="none" 
                                       {{ empty($reservation->equipment) ? 'checked' : '' }}
                                       class="w-4 h-4 text-maroon border-gray-300 rounded focus:ring-maroon">
                                <label for="equipment_none" class="ml-2 text-sm font-medium text-gray-700">No Equipment Needed</label>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-2">Select equipment and specify quantities. Quantities cannot exceed available amounts.</p>
                    </div>

                    <!-- Base Price Calculation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calculator text-maroon mr-2"></i>
                            Base Price <span class="text-red-500">*</span>
                        </label>
                        <div id="base_price_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                            @if($reservation->base_price)
                                <i class="fas fa-check-circle mr-2 text-green-500"></i> ₱{{ number_format($reservation->base_price) }} <span class="text-sm text-gray-500">(Total for {{ $reservation->start_date && $reservation->end_date ? $reservation->start_date->diffInHours($reservation->end_date) : 0 }} hours)</span>
                            @else
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity and dates first
                            @endif
                        </div>
                        <input type="hidden" id="base_price" name="base_price" value="{{ old('base_price', $reservation->base_price) }}">
                        <div id="price_breakdown" class="mt-2 text-xs text-gray-500 {{ $reservation->base_price ? '' : 'hidden' }}">
                            <div class="space-y-1">
                                <div id="duration_info">Duration: {{ $reservation->start_date && $reservation->end_date ? $reservation->start_date->diffInHours($reservation->end_date) : 0 }} hours</div>
                                <div id="rate_info">Rate: ₱{{ number_format($reservation->venue->price_per_hour ?? 0) }} per hour</div>
                                <div id="total_info">Total: ₱{{ number_format($reservation->base_price) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Grid (Current file info) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-file-upload text-maroon mr-2"></i>
                            Current Activity Grid
                        </label>
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            @if($reservation->activity_grid)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                        <span class="text-sm text-gray-700">Current file uploaded</span>
                                    </div>
                                    <a href="{{ Storage::url($reservation->activity_grid) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            @else
                                <div class="text-gray-500 text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>No activity grid file uploaded
                                </div>
                            @endif
                        </div>
                        
                        <!-- New Activity Grid Upload (Optional) -->
                        <div class="mt-3">
                            <label for="activity_grid" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload New Activity Grid (Optional)
                            </label>
                            <input type="file" id="activity_grid" name="activity_grid" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            <p class="text-xs text-gray-500 mt-1">Leave empty to keep the current file. Max: 10MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('user.reservations.show', $reservation->id) }}" 
                   class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-maroon text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Reservation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col items-end"></div>
@endsection

@push('scripts')
<script>
// Auto-select venue based on capacity
const venuesData = [
    @foreach($venues as $venue)
        @if($venue && is_object($venue) && isset($venue->id))
            {
                id: {{ $venue->id }}, 
                name: "{{ addslashes($venue->name) }}", 
                capacity: {{ $venue->capacity }}, 
                price_per_hour: {{ $venue->price_per_hour ?? 0 }},
                available_equipment: @json($venue->available_equipment ?? [])
            },
        @endif
    @endforeach
];

document.addEventListener('DOMContentLoaded', function() {
    // Initial calculation
    calculateBasePrice();
    
    // Add event listeners
    document.getElementById('capacity').addEventListener('input', autoSelectVenue);
    document.getElementById('start_date').addEventListener('change', calculateBasePrice);
    document.getElementById('end_date').addEventListener('change', calculateBasePrice);
    
    // Equipment event listeners
    attachEquipmentEventListeners();
});

function autoSelectVenue() {
    const capacity = parseInt(document.getElementById('capacity').value) || 0;
    const venueDisplay = document.getElementById('venue_display');
    const venueIdInput = document.getElementById('venue_id');
    const priceDisplay = document.getElementById('price_display');
    const priceInput = document.getElementById('price_per_hour');
    
    if (capacity <= 0) {
        venueDisplay.innerHTML = '<i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first';
        priceDisplay.innerHTML = '<i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first';
        venueIdInput.value = '';
        priceInput.value = '';
        return;
    }
    
    // Find venues that can accommodate the capacity
    const suitableVenues = venuesData.filter(venue => venue.capacity >= capacity);
    
    if (suitableVenues.length === 0) {
        venueDisplay.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> No venue available for this capacity';
        priceDisplay.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> No venue available for this capacity';
        venueIdInput.value = '';
        priceInput.value = '';
        return;
    }
    
    // Select the venue with the closest capacity
    const selectedVenue = suitableVenues.reduce((closest, current) => {
        const closestDiff = closest.capacity - capacity;
        const currentDiff = current.capacity - capacity;
        return currentDiff < closestDiff ? current : closest;
    });
    
    venueDisplay.innerHTML = `<i class="fas fa-check-circle mr-2 text-green-500"></i> ${selectedVenue.name} <span class="text-sm text-gray-500">(Capacity: ${selectedVenue.capacity})</span>`;
    venueIdInput.value = selectedVenue.id;
    
    // Update price display
    const price = selectedVenue.price_per_hour || 0;
    priceDisplay.innerHTML = `<i class="fas fa-check-circle mr-2 text-green-500"></i> ₱${price.toLocaleString()} <span class="text-sm text-gray-500">per hour</span>`;
    priceInput.value = price;
    
    // Calculate base price after venue selection
    calculateBasePrice();
    
    // Generate equipment options for the selected venue
    generateEquipmentOptions(selectedVenue);
}

function calculateBasePrice() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const pricePerHour = parseFloat(document.getElementById('price_per_hour').value) || 0;
    const basePriceDisplay = document.getElementById('base_price_display');
    const basePriceInput = document.getElementById('base_price');
    const priceBreakdown = document.getElementById('price_breakdown');
    
    if (!startDate || !endDate || pricePerHour <= 0) {
        basePriceDisplay.innerHTML = '<i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity and dates first';
        basePriceInput.value = '';
        priceBreakdown.classList.add('hidden');
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (end <= start) {
        basePriceDisplay.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> End time must be after start time';
        basePriceInput.value = '';
        priceBreakdown.classList.add('hidden');
        return;
    }
    
    // Calculate duration in hours (rounded up to nearest hour)
    const durationMs = end - start;
    const durationHours = Math.max(1, Math.ceil(durationMs / (1000 * 60 * 60)));
    
    // Calculate base price
    const basePrice = durationHours * pricePerHour;
    
    // Update display
    basePriceDisplay.innerHTML = `<i class="fas fa-check-circle mr-2 text-green-500"></i> ₱${basePrice.toLocaleString()} <span class="text-sm text-gray-500">(Total for ${durationHours} hour${durationHours > 1 ? 's' : ''})</span>`;
    basePriceInput.value = basePrice;
    
    // Show price breakdown
    priceBreakdown.classList.remove('hidden');
    document.getElementById('duration_info').textContent = `Duration: ${durationHours} hour${durationHours > 1 ? 's' : ''}`;
    document.getElementById('rate_info').textContent = `Rate: ₱${pricePerHour.toLocaleString()} per hour`;
    document.getElementById('total_info').textContent = `Total: ₱${basePrice.toLocaleString()}`;
}

function toggleOtherDepartment() {
    const departmentSelect = document.getElementById('department');
    const otherContainer = document.getElementById('other_department_container');
    const otherInput = document.getElementById('other_department');
    
    if (departmentSelect.value === 'Other') {
        otherContainer.classList.remove('hidden');
        otherInput.required = true;
        otherInput.focus();
    } else {
        otherContainer.classList.add('hidden');
        otherInput.required = false;
        otherInput.value = '';
    }
}

function attachEquipmentEventListeners() {
    const equipmentCheckboxes = document.querySelectorAll('input[name="equipment[]"]');
    equipmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // If "No Equipment Needed" is selected, uncheck others and hide quantity containers
                if (this.value === 'none') {
                    equipmentCheckboxes.forEach(cb => {
                        if (cb !== this) {
                            cb.checked = false;
                            // Hide quantity container for unchecked equipment
                            const containerId = cb.value.toLowerCase().replace(/\s+/g, '_') + '_quantity_container';
                            const container = document.getElementById(containerId);
                            if (container) container.classList.add('hidden');
                        }
                    });
                } else {
                    // If other equipment is selected, uncheck "No Equipment Needed"
                    const noEquipmentCheckbox = document.getElementById('equipment_none');
                    if (noEquipmentCheckbox) noEquipmentCheckbox.checked = false;
                    
                    // Show quantity container for selected equipment
                    const containerId = this.value.toLowerCase().replace(/\s+/g, '_') + '_quantity_container';
                    const container = document.getElementById(containerId);
                    if (container) container.classList.remove('hidden');
                }
            } else {
                // If equipment is unchecked, hide its quantity container
                const containerId = this.value.toLowerCase().replace(/\s+/g, '_') + '_quantity_container';
                const container = document.getElementById(containerId);
                if (container) container.classList.add('hidden');
            }
        });
    });
}

function generateEquipmentOptions(venue) {
    const equipmentContainer = document.getElementById('equipment_container');
    const availableEquipment = venue.available_equipment || [];
    
    if (availableEquipment.length === 0) {
        equipmentContainer.innerHTML = '<div class="text-center text-gray-500 py-4">No equipment available for this venue</div>';
        return;
    }
    
    let equipmentHTML = '';
    
    availableEquipment.forEach((equipment, index) => {
        const equipmentId = equipment.name.toLowerCase().replace(/\s+/g, '_');
        const maxQuantity = equipment.quantity || 1;
        
        equipmentHTML += `
            <div class="border border-gray-200 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="equipment_${equipmentId}" name="equipment[]" value="${equipment.name}" class="w-4 h-4 text-maroon border-gray-300 rounded focus:ring-maroon">
                        <label for="equipment_${equipmentId}" class="ml-2 text-sm font-medium text-gray-700">${equipment.name}</label>
                    </div>
                    <span class="text-xs text-gray-500">Available: <span id="available_${equipmentId}">${maxQuantity}</span></span>
                </div>
                <div id="${equipmentId}_quantity_container" class="hidden ml-6">
                    <div class="flex items-center space-x-2">
                        <label class="text-xs text-gray-600">Quantity:</label>
                        <input type="number" id="${equipmentId}_quantity" name="equipment_quantity[${equipment.name}]" min="1" max="${maxQuantity}" value="1" class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-maroon focus:border-maroon">
                        <span class="text-xs text-gray-500">/ ${maxQuantity}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    equipmentContainer.innerHTML = equipmentHTML;
    
    // Re-attach event listeners for the new equipment checkboxes
    attachEquipmentEventListeners();
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const capacity = parseInt(document.getElementById('capacity').value);
    const venueIdInput = document.getElementById('venue_id');
    
    // 3 days in advance restriction
    const now = new Date();
    const minDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 3);
    if (startDate < minDate) {
        e.preventDefault();
        showToast('Reservations must be made at least 3 days in advance.', 'error');
        return;
    }
    
    if (startDate <= now) {
        e.preventDefault();
        showToast('Start date must be in the future.', 'error');
        return;
    }
    
    if (endDate <= startDate) {
        e.preventDefault();
        showToast('End date must be after start date.', 'error');
        return;
    }
    
    if (!venueIdInput.value) {
        e.preventDefault();
        showToast('Please enter a capacity to automatically select a venue.', 'error');
        return;
    }
    
    // Check if department is selected
    const department = document.getElementById('department').value;
    if (!department) {
        e.preventDefault();
        showToast('Please select a department.', 'error');
        return;
    }
    
    // If "Other" is selected, check if other department is specified
    if (department === 'Other') {
        const otherDepartment = document.getElementById('other_department').value.trim();
        if (!otherDepartment) {
            e.preventDefault();
            showToast('Please specify the other department name.', 'error');
            return;
        }
    }
    
    const basePrice = document.getElementById('base_price').value;
    if (!basePrice || basePrice <= 0) {
        e.preventDefault();
        showToast('Please ensure all dates and capacity are selected to calculate the base price.', 'error');
        return;
    }
    
    // Equipment validation
    const selectedEquipment = document.querySelectorAll('input[name="equipment[]"]:checked');
    let hasEquipmentError = false;
    
    selectedEquipment.forEach(equipment => {
        if (equipment.value !== 'none') {
            const equipmentId = equipment.value.toLowerCase().replace(/\s+/g, '_');
            const quantityInput = document.querySelector(`input[name="equipment_quantity[${equipment.value}]"]`);
            if (quantityInput && (!quantityInput.value || parseInt(quantityInput.value) < 1)) {
                hasEquipmentError = true;
                showToast(`Please specify quantity for ${equipment.value}`, 'error');
            }
        }
    });
    
    if (hasEquipmentError) {
        e.preventDefault();
        return;
    }
});

// Toast helper
function showToast(message, type = 'error') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `flex items-center p-4 mb-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out translate-x-full`;
    
    // Set background color based on type
    if (type === 'success') {
        toast.classList.add('bg-gradient-to-r', 'from-green-500', 'to-green-600', 'text-white');
    } else if (type === 'info') {
        toast.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white');
    } else {
        toast.classList.add('bg-gradient-to-r', 'from-red-500', 'to-red-600', 'text-white');
    }
    
    // Set icon based on type
    const icon = type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-circle';
    
    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">
            <i class="fas fa-${icon} text-xl"></i>
        </div>
        <div class="flex-1 font-poppins">
            ${message}
        </div>
        <div class="ml-3 flex-shrink-0">
            <button class="text-white focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);
    
    // Close button functionality
    toast.querySelector('button').addEventListener('click', () => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}
</script>
@endpush 