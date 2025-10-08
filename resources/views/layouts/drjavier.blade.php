<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'PPGS Dashboard') - PCC Venue Reservation</title>
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
	</style>
	@yield('styles')
</head>
<body class="bg-gray-50">
	<!-- Include safelist to preserve Tailwind classes -->
	@include('tw-safelist')
	
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
						<h1 class="text-lg font-bold text-gray-800">PPGS</h1>
						<p class="text-xs text-gray-500">Physical Plan & General Service</p>
					</div>
				</div>
			</div>

			<!-- Navigation Menu -->
			<nav class="flex-1 p-4">
				<ul class="space-y-2">
					<!-- Dashboard -->
					<li>
						<a href="{{ route('drjavier.dashboard') }}" 
							class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.dashboard') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-tachometer-alt w-5 h-5"></i>
							<span class="font-medium">Dashboard</span>
						</a>
					</li>

					<!-- Reservations -->
					<li>
						<a href="{{ route('drjavier.reservations.index') }}"
							class="flex items-center p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.reservations*') ? 'active-maroon' : 'text-gray-700' }}">
							<div class="flex items-center flex-1">
								<i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
								<span class="font-medium">Reservations</span>
								@if(isset($sidebarCounts['reservations']) && $sidebarCounts['reservations'] > 0)
									<span class="ml-auto px-2 py-1 text-xs font-medium bg-red-100 text-red-600 rounded-full">{{ $sidebarCounts['reservations'] }}</span>
								@endif
							</div>
						</a>
					</li>

					<!-- Events -->
					<li>
						<a href="{{ route('drjavier.events.index') }}"
							class="flex items-center p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.events*') ? 'active-maroon' : 'text-gray-700' }}">
							<div class="flex items-center flex-1">
								<i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
								<span class="font-medium">Events</span>
							</div>
						</a>
					</li>

					<!-- GSU Reports -->
					<li>
						<a href="{{ route('drjavier.gsu-reports') }}" 
							class="flex items-center p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.gsu-reports*') ? 'active-maroon' : 'text-gray-700' }}">
							<div class="flex items-center flex-1">
								<i class="fas fa-exclamation-triangle w-5 h-5 mr-3"></i>
								<span class="font-medium">GSU Reports</span>
								@if(isset($sidebarCounts['gsu_reports']) && $sidebarCounts['gsu_reports'] > 0)
									<span class="ml-auto px-2 py-1 text-xs font-medium bg-red-100 text-red-600 rounded-full">{{ $sidebarCounts['gsu_reports'] }}</span>
								@endif
							</div>
						</a>
					</li>

					<!-- Reports -->
					<li>
						<a href="{{ route('drjavier.reports.reservation-reports') }}" 
							class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.reports*') ? 'active-maroon' : 'text-gray-700' }}">
							<i class="fas fa-chart-bar w-5 h-5"></i>
							<span class="font-medium">Reports</span>
						</a>
					</li>

					<!-- Settings -->
					<li>
						<a href="{{ route('drjavier.profile') }}" 
							class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover-bg-maroon {{ request()->routeIs('drjavier.profile*') ? 'active-maroon' : 'text-gray-700' }}">
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
							{{ Auth::user()->name ?? 'PPGS Representative' }}
						</p>
						<p class="text-xs text-gray-500 truncate">
							{{ Auth::user()->email ?? 'ppgs@pcc.edu.ph' }}
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
					@hasSection('header-actions')
						<div class="flex items-center space-x-3">
							@yield('header-actions')
						</div>
					@endif
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

	@yield('scripts')
</body>
</html> 