<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard') - PCC Venue Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-maroon {
            background-color: #8B1818;
        }
        .text-maroon {
            color: #8B1818;
        }
        .border-maroon {
            border-color: #8B1818;
        }
        .hover-bg-maroon:hover {
            background-color: rgba(139, 24, 24, 0.1);
        }
        .active-maroon {
            background-color: #8B1818;
            color: white;
        }
        .sidebar-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg flex flex-col">
            <!-- Logo and Brand -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-maroon rounded flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">PCC Venue</h1>
                        <p class="text-xs text-gray-500">Reservation System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('user.dashboard') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('user.dashboard') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-home w-5 h-5"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>

                    <!-- Reservations -->
                    <li>
                        <a href="{{ route('user.reservations.index') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('user.reservations.index') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-calendar-alt w-5 h-5"></i>
                            <span class="font-medium">My Reservations</span>
                        </a>
                    </li>

                    <!-- New Reservation -->
                    <li>
                        <a href="{{ route('user.reservations.calendar') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('user.reservations.calendar') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-calendar-plus w-5 h-5"></i>
                            <span class="font-medium">New Reservation</span>
                        </a>
                    </li>

                    <!-- Profile -->
                    <li>
                        <a href="{{ route('user.profile') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('user.profile*') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-user w-5 h-5"></i>
                            <span class="font-medium">Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info and Logout -->
            <div class="border-t border-gray-200 p-4">
                <!-- User Info -->
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">
                            {{ Auth::user()->name ?? 'User' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()->email ?? 'user@example.com' }}
                        </p>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon text-gray-700 hover:text-maroon">
                        <i class="fas fa-sign-out-alt w-5 h-5"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h2>
                    
                    <!-- Breadcrumb or additional header content -->
                    <div class="flex items-center space-x-4">
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <p class="text-red-700 text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <p class="text-blue-700 text-sm">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-2">
        @if(session('success'))
            <div class="toast bg-green-500 text-white px-4 py-3 rounded shadow flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="toast bg-red-500 text-white px-4 py-3 rounded shadow flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="toast bg-blue-500 text-white px-4 py-3 rounded shadow flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast bg-red-500 text-white px-4 py-3 rounded shadow flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        @endif
    </div>

    <!-- JavaScript -->
    <script>
        // Add active state handling
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar navigation active states
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active-maroon');
                    link.classList.remove('text-gray-700');
                }
            });
        });

        // Toast auto-dismiss
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(toast) {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => toast.remove(), 500);
                }, 4000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
