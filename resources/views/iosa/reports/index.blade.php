@extends('layouts.iosa')

@section('title', 'GSU Reports - IOSA')
@section('page-title', 'GSU Reports')
@section('page-subtitle', 'Reports filed by General Services Unit')

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
    
    .report-card { 
        transition: all 0.3s ease; 
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    
    .report-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
        border-color: #dc2626;
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
    
    .status-dismissed { 
        background-color: #6B7280; 
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
                        GSU Reports
                    </h1>
                    <p class="text-gray-600 font-medium">Monitor and manage reports filed by General Services Unit</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="openExportModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-file-excel mr-1.5"></i>
                        <span>Export to Excel</span>
                    </button>
                    <a href="{{ route('iosa.dashboard') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2 text-sm">
                        <i class="fas fa-arrow-left mr-1.5"></i>
                        <span>Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-file-alt text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Reports</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-search text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Investigating</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['investigating'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Resolved</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['resolved'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-gray-50 p-3 mr-4">
                <i class="fas fa-times-circle text-gray-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Dismissed</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['dismissed'] ?? 0 }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-exclamation text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Critical</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['critical'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center animate-fadeIn">
            <div class="rounded-full bg-orange-50 p-3 mr-4">
                <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">High Priority</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['high'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Issue Type</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <option value="">All Types</option>
                    <option value="accident" {{ request('type') == 'accident' ? 'selected' : '' }}>Accident</option>
                    <option value="problem" {{ request('type') == 'problem' ? 'selected' : '' }}>Problem</option>
                    <option value="violation" {{ request('type') == 'violation' ? 'selected' : '' }}>Violation</option>
                    <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Damage</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                <select name="severity" id="severity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <option value="">All Severities</option>
                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-poppins">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                    GSU Reports List
                </h2>
                <div class="text-sm text-gray-500">
                    Showing {{ $reports->count() }} of {{ $stats['total'] }} reports
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if($reports->count() > 0)
                <div class="space-y-4">
                    @foreach($reports as $report)
                        <div class="report-card rounded-lg p-6 hover:shadow-lg transition-all duration-300">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="status-badge status-{{ $report->status }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        #{{ $report->id }}
                                    </span>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ $report->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('iosa.reports.show', $report) }}" 
                                       class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg flex items-center space-x-2">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        <span>View Details</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Content Grid -->
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <!-- Report Details -->
                                <div class="space-y-3">
                                    <h3 class="font-bold text-gray-800 text-lg mb-3">Issue Report</h3>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Type:</span>
                                            <span class="ml-1 status-badge type-{{ $report->type }} text-xs">
                                                {{ ucfirst($report->type) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Severity:</span>
                                            <span class="ml-1 status-badge severity-{{ $report->severity }} text-xs">
                                                {{ ucfirst($report->severity) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Date:</span>
                                            <span class="ml-1">{{ $report->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Reported User -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-user text-red-600 mr-2"></i>
                                        Reported User
                                    </h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Name:</span>
                                            <span class="ml-1">{{ $report->reportedUser->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Email:</span>
                                            <span class="ml-1">{{ $report->reportedUser->email ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-building mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Department:</span>
                                            <span class="ml-1">{{ $report->reportedUser->department ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Reporter -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-user-shield text-red-600 mr-2"></i>
                                        Reporter
                                    </h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-user mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Name:</span>
                                            <span class="ml-1">{{ $report->reporter->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-badge mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Role:</span>
                                            <span class="ml-1">GSU Staff</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-red-600 w-4"></i>
                                            <span class="font-medium">Reported:</span>
                                            <span class="ml-1">{{ $report->created_at->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description Preview -->
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-file-alt text-red-600 mr-2"></i>
                                        Description
                                    </h4>
                                    <div class="text-sm text-gray-600">
                                        @if(strlen($report->description) > 100)
                                            {{ substr($report->description, 0, 100) }}...
                                        @else
                                            {{ $report->description }}
                                        @endif
                                    </div>
                                    @if($report->actions_taken)
                                        <div class="mt-2">
                                            <span class="text-xs font-medium text-gray-500">Actions Taken:</span>
                                            <div class="text-xs text-gray-600 mt-1">
                                                @if(strlen($report->actions_taken) > 80)
                                                    {{ substr($report->actions_taken, 0, 80) }}...
                                                @else
                                                    {{ $report->actions_taken }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($reports->hasPages())
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-lg shadow-md p-3">
                        {{ $reports->links() }}
                    </div>
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No GSU Reports Found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto text-sm">Reports filed by GSU will appear here once they are submitted.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-file-excel text-green-600 mr-3"></i>
                        Export GSU Reports to Excel
                    </h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="exportForm" method="GET" action="{{ route('iosa.reports.export') }}" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Export Options -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Export Options</h4>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="radio" id="exportAll" name="export_type" value="all" checked 
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <label for="exportAll" class="ml-3 text-sm font-medium text-gray-700">
                                        Export All Reports
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="includeSummary" name="include_summary" value="1" checked 
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <label for="includeSummary" class="ml-3 text-sm font-medium text-gray-700">
                                        Include Summary Sheet
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Current Filters</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Issue Type:</span>
                                    <span class="text-gray-800">{{ request('type') ? ucfirst(request('type')) : 'All' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Severity:</span>
                                    <span class="text-gray-800">{{ request('severity') ? ucfirst(request('severity')) : 'All' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Status:</span>
                                    <span class="text-gray-800">{{ request('status') ? ucfirst(request('status')) : 'All' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600">Date Range:</span>
                                    <span class="text-gray-800">
                                        @if(request('start_date') && request('end_date'))
                                            {{ request('start_date') }} to {{ request('end_date') }}
                                        @elseif(request('start_date'))
                                            From {{ request('start_date') }}
                                        @elseif(request('end_date'))
                                            Until {{ request('end_date') }}
                                        @else
                                            All dates
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Summary -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Export Summary</h4>
                            <div class="bg-green-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">Total Reports:</span>
                                    <span class="text-2xl font-bold text-green-600">{{ $stats['total'] }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pending:</span>
                                        <span class="font-medium text-yellow-600">{{ $stats['pending'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Investigating:</span>
                                        <span class="font-medium text-blue-600">{{ $stats['investigating'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Resolved:</span>
                                        <span class="font-medium text-green-600">{{ $stats['resolved'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Critical:</span>
                                        <span class="font-medium text-red-600">{{ $stats['critical'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Export Information:</p>
                                    <ul class="space-y-1 text-xs">
                                        <li>• Excel file will include all filtered reports</li>
                                        <li>• Summary sheet provides overview statistics</li>
                                        <li>• File will be downloaded automatically</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden inputs to preserve current filters -->
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="severity" value="{{ request('severity') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeExportModal()" 
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center space-x-2">
                        <i class="fas fa-download mr-2"></i>
                        <span>Export to Excel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});
</script>
@endsection 