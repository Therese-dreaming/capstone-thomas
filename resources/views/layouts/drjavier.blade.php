<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dr. Javier - OTP') - PCC Reservation System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: '#800000',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        @media (min-width: 768px) {
            .sidebar.collapsed {
                transform: translateX(0);
            }
        }
        .toast {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 font-sans">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar" id="sidebar">
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
            <div class="flex items-center">
                <img src="{{ asset('images/pcclogo.png') }}" alt="PCC Logo" class="h-8 w-auto">
                <span class="ml-3 text-lg font-semibold text-gray-800">Dr. Javier</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="mt-6">
            <div class="px-6 mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-maroon rounded-full flex items-center justify-center text-white font-medium">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Dr. Javier (OTP)</p>
                    </div>
                </div>
            </div>
            
            <div class="px-3">
                <a href="{{ route('drjavier.dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('drjavier.dashboard') ? 'bg-gray-100' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('drjavier.reservations.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('drjavier.reservations.*') ? 'bg-gray-100' : '' }}">
                    <i class="fas fa-calendar-check w-5 mr-3"></i>
                    Reservations
                </a>
                
                <a href="#" class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Reports
                </a>
                
                <a href="#" class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    Settings
                </a>
            </div>
        </nav>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('page-subtitle')
                            <p class="text-sm text-gray-500">@yield('page-subtitle')</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @hasSection('header-actions')
                        @yield('header-actions')
                    @endif
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</span>
                        <span class="text-sm text-gray-500">{{ now()->format('g:i A') }}</span>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-6">
            @if(session('success'))
                <div class="mb-6 toast bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 toast bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
        
        // Auto-hide toasts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 