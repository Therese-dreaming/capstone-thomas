@extends('layouts.user')

@section('title', 'Reservations')
@section('page-title', 'Reservations')

@section('header-actions')
<a href="{{ route('user.reservations.index') }}" class="font-montserrat font-bold px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all duration-300 flex items-center space-x-2 shadow-md">
    <i class="fas fa-bookmark text-lg"></i>
    <span>My Reservations</span>
</a>
<button onclick="openReservationModal()" class="font-montserrat font-bold px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all duration-300 flex items-center space-x-2 shadow-md">
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
    
    <!-- Activity Grid Modal (Step 1) -->
    <div id="activityGridModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto font-poppins">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-file-upload text-maroon mr-2"></i>
                            Step 1: Upload Activity Grid
                        </h3>
                        <button onclick="closeActivityGridModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form id="activityGridForm" class="p-6">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label for="step1_event_title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-heading text-maroon mr-2"></i>
                                Event Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="step1_event_title" name="event_title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="step1_purpose" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-align-left text-maroon mr-2"></i>
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <textarea id="step1_purpose" name="purpose" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors"></textarea>
                        </div>

                        <!-- Activity Grid -->
                        <div>
                            <label for="step1_activity_grid" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-file-upload text-maroon mr-2"></i>
                                Activity Grid <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-maroon transition-colors">
                                <input type="file" id="step1_activity_grid" name="activity_grid" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                       class="hidden">
                                <label for="step1_activity_grid" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</p>
                                </label>
                                <div id="step1_file_name" class="mt-2 text-sm text-gray-600 hidden"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Activity Grid is required before proceeding to reservation details</p>
                        </div>
                    </div>
    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" onclick="closeActivityGridModal()"
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="button" onclick="submitActivityGrid()"
                                class="px-6 py-3 bg-maroon text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md flex items-center">
                            <i class="fas fa-arrow-right mr-2"></i> Next: Reservation Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reservation Modal (Step 2) -->
    <div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto font-poppins">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-calendar-plus text-maroon mr-2"></i>
                            Step 2: Reservation Details
                        </h3>
                        <button onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="text-sm">Activity Grid uploaded successfully. You can now proceed with reservation details.</span>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('user.reservations.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <!-- Hidden fields for activity grid data -->
                    <input type="hidden" id="final_event_title" name="event_title" value="">
                    <input type="hidden" id="final_purpose" name="purpose" value="">
                    <input type="hidden" id="final_activity_grid" name="activity_grid" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Event Title Display (Read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-heading text-maroon mr-2"></i>
                                    Event Title
                                </label>
                                <div id="display_event_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>
    
                            <!-- Purpose Display (Read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-align-left text-maroon mr-2"></i>
                                    Purpose
                                </label>
                                <div id="display_purpose" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
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
                                    <option value="ECE">ECE</option>
                                    <option value="JHS">JHS</option>
                                    <option value="SHS">SHS</option>
                                    <option value="BSIT">BSIT</option>
                                    <option value="BSENT">BSENT</option>
                                    <option value="BSP">BSP</option>
                                    <option value="BSBA">BSBA</option>
                                    <option value="BSA">BSA</option>
                                    <option value="TED">TED</option>
                                    <option value="Other">Other</option>
                                </select>
                                
                                <!-- Other Department Input (Hidden by default) -->
                                <div id="other_department_container" class="mt-3 hidden">
                                    <label for="other_department" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-edit text-maroon mr-2"></i>
                                        Specify Other Department <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="other_department" name="other_department" 
                                           placeholder="Enter department name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                                </div>
                            </div>

                            <!-- Capacity -->
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-users text-maroon mr-2"></i>
                                    Expected Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="capacity" name="capacity" min="1" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
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
                        </div>
    
                        <!-- Right Column -->
                        <div class="space-y-6">
    
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

                                <!-- Selected Equipment Summary -->
                                <div id="selected_equipment_summary" class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center text-gray-700 text-sm">
                                        <i class="fas fa-list-ul text-maroon mr-2"></i>
                                        <span class="font-medium">Selected:</span>
                                        <span id="selected_equipment_text" class="ml-2 text-gray-600">None</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Base Price Calculation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-calculator text-maroon mr-2"></i>
                                    Base Price <span class="text-red-500">*</span>
                                </label>
                                <div id="base_price_display" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Select capacity and dates first
                                </div>
                                <input type="hidden" id="base_price" name="base_price" value="">
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
                        <button type="button" onclick="goBackToStep1()"
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Step 1
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-maroon text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md flex items-center">
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
let unavailableDates = [
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
    
    // File upload preview for step 1
    const step1FileInput = document.getElementById('step1_activity_grid');
    const step1FileNameDisplay = document.getElementById('step1_file_name');
    
    step1FileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            step1FileNameDisplay.textContent = this.files[0].name;
            step1FileNameDisplay.classList.remove('hidden');
        } else {
            step1FileNameDisplay.classList.add('hidden');
        }
    });
    
    // Initial attachment of event listeners
    attachEquipmentEventListeners();

    // Add event listeners for date inputs to recalculate base price
    document.getElementById('start_date').addEventListener('change', calculateBasePrice);
    document.getElementById('end_date').addEventListener('change', calculateBasePrice);

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
        
        // Check if date is unavailable (booked) - allow multiple reservations per day; block by timeslots only
        const dateString = date.toISOString().split('T')[0];
        const isUnavailable = false;
        
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
            <div class="${dayClass}" style="${dayStyle}" ${(!isPast && !isTooSoon && !isUnavailable) ? `onclick="selectDate('${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}T00:00')"` : ''}>
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
    openActivityGridModal();
    
    // Show a toast notification
    showToast(`Selected date: ${selectedDate.toLocaleDateString()}. Please upload your Activity Grid first.`, 'info');
}

function openActivityGridModal() {
    document.getElementById('activityGridModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Add animation
    const modalContent = document.querySelector('#activityGridModal > div > div');
    modalContent.classList.add('animate-fadeIn');
}

function closeActivityGridModal() {
    document.getElementById('activityGridModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
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

function goBackToStep1() {
    closeReservationModal();
    openActivityGridModal();
}

function submitActivityGrid() {
    const eventTitle = document.getElementById('step1_event_title').value.trim();
    const purpose = document.getElementById('step1_purpose').value.trim();
    const activityGrid = document.getElementById('step1_activity_grid').files[0];
    
    // Validation
    if (!eventTitle) {
        showToast('Please enter an event title.', 'error');
        return;
    }
    
    if (!purpose) {
        showToast('Please enter a purpose.', 'error');
        return;
    }
    
    if (!activityGrid) {
        showToast('Please upload an activity grid file.', 'error');
        return;
    }
    
    // Store the data for step 2
    document.getElementById('final_event_title').value = eventTitle;
    document.getElementById('final_purpose').value = purpose;
    
    // Store the file for later submission
    window.storedActivityGrid = activityGrid;
    
    // Update display fields in step 2
    document.getElementById('display_event_title').textContent = eventTitle;
    document.getElementById('display_purpose').textContent = purpose;
    
    // Close step 1 and open step 2
    closeActivityGridModal();
    openReservationModal();
    
    // Set the selected date in the form using local time (avoid UTC shift)
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    function fmt(d){
        const y=d.getFullYear();
        const m=String(d.getMonth()+1).padStart(2,'0');
        const day=String(d.getDate()).padStart(2,'0');
        const hh=String(d.getHours()).padStart(2,'0');
        const mm=String(d.getMinutes()).padStart(2,'0');
        return `${y}-${m}-${day}T${hh}:${mm}`;
    }

    // default start at 00:00; you can adjust to a preferred default hour if needed
    startDateInput.value = fmt(selectedDate);

    // Set end time to 1 hour later (local)
    const endDate = new Date(selectedDate);
    endDate.setHours(endDate.getHours() + 1);
    endDateInput.value = fmt(endDate);
    
    // Fetch and display unavailable times for this date/venue
    if (typeof showUnavailableTimes === 'function') {
        showUnavailableTimes();
    }
    
    showToast('Activity Grid uploaded successfully! You can now proceed with reservation details.', 'success');
}

// Function to toggle the "Other Department" input field
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
    
    // Calculate base price after venue selection
    calculateBasePrice();
    
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

            updateSelectedEquipmentSummary();
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

            updateSelectedEquipmentSummary();
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

    // Initialize summary after rendering
    updateSelectedEquipmentSummary();
}

// Build the selected equipment summary text
function updateSelectedEquipmentSummary() {
    const summaryEl = document.getElementById('selected_equipment_text');
    if (!summaryEl) return;

    const selected = [];
    const equipmentCheckboxes = document.querySelectorAll('input[name="equipment[]"]');
    const noneChecked = document.getElementById('equipment_none')?.checked;

    equipmentCheckboxes.forEach(cb => {
        if (cb.checked && cb.value !== 'none') {
            const id = cb.value.toLowerCase().replace(/\s+/g, '_');
            const qtyInput = document.getElementById(`${id}_quantity`);
            const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
            selected.push(`${cb.value} (x${qty})`);
        }
    });

    if (noneChecked) {
        summaryEl.textContent = 'No equipment needed';
        return;
    }

    summaryEl.textContent = selected.length ? selected.join(', ') : 'None';
}

// Calculate base price based on duration and rate
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

// Fetch unavailable timeslots for a given venue and date
async function fetchUnavailableSlots(venueId, dateStr){
	if(!venueId || !dateStr) return [];
	try{
		const params = new URLSearchParams({ venue_id: venueId, date: dateStr });
		const res = await fetch(`/user/reservations/unavailable?${params.toString()}`);
		if(!res.ok) return [];
		const data = await res.json();
		return data.slots || [];
	}catch{ return []; }
}

// Display unavailable timeslots below the date inputs
async function showUnavailableTimes(){
	const venueId = document.getElementById('venue_id').value;
	const startVal = document.getElementById('start_date').value;
	if(!venueId || !startVal) return;
	const dateOnly = startVal.slice(0,10);
	const slots = await fetchUnavailableSlots(venueId, dateOnly);
	let container = document.getElementById('unavailable_container');
	if(!container){
		container = document.createElement('div');
		container.id = 'unavailable_container';
		container.className = 'mt-3 text-sm';
		document.getElementById('end_date').closest('.grid').after(container);
	}
	if(slots.length === 0){
		container.innerHTML = '<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3"><i class="fas fa-check-circle mr-2"></i>No unavailable times for this day.</div>';
		return;
	}
	const list = slots.map(s=>`<li class="flex items-center"><i class="fas fa-ban text-red-500 mr-2"></i>${s.start} – ${s.end} <span class="text-gray-500 ml-2">(${s.title})</span></li>`).join('');
	container.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-3"><div class="font-medium mb-1">Unavailable timeslots:</div><ul class="space-y-1">${list}</ul></div>`;
}

document.getElementById('start_date').addEventListener('change', showUnavailableTimes);
// When venue gets selected automatically, also refresh
const venueHiddenInput = document.getElementById('venue_id');
const observer = new MutationObserver(showUnavailableTimes);
observer.observe(venueHiddenInput, { attributes: true, attributeFilter: ['value'] });

// Prevent overlap on submit using fetched slots
function overlaps(aStart, aEnd, bStart, bEnd){ return aStart < bEnd && aEnd > bStart; }

document.querySelector('form[action*="reservations"]').addEventListener('submit', async function(e){
	const venueId = document.getElementById('venue_id').value;
	const startVal = document.getElementById('start_date').value;
	const endVal = document.getElementById('end_date').value;
	if(!venueId || !startVal || !endVal) return; // other validators will handle
	const slots = await fetchUnavailableSlots(venueId, startVal.slice(0,10));
	if(slots.length){
		const start = new Date(startVal);
		const end = new Date(endVal);
		for(const s of slots){
			const [sh, sm] = s.start.split(':').map(Number);
			const [eh, em] = s.end.split(':').map(Number);
			const blockStart = new Date(startVal.slice(0,10)+'T00:00'); blockStart.setHours(sh, sm||0, 0, 0);
			const blockEnd = new Date(startVal.slice(0,10)+'T00:00'); blockEnd.setHours(eh, em||0, 0, 0);
			if(overlaps(start, end, blockStart, blockEnd)){
				e.preventDefault();
				showToast(`Selected time overlaps with ${s.start}–${s.end}. Please pick a different time.`, 'error');
				return;
			}
		}
	}
});

// Form validation - only for the reservation form
document.querySelector('form[action*="reservations"]').addEventListener('submit', function(e) {
    // Check if activity grid was uploaded in step 1
    if (!window.storedActivityGrid) {
        e.preventDefault();
        showToast('Please complete Step 1 (Activity Grid) before submitting the reservation.', 'error');
        return;
    }
    
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
    
    // Attach the stored activity grid file to the form
    const formData = new FormData(this);
    formData.delete('activity_grid'); // Remove the hidden input
    formData.append('activity_grid', window.storedActivityGrid);
    
    // Submit the form with the file
    e.preventDefault();
    submitReservationWithFile(formData);
});

// Function to submit the reservation with the stored file
async function submitReservationWithFile(formData) {
    try {
        console.log('Submitting reservation...');
        
        const response = await fetch('{{ route("user.reservations.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (response.ok) {
            const result = await response.json();
            console.log('Success response:', result);
            
            showToast('Reservation submitted successfully!', 'success');
            closeReservationModal();
            
            // Clear stored data
            window.storedActivityGrid = null;
            
            // Optionally refresh the page or calendar after a short delay
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Handle validation errors or other server errors
            if (response.status === 422) {
                const errors = await response.json();
                console.log('Validation errors:', errors);
                const errorMessage = Object.values(errors.errors || {}).flat().join(', ');
                showToast(errorMessage || 'Validation failed. Please check your inputs.', 'error');
            } else {
                const error = await response.json();
                console.log('Error response:', error);
                showToast(error.message || 'Failed to submit reservation. Please try again.', 'error');
            }
        }
    } catch (error) {
        console.error('Exception occurred:', error);
        showToast('An error occurred while submitting the reservation. Please try again.', 'error');
    }
}
</script>
@endpush