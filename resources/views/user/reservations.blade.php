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

<div class="space-y-8 font-poppins">
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
    
                            <!-- Equipment -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-tools text-maroon mr-2"></i>
                                    Equipment Needed
                                </label>
                                @error('equipment')
                                    <div class="text-red-600 text-sm mb-2">{{ $message }}</div>
                                @enderror
                                <div class="border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                                    @if($equipment->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($equipment as $item)
                                                @if($item && is_object($item) && isset($item->id))
                                                <div class="flex items-center space-x-2 p-2 hover:bg-white rounded-lg transition-colors">
                                                    <input type="checkbox" name="equipment[{{ $item->id }}][checked]" value="1" id="equipment_{{ $item->id }}" class="rounded border-gray-300 text-maroon focus:ring-maroon equipment-checkbox">
                                                    <label for="equipment_{{ $item->id }}" class="text-sm text-gray-700 flex-1">{{ $item->name }} <span class="text-xs text-gray-500">(Available: {{ $item->total_quantity }})</span></label>
                                                    <div class="relative">
                                                        <input type="number" name="equipment[{{ $item->id }}][quantity]" min="1" max="{{ $item->total_quantity }}" placeholder="Qty" class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-maroon focus:border-maroon equipment-qty" disabled>
                                                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                            <i class="fas fa-sort text-gray-400 text-xs"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-tools text-gray-300 text-3xl mb-2"></i>
                                            <p class="text-gray-500 text-sm">No equipment available.</p>
                                        </div>
                                    @endif
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
    
    // Equipment checkboxes
    document.querySelectorAll('.equipment-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const qtyInput = this.closest('div').querySelector('.equipment-qty');
            qtyInput.disabled = !this.checked;
            if (!this.checked) qtyInput.value = '';
            else qtyInput.focus();
        });
    });
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
            {id: {{ $venue->id }}, name: "{{ addslashes($venue->name) }}", capacity: {{ $venue->capacity }}},
        @endif
    @endforeach
];

document.getElementById('capacity').addEventListener('input', function() {
    const capacity = parseInt(this.value) || 0;
    const venueDisplay = document.getElementById('venue_display');
    const venueIdInput = document.getElementById('venue_id');
    
    if (capacity <= 0) {
        venueDisplay.innerHTML = '<i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity first';
        venueIdInput.value = '';
        return;
    }
    
    // Find venues that can accommodate the capacity
    const suitableVenues = venuesData.filter(venue => venue.capacity >= capacity);
    
    if (suitableVenues.length === 0) {
        venueDisplay.innerHTML = '<i class="fas fa-exclamation-circle mr-2 text-red-500"></i> No venue available for this capacity';
        venueIdInput.value = '';
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
    
    // Show success toast
    showToast(`Venue "${selectedVenue.name}" automatically selected`, 'success');
});

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
    
    // Equipment validation
    let valid = true;
    let equipmentChecked = false;
    
    document.querySelectorAll('.equipment-checkbox').forEach(function(checkbox) {
        if (checkbox.checked) {
            equipmentChecked = true;
            const qtyInput = checkbox.closest('div').querySelector('.equipment-qty');
            const min = parseInt(qtyInput.min);
            const max = parseInt(qtyInput.max);
            const val = parseInt(qtyInput.value);
            
            if (!val || val < min || val > max) {
                valid = false;
                qtyInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                showToast(`Please enter a valid quantity (${min}-${max}) for ${checkbox.nextElementSibling.textContent.trim().split('(')[0]}`, 'error');
            } else {
                qtyInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
            }
        }
    });
    
    if (!valid) {
        e.preventDefault();
    }
});
</script>
@endpush