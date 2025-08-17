@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Venue, Equipment & Event Management')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-maroon to-red-700 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
                <p class="text-gray-200">You are logged in as an Administrator. Manage venues, equipment, and events here.</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ date('M d, Y') }}</div>
                <div class="text-gray-200">{{ date('l') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-blue-50 p-3 mr-4">
                    <i class="fas fa-building text-blue-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Venues</p>
                    <h3 class="text-2xl font-bold text-gray-800">12</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-green-50 p-3 mr-4">
                    <i class="fas fa-tools text-green-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Equipment Items</p>
                    <h3 class="text-2xl font-bold text-gray-800">45</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="rounded-full bg-purple-50 p-3 mr-4">
                    <i class="fas fa-calendar-alt text-purple-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Active Events</p>
                    <h3 class="text-2xl font-bold text-gray-800">8</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Management Cards -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-cogs text-maroon mr-2"></i>
                    Management Tools
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.venues.index') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-building text-blue-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">Venues</span>
                    </a>
                    
                    <a href="" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <i class="fas fa-tools text-green-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">Equipment</span>
                    </a>
                    
                    <a href="{{ route('admin.events.index') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <i class="fas fa-calendar-alt text-purple-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">Events</span>
                    </a>
                    
                    <a href="#" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <i class="fas fa-chart-bar text-orange-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-700">Reports</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-maroon mr-2"></i>
                    System Information
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Role</h4>
                        <p class="text-gray-600">Administrator</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Permissions</h4>
                        <p class="text-gray-600">Venue, Equipment & Event Management</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Last Login</h4>
                        <p class="text-gray-600">{{ date('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">System Status</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            All Systems Operational
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-maroon mr-2"></i>
                Recent Activity
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-800">New venue added</h4>
                        <p class="text-sm text-gray-600">Conference Room B</p>
                        <p class="text-xs text-gray-500">2 hours ago</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Added</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-800">Equipment updated</h4>
                        <p class="text-sm text-gray-600">Projector - Main Auditorium</p>
                        <p class="text-xs text-gray-500">4 hours ago</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Updated</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-800">Event created</h4>
                        <p class="text-sm text-gray-600">Faculty Meeting</p>
                        <p class="text-xs text-gray-500">1 day ago</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Created</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 