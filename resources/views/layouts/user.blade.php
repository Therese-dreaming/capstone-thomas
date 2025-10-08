<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'User Dashboard') - PCC Venue Reservation</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
	<style>
		:root { --maroon: #8B1818; --maroon-dark: #6f1313; --bg: #F4F5F7; }
		body { font-family: 'Poppins', sans-serif; }
		.font-poppins { font-family: 'Poppins', sans-serif; }
		.font-montserrat { font-family: 'Montserrat', sans-serif; }
		.bg-maroon { background-color: var(--maroon); }
		.text-maroon { color: var(--maroon); }
		.border-maroon { border-color: var(--maroon); }
		.sidebar-transition { transition: all 0.2s ease; }
		.sidebar { background: #ffffff; color: #111827; }
		.sidebar a { color: #374151; }
		.sidebar a:hover { background: #F3F4F6; color: #111827; }
		.nav-active { background: #F3F4F6; color: #111827 !important; }
		.nav-active i { color: #111827 !important; }
		.header { background: #ffffff; }
		.badge { background: #f3f4f6; color: #374151; }
		/* Loading Overlay */
		.loading-overlay { position: fixed; inset: 0; background: rgba(17,24,39,0.55); backdrop-filter: blur(3px); z-index: 9999; display: none; align-items: center; justify-content: center; }
		.loading-card { background: #fff; border-radius: 1rem; padding: 1.25rem 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); width: 92%; max-width: 360px; text-align: center; }
		.spinner { width: 2rem; height: 2rem; border: 3px solid #eee; border-top-color: var(--maroon); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 0.75rem; }
		@keyframes spin { to { transform: rotate(360deg); } }
	</style>
	@yield('styles')
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
		<div class="w-64 shadow-lg flex flex-col sidebar">
			<!-- Logo and Brand -->
			<div class="p-6 border-b border-gray-200">
				<div class="flex items-center space-x-3">
					<img src="{{ asset('images/pcclogo.png') }}" alt="PCC Logo" class="w-10 h-10 object-contain rounded bg-white p-1">
					<div>
						<h1 class="text-lg font-semibold text-gray-800">PCC Venue</h1>
						<p class="text-xs text-gray-500">Reservation System</p>
					</div>
				</div>
			</div>

			<!-- Navigation Menu -->
			<nav class="flex-1 p-4">
				<ul class="space-y-1">
					<!-- Dashboard -->
					<li>
						<a href="{{ route('user.dashboard') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition {{ request()->routeIs('user.dashboard') ? 'nav-active' : '' }}">
							<i class="fas fa-home w-5 h-5"></i>
							<span class="font-medium">Dashboard</span>
						</a>
					</li>

					<!-- Reservations -->
					<li>
						<a href="{{ route('user.reservations.index') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition {{ request()->routeIs('user.reservations.index') ? 'nav-active' : '' }}">
							<i class="fas fa-calendar-alt w-5 h-5"></i>
							<span class="font-medium">My Reservations</span>
						</a>
					</li>

					<!-- New Reservation -->
					<li>
						<a href="{{ route('user.reservations.calendar') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition {{ request()->routeIs('user.reservations.calendar') ? 'nav-active' : '' }}">
							<i class="fas fa-calendar-plus w-5 h-5"></i>
							<span class="font-medium">New Reservation</span>
						</a>
					</li>

					<!-- Profile -->
					<li>
						<a href="{{ route('user.profile') }}" 
						   class="flex items-center space-x-3 p-3 rounded-lg sidebar-transition {{ request()->routeIs('user.profile*') ? 'nav-active' : '' }}">
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
					<div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
						<i class="fas fa-user text-gray-600 text-sm"></i>
					</div>
					<div class="flex-1 min-w-0">
						<p class="text-sm font-medium text-gray-800 truncate">
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
							class="w-full flex items-center space-x-3 p-3 rounded-lg sidebar-transition hover:bg-gray-100 text-gray-700">
						<i class="fas fa-sign-out-alt w-5 h-5"></i>
						<span class="font-medium">Logout</span>
					</button>
				</form>
			</div>
		</div>

		<!-- Main Content -->
		<div class="flex-1 flex flex-col overflow-hidden">
			<!-- Top Header -->
			<header class="header shadow-sm border-b border-gray-200 p-4">
				<div class="flex items-center justify-between">
					<h2 class="text-xl font-semibold text-gray-800">
						@yield('page-title', 'Dashboard')
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
										@php
											// Generate the appropriate URL based on notification type and related data
											$notificationUrl = null;
											$isClickable = false;
											
											// Check if notification has related data
											if ($n->related_id && $n->related_type) {
												$isClickable = true;
												
												// Handle different related types - User role goes to user routes
												if ($n->related_type === 'App\Models\Reservation' || $n->related_type === 'Reservation') {
													$notificationUrl = route('user.reservations.show', $n->related_id);
												}
												// Users typically don't have direct access to events, so we skip event notifications
											} else {
												// Fallback: try to extract ID from notification content for older notifications
												if (preg_match('/reservation.*?(\d+)/i', $n->body, $matches)) {
													$extractedId = $matches[1];
													
													// Determine if it's about reservations based on content
													if (stripos($n->title, 'reservation') !== false || stripos($n->body, 'reservation') !== false) {
														$isClickable = true;
														$notificationUrl = route('user.reservations.show', $extractedId);
													}
												}
											}
										@endphp
										
										<div class="p-3 border-b border-gray-100 {{ $n->read_at ? '' : 'bg-gray-50' }} {{ $isClickable ? 'cursor-pointer hover:bg-blue-50 transition-colors' : '' }}"
											 @if($isClickable && $notificationUrl) 
												onclick="handleDropdownNotificationClick('{{ $notificationUrl }}', {{ $n->id }}, {{ $n->read_at ? 'true' : 'false' }})"
												data-url="{{ $notificationUrl }}"
												data-notification-id="{{ $n->id }}"
												data-is-read="{{ $n->read_at ? 'true' : 'false' }}"
											 @endif>
											<div class="text-sm font-medium text-gray-800 {{ $isClickable ? 'text-blue-700 hover:text-blue-900' : '' }}">
												{{ $n->title }}
												@if($isClickable)
													<i class="fas fa-external-link-alt text-xs ml-1 opacity-60"></i>
												@endif
											</div>
											<div class="text-xs text-gray-600">{{ $n->body }}</div>
											<div class="mt-2 flex items-center justify-between text-xs text-gray-500">
												<span>{{ $n->created_at->diffForHumans() }}</span>
												@if($isClickable)
													<span class="text-blue-600 font-medium">• Click to view</span>
												@endif
												@if(!$n->read_at)
												<form method="POST" action="{{ route('notifications.read', $n->id) }}" onclick="event.stopPropagation()">
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

				@if($errors->any())
					@foreach($errors->all() as $error)
						<div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center">
							<i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
							<p class="text-red-700 text-sm">{{ $error }}</p>
						</div>
					@endforeach
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
		// Add active state handling (no gradient; solid colors)
		document.addEventListener('DOMContentLoaded', function() {
			const currentPath = window.location.pathname;
			const navLinks = document.querySelectorAll('nav a');
			navLinks.forEach(link => {
				if (link.getAttribute('href') === currentPath) {
					link.classList.add('nav-active');
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

		// Handle dropdown notification click
		window.handleDropdownNotificationClick = function(url, notificationId, isRead) {
			console.log('Dropdown notification clicked:', { url, notificationId, isRead });
			
			// Close the dropdown first
			const dropdown = document.getElementById('notifDropdown');
			if (dropdown) {
				dropdown.classList.add('hidden');
			}
			
			// If notification is unread, mark it as read first
			if (!isRead) {
				// Send AJAX request to mark as read
				fetch(`/notifications/${notificationId}/read`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				}).then(() => {
					// Navigate to the URL
					window.location.href = url;
				}).catch((error) => {
					console.error('Error marking notification as read:', error);
					// If marking as read fails, still navigate
					window.location.href = url;
				});
			} else {
				// If already read, just navigate
				window.location.href = url;
			}
		};

		// Notifications dropdown toggle
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
		document.addEventListener('DOMContentLoaded', function(){
			document.querySelectorAll('form[data-loading]')?.forEach(frm=>{
				frm.addEventListener('submit', function(){ showLoading(frm.getAttribute('data-loading')||'Processing…'); });
			});
		});
	</script>

	@yield('scripts')
	@stack('scripts')
</body>
</html>
