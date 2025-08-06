@extends('layouts.admin')

@section('title', 'Reservations Management')
@section('page-title', 'Reservations Management')

@section('header-actions')
    <a href="#" class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-opacity-80 transition">
        <i class="fas fa-plus mr-2"></i>Add New Reservation
    </a>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">a@extends('layouts.admin')

@section('title', 'Reservations Management')
@section('page-title', 'Reservations Management')

@section('header-actions')
    <button id="openFilterBtn" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm mr-2 flex items-center">
        <i class="fas fa-filter mr-2 text-maroon"></i>Filter
    </button>
    <a href="#" class="bg-gradient-to-r from-maroon to-red-700 text-white px-4 py-2 rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md flex items-center">
        <i class="fas fa-plus mr-2"></i>Add New Reservation
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
        min-width: 2.5rem;
        max-width: 3rem;
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
    .reservation-card {
        transition: all 0.3s ease;
    }
    .reservation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        background-color: #FEF3C7;
        color: #92400E;
    }
    .status-approved {
        background-color: #D1FAE5;
        color: #065F46;
    }
    .status-rejected {
        background-color: #FEE2E2;
        color: #991B1B;
    }
    .status-completed {
        background-color: #E0E7FF;
        color: #3730A3;
    }
    .tab-active {
        border-bottom: 2px solid #800000;
        color: #800000;
        font-weight: 500;
    }
</style>

<div class="space-y-6 font-poppins">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-blue-50 p-3 mr-4">
                <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Reservations</p>
                <h3 class="text-2xl font-bold text-gray-800">124</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-green-50 p-3 mr-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Approved</p>
                <h3 class="text-2xl font-bold text-gray-800">86</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-yellow-50 p-3 mr-4">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-2xl font-bold text-gray-800">28</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
            <div class="rounded-full bg-red-50 p-3 mr-4">
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Rejected</p>
                <h3 class="text-2xl font-bold text-gray-800">10</h3>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                    <i class="fas fa-calendar-check text-maroon mr-3"></i>
                    Reservation Management
                </h2>
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <input type="text" placeholder="Search reservations..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon transition-colors">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200">
            <button class="px-6 py-3 text-gray-700 hover:text-maroon transition-colors tab-active">
                All Reservations
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Pending
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Approved
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Rejected
            </button>
            <button class="px-6 py-3 text-gray-500 hover:text-maroon transition-colors">
                Completed
            </button>
        </div>
        
        <!-- Calendar View Toggle -->
        <div class="flex justify-end p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                <button class="px-4 py-2 bg-white text-gray-700 border-r border-gray-300 flex items-center">
                    <i class="fas fa-list mr-2 text-maroon"></i>List View
                </button>
                <button class="px-4 py-2 bg-gray-100 text-gray-500 flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>Calendar View
                </button>
            </div>
        </div>
        
        <!-- Reservations List -->
        <div class="p-6">
            <!-- Sample Reservations - Replace with actual data -->
            <div class="space-y-4">
                <!-- Reservation Card 1 -->
                <div class="reservation-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="status-badge status-pending mr-3">Pending</span>
                                <span class="text-sm text-gray-500">Submitted 2 days ago</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Annual Department Meeting</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-user mr-2 text-maroon"></i>
                                <span>John Doe (Computer Science Department)</span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-maroon"></i>
                                    <span>May 15, 2023</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-maroon"></i>
                                    <span>9:00 AM - 12:00 PM</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                                    <span>Main Auditorium</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2 text-maroon"></i>
                                    <span>120 attendees</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 mt-4 md:mt-0">
                            <button class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reservation Card 2 -->
                <div class="reservation-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="status-badge status-approved mr-3">Approved</span>
                                <span class="text-sm text-gray-500">Approved 1 day ago</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Student Council Elections</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-user mr-2 text-maroon"></i>
                                <span>Jane Smith (Student Affairs)</span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-maroon"></i>
                                    <span>May 20, 2023</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-maroon"></i>
                                    <span>8:00 AM - 5:00 PM</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                                    <span>Student Center</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2 text-maroon"></i>
                                    <span>500 attendees</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 mt-4 md:mt-0">
                            <button class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Reservation Card 3 -->
                <div class="reservation-card bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="status-badge status-rejected mr-3">Rejected</span>
                                <span class="text-sm text-gray-500">Rejected 3 days ago</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">Faculty Workshop</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-user mr-2 text-maroon"></i>
                                <span>Robert Johnson (Faculty Development)</span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-maroon"></i>
                                    <span>May 10, 2023</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-maroon"></i>
                                    <span>1:00 PM - 4:00 PM</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-maroon"></i>
                                    <span>Conference Room A</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-2 text-maroon"></i>
                                    <span>30 attendees</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 mt-4 md:mt-0">
                            <button class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing 1-3 of 124 reservations
                </div>
                <div class="flex space-x-1">
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-1 rounded border border-gray-300 bg-maroon text-white">1</button>
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">4</button>
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">5</button>
                    <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-screen overflow-y-auto font-poppins animate-fadeIn">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center font-montserrat">
                            <i class="fas fa-filter text-maroon mr-2"></i>
                            Filter Reservations
                        </h3>
                        <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 bg-white rounded-full p-2 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                <span class="ml-2 text-gray-700">Pending</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                <span class="ml-2 text-gray-700">Approved</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                <span class="ml-2 text-gray-700">Rejected</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                <span class="ml-2 text-gray-700">Completed</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">From</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">To</label>
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Venue Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Venues</option>
                            <option value="1">Main Auditorium</option>
                            <option value="2">Conference Room A</option>
                            <option value="3">Conference Room B</option>
                            <option value="4">Student Center</option>
                            <option value="5">Faculty Lounge</option>
                        </select>
                    </div>
                    
                    <!-- Department Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">All Departments</option>
                            <option value="1">Computer Science</option>
                            <option value="2">Engineering</option>
                            <option value="3">Business Administration</option>
                            <option value="4">Student Affairs</option>
                            <option value="5">Faculty Development</option>
                        </select>
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeFilterModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Reset
                    </button>
                    <button class="px-4 py-2 bg-gradient-to-r from-maroon to-red-700 text-white rounded-lg hover:from-red-700 hover:to-maroon transition-all duration-300 shadow-md">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter Modal Functions
    function openFilterModal() {
        document.getElementById('filterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeFilterModal() {
        document.getElementById('filterModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set up event listeners
        document.getElementById('openFilterBtn').addEventListener('click', openFilterModal);
        
        // Close modal when clicking outside
        document.getElementById('filterModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFilterModal();
            }
        });
    });
</script>
@endsection
        <h3 class="text-lg font-semibold text-gray-800">All Reservations</h3>
        <p class="text-gray-600 text-sm">Manage venue reservations</p>
    </div>

    <div class="p-6">
        <div class="text-center py-12">
            <i class="fas fa-calendar-check text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-medium text-gray-700 mb-2">Reservations Coming Soon</h3>
            <p class="text-gray-500">This feature is currently under development.</p>
        </div>
    </div>
</div>
@endsection
