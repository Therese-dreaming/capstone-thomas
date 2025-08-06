@extends('layouts.admin')

@section('title', 'Events Management')
@section('page-title', 'Events Management')

@section('header-actions')
    <a href="#" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-opacity-80 transition">
        <i class="fas fa-plus mr-2"></i>Add New Event
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Events</h3>
        <p class="text-gray-600 text-sm">Manage venue events and activities</p>
    </div>

    <div class="p-6">
        <div class="text-center py-12">
            <i class="fas fa-calendar-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-700 mb-2">Events Management Coming Soon</h3>
            <p class="text-gray-500">This feature is currently under development.</p>
        </div>
    </div>
</div>
@endsection
