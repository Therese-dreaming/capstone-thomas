@extends('layouts.iosa')

@section('title', 'Report Details - IOSA')
@section('page-title', 'Report Details')
@section('page-subtitle', 'GSU Report Information')

@section('content')
<!-- Google Fonts Import -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .font-poppins { font-family: 'Poppins', sans-serif; }
    
    .animate-fadeIn { 
        animation: fadeIn 0.3s ease-out; 
    }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .status-badge { 
        padding: 0.25rem 0.75rem; 
        border-radius: 9999px; 
        font-size: 0.75rem; 
        font-weight: 500; 
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
    }
    
    .status-pending { 
        background-color: #F59E0B; 
        color: #ffffff; 
    }
    
    .status-investigating { 
        background-color: #3B82F6; 
        color: #ffffff; 
    }
    
    .status-resolved { 
        background-color: #10B981; 
        color: #ffffff; 
    }
    
    .severity-critical { 
        background-color: #EF4444; 
        color: #ffffff; 
    }
    
    .severity-high { 
        background-color: #F97316; 
        color: #ffffff; 
    }
    
    .severity-medium { 
        background-color: #F59E0B; 
        color: #ffffff; 
    }
    
    .severity-low { 
        background-color: #10B981; 
        color: #ffffff; 
    }
    
    .type-accident { 
        background-color: #EF4444; 
        color: #ffffff; 
    }
    
    .type-problem { 
        background-color: #F59E0B; 
        color: #ffffff; 
    }
    
    .type-violation { 
        background-color: #8B5CF6; 
        color: #ffffff; 
    }
    
    .type-damage { 
        background-color: #F97316; 
        color: #ffffff; 
    }
    
    .type-other { 
        background-color: #6B7280; 
        color: #ffffff; 
    }
    
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        background-color: #dc2626;
        z-index: 2;
    }
    
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 0.875rem;
        top: 1.25rem;
        width: 2px;
        height: calc(100% + 0.5rem);
        background-color: #e5e7eb;
        z-index: 1;
    }
    
    .timeline-item:last-child::after {
        display: none;
    }
    
    .timeline-item.active::before {
        background-color: #10B981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
    
    .timeline-item.pending::before {
        background-color: #F59E0B;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }
    
    .timeline-item.investigating::before {
        background-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
</style>

<div class="space-y-6 font-inter animate-fadeIn">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center font-poppins mb-2">
                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        Report #{{ $report->id }}
                    </h1>
                    <p class="text-gray-600 font-medium">Detailed information about the reported issue</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('iosa.reports') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-arrow-left mr-1.5"></i>
                        <span>Back to Reports</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Report Information & Timeline -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Report Details Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">Report Information</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-red-600 mr-3 w-4"></i>
                                <span class="text-sm font-medium text-gray-700">Issue Type</span>
                            </div>
                            <span class="status-badge type-{{ $report->type }} text-xs">
                                {{ ucfirst($report->type) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation text-red-600 mr-3 w-4"></i>
                                <span class="text-sm font-medium text-gray-700">Severity</span>
                            </div>
                            <span class="status-badge severity-{{ $report->severity }} text-xs">
                                {{ ucfirst($report->severity) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-red-600 mr-3 w-4"></i>
                                <span class="text-sm font-medium text-gray-700">Status</span>
                            </div>
                            <span class="status-badge status-{{ $report->status }} text-xs">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-red-600 mr-3 w-4"></i>
                                <span class="text-sm font-medium text-gray-700">Reported Date</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $report->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($report->description)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border-l-4 border-red-600">
                        <div class="flex items-start">
                            <i class="fas fa-file-alt text-red-600 mr-3 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Description</p>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $report->description }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($report->actions_taken)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div class="flex items-start">
                            <i class="fas fa-tools text-blue-600 mr-3 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Actions Taken</p>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $report->actions_taken }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Timeline Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">Report Timeline</h3>
                </div>
                
                <div class="relative">
                    <!-- Report Created -->
                    <div class="timeline-item active">
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-green-800">Report Submitted</h4>
                                <span class="text-xs text-green-600">{{ $report->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            <p class="text-sm text-green-700">GSU staff {{ $report->reporter->name ?? 'N/A' }} submitted a report for {{ $report->reportedUser->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Status Updates -->
                    @if($report->status == 'investigating')
                        <div class="timeline-item investigating">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-blue-800">Under Investigation</h4>
                                    <span class="text-xs text-blue-600">Current</span>
                                </div>
                                <p class="text-sm text-blue-700">Report is currently being investigated by administration</p>
                            </div>
                        </div>
                    @endif

                    @if($report->status == 'resolved')
                        <div class="timeline-item investigating">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-blue-800">Under Investigation</h4>
                                    <span class="text-xs text-blue-600">{{ $report->updated_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <p class="text-sm text-blue-700">Report was moved to investigation phase</p>
                            </div>
                        </div>

                        <div class="timeline-item active">
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-green-800">Resolved</h4>
                                    <span class="text-xs text-green-600">{{ $report->resolved_at ? $report->resolved_at->format('M d, Y g:i A') : $report->updated_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <p class="text-sm text-green-700">Report has been resolved and closed</p>
                                @if($report->admin_notes)
                                    <div class="mt-2 p-2 bg-white rounded border">
                                        <p class="text-xs text-gray-600"><strong>Admin Notes:</strong> {{ $report->admin_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($report->status == 'pending')
                        <div class="timeline-item pending">
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-yellow-800">Pending Review</h4>
                                    <span class="text-xs text-yellow-600">Current</span>
                                </div>
                                <p class="text-sm text-yellow-700">Report is pending administrative review</p>
                            </div>
                        </div>
                    @endif

                    @if($report->status == 'dismissed')
                        <div class="timeline-item">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">Dismissed</h4>
                                    <span class="text-xs text-gray-600">{{ $report->updated_at->format('M d, Y g:i A') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">Report has been dismissed</p>
                                @if($report->admin_notes)
                                    <div class="mt-2 p-2 bg-white rounded border">
                                        <p class="text-xs text-gray-600"><strong>Admin Notes:</strong> {{ $report->admin_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Item Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-link text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">Related Item</h3>
                </div>
                
                @if($report->reservation)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Type</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">Reservation</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Event Title</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $report->reservation->event_title }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Date & Time</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $report->reservation->start_date->format('M d, Y g:i A') }} - {{ $report->reservation->end_date->format('g:i A') }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Venue</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $report->reservation->venue->name }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($report->event)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Type</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">Event</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Event Title</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $report->event->title }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Date & Time</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $report->event->start_date->format('M d, Y g:i A') }} - {{ $report->event->end_date->format('g:i A') }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-600 mr-3 w-4"></i>
                                    <span class="text-sm font-medium text-gray-700">Venue</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $report->event->venue->name }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <i class="fas fa-unlink text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">No related item found</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - People & Actions -->
        <div class="space-y-6">
            <!-- People Involved Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">People Involved</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user text-red-600 mr-2"></i>
                            <span class="text-sm font-medium text-red-800">Reported User</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">{{ $report->reportedUser->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $report->reportedUser->email ?? 'N/A' }}</p>
                        @if($report->reportedUser->department)
                            <p class="text-xs text-gray-500 mt-1">{{ $report->reportedUser->department }}</p>
                        @endif
                    </div>
                    
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-800">Reporter (GSU)</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">{{ $report->reporter->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $report->reporter->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Status Management Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">Status Management</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Current Status</span>
                            <span class="status-badge status-{{ $report->status }} text-xs">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($report->resolved_at)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Resolved Date</span>
                                <span class="text-sm font-medium text-gray-800">{{ $report->resolved_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Status Update Form -->
                    <div class="border-t border-gray-200 pt-4">
                        <form id="statusUpdateForm">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                                        <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="investigating" {{ $report->status == 'investigating' ? 'selected' : '' }}>Investigating</option>
                                        <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="dismissed" {{ $report->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                    <textarea id="admin_notes" name="admin_notes" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                                              placeholder="Add notes about the investigation or resolution...">{{ $report->admin_notes }}</textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                                    <i class="fas fa-save mr-2"></i>Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 font-poppins">Quick Actions</h3>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('iosa.reports') }}" 
                       class="w-full px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2 text-sm">
                        <i class="fas fa-list mr-1.5"></i>
                        <span>View All Reports</span>
                    </a>
                    
                    @if($report->reservation)
                        <a href="{{ route('iosa.reservations.show', $report->reservation->id) }}" 
                           class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2 text-sm">
                            <i class="fas fa-eye mr-1.5"></i>
                            <span>View Reservation</span>
                        </a>
                    @endif
                    
                    @if($report->event)
                        <a href="{{ route('iosa.events.show', $report->event->id) }}" 
                           class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center space-x-2 text-sm">
                            <i class="fas fa-eye mr-1.5"></i>
                            <span>View Event</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusUpdateForm = document.getElementById('statusUpdateForm');
    
    statusUpdateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("iosa.reports.update-status", $report) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification(data.message, 'success');
                
                // Reload the page to reflect changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Error updating status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating status', 'error');
        });
    });
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}
</script>
@endsection 