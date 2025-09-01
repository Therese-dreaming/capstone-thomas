@extends('layouts.mhadel')

@section('title', 'Report Details - Ms. Mhadel')
@section('page-title', 'Report Details')

@section('header-actions')
    <a href="{{ route('mhadel.reports') }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm mr-2 flex items-center">
        <i class="fas fa-arrow-left mr-2 text-maroon"></i>Back to Reports
    </a>
@endsection

@section('content')
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
    .btn-dark-green {
        background-color: #166534;
        color: white;
    }
    .btn-dark-green:hover {
        background-color: #15803d;
    }
    .btn-dark-red {
        background-color: #991b1b;
        color: white;
    }
    .btn-dark-red:hover {
        background-color: #dc2626;
    }
    .btn-dark-blue {
        background-color: #1e40af;
        color: white;
    }
    .btn-dark-blue:hover {
        background-color: #2563eb;
    }
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
        color: #1F2937;
    }
    .status-approved {
        background-color: #10B981;
        color: #1F2937;
    }
    .status-rejected {
        background-color: #EF4444;
        color: #1F2937;
    }
    .status-completed {
        background-color: #6366F1;
        color: #1F2937;
    }
</style>

<div class="space-y-6 font-inter">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center font-poppins">
                    <i class="fas fa-chart-bar text-maroon mr-3"></i>
                    Report Details
                </h1>
                <div class="flex items-center space-x-2">
                    @php
                        $st = $report->status;
                        $badge = 'status-pending';
                        if ($st === 'approved_IOSA' || $st === 'approved_mhadel' || $st === 'approved_OTP' || $st === 'approved') { 
                            $badge = 'status-approved'; 
                        }
                        if (str_starts_with($st, 'rejected') || $st === 'rejected') { 
                            $badge = 'status-rejected'; 
                        }
                    @endphp
                    <span class="status-badge {{ $badge }}">
                        {{ str_replace('_',' ', $report->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Report Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Details -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Event Information</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-xl mb-2">{{ $report->event_title ?? 'N/A' }}</h4>
                        <p class="text-gray-600">{{ $report->purpose ?? 'No purpose specified' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
                            <p class="text-gray-900">{{ optional($report->start_date)->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Time</label>
                            <p class="text-gray-900">
                                @if($report->start_date && $report->end_date)
                                    {{ \Carbon\Carbon::parse($report->start_date)->format('g:i A') }} - {{ \Carbon\Carbon::parse($report->end_date)->format('g:i A') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                            <p class="text-gray-900">
                                @if($report->start_date && $report->end_date)
                                    {{ \Carbon\Carbon::parse($report->start_date)->diffInHours(\Carbon\Carbon::parse($report->end_date)) }} hours
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                            <p class="text-gray-900">{{ $report->capacity ?? 'N/A' }} participants</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Information -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Venue Details</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-lg mb-2">{{ optional($report->venue)->name ?? 'N/A' }}</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Venue Capacity</label>
                            <p class="text-gray-900">{{ optional($report->venue)->capacity ?? 'N/A' }} people</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Venue Type</label>
                            <p class="text-gray-900">{{ optional($report->venue)->type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <p class="text-gray-900">{{ optional($report->venue)->location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rate per Hour</label>
                            <p class="text-gray-900">
                                @if(optional($report->venue)->price_per_hour)
                                    ₱{{ number_format(optional($report->venue)->price_per_hour, 2) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-peso-sign text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Financial Details</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                            <p class="text-gray-900">
                                @if($report->base_price)
                                    ₱{{ number_format($report->base_price, 2) }}
                                @else
                                    ₱0.00
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Applied</label>
                            <p class="text-gray-900">
                                @if($report->discount_percentage && $report->discount_percentage > 0)
                                    {{ $report->discount_percentage }}%
                                @else
                                    No discount
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Final Price</label>
                            <p class="text-xl font-bold text-green-600">
                                @if($report->final_price)
                                    ₱{{ number_format($report->final_price, 2) }}
                                @else
                                    ₱0.00
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <p class="text-gray-900">
                                @if($report->final_price && $report->final_price > 0)
                                    <span class="text-yellow-600 font-medium">Pending Payment</span>
                                @else
                                    <span class="text-green-600 font-medium">Free Event</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: User Information & Actions -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Requester Information</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <p class="text-gray-900 font-medium">{{ optional($report->user)->name ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <p class="text-gray-900">{{ optional($report->user)->email ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <p class="text-gray-900">{{ optional($report->user)->phone ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <p class="text-gray-900">{{ $report->department ?? optional($report->user)->department ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student/Faculty ID</label>
                        <p class="text-gray-900">{{ optional($report->user)->student_id ?? optional($report->user)->faculty_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Report Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tools text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Actions</h3>
                </div>
                
                <div class="space-y-3">
                    <button onclick="exportReport({{ $report->id }})" class="w-full btn-dark-green px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                    
                    <button onclick="printReport()" class="w-full btn-dark-blue px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-print mr-2"></i>Print Report
                    </button>
                    
                    <a href="{{ route('mhadel.reservations.show', $report->id) }}" class="w-full btn-dark-blue px-4 py-3 rounded-lg text-sm font-medium transition-colors text-center block">
                        <i class="fas fa-eye mr-2"></i>View Full Reservation
                    </a>
                </div>
            </div>

            <!-- Report Metadata -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Report Metadata</h3>
                </div>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Report ID:</span>
                        <span class="text-gray-900 font-medium">#{{ $report->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span class="text-gray-900">{{ $report->created_at ? $report->created_at->format('M d, Y g:i A') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="text-gray-900">{{ $report->updated_at ? $report->updated_at->format('M d, Y g:i A') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="status-badge {{ $badge }} text-xs">
                            {{ str_replace('_',' ', $report->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function exportReport(reportId) {
        // Add export functionality here
        console.log('Exporting report:', reportId);
        // You can implement CSV/Excel export here
        alert('Export functionality will be implemented here');
    }
    
    function printReport() {
        window.print();
    }
</script>
@endsection 