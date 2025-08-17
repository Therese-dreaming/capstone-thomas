@extends('layouts.user')

@section('title', 'Reservations')
@section('page-title', 'Reservations')

@section('header-actions')
<a href="{{ route('user.reservations.index') }}" class="px-4 py-2 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 flex items-center space-x-2 shadow-md">
    <i class="fas fa-bookmark text-lg"></i>
    <span>My Reservations</span>
</a>
<button onclick="openReservationModal()" class="px-4 py-2 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 flex items-center space-x-2 shadow-md">
    <i class="fas fa-calendar-plus text-lg"></i>
    <span>New Reservation</span>
</button>
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
    .calendar-day {
        aspect-ratio: 1/1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 0.9rem;
        padding: 0.25rem;
        min-width: 5.5rem;
        max-width: 6rem;
    }
    .calendar-day:hover:not(.disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(128, 0, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(128, 0, 0, 0); }
    }
</style>

<div class="font-poppins">
    <!-- Calendar Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-calendar-alt text-maroon mr-3"></i>
                    Reservation Calendar
                </h2>
                <div class="flex items-center space-x-2 bg-white rounded-lg shadow-md p-1.5">
                    <button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="currentMonth" class="font-medium text-gray-700 px-3 font-montserrat"></span>
                    <button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg text-maroon transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <!-- Calendar Legend -->
            <div class="flex flex-wrap items-center justify-end mb-4 gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-100 border border-red-300 rounded-md mr-2"></div>
                    <span class="text-gray-600">Not Available</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-maroon text-white rounded-md mr-2 animate-pulse"></div>
                    <span class="text-gray-600">Today</span>
                </div>
            </div>
            <div id="calendar" class="grid grid-cols-7 gap-1 max-w-4xl mx-auto">
                <!-- Calendar will be populated by JavaScript -->
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col items-end"></div>
    
    <!-- Reservation Modal -->
    <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto font-poppins">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-calendar-plus text-maroon mr-2"></i>
                            New Reservation
                        </h3>
                        <button onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Rest of the form remains the same -->
                <form action="{{ route('user.reservations.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Event Title -->
                            <div>
                                <label for="event_title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-heading text-maroon mr-2"></i>
                                    Event Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="event_title" name="event_title" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            </div>
    
                            <!-- Purpose -->
                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-align-left text-maroon mr-2"></i>
                                    Purpose <span class="text-red-500">*</span>
                                </label>
                                <textarea id="purpose" name="purpose" rows="3" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors"></textarea>
                            </div>
    
                            <!-- Date and Time -->
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-hourglass-start text-maroon mr-2"></i>
                                        Start Date & Time <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="start_date" name="start_date" required
                                           min="{{ now()->addDays(3)->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-hourglass-end text-maroon mr-2"></i>
                                        End Date & Time <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="end_date" name="end_date" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                                </div>
                            </div>
    
                            <!-- Activity Grid -->
                            <div>
                                <label for="activity_grid" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-file-upload text-maroon mr-2"></i>
                                    Activity Grid (Optional)
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-maroon transition-colors">
                                    <input type="file" id="activity_grid" name="activity_grid" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           class="hidden">
                                    <label for="activity_grid" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</p>
                                    </label>
                                    <div id="file-name" class="mt-2 text-sm text-gray-600 hidden"></div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Capacity -->
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-users text-maroon mr-2"></i>
                                    Expected Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="capacity" name="capacity" min="1" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            </div>
    
                            <!-- Venue (Auto-selected based on capacity, not editable) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                                    Venue <span class="text-red-500">*</span>
                                </label>
                                <div id="venue_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first
                                </div>
                                <input type="hidden" id="venue_id" name="venue_id" value="">
                                <p class="text-xs text-gray-500 mt-1">Venue will be automatically selected based on your capacity requirement</p>
                            </div>

                            <!-- Price Rate (Auto-calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-tag text-maroon mr-2"></i>
                                    Rate per Hour <span class="text-red-500">*</span>
                                </label>
                                <div id="price_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first
                                </div>
                                <input type="hidden" id="price_per_hour" name="price_per_hour" value="">
                                <p class="text-xs text-gray-500 mt-1">Rate will be automatically calculated based on venue selection</p>
                            </div>

                            <!-- Equipment Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-tools text-maroon mr-2"></i>
                                    Equipment to Borrow
                                </label>
                                <div id="equipment_container" class="space-y-4">
                                    <!-- Equipment will be dynamically generated here -->
                                </div>
                                
                                <!-- No Equipment Needed -->
                                <div class="border border-gray-200 rounded-lg p-3 mt-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="equipment_none" name="equipment[]" value="none" class="w-4 h-4 text-maroon border-gray-300 rounded focus:ring-maroon">
                                        <label for="equipment_none" class="ml-2 text-sm font-medium text-gray-700">No Equipment Needed</label>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-500 mt-2">Select equipment and specify quantities. Quantities cannot exceed available amounts.</p>
                            </div>

                            <!-- Final Price Calculation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-calculator text-maroon mr-2"></i>
                                    Final Price <span class="text-red-500">*</span>
                                </label>
                                <div id="final_price_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity and dates first
                                </div>
                                <input type="hidden" id="final_price" name="final_price" value="">
                                <div id="price_breakdown" class="mt-2 text-xs text-gray-500 hidden">
                                    <div class="space-y-1">
                                        <div id="duration_info"></div>
                                        <div id="rate_info"></div>
                                        <div id="total_info"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" onclick="closeReservationModal()"
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md flex items-center">
                            <i class="fas fa-check mr-2"></i> Submit Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDate = new Date();
let selectedDate = null;
// Sample data for unavailable dates - this should be populated from your backend
const unavailableDates = [
    // Format: 'YYYY-MM-DD'
    // This is just a placeholder - you should replace this with actual data from your backend
    new Date().toISOString().split('T')[0], // Today as an example
    new Date(new Date().setDate(new Date().getDate() + 5)).toISOString().split('T')[0] // Today + 5 days
];

// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    updateCurrentMonth();
    
    // Create toast container if it doesn't exist
    if (!document.getElementById('toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed bottom-4 right-4 z-50 flex flex-col items-end';
        document.body.appendChild(toastContainer);
    }
    
    // File upload preview
    const fileInput = document.getElementById('activity_grid');
    const fileNameDisplay = document.getElementById('file-name');
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
            fileNameDisplay.classList.remove('hidden');
        } else {
            fileNameDisplay.classList.add('hidden');
        }
    });
    
    // Initial attachment of event listeners
    attachEquipmentEventListeners();

    // Add event listeners for date inputs to recalculate final price
    document.getElementById('start_date').addEventListener('change', calculateFinalPrice);
    document.getElementById('end_date').addEventListener('change', calculateFinalPrice);

});

function renderCalendar() {
    const calendar = document.getElementById('calendar');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    let html = '';
    
    // Day headers
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    days.forEach(day => {
        html += `<div class="text-center text-sm font-medium text-gray-500 py-2 font-montserrat">${day}</div>`;
    });
    
    // Calculate the minimum selectable date (today + 3 days)
    const now = new Date();
    const minSelectable = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 3);
    
    // Calendar days
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const isCurrentMonth = date.getMonth() === month;
        const isToday = date.toDateString() === new Date().toDateString();
        const isPast = date < new Date();
        const isTooSoon = date < minSelectable;
        
        // Check if date is unavailable (booked)
        const dateString = date.toISOString().split('T')[0];
        const isUnavailable = unavailableDates.includes(dateString);
        
        let dayClass = 'calendar-day text-center py-3 relative rounded-lg transition-all duration-200';
        let dayStyle = '';
        
        if (!isCurrentMonth) {
            dayClass += ' text-gray-300';
            dayStyle = 'opacity: 0.5;';
        }
        
        if (isToday) {
            dayClass += ' bg-maroon text-white font-bold animate-pulse';
        } else if (isUnavailable) {
            dayClass += ' bg-red-100 text-red-800 border border-red-200';
        } else if (!isPast && !isTooSoon) {
            dayClass += ' bg-white text-gray-800 border border-gray-200 hover:bg-gray-50 hover:border-gray-300';
        }
        
        if (isPast || isTooSoon || isUnavailable) {
            dayClass += ' opacity-60 cursor-not-allowed disabled';
        } else {
            dayClass += ' cursor-pointer transform hover:scale-105 hover:shadow-md';
        }
        
        html += `
            <div class="${dayClass}" style="${dayStyle}" ${(!isPast && !isTooSoon && !isUnavailable) ? `onclick="selectDate('${date.toISOString()}')"` : ''}>
                <span class="text-lg ${isToday ? 'font-bold' : ''}">${date.getDate()}</span>
                ${isUnavailable ? '<div class="absolute bottom-1 left-1/2 transform -translate-x-1/2"><i class="fas fa-lock text-red-500 text-xs"></i></div>' : ''}
            </div>
        `;
    }
    
    calendar.innerHTML = html;
}

// Rest of the JavaScript remains the same
function updateCurrentMonth() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('currentMonth').textContent = 
        `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    updateCurrentMonth();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    updateCurrentMonth();
}

function selectDate(dateString) {
    selectedDate = new Date(dateString);
    openReservationModal();
    
    // Set the selected date in the form
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    const dateStr = selectedDate.toISOString().slice(0, 16);
    startDateInput.value = dateStr;
    
    // Set end time to 1 hour later
    const endDate = new Date(selectedDate);
    endDate.setHours(endDate.getHours() + 1);
    endDateInput.value = endDate.toISOString().slice(0, 16);
    
    // Show a toast notification
    showToast(`Selected date: ${selectedDate.toLocaleDateString()}`, 'info');
}

function openReservationModal() {
    document.getElementById('reservationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Add animation
    const modalContent = document.querySelector('#reservationModal > div > div');
    modalContent.classList.add('animate-fadeIn');
}

function closeReservationModal() {
    document.getElementById('reservationModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('reservationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReservationModal();
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

document.getElementById('capacity').addEventListener('input', function() {
    const capacity = parseInt(this.value) || 0;
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
        showToast('No venue available for the specified capacity. Please reduce the capacity or contact admin.', 'error');
        return;
    }
    
    // Select the venue with the closest capacity (smallest difference)
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
    
    // Show success toast
    showToast(`Venue "${selectedVenue.name}" automatically selected - Rate: ₱${price.toLocaleString()}/hour`, 'success');
    
    // Calculate final price after venue selection
    calculateFinalPrice();
    
    // Generate equipment options for the selected venue
    generateEquipmentOptions(selectedVenue);
});

// Function to attach event listeners to equipment checkboxes
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

// Equipment quantity validation
function attachQuantityValidation() {
    const quantityInputs = document.querySelectorAll('input[name^="equipment_quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);
            
            if (value > max) {
                this.value = max;
                showToast(`Maximum available quantity for this equipment is ${max}`, 'error');
            } else if (value < 1) {
                this.value = 1;
            }
        });
    });
}

// Generate equipment options based on venue's available equipment
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
    
    // Re-attach quantity validation for new inputs
    attachQuantityValidation();
}

// Calculate final price based on duration and rate
function calculateFinalPrice() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const pricePerHour = parseFloat(document.getElementById('price_per_hour').value) || 0;
    const finalPriceDisplay = document.getElementById('final_price_display');
    const finalPriceInput = document.getElementById('final_price');
    const priceBreakdown = document.getElementById('price_breakdown');
    
    if (!startDate || !endDate || pricePerHour <= 0) {
        finalPriceDisplay.innerHTML = '<i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity and dates first';
        finalPriceInput.value = '';
        priceBreakdown.classList.add('hidden');
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (end <= start) {
        finalPriceDisplay.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> End time must be after start time';
        finalPriceInput.value = '';
        priceBreakdown.classList.add('hidden');
        return;
    }
    
    // Calculate duration in hours (rounded up to nearest hour)
    const durationMs = end - start;
    const durationHours = Math.max(1, Math.ceil(durationMs / (1000 * 60 * 60)));
    
    // Calculate final price
    const finalPrice = durationHours * pricePerHour;
    
    // Update display
    finalPriceDisplay.innerHTML = `<i class="fas fa-check-circle mr-2 text-green-500"></i> ₱${finalPrice.toLocaleString()} <span class="text-sm text-gray-500">(Total for ${durationHours} hour${durationHours > 1 ? 's' : ''})</span>`;
    finalPriceInput.value = finalPrice;
    
    // Show price breakdown
    priceBreakdown.classList.remove('hidden');
    document.getElementById('duration_info').textContent = `Duration: ${durationHours} hour${durationHours > 1 ? 's' : ''}`;
    document.getElementById('rate_info').textContent = `Rate: ₱${pricePerHour.toLocaleString()} per hour`;
    document.getElementById('total_info').textContent = `Total: ₱${finalPrice.toLocaleString()}`;
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
    
    const finalPrice = document.getElementById('final_price').value;
    if (!finalPrice || finalPrice <= 0) {
        e.preventDefault();
        showToast('Please ensure all dates and capacity are selected to calculate the final price.', 'error');
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
</script>
@endpush