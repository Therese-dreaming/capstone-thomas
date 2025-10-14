<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'GSU Dashboard') - PCC Venue Reservation</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		.bg-maroon { background-color: #8B1818; }
		.text-maroon { color: #8B1818; }
		.hover-bg-maroon:hover { background-color: rgba(139, 24, 24, 0.1); }
		.active-maroon { background-color: #8B1818; color: white; }
		.sidebar-transition { transition: all 0.3s ease; }
	</style>
	@yield('styles')
</head>
<body class="bg-gray-50">
	@include('tw-safelist')
	<div class="flex h-screen">
		<!-- Sidebar -->
		<div class="w-64 bg-white shadow-lg flex flex-col">
			<div class="p-6 border-b border-gray-200">
				<div class="flex items-center space-x-3">
					<div class="w-10 h-10 bg-maroon rounded-full flex items-center justify-center">
						<i class="fas fa-user text-white text-sm"></i>
					</div>
					<div class="flex-1 min-w-0">
						<h1 class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'GSU Staff' }}</h1>
						<p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'gsu@pcc.edu.ph' }}</p>
					</div>
				</div>
			</div>
			<nav class="flex-1 p-4">
				<ul class="space-y-2">
					<li>
						<a href="{{ route('gsu.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('gsu.dashboard') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-tachometer-alt w-5 h-5"></i>
							<span class="font-medium">Dashboard</span>
						</a>
					</li>
					<li>
						<a href="{{ route('gsu.reservations.index') }}" class="flex items-center p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('gsu.reservations*') ? 'active-maroon' : 'text-gray-700' }}">
							<div class="flex items-center flex-1">
								<i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
								<span class="font-medium">Reservations</span>
								@if(isset($sidebarCounts['reservations']) && $sidebarCounts['reservations'] > 0)
									<span class="ml-auto px-2 py-1 text-xs font-medium bg-red-100 text-red-600 rounded-full">{{ $sidebarCounts['reservations'] }}</span>
								@endif
							</div>
						</a>
					</li>
					<li>
						<a href="{{ route('gsu.events.index') }}" class="flex items-center p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('gsu.events*') ? 'active-maroon' : 'text-gray-700' }}">
							<div class="flex items-center flex-1">
								<i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
								<span class="font-medium">Events</span>
								@if(isset($sidebarCounts['events']) && $sidebarCounts['events'] > 0)
									<span class="ml-auto px-2 py-1 text-xs font-medium bg-red-100 text-red-600 rounded-full">{{ $sidebarCounts['events'] }}</span>
								@endif
							</div>
						</a>
					</li>
					<li>
						<a href="{{ route('gsu.calendar') }}" class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('gsu.calendar*') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-calendar w-5 h-5"></i>
							<span class="font-medium">Calendar</span>
						</a>
					</li>
				</ul>
			</nav>
			<div class="border-t border-gray-200 p-4">
				<a href="{{ route('gsu.profile') }}" class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('gsu.profile*') ? 'active-maroon' : 'text-gray-700' }} mb-2">
					<i class="fas fa-user-cog w-5 h-5"></i>
					<span class="font-medium">Profile</span>
				</a>
			</div>
			<div class="border-t border-gray-200 p-4">
				<form action="{{ route('logout') }}" method="POST" class="w-full">
					@csrf
					<button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon text-gray-700">
						<i class="fas fa-sign-out-alt w-5 h-5"></i>
						<span class="font-medium">Logout</span>
					</button>
				</form>
			</div>
		</div>
		<div class="flex-1 flex flex-col overflow-hidden">
			<header class="bg-white shadow-sm border-b border-gray-200">
				<div class="flex items-center justify-between px-6 py-4">
					<div>
						<h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
						@hasSection('page-subtitle')
							<p class="text-gray-600">@yield('page-subtitle')</p>
						@endif
					</div>
					<div class="flex items-center space-x-4">
						@hasSection('header-actions')
							@yield('header-actions')
						@endif
						<div class="flex items-center space-x-3 border-l pl-4">
							<div class="text-right">
								<p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'GSU Staff' }}</p>
								<p class="text-xs text-gray-500">General Services Unit</p>
							</div>
							<div class="w-10 h-10 bg-maroon rounded-full flex items-center justify-center">
								<i class="fas fa-warehouse text-white"></i>
							</div>
						</div>
					</div>
				</div>
			</header>
			<main class="flex-1 overflow-y-auto p-6">
				@yield('content')
			</main>
		</div>
	</div>
	@stack('scripts')
</body>
</html> 