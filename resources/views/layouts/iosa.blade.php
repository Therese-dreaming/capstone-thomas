<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'IOSA Dashboard') - PCC Venue Reservation</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
	<style>
		.bg-maroon { background-color: #8B1818; }
		.text-maroon { color: #8B1818; }
		.border-maroon { border-color: #8B1818; }
		.hover-bg-maroon:hover { background-color: rgba(139, 24, 24, 0.1); }
		.active-maroon { background-color: #8B1818; color: white; }
		.sidebar-transition { transition: all 0.3s ease; }
		.font-poppins { font-family: 'Poppins', sans-serif; }
		.font-montserrat { font-family: 'Montserrat', sans-serif; }
	</style>
	@yield('styles')
</head>
<body class="bg-gray-50 font-poppins">
	<!-- Include safelist to preserve Tailwind classes -->
	@include('tw-safelist')
	
	<div class="flex h-screen">
		<!-- Sidebar -->
		<div class="w-64 bg-white shadow-lg flex flex-col">
			<!-- Logo and Brand -->
			<div class="p-6 border-b border-gray-200">
				<div class="flex items-center space-x-3">
					<div class="w-8 h-8 bg-maroon rounded flex items-center justify-center">
						<i class="fas fa-shield-alt text-white text-sm"></i>
					</div>
					<div>
						<h1 class="text-lg font-bold text-gray-800 font-montserrat">IOSA Panel</h1>
						<p class="text-xs text-gray-500">PCC Venue System</p>
					</div>
				</div>
			</div>

			<!-- Navigation Menu -->
			<nav class="flex-1 p-4">
				<ul class="space-y-2">
					<!-- Dashboard -->
					<li>
						<a href="{{ route('iosa.dashboard') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.dashboard') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-tachometer-alt w-5 h-5"></i>
							<span class="font-medium">Dashboard</span>
						</a>
					</li>

					<!-- Reservations -->
					<li>
						<a href="{{ route('iosa.reservations.index') }}"
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.reservations*') && !request()->routeIs('iosa.reservations.calendar') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-calendar-check w-5 h-5"></i>
							<span class="font-medium">Reservations</span>
						</a>
					</li>

					<!-- Events -->
					<li>
						<a href="{{ route('iosa.events.index') }}"
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.events*') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-calendar-alt w-5 h-5"></i>
							<span class="font-medium">Events</span>
						</a>
					</li>

					<!-- Calendar -->
					<li>
						<a href="{{ route('iosa.reservations.calendar') }}"
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.reservations.calendar') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-calendar w-5 h-5"></i>
							<span class="font-medium">Calendar</span>
						</a>
					</li>

					<!-- Reservation Reports -->
					<li>
						<a href="{{ route('iosa.reservation-reports') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.reservation-reports*') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-chart-bar w-5 h-5"></i>
							<span class="font-medium">Reservation Reports</span>
						</a>
					</li>

					<!-- Reports -->
					<li>
						<a href="{{ route('iosa.reports') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.reports*') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-exclamation-triangle w-5 h-5"></i>
							<span class="font-medium">GSU Reports</span>
						</a>
					</li>

					<!-- Settings -->
					<li>
						<a href="{{ route('iosa.profile') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('iosa.profile*') ? 'active-maroon' : 'text-gray-700' }}">
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
						<i class="fas fa-user-shield text-gray-600 text-sm"></i>
					</div>
					<div class="flex-1 min-w-0">
						<p class="text-sm font-medium text-gray-700 truncate">
							{{ Auth::user()->name ?? 'IOSA' }}
						</p>
						<p class="text-xs text-gray-500 truncate">
							{{ Auth::user()->email ?? 'iosa@example.com' }}
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
					<h2 class="text-xl font-semibold text-gray-800 font-montserrat">
						@yield('page-title', 'IOSA Dashboard')
					</h2>
					
					<!-- Breadcrumb or additional header content -->
					<div class="flex items-center space-x-4">
						@yield('header-actions')
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

	<!-- JavaScript for Toast Notifications and Notifications Dropdown -->
	<script>
		// Auto-hide toast notifications after 5 seconds
		setTimeout(function() {
			const toasts = document.querySelectorAll('.toast');
			toasts.forEach(toast => {
				toast.style.transition = 'opacity 0.5s ease-out';
				toast.style.opacity = '0';
				setTimeout(() => { toast.remove(); }, 500);
			});
		}, 5000);

		document.addEventListener('DOMContentLoaded', function(){
			const bell=document.getElementById('notifBell');
			const dd=document.getElementById('notifDropdown');
			if(bell && dd){
				bell.addEventListener('click', ()=> dd.classList.toggle('hidden'));
				document.addEventListener('click', (e)=>{ if(!dd.contains(e.target) && !bell.contains(e.target)){ dd.classList.add('hidden'); } });
			}
		});
	</script>
	@yield('scripts')
</body>
</html> 