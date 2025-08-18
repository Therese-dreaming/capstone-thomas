@extends('layouts.mhadel')

@section('title', 'Add New Event')
@section('page-title', 'Add New Event')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200 flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Create an Event</h3>
                <p class="text-gray-600 text-sm">Provide details below to publish your event.</p>
            </div>
            <a href="{{ route('mhadel.events.index') }}" class="hidden sm:inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Events
            </a>
        </div>

        <form action="{{ route('mhadel.events.store') }}" method="POST" class="p-6">
            @csrf

            @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <p class="font-semibold mb-2">Please fix the following issues:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Event Title <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('title') border-red-500 @enderror"
                        placeholder="Enter event title"
                        required
                        aria-required="true"
                    >
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Organizer -->
                <div>
                    <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">
                        Organizer <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="organizer" 
                        name="organizer" 
                        value="{{ old('organizer') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('organizer') border-red-500 @enderror"
                        placeholder="Enter organizer name"
                        required
                        aria-required="true"
                    >
                    @error('organizer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Venue -->
                <div class="md:col-span-2">
                    <label for="venue_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Venue <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="venue_id" 
                        name="venue_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('venue_id') border-red-500 @enderror"
                        required
                        aria-required="true"
                    >
                        <option value="">Select venue</option>
                        @foreach(\App\Models\Venue::where('is_available', true)->get() as $venue)
                        <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                            {{ $venue->name }} (Capacity: {{ $venue->capacity }})
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Only currently available venues are listed.</p>
                    @error('venue_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="start_date" 
                        name="start_date" 
                        value="{{ old('start_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('start_date') border-red-500 @enderror"
                        required
                        aria-required="true"
                    >
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        id="end_date" 
                        name="end_date" 
                        value="{{ old('end_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('end_date') border-red-500 @enderror"
                        required
                        aria-required="true"
                    >
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('status') border-red-500 @enderror"
                        required
                        aria-required="true"
                    >
                        <option value="">Select status</option>
                        <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Participants -->
                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                        Maximum Participants
                    </label>
                    <input 
                        type="number" 
                        id="max_participants" 
                        name="max_participants" 
                        value="{{ old('max_participants') }}"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('max_participants') border-red-500 @enderror"
                        placeholder="Leave empty for no limit"
                    >
                    <p class="mt-1 text-xs text-gray-500">Optional. Leave blank to allow unlimited attendees.</p>
                    @error('max_participants')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon focus:border-maroon @error('description') border-red-500 @enderror"
                        placeholder="Enter event description (optional)"
                    >{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-8">
                <p class="text-xs text-gray-500">Fields marked with <span class="text-red-500">*</span> are required.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('mhadel.events.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-opacity-80 transition">
                        Save Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
