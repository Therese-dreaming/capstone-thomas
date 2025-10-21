@extends('layouts.user')

@section('title', 'Reservations')
@section('page-title', 'Reservations')

@section('header-actions')
<a href="{{ route('user.reservations.index') }}" class="font-montserrat font-bold px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all duration-300 flex items-center space-x-2 shadow-md">
    <i class="fas fa-bookmark text-lg"></i>
    <span>My Reservations</span>
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
    
    /* Loading Animation Styles */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .button-loading {
        opacity: 0.8;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Modal Animation Styles */
    .modal-enter {
        animation: modalEnter 0.3s ease-out;
    }
    
    @keyframes modalEnter {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .modal-backdrop {
        backdrop-filter: blur(4px);
        transition: all 0.3s ease;
    }
    
    /* Capacity validation styles */
    .capacity-warning {
        animation: capacityPulse 2s infinite;
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
    
    <!-- Step 1: Date and Venue Selection Modal -->
    <div id="dateVenueModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto font-poppins">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-calendar-check text-maroon mr-2"></i>
                            Step 1: Select Date and Venue
                        </h3>
                        <button onclick="closeDateVenueModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form id="dateVenueForm" class="p-6">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Capacity -->
                        <div>
                            <label for="step1_capacity" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-users text-maroon mr-2"></i>
                                Expected Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="step1_capacity" name="capacity" min="1" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            <p class="text-xs text-gray-500 mt-1">Enter the number of expected attendees</p>
                        </div>

                        <!-- Venue Selection -->
                        <div>
                            <label for="step1_venue_id" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt text-maroon mr-2"></i>
                                Venue <span class="text-red-500">*</span>
                            </label>
                            <select id="step1_venue_id" name="venue_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                                <option value="">Select a venue</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" 
                                            data-price="{{ $venue->price_per_hour }}"
                                            data-capacity="{{ $venue->capacity }}">
                                        {{ $venue->name }} - ₱{{ number_format($venue->price_per_hour, 2) }}/hr ({{ $venue->capacity }} capacity)
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Venue Suggestions -->
                            <div id="step1_venueSuggestions" class="hidden mt-2 p-3 bg-green-50 border border-green-200 rounded-lg venue-suggestions">
                                <div class="flex items-start">
                                    <i class="fas fa-lightbulb text-green-600 mr-2 mt-0.5"></i>
                                    <div class="text-sm text-green-800">
                                        <p class="font-medium mb-1">💡 Recommended Venues</p>
                                        <p class="text-xs mb-2">Based on your expected capacity:</p>
                                        <div id="step1_suggestedVenuesList" class="space-y-1">
                                            <!-- Suggested venues will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Capacity Warning -->
                            <div id="step1_capacityWarning" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-lg capacity-warning">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-red-600 mr-2 mt-0.5"></i>
                                    <div class="text-sm text-red-800">
                                        <p class="font-medium mb-1">⚠️ Capacity Exceeded!</p>
                                        <p id="step1_capacityWarningText" class="text-xs"><!-- Warning text will be populated here --></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="step1_start_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-hourglass-start text-maroon mr-2"></i>
                                    Start Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="step1_start_date" name="start_date" required
                                       min="{{ now()->addDays(3)->format('Y-m-d\T00:00') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            </div>
                            <div>
                                <label for="step1_end_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-hourglass-end text-maroon mr-2"></i>
                                    End Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="step1_end_date" name="end_date" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                            </div>
                        </div>

                        <!-- Conflict Check Display -->
                        <div id="step1_conflictCheck" class="hidden"></div>
                    </div>
    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" onclick="closeDateVenueModal()"
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="button" onclick="submitDateVenue()"
                                class="px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md flex items-center">
                            <i class="fas fa-arrow-right mr-2"></i> Next: Upload Activity Grid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Step 2: Activity Grid Modal -->
    <div id="activityGridModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto font-poppins">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-file-upload text-maroon mr-2"></i>
                            Step 2: Upload Activity Grid
                        </h3>
                        <button onclick="closeActivityGridModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="text-sm">Date and venue selected successfully. Please upload your activity grid.</span>
                        </div>
                    </div>
                </div>
                
                <form id="activityGridForm" class="p-6">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label for="step2_event_title" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-heading text-maroon mr-2"></i>
                                Event Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="step2_event_title" name="event_title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label for="step2_purpose" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-align-left text-maroon mr-2"></i>
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <textarea id="step2_purpose" name="purpose" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors"></textarea>
                        </div>

                        <!-- Activity Grid -->
                        <div>
                            <label for="step2_activity_grid" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-file-upload text-maroon mr-2"></i>
                                Activity Grid <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-maroon transition-colors">
                                <input type="file" id="step2_activity_grid" name="activity_grid" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                       class="hidden">
                                <label for="step2_activity_grid" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</p>
                                </label>
                                <div id="step2_file_name" class="mt-2 text-sm text-gray-600 hidden"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Activity Grid is required before proceeding to final details</p>
                        </div>
                    </div>
    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" onclick="goBackToStep1()"
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Step 1
                        </button>
                        <button type="button" onclick="submitActivityGrid()"
                                class="px-6 py-3 bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md flex items-center">
                            <i class="fas fa-arrow-right mr-2"></i> Next: Equipment & Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Step 3: Final Details Modal -->
    <div id="finalDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-7xl w-full max-h-[85vh] overflow-y-auto font-poppins">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-clipboard-list text-maroon mr-2"></i>
                            Step 3: Equipment, Department & Final Details
                        </h3>
                        <button onclick="closeFinalDetailsModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center text-green-800">
                            <i class="fas fa-check-circle mr-2 text-sm"></i>
                            <span class="text-xs">Activity Grid uploaded successfully. Complete the final details to submit your reservation.</span>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('user.reservations.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf
                    
                    <!-- Hidden fields from previous steps -->
                    <input type="hidden" id="final_event_title" name="event_title" value="">
                    <input type="hidden" id="final_purpose" name="purpose" value="">
                    <input type="hidden" id="final_activity_grid" name="activity_grid" value="">
                    <input type="hidden" id="final_capacity" name="capacity" value="">
                    <input type="hidden" id="final_venue_id" name="venue_id" value="">
                    <input type="hidden" id="final_start_date" name="start_date" value="">
                    <input type="hidden" id="final_end_date" name="end_date" value="">
                    <input type="hidden" id="final_price_per_hour" name="price_per_hour" value="">
                    <input type="hidden" id="final_base_price" name="base_price" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Column 1: Summary Info (Read-only) -->
                        <div class="space-y-3">
                            <!-- Event Title Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-heading text-maroon mr-1 text-xs"></i>
                                    Event Title
                                </label>
                                <div id="display_event_title" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 2 -->
                                </div>
                            </div>
    
                            <!-- Purpose Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-align-left text-maroon mr-1 text-xs"></i>
                                    Purpose
                                </label>
                                <div id="display_purpose" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 max-h-20 overflow-y-auto">
                                    <!-- Will be populated from step 2 -->
                                </div>
                            </div>

                            <!-- Venue Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-map-marker-alt text-maroon mr-1 text-xs"></i>
                                    Venue
                                </label>
                                <div id="display_venue" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>

                            <!-- Date/Time Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-calendar text-maroon mr-1 text-xs"></i>
                                    Date & Time
                                </label>
                                <div id="display_datetime" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>

                            <!-- Capacity Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-users text-maroon mr-1 text-xs"></i>
                                    Capacity
                                </label>
                                <div id="display_capacity" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>
                        </div>
    
                        <!-- Column 2: Department & Pricing -->
                        <div class="space-y-3">
    
                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-building text-maroon mr-1 text-xs"></i>
                                    Department <span class="text-red-500">*</span>
                                </label>
                                <select id="department" name="department" required onchange="toggleOtherDepartment()"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
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
                                <div id="other_department_container" class="mt-2 hidden">
                                    <input type="text" id="other_department" name="other_department" 
                                           placeholder="Enter department name"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                                </div>
                            </div>

                            <!-- Price Rate Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-tag text-maroon mr-1 text-xs"></i>
                                    Rate per Hour
                                </label>
                                <div id="display_price_rate" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>

                            <!-- Base Price Display (Read-only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-calculator text-maroon mr-1 text-xs"></i>
                                    Total Base Price
                                </label>
                                <div id="display_base_price" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold">
                                    <!-- Will be populated from step 1 -->
                                </div>
                            </div>
                        </div>
    
                        <!-- Column 3: Equipment -->
                        <div class="space-y-3">

                            <!-- Equipment Selection -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center">
                                    <i class="fas fa-tools text-maroon mr-1 text-xs"></i>
                                    Equipment to Borrow
                                </label>
                                <div id="equipment_container" class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2">
                                    <!-- Equipment will be dynamically generated here -->
                                </div>
                                
                                <!-- No Equipment Needed -->
                                <div class="border border-gray-200 rounded-lg p-2 mt-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="equipment_none" name="equipment[]" value="none" class="w-3 h-3 text-maroon border-gray-300 rounded focus:ring-maroon">
                                        <label for="equipment_none" class="ml-2 text-xs font-medium text-gray-700">No Equipment Needed</label>
                                    </div>
                                </div>

                                <!-- Selected Equipment Summary -->
                                <div id="selected_equipment_summary" class="mt-2 bg-gray-50 border border-gray-200 rounded-lg p-2">
                                    <div class="flex items-center text-gray-700 text-xs">
                                        <i class="fas fa-list-ul text-maroon mr-1 text-xs"></i>
                                        <span class="font-medium">Selected:</span>
                                        <span id="selected_equipment_text" class="ml-1 text-gray-600">None</span>
                                    </div>
                                </div>

                                <!-- Custom Equipment Section -->
                                <div class="mt-3 border-t border-gray-200 pt-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-medium text-gray-700 flex items-center">
                                            <i class="fas fa-plus-circle text-maroon mr-1 text-xs"></i>
                                            Additional Equipment
                                        </label>
                                        <button type="button" onclick="addCustomEquipment()" 
                                                class="px-2 py-1 bg-maroon text-white text-xs rounded-lg hover:bg-red-700 transition-colors flex items-center">
                                            <i class="fas fa-plus mr-1"></i> Add
                                        </button>
                                    </div>

                                    <div id="custom_equipment_list" class="space-y-2 max-h-32 overflow-y-auto">
                                        <!-- Custom equipment items will be added here dynamically -->
                                    </div>

                                    <!-- Template for custom equipment item (hidden) -->
                                    <div id="custom_equipment_template" class="hidden">
                                        <div class="custom-equipment-item bg-blue-50 border border-blue-200 rounded-lg p-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-medium text-blue-800">Custom Request</span>
                                                <button type="button" onclick="removeCustomEquipment(this)" 
                                                        class="text-red-500 hover:text-red-700 transition-colors">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <input type="text" name="custom_equipment_name[]" 
                                                           placeholder="Equipment Name"
                                                           class="w-full px-2 py-1 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <input type="number" name="custom_equipment_quantity[]" 
                                                           min="1" value="1" placeholder="Qty"
                                                           class="w-full px-2 py-1 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-3 mt-3 border-t border-gray-200">
                        <button type="button" onclick="goBackToStep2()"
                                class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to Step 2
                        </button>
                        <button type="submit" id="submitReservationBtn"
                                class="px-6 py-2 text-sm bg-maroon text-white rounded-lg hover:bg-red-700 transition-all duration-300 shadow-md flex items-center">
                            <span id="submitBtnContent">
                                <i class="fas fa-check mr-1 text-xs"></i> Submit Reservation
                            </span>
                            <span id="submitBtnLoading" class="hidden">
                                <div class="loading-spinner mr-1"></div> Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins modal-enter">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 font-montserrat">Reservation Submitted Successfully!</h3>
                    <p class="text-gray-600 mb-6">Your reservation request has been submitted and is now pending approval. You will receive a notification once it's reviewed.</p>
                    <div class="flex justify-center space-x-3">
                        <button onclick="closeSuccessModal()" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                            <i class="fas fa-check mr-2"></i> OK
                        </button>
                        <button onclick="viewMyReservations()" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-list mr-2"></i> View My Reservations
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full font-poppins modal-enter">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-circle text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 font-montserrat">Submission Failed</h3>
                    <p id="errorMessage" class="text-gray-600 mb-6">An error occurred while submitting your reservation. Please try again.</p>
                    <div class="flex justify-center space-x-3">
                        <button onclick="closeErrorModal()" 
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                            <i class="fas fa-times mr-2"></i> Close
                        </button>
                        <button onclick="retrySubmission()" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                            <i class="fas fa-redo mr-2"></i> Try Again
                        </button>
                    </div>
                </div>
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

// Venue data for capacity validation and suggestions - MUST BE DEFINED BEFORE USE
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
    
    // File upload preview for step 2
    const step2FileInput = document.getElementById('step2_activity_grid');
    const step2FileNameDisplay = document.getElementById('step2_file_name');
    
    if (step2FileInput) {
        step2FileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                step2FileNameDisplay.textContent = this.files[0].name;
                step2FileNameDisplay.classList.remove('hidden');
            } else {
                step2FileNameDisplay.classList.add('hidden');
            }
        });
    }
    
    // Step 1: Capacity and venue suggestions
    const step1Capacity = document.getElementById('step1_capacity');
    const step1VenueId = document.getElementById('step1_venue_id');
    const step1StartDate = document.getElementById('step1_start_date');
    const step1EndDate = document.getElementById('step1_end_date');
    
    if (step1Capacity) {
        step1Capacity.addEventListener('input', function() {
            showStep1VenueSuggestions();
            validateStep1Capacity();
        });
    }
    
    if (step1VenueId) {
        step1VenueId.addEventListener('change', function() {
            validateStep1Capacity();
            checkStep1Conflicts();
        });
    }
    
    if (step1StartDate) {
        step1StartDate.addEventListener('change', function() {
            checkStep1Conflicts();
        });
    }
    
    if (step1EndDate) {
        step1EndDate.addEventListener('change', function() {
            checkStep1Conflicts();
        });
    }
    
    // Initial attachment of event listeners
    attachEquipmentEventListeners();

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
    
    // Calculate the minimum selectable date (today + 3 days at midnight)
    const now = new Date();
    const minSelectable = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 3);
    minSelectable.setHours(0, 0, 0, 0); // Set to midnight
    
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
    openDateVenueModal();
    
    // Set the selected date in Step 1 form
    const startDateInput = document.getElementById('step1_start_date');
    const endDateInput = document.getElementById('step1_end_date');
    
    function fmt(d){
        const y=d.getFullYear();
        const m=String(d.getMonth()+1).padStart(2,'0');
        const day=String(d.getDate()).padStart(2,'0');
        const hh=String(d.getHours()).padStart(2,'0');
        const mm=String(d.getMinutes()).padStart(2,'0');
        return `${y}-${m}-${day}T${hh}:${mm}`;
    }
    
    startDateInput.value = fmt(selectedDate);
    const endDate = new Date(selectedDate);
    endDate.setHours(endDate.getHours() + 1);
    endDateInput.value = fmt(endDate);
    
    // Show a toast notification
    showToast(`Selected date: ${selectedDate.toLocaleDateString()}. Please select venue and time.`, 'info');
}

// Step 1: Date & Venue Modal Functions
function openDateVenueModal() {
    document.getElementById('dateVenueModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    const modalContent = document.querySelector('#dateVenueModal > div > div');
    modalContent.classList.add('animate-fadeIn');
}

function closeDateVenueModal() {
    document.getElementById('dateVenueModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Step 2: Activity Grid Modal Functions
function openActivityGridModal() {
    document.getElementById('activityGridModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    const modalContent = document.querySelector('#activityGridModal > div > div');
    modalContent.classList.add('animate-fadeIn');
}

function closeActivityGridModal() {
    document.getElementById('activityGridModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Step 3: Final Details Modal Functions
function openFinalDetailsModal() {
    document.getElementById('finalDetailsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    const modalContent = document.querySelector('#finalDetailsModal > div > div');
    modalContent.classList.add('animate-fadeIn');
}

function closeFinalDetailsModal() {
    document.getElementById('finalDetailsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Navigation Functions
function goBackToStep1() {
    closeActivityGridModal();
    openDateVenueModal();
}

function goBackToStep2() {
    closeFinalDetailsModal();
    openActivityGridModal();
}

// Submit Step 1: Date & Venue
async function submitDateVenue() {
    const capacity = document.getElementById('step1_capacity').value;
    const venueId = document.getElementById('step1_venue_id').value;
    const startDate = document.getElementById('step1_start_date').value;
    const endDate = document.getElementById('step1_end_date').value;
    
    // Validation
    if (!capacity || capacity < 1) {
        showToast('Please enter expected capacity.', 'error');
        return;
    }
    
    if (!venueId) {
        showToast('Please select a venue.', 'error');
        return;
    }
    
    if (!startDate) {
        showToast('Please select start date and time.', 'error');
        return;
    }
    
    if (!endDate) {
        showToast('Please select end date and time.', 'error');
        return;
    }
    
    // Validate dates
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    if (end <= start) {
        showToast('End time must be after start time.', 'error');
        return;
    }
    
    // Check for conflicts
    const dateOnly = startDate.slice(0,10);
    const slots = await fetchUnavailableSlots(venueId, dateOnly);
    
    if(slots.length > 0) {
        for(const s of slots){
            const [sh, sm] = s.start.split(':').map(Number);
            const [eh, em] = s.end.split(':').map(Number);
            const blockStart = new Date(dateOnly+'T00:00'); 
            blockStart.setHours(sh, sm||0, 0, 0);
            const blockEnd = new Date(dateOnly+'T00:00'); 
            blockEnd.setHours(eh, em||0, 0, 0);
            
            // Check overlap
            if(start < blockEnd && end > blockStart){
                showToast(`Time conflict detected! Your selected time overlaps with ${s.start}–${s.end}. Please select a different time.`, 'error');
                return;
            }
        }
    }
    
    // Validate capacity against venue
    const venueSelect = document.getElementById('step1_venue_id');
    const selectedOption = venueSelect.options[venueSelect.selectedIndex];
    const venueCapacity = parseInt(selectedOption.dataset.capacity) || 0;
    
    if (parseInt(capacity) > venueCapacity) {
        showToast('Warning: The requested capacity exceeds the venue capacity. This will be flagged for admin review.', 'info');
    }
    
    // Store Step 1 data
    window.step1Data = {
        capacity: capacity,
        venueId: venueId,
        startDate: startDate,
        endDate: endDate
    };
    
    // Get venue details
    const venueName = selectedOption.text.split(' - ')[0];
    const pricePerHour = parseFloat(selectedOption.dataset.price);
    
    // Calculate base price
    const durationMs = end - start;
    const durationHours = Math.max(1, Math.ceil(durationMs / (1000 * 60 * 60)));
    const basePrice = durationHours * pricePerHour;
    
    window.step1Data.venueName = venueName;
    window.step1Data.pricePerHour = pricePerHour;
    window.step1Data.basePrice = basePrice;
    window.step1Data.durationHours = durationHours;
    
    // Close Step 1 and open Step 2
    closeDateVenueModal();
    openActivityGridModal();
    
    showToast('Date and venue selected successfully! Please upload your activity grid.', 'success');
}

// Submit Step 2: Activity Grid
function submitActivityGrid() {
    const eventTitle = document.getElementById('step2_event_title').value.trim();
    const purpose = document.getElementById('step2_purpose').value.trim();
    const activityGrid = document.getElementById('step2_activity_grid').files[0];
    
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
    
    // Store the file for later submission
    window.storedActivityGrid = activityGrid;
    console.log('Storing activity grid file:', {
        name: activityGrid.name,
        size: activityGrid.size,
        type: activityGrid.type,
        lastModified: activityGrid.lastModified
    });
    
    // Store Step 2 data
    window.step2Data = {
        eventTitle: eventTitle,
        purpose: purpose
    };
    
    // Update display fields in Step 3
    document.getElementById('display_event_title').textContent = eventTitle;
    document.getElementById('display_purpose').textContent = purpose;
    document.getElementById('display_venue').textContent = window.step1Data.venueName;
    document.getElementById('display_capacity').textContent = window.step1Data.capacity + ' people';
    document.getElementById('display_price_rate').textContent = '₱' + window.step1Data.pricePerHour.toLocaleString() + '/hour';
    document.getElementById('display_base_price').textContent = '₱' + window.step1Data.basePrice.toLocaleString();
    
    // Format datetime display
    const startDate = new Date(window.step1Data.startDate);
    const endDate = new Date(window.step1Data.endDate);
    const dateTimeStr = startDate.toLocaleString() + ' - ' + endDate.toLocaleTimeString();
    document.getElementById('display_datetime').textContent = dateTimeStr;
    
    // Populate hidden fields
    document.getElementById('final_event_title').value = eventTitle;
    document.getElementById('final_purpose').value = purpose;
    document.getElementById('final_capacity').value = window.step1Data.capacity;
    document.getElementById('final_venue_id').value = window.step1Data.venueId;
    document.getElementById('final_start_date').value = window.step1Data.startDate;
    document.getElementById('final_end_date').value = window.step1Data.endDate;
    document.getElementById('final_price_per_hour').value = window.step1Data.pricePerHour;
    document.getElementById('final_base_price').value = window.step1Data.basePrice;
    
    // Close Step 2 and open Step 3
    closeActivityGridModal();
    openFinalDetailsModal();
    
    showToast('Activity Grid uploaded successfully! Complete the final details.', 'success');
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
const finalDetailsModalEl = document.getElementById('finalDetailsModal');
if (finalDetailsModalEl) {
    finalDetailsModalEl.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFinalDetailsModal();
        }
    });
}

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

// Step 1: Show venue suggestions based on capacity
function showStep1VenueSuggestions() {
    const capacityInput = document.getElementById('step1_capacity');
    const venueSuggestions = document.getElementById('step1_venueSuggestions');
    const suggestedVenuesList = document.getElementById('step1_suggestedVenuesList');
    
    const requestedCapacity = parseInt(capacityInput.value) || 0;
    
    if (requestedCapacity > 0) {
        const suitableVenues = venuesData.filter(venue => venue.capacity >= requestedCapacity);
        
        if (suitableVenues.length > 0) {
            suitableVenues.sort((a, b) => a.capacity - b.capacity);
            
            let suggestionsHtml = '';
            suitableVenues.slice(0, 3).forEach(venue => {
                const remainingCapacity = venue.capacity - requestedCapacity;
                
                suggestionsHtml += `
                    <div class="suggestion-item flex items-center justify-between p-2 bg-white rounded border border-green-200 transition-all duration-200 cursor-pointer" 
                         onclick="selectStep1SuggestedVenue('${venue.id}')">
                        <div class="flex-1">
                            <div class="font-medium text-green-800 text-xs">${venue.name}</div>
                            <div class="text-xs text-green-600">
                                Capacity: ${venue.capacity} | Price: ₱${venue.price_per_hour.toFixed(2)}/hr | +${remainingCapacity} extra spaces
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
            suggestedVenuesList.innerHTML = `
                <div class="p-2 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    No venues can accommodate ${requestedCapacity} attendees.
                </div>
            `;
            venueSuggestions.classList.remove('hidden');
        }
    } else {
        venueSuggestions.classList.add('hidden');
    }
}

// Select a suggested venue in Step 1
function selectStep1SuggestedVenue(venueId) {
    const venueSelect = document.getElementById('step1_venue_id');
    venueSelect.value = venueId;
    venueSelect.dispatchEvent(new Event('change'));
    document.getElementById('step1_venueSuggestions').classList.add('hidden');
    showToast('Venue selected from suggestions!', 'success');
}

// Validate capacity against selected venue in Step 1
function validateStep1Capacity() {
    const venueSelect = document.getElementById('step1_venue_id');
    const capacityInput = document.getElementById('step1_capacity');
    const capacityWarning = document.getElementById('step1_capacityWarning');
    const capacityWarningText = document.getElementById('step1_capacityWarningText');
    
    const requestedCapacity = parseInt(capacityInput.value) || 0;
    
    if (venueSelect.value && requestedCapacity > 0) {
        const selectedOption = venueSelect.options[venueSelect.selectedIndex];
        const venueCapacity = parseInt(selectedOption.dataset.capacity) || 0;
        const venueName = selectedOption.text.split(' - ')[0];
        
        capacityWarning.classList.add('hidden');
        
        if (requestedCapacity > venueCapacity) {
            capacityWarningText.textContent = `You are requesting ${requestedCapacity} attendees, but ${venueName} can only accommodate ${venueCapacity} people. Please reduce the number of attendees or select a larger venue.`;
            capacityWarning.classList.remove('hidden');
            capacityInput.classList.add('border-red-500');
        } else {
            capacityInput.classList.remove('border-red-500');
        }
    } else {
        capacityWarning.classList.add('hidden');
        capacityInput.classList.remove('border-red-500');
    }
}

// Fetch unavailable timeslots for Step 1
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

// Check for conflicts in Step 1
async function checkStep1Conflicts(){
    const venueId = document.getElementById('step1_venue_id').value;
    const startVal = document.getElementById('step1_start_date').value;
    const endVal = document.getElementById('step1_end_date').value;
    const container = document.getElementById('step1_conflictCheck');
    
    if(!venueId || !startVal) {
        container.classList.add('hidden');
        return;
    }
    
    const dateOnly = startVal.slice(0,10);
    const slots = await fetchUnavailableSlots(venueId, dateOnly);
    
    if(slots.length === 0){
        container.innerHTML = '<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3"><i class="fas fa-check-circle mr-2"></i>No conflicts found for this date and venue.</div>';
        container.classList.remove('hidden');
        return;
    }
    
    // Check if selected time overlaps with any unavailable slot
    if(endVal) {
        const start = new Date(startVal);
        const end = new Date(endVal);
        let hasConflict = false;
        let conflictSlot = null;
        
        for(const s of slots){
            const [sh, sm] = s.start.split(':').map(Number);
            const [eh, em] = s.end.split(':').map(Number);
            const blockStart = new Date(dateOnly+'T00:00'); 
            blockStart.setHours(sh, sm||0, 0, 0);
            const blockEnd = new Date(dateOnly+'T00:00'); 
            blockEnd.setHours(eh, em||0, 0, 0);
            
            // Check overlap
            if(start < blockEnd && end > blockStart){
                hasConflict = true;
                conflictSlot = s;
                break;
            }
        }
        
        if(hasConflict) {
            container.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-3">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2 mt-0.5"></i>
                        <div>
                            <p class="font-medium mb-1">⚠️ Time Conflict Detected!</p>
                            <p class="text-sm">Your selected time overlaps with: <strong>${conflictSlot.start} – ${conflictSlot.end}</strong> (${conflictSlot.title})</p>
                            <p class="text-xs mt-1">Please select a different time slot.</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            const list = slots.map(s=>`<li class="flex items-center text-xs"><i class="fas fa-ban text-red-500 mr-2"></i>${s.start} – ${s.end} <span class="text-gray-500 ml-2">(${s.title})</span></li>`).join('');
            container.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-3">
                    <div class="font-medium mb-1 text-sm">Unavailable timeslots for this day:</div>
                    <ul class="space-y-1 ml-4">${list}</ul>
                    <p class="text-xs mt-2 text-green-700"><i class="fas fa-check-circle mr-1"></i>Your selected time does not conflict.</p>
                </div>
            `;
        }
    } else {
        const list = slots.map(s=>`<li class="flex items-center text-xs"><i class="fas fa-ban text-red-500 mr-2"></i>${s.start} – ${s.end} <span class="text-gray-500 ml-2">(${s.title})</span></li>`).join('');
        container.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-3">
                <div class="font-medium mb-1 text-sm">Unavailable timeslots for this day:</div>
                <ul class="space-y-1 ml-4">${list}</ul>
            </div>
        `;
    }
    
    container.classList.remove('hidden');
}


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
        equipmentContainer.innerHTML = '<div class="text-center text-gray-500 py-2 text-xs">No equipment available for this venue</div>';
        return;
    }
    
    let equipmentHTML = '';
    
    availableEquipment.forEach((equipment, index) => {
        const equipmentId = equipment.name.toLowerCase().replace(/\s+/g, '_');
        const maxQuantity = equipment.quantity || 1;
        
        equipmentHTML += `
            <div class="border border-gray-200 rounded-lg p-2">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center">
                        <input type="checkbox" id="equipment_${equipmentId}" name="equipment[]" value="${equipment.name}" class="w-3 h-3 text-maroon border-gray-300 rounded focus:ring-maroon">
                        <label for="equipment_${equipmentId}" class="ml-1 text-xs font-medium text-gray-700">${equipment.name}</label>
                    </div>
                    <span class="text-xs text-gray-500">Avail: ${maxQuantity}</span>
                </div>
                <div id="${equipmentId}_quantity_container" class="hidden ml-4">
                    <div class="flex items-center space-x-1">
                        <label class="text-xs text-gray-600">Qty:</label>
                        <input type="number" id="${equipmentId}_quantity" name="equipment_quantity[${equipment.name}]" min="1" max="${maxQuantity}" value="1" class="w-12 px-1 py-0.5 text-xs border border-gray-300 rounded focus:ring-maroon focus:border-maroon">
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

    // Add custom equipment to summary
    const customEquipmentItems = document.querySelectorAll('#custom_equipment_list .custom-equipment-item');
    customEquipmentItems.forEach(item => {
        const nameInput = item.querySelector('input[name="custom_equipment_name[]"]');
        const qtyInput = item.querySelector('input[name="custom_equipment_quantity[]"]');
        if (nameInput && nameInput.value.trim() && qtyInput && qtyInput.value) {
            selected.push(`${nameInput.value.trim()} (x${qtyInput.value}) [Custom]`);
        }
    });

    if (noneChecked) {
        summaryEl.textContent = 'No equipment needed';
        return;
    }

    summaryEl.textContent = selected.length ? selected.join(', ') : 'None';
}

// Add custom equipment item
function addCustomEquipment() {
    const template = document.getElementById('custom_equipment_template');
    const container = document.getElementById('custom_equipment_list');
    
    // Clone the template
    const newItem = template.cloneNode(true);
    newItem.id = ''; // Remove the template id
    newItem.classList.remove('hidden');
    
    // Add required attributes to the inputs in the cloned item
    const nameInput = newItem.querySelector('input[name="custom_equipment_name[]"]');
    const quantityInput = newItem.querySelector('input[name="custom_equipment_quantity[]"]');
    if (nameInput) nameInput.setAttribute('required', 'required');
    if (quantityInput) quantityInput.setAttribute('required', 'required');
    
    // Add animation class
    newItem.classList.add('animate-fadeIn');
    
    // Append to container
    container.appendChild(newItem);
    
    // Focus on the first input
    if (nameInput) {
        nameInput.focus();
    }
    
    // Add event listeners for real-time summary update
    const inputs = newItem.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', updateSelectedEquipmentSummary);
    });
    
    // Update summary
    updateSelectedEquipmentSummary();
    
    showToast('Custom equipment item added. Fill in the details.', 'info');
}

// Remove custom equipment item
function removeCustomEquipment(button) {
    const item = button.closest('.custom-equipment-item');
    if (item) {
        // Add fade out animation
        item.style.opacity = '0';
        item.style.transform = 'translateX(100%)';
        item.style.transition = 'all 0.3s ease-out';
        
        setTimeout(() => {
            item.remove();
            updateSelectedEquipmentSummary();
        }, 300);
        
        showToast('Custom equipment item removed.', 'info');
    }
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

// Form validation - only for the reservation form
document.querySelector('form[action*="reservations"]').addEventListener('submit', function(e) {
    // Check if activity grid was uploaded in step 1
    if (!window.storedActivityGrid) {
        e.preventDefault();
        showToast('Please complete Step 1 (Activity Grid) before submitting the reservation.', 'error');
        return;
    }
    
    // Check capacity validation
    const venueSelect = document.getElementById('venue_id');
    const capacityInput = document.getElementById('capacity');
    const requestedCapacity = parseInt(capacityInput.value) || 0;
    
    if (venueSelect.value && requestedCapacity > 0) {
        const selectedOption = venueSelect.options[venueSelect.selectedIndex];
        const venueCapacity = parseInt(selectedOption.dataset.capacity) || 0;
        const venueName = selectedOption.text.split(' - ')[0];
        
        if (requestedCapacity > venueCapacity) {
            // Show warning but allow submission
            showToast(`⚠️ Warning: The requested capacity (${requestedCapacity}) exceeds the venue capacity (${venueCapacity}) for ${venueName}. This will be flagged for admin review.`, 'info');
        }
    }
    
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const capacity = parseInt(document.getElementById('capacity').value);
    const venueIdInput = document.getElementById('venue_id');
    
    // 3 days in advance restriction (must be at least 3 calendar days from today at midnight)
    const now = new Date();
    const minDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 3);
    minDate.setHours(0, 0, 0, 0); // Set to midnight
    
    if (startDate < minDate) {
        e.preventDefault();
        const minDateStr = minDate.toLocaleDateString();
        showToast(`Reservations must be made at least 3 days in advance. Earliest available date: ${minDateStr}`, 'error');
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
    
    // Custom equipment validation
    const customEquipmentItems = document.querySelectorAll('#custom_equipment_list .custom-equipment-item');
    customEquipmentItems.forEach((item, index) => {
        const nameInput = item.querySelector('input[name="custom_equipment_name[]"]');
        const qtyInput = item.querySelector('input[name="custom_equipment_quantity[]"]');
        
        if (nameInput && nameInput.value.trim()) {
            // If name is provided, quantity must also be provided and valid
            if (!qtyInput || !qtyInput.value || parseInt(qtyInput.value) < 1) {
                hasEquipmentError = true;
                showToast(`Please specify a valid quantity for custom equipment: ${nameInput.value.trim()}`, 'error');
                qtyInput?.focus();
            }
        } else if (qtyInput && qtyInput.value) {
            // If quantity is provided but name is empty
            hasEquipmentError = true;
            showToast(`Please specify the name for custom equipment item ${index + 1}`, 'error');
            nameInput?.focus();
        }
    });
    
    if (hasEquipmentError) {
        e.preventDefault();
        return;
    }
    
    // Attach the stored activity grid file to the form
    const formData = new FormData(this);
    formData.delete('activity_grid'); // Remove the hidden input
    
    // Add explicit JSON request flag
    formData.append('ajax', '1');
    
    // Check if activity grid file exists
    if (!window.storedActivityGrid) {
        e.preventDefault();
        showToast('Activity Grid file is missing. Please go back to Step 1 and re-upload the file.', 'error');
        return;
    }
    
    try {
        console.log('Retrieving stored activity grid file:', {
            exists: !!window.storedActivityGrid,
            name: window.storedActivityGrid?.name,
            size: window.storedActivityGrid?.size,
            type: window.storedActivityGrid?.type,
            isFile: window.storedActivityGrid instanceof File
        });
        
        formData.append('activity_grid', window.storedActivityGrid);
        console.log('Activity grid file attached successfully:', window.storedActivityGrid.name, window.storedActivityGrid.size + ' bytes');
    } catch (error) {
        e.preventDefault();
        console.error('Error attaching activity grid file:', error);
        showToast('Error preparing the activity grid file. Please try uploading it again.', 'error');
        return;
    }
    
    // Submit the form with the file
    e.preventDefault();
    submitReservationWithFile(formData);
});

// Function to show loading state
function showLoadingState() {
    const submitBtn = document.getElementById('submitReservationBtn');
    const btnContent = document.getElementById('submitBtnContent');
    const btnLoading = document.getElementById('submitBtnLoading');
    
    submitBtn.classList.add('button-loading');
    btnContent.classList.add('hidden');
    btnLoading.classList.remove('hidden');
}

// Function to hide loading state
function hideLoadingState() {
    const submitBtn = document.getElementById('submitReservationBtn');
    const btnContent = document.getElementById('submitBtnContent');
    const btnLoading = document.getElementById('submitBtnLoading');
    
    submitBtn.classList.remove('button-loading');
    btnContent.classList.remove('hidden');
    btnLoading.classList.add('hidden');
}

// Function to show success modal
function showSuccessModal() {
    document.getElementById('successModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Function to close success modal
function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Clear stored data and refresh page
    window.storedActivityGrid = null;
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Function to show error modal
function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Function to close error modal
function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Function to view my reservations
function viewMyReservations() {
    window.location.href = '{{ route("user.reservations.index") }}';
}

// Function to retry submission
function retrySubmission() {
    closeErrorModal();
    // The form is still populated, user can try submitting again
}

// Store the last form data for retry functionality
let lastFormData = null;

// Function to submit the reservation with the stored file
async function submitReservationWithFile(formData) {
    // Store form data for potential retry
    lastFormData = formData;
    
    // Show loading state
    showLoadingState();
    
    try {
        console.log('Submitting reservation...');
        console.log('Submitting to URL:', '{{ route("user.reservations.store") }}');
        
        // Check CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (!csrfToken || !csrfToken.value) {
            throw new Error('CSRF token is missing. Please refresh the page and try again.');
        }
        console.log('CSRF token found:', csrfToken.value.substring(0, 10) + '...');
        
        // Log form data for debugging
        console.log('Form data entries:');
        let hasActivityGrid = false;
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(key + ':', value.name, value.size + ' bytes', value.type);
                if (key === 'activity_grid') {
                    hasActivityGrid = true;
                }
            } else {
                console.log(key + ':', value);
            }
        }
        
        if (!hasActivityGrid) {
            console.log('⚠️ WARNING: No activity_grid file found in form data!');
        } else {
            console.log('✅ Activity grid file is included in form data');
        }
        
        const response = await fetch('{{ route("user.reservations.store") }}?ajax=1', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        // Check what type of response we got
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        // Hide loading state
        hideLoadingState();
        
        if (response.ok) {
            if (contentType && contentType.includes('application/json')) {
                const result = await response.json();
                console.log('✅ SUCCESS: JSON response received!');
                console.log('Success response:', result);
                
                if (result.success) {
                    console.log('✅ Database save confirmed!');
                    console.log('Reservation ID:', result.reservation_id);
                } else {
                    console.log('⚠️ Server returned success status but success=false');
                }
            } else {
                // Server returned HTML instead of JSON
                const htmlText = await response.text();
                console.log('Server returned HTML instead of JSON:');
                console.log('Full HTML response:', htmlText);
                console.log('First 1000 characters:', htmlText.substring(0, 1000));
                
                // Try to extract information from HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const title = doc.querySelector('title')?.textContent || '';
                const h1 = doc.querySelector('h1')?.textContent || '';
                
                console.log('HTML Title:', title);
                console.log('HTML H1:', h1);
                
                // Check for success indicators in the HTML first
                const successElements = doc.querySelectorAll('.alert-success, .success, .toast-success');
                const successTexts = Array.from(successElements).map(el => el.textContent.trim()).filter(text => text);
                
                // Also check if the HTML contains success keywords
                const hasSuccessKeywords = htmlText.includes('successfully') || 
                                         htmlText.includes('submitted') || 
                                         htmlText.includes('created') ||
                                         htmlText.includes('reservation has been');
                
                // Check for specific Laravel error patterns
                const hasValidationErrors = htmlText.includes('validation') || 
                                          htmlText.includes('required') ||
                                          htmlText.includes('invalid') ||
                                          htmlText.includes('error');
                
                // Check for database errors
                const hasDatabaseErrors = htmlText.includes('SQLSTATE') ||
                                        htmlText.includes('Integrity constraint') ||
                                        htmlText.includes('database');
                
                console.log('Detailed HTML analysis:');
                console.log('- Has success keywords:', hasSuccessKeywords);
                console.log('- Has validation errors:', hasValidationErrors);
                console.log('- Has database errors:', hasDatabaseErrors);
                console.log('- Success elements found:', successTexts.length);
                if (successTexts.length > 0 || hasSuccessKeywords) {
                    // Treat as success even though it returned HTML
                    console.log('✅ Success detected in HTML response!');
                    console.log('Success indicators:', successTexts);
                    console.log('Has success keywords:', hasSuccessKeywords);
                    
                    // But warn if there might be issues
                    if (hasValidationErrors || hasDatabaseErrors) {
                        console.log('❌ CRITICAL: Success detected but validation/database errors found!');
                        console.log('This means the form appeared to succeed but data was NOT saved to database!');
                        console.log('Check the full HTML response for error details.');
                        console.log('=== HTML RESPONSE EXCERPT (first 2000 chars) ===');
                        console.log(htmlText.substring(0, 2000));
                        console.log('=== END HTML EXCERPT ===');
                        
                        // Try to extract specific error messages
                        const errorPatterns = [
                            /validation.*failed/gi,
                            /required.*field/gi,
                            /SQLSTATE\[.*?\]/gi,
                            /Integrity constraint violation/gi,
                            /Column.*cannot be null/gi,
                            /Duplicate entry/gi
                        ];
                        
                        errorPatterns.forEach(pattern => {
                            const matches = htmlText.match(pattern);
                            if (matches) {
                                console.log('Found error pattern:', matches);
                            }
                        });
                        
                        // Try to extract more specific error details
                        console.log('Searching for specific validation errors...');
                        
                        // Extract actual Laravel validation errors from HTML
                        const errorDivs = doc.querySelectorAll('.alert-danger, .text-red-500, .error, .invalid-feedback');
                        if (errorDivs.length > 0) {
                            console.log('Laravel validation errors found:');
                            errorDivs.forEach((div, index) => {
                                const errorText = div.textContent.trim();
                                if (errorText && !errorText.includes('validation.*failed')) {
                                    console.log(`Error ${index + 1}: ${errorText}`);
                                }
                            });
                        }
                        
                        // Look for specific field validation messages in HTML
                        const fieldErrorRegex = /The ([a-zA-Z_]+) field is required/g;
                        let match;
                        const missingFields = [];
                        while ((match = fieldErrorRegex.exec(htmlText)) !== null) {
                            missingFields.push(match[1]);
                        }
                        
                        if (missingFields.length > 0) {
                            console.log('Missing required fields detected:', missingFields);
                        }
                        
                        // Look for database constraint errors
                        const sqlErrorRegex = /SQLSTATE\[(\d+)\].*?(\w+.*?)(?:\n|$)/g;
                        let sqlMatch;
                        while ((sqlMatch = sqlErrorRegex.exec(htmlText)) !== null) {
                            console.log('SQL Error:', sqlMatch[0]);
                        }
                        
                        // Look for Laravel validation error messages
                        const validationErrorRegex = /The\s+(\w+)\s+field\s+is\s+required/gi;
                        const validationMatches = [...htmlText.matchAll(validationErrorRegex)];
                        if (validationMatches.length > 0) {
                            console.log('Required field errors:');
                            validationMatches.forEach(match => {
                                console.log(`- The ${match[1]} field is required`);
                            });
                        }
                        
                        // Look for database constraint errors
                        const constraintRegex = /SQLSTATE\[23000\].*?Integrity constraint violation.*?Column '(\w+)' cannot be null/gi;
                        const constraintMatches = [...htmlText.matchAll(constraintRegex)];
                        if (constraintMatches.length > 0) {
                            console.log('Database constraint errors:');
                            constraintMatches.forEach(match => {
                                console.log(`- Column '${match[1]}' cannot be null`);
                            });
                        }
                        
                        // Look for foreign key constraint errors
                        const foreignKeyRegex = /foreign key constraint fails.*?`(\w+)`/gi;
                        const foreignKeyMatches = [...htmlText.matchAll(foreignKeyRegex)];
                        if (foreignKeyMatches.length > 0) {
                            console.log('Foreign key constraint errors:');
                            foreignKeyMatches.forEach(match => {
                                console.log(`- Foreign key constraint fails for table: ${match[1]}`);
                            });
                        }
                    }
                    // Continue to success handling below
                } else {
                    // Check for error indicators
                    const alertElements = doc.querySelectorAll('.alert-danger, .invalid-feedback, .error');
                    const errorTexts = Array.from(alertElements).map(el => el.textContent.trim()).filter(text => text);
                    
                    let specificError = 'Server returned HTML page instead of JSON response.';
                    if (title.includes('419') || title.includes('Page Expired')) {
                        specificError = 'Session expired (419). Please refresh the page and try again.';
                    } else if (title.includes('500') || title.includes('Server Error')) {
                        specificError = 'Server error (500). Please check the server logs.';
                    } else if (title.includes('404') || title.includes('Not Found')) {
                        specificError = 'Route not found (404). The submission endpoint may not exist.';
                    } else if (errorTexts.length > 0) {
                        specificError = `Validation errors found in response: ${errorTexts.join(', ')}`;
                    } else if (title.includes('Reservations')) {
                        specificError = 'Form submission was redirected back to reservations page. This usually means validation failed or the form was not processed correctly.';
                    }
                    
                    throw new Error(specificError);
                }
            }
            
            // Close reservation modal first
            closeReservationModal();
            
            // Show success modal
            showSuccessModal();
            
        } else {
            // Handle validation errors or other server errors
            let errorMessage = 'Failed to submit reservation. Please try again.';
            
            console.log('Error response status:', response.status);
            console.log('Error response content-type:', contentType);
            
            try {
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    console.log('Error response (JSON):', errorData);
                    
                    if (response.status === 422) {
                        // Validation errors - show detailed information
                        console.log('Validation errors details:', errorData.errors);
                        const errorFields = Object.keys(errorData.errors || {});
                        console.log('Fields with errors:', errorFields);
                        
                        // Create detailed error message
                        const errorMessages = Object.entries(errorData.errors || {}).map(([field, messages]) => {
                            return `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
                        });
                        
                        errorMessage = `Validation failed:\n${errorMessages.join('\n')}`;
                        console.log('Detailed error message:', errorMessage);
                    } else {
                        errorMessage = errorData.message || errorMessage;
                    }
                } else {
                    // Server returned HTML error page
                    const htmlText = await response.text();
                    console.log('Error response (HTML):', htmlText.substring(0, 1000) + '...');
                    
                    if (response.status === 419) {
                        errorMessage = 'Session expired. Please refresh the page and try again.';
                    } else if (response.status === 403) {
                        errorMessage = 'Access denied. Please check your permissions.';
                    } else if (response.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later or contact support.';
                    } else {
                        errorMessage = `Server error (${response.status}). Please try again.`;
                    }
                }
            } catch (e) {
                console.log('Could not parse error response:', e);
                errorMessage = `Server error (${response.status}). Please try again.`;
            }
            
            showErrorModal(errorMessage);
        }
    } catch (error) {
        console.error('Exception occurred:', error);
        hideLoadingState();
        
        let errorMessage = 'An error occurred while submitting the reservation.';
        
        if (error.name === 'TypeError' && error.message.includes('fetch')) {
            errorMessage = 'Network error: Unable to connect to the server. Please check your internet connection and try again.';
        } else if (error.name === 'TypeError') {
            errorMessage = 'Data processing error: There was an issue with the form data. Please refresh the page and try again.';
        } else if (error.message) {
            errorMessage = `Error: ${error.message}. Please try again.`;
        }
        
        showErrorModal(errorMessage);
    }
}

</script>
@endpush