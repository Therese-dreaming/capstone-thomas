<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ms. Mhadel Dashboard') - PCC Venue Reservation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        /* Loading Overlay */
        .loading-overlay { position: fixed; inset: 0; background: rgba(17,24,39,0.55); backdrop-filter: blur(3px); z-index: 9999; display: none; align-items: center; justify-content: center; }
        .loading-card { background: #fff; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); width: 92%; max-width: 360px; text-align: center; }
        .spinner { width: 2rem; height: 2rem; border: 3px solid #eee; border-top-color: #8B1818; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 0.75rem; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Include safelist to preserve Tailwind classes -->
    @include('tw-safelist')
    
    <!-- Global Loading Overlay -->
    <div id="globalLoading" class="loading-overlay">
        <div class="loading-card">
            <div class="spinner"></div>
            <div class="text-sm text-gray-700" id="globalLoadingText">Processing your request…</div>
        </div>
    </div>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg flex flex-col">
            <!-- Logo and Brand -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-maroon rounded flex items-center justify-center">
                        <i class="fas fa-user-tie text-white text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">Ms. Mhadel</h1>
                        <p class="text-xs text-gray-500">PCC Venue System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('mhadel.dashboard') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.dashboard') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>

                    <!-- Reservations -->
                    <li>
                        <a href="{{ route('mhadel.reservations.index') }}"
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.reservations.index') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-calendar-check w-5 h-5"></i>
                            <span class="font-medium">Reservations</span>
                        </a>
                    </li>

                    <!-- Calendar -->
                    <li>
                        <a href="{{ route('mhadel.reservations.calendar') }}"
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.reservations.calendar') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-calendar-alt w-5 h-5"></i>
                            <span class="font-medium">Calendar</span>
                        </a>
                    </li>

                    <!-- Venues -->
                    <li>
                        <a href="{{ route('mhadel.venues.index') }}"
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.venues*') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-building w-5 h-5"></i>
                            <span class="font-medium">Venues</span>
                        </a>
                    </li>

                    <!-- Events -->
                    <li>
                        <a href="{{ route('mhadel.events.index') }}"
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.events*') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-calendar-alt w-5 h-5"></i>
                            <span class="font-medium">Events</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li>
                        <a href="{{ route('mhadel.reports') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.reports') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-chart-bar w-5 h-5"></i>
                            <span class="font-medium">Reports</span>
                        </a>
                    </li>

                    <!-- GSU Reports -->
                    <li>
                        <a href="{{ route('mhadel.gsu-reports') }}" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('mhadel.gsu-reports*') ? 'active-maroon' : 'text-gray-700' }}">
                            <i class="fas fa-exclamation-triangle w-5 h-5"></i>
                            <span class="font-medium">GSU Reports</span>
                        </a>
                    </li>

                    <!-- Settings -->
                    <li>
                        <a href="#" 
                           class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon text-gray-700">
                            <i class="fas fa-cog w-5 h-5"></i>
                            <span class="font-medium">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info and Logout -->
            <div class="border-t border-gray-200 p-4">
                <!-- User Info -->
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-gray-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">
                            {{ Auth::user()->name ?? 'Ms. Mhadel' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()->email ?? 'mhadel@pcc.edu.ph' }}
                        </p>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon text-gray-700">
                        <i class="fas fa-sign-out-alt w-5 h-5"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        @hasSection('page-subtitle')
                            <p class="text-gray-600">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        @hasSection('header-actions')
                            @yield('header-actions')
                        @endif
                        <div class="relative">
                            <button id="notifBell" class="relative px-3 py-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-bell"></i>
                                @if(($globalUnreadNotifications ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1">{{ $globalUnreadNotifications }}</span>
                                @endif
                            </button>
                            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                                    <span class="text-sm font-medium">Notifications</span>
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-600 hover:text-gray-900">Mark all read</button>
                                    </form>
                                </div>
                                <div class="max-h-80 overflow-auto">
                                    @forelse(($globalLatestNotifications ?? []) as $n)
                                        <div class="p-3 border-b border-gray-100 {{ $n->read_at ? '' : 'bg-gray-50' }}">
                                            <div class="text-sm font-medium text-gray-800">{{ $n->title }}</div>
                                            <div class="text-xs text-gray-600">{{ $n->body }}</div>
                                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                                <span>{{ $n->created_at->diffForHumans() }}</span>
                                                @if(!$n->read_at)
                                                <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:underline">Mark read</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-sm text-gray-600">No notifications</div>
                                    @endforelse
                                </div>
                                <div class="p-2 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-gray-700 hover:text-black">View all</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Toast Notifications -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ session('info') }}</span>
                    </div>
                @endif
                
                				@yield('content')
			</main>
		</div>
	</div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const bell=document.getElementById('notifBell');
            const dd=document.getElementById('notifDropdown');
            if(bell && dd){
                bell.addEventListener('click', ()=> dd.classList.toggle('hidden'));
                document.addEventListener('click', (e)=>{ if(!dd.contains(e.target) && !bell.contains(e.target)){ dd.classList.add('hidden'); } });
            }
        });

        // Global loading API
        function showLoading(text){
            const overlay=document.getElementById('globalLoading');
            const t=document.getElementById('globalLoadingText');
            if(text) t.textContent=text; else t.textContent='Processing your request…';
            overlay.style.display='flex';
        }
        function hideLoading(){ document.getElementById('globalLoading').style.display='none'; }

        // Auto-show for forms with data-loading attribute
        document.querySelectorAll('form[data-loading]')?.forEach(frm=>{
            frm.addEventListener('submit', function(){ showLoading(frm.getAttribute('data-loading')||'Processing…'); });
        });
    </script>
    @stack('scripts')
</body>
</html> 