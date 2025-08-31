@extends('layouts.mhadel')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')
@section('page-subtitle', 'Update event details and information')

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
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: #8B0000;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(139, 0, 0, 0.2);
    }
    
    .btn-primary:hover {
        background: #7F0000;
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(139, 0, 0, 0.3);
    }
    
    .btn-secondary {
        background: #6B7280;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.2);
    }
    
    .btn-secondary:hover {
        background: #4B5563;
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(107, 114, 128, 0.3);
    }
    
    .btn-outline {
        background: transparent;
        color: #8B0000;
        border: 2px solid #8B0000;
    }
    
    .btn-outline:hover {
        background: #8B0000;
        color: white;
    }
    
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        margin: 2rem 0;
    }
    
    .info-box {
        background: #eff6ff;
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
                    <i class="fas fa-edit text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 font-poppins mb-2">Edit Event</h1>
                    <p class="text-gray-600 text-lg">Update the details for "{{ $event->title }}"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="form-card animate-fadeIn">
        <div class="form-header">
            <h2 class="text-xl font-bold text-gray-800 font-poppins flex items-center">
                <i class="fas fa-calendar-edit text-maroon mr-3 text-2xl"></i>
                Event Details
            </h2>
            <p class="text-gray-600 mt-2">Modify the information below to update your event</p>
        </div>

        <form action="{{ route('mhadel.events.update', $event) }}" method="POST" class="form-content">
            @csrf
            @method('PUT')

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
                        value="{{ old('title', $event->title) }}"
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
                        value="{{ old('organizer', $event->organizer) }}"
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
                        value="{{ old('department', $event->department) }}"
                        class="form-input @error('department') error @enderror"
                        placeholder="Enter department name"
                    >
                    <p class="help-text">Optional. Specify which department this event belongs to.</p>
                    @error('department')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description" class="form-label">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-textarea @error('description') error @enderror"
                    placeholder="Enter event description (optional)"
                >{{ old('description', $event->description) }}</textarea>
                <p class="help-text">Provide a detailed description of the event for participants.</p>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="section-divider"></div>

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
                    <option value="{{ $venue->id }}" {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
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
                        value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}"
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
                        value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}"
                        class="form-input @error('end_date') error @enderror"
                        required
                    >
                    @error('end_date')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="section-divider"></div>

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
                        value="{{ old('max_participants', $event->max_participants) }}"
                        min="1"
                        class="form-input @error('max_participants') error @enderror"
                        placeholder="Enter maximum number of participants"
                    >
                    <p class="help-text">Leave empty if there's no participant limit.</p>
                    @error('max_participants')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="section-divider"></div>

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
                    <p class="mt-2 text-sm"><em>Note: Cancelled events will remain cancelled unless manually reactivated.</em></p>
                </div>
            </div>

            <!-- Helpful Information -->
            <div class="info-box">
                <div class="info-box-title">
                    <i class="fas fa-lightbulb"></i>
                    Tips for Editing Events
                </div>
                <div class="info-box-text">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Changing the venue will check for conflicts with existing reservations and events</li>
                        <li>Updating dates will validate against venue availability</li>
                        <li>Status changes will affect how the event appears in the system</li>
                        <li>All changes are logged for audit purposes</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-save"></i>
                    Update Event
                </button>
                <a href="{{ route('mhadel.events.show', $event) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    View Event
                </a>
                <a href="{{ route('mhadel.events.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-calculate end date when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('end_date');
    
    if (startDate && !endDateInput.value) {
        // Set end date to 2 hours after start date
        const endDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000));
        endDateInput.value = endDate.toISOString().slice(0, 16);
    }
});

// Validate that end date is after start date
document.getElementById('end_date').addEventListener('change', function() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(this.value);
    
    if (endDate <= startDate) {
        alert('End date must be after start date');
        this.value = '';
    }
});

// Validate that start date is not in the past
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const now = new Date();
    
    if (startDate < now) {
        alert('Start date cannot be in the past');
        this.value = '';
    }
});
</script>
@endsection 