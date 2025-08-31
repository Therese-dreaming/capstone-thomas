@php
    $user = Auth::user();
    $layout = 'layouts.user'; // Default layout
    
    // Determine layout based on user role
    if ($user->role === 'Admin') {
        $layout = 'layouts.admin';
    } elseif ($user->role === 'Ms. Mhadel' || $user->role === 'Mhadel') {
        $layout = 'layouts.mhadel';
    } elseif ($user->role === 'IOSA') {
        $layout = 'layouts.iosa';
    } elseif ($user->role === 'GSU') {
        $layout = 'layouts.gsu';
    } elseif ($user->role === 'OTP') {
        $layout = 'layouts.drjavier';
    }
    // User role uses the default 'layouts.user'
@endphp

@extends($layout)

@section('title','Notifications')
@section('page-title','Notifications')
@section('page-subtitle','View and manage your system notifications')

@section('header-actions')
	<div class="flex items-center space-x-3">
		<form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline">
			@csrf
			<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
				<i class="fas fa-check-double mr-2"></i>Mark All Read
			</button>
		</form>
	</div>
@endsection

@section('styles')
<style>
	.notification-item {
		transition: all 0.2s ease;
		border-left: 3px solid transparent;
	}
	
	.notification-item:hover {
		background-color: #f9fafb;
		border-left-color: #e5e7eb;
	}
	
	.notification-item:not(.bg-gray-50):hover {
		background-color: #f9fafb;
	}
	
	.mark-read-btn {
		transition: all 0.2s ease;
	}
	
	.mark-read-btn:hover {
		color: #1d4ed8;
		background-color: #eff6ff;
	}
	
	/* Empty state styling */
	.empty-state {
		animation: fadeIn 0.5s ease-out;
	}
	
	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(10px); }
		to { opacity: 1; transform: translateY(0); }
	}
	
	/* Pagination styling */
	.pagination {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 0.5rem;
	}
	
	.pagination a,
	.pagination span {
		padding: 0.5rem 0.75rem;
		border: 1px solid #d1d5db;
		border-radius: 0.375rem;
		text-decoration: none;
		color: #374151;
		transition: all 0.2s ease;
	}
	
	.pagination a:hover {
		background-color: #f3f4f6;
		border-color: #9ca3af;
	}
	
	.pagination .current {
		background-color: #8B1818;
		color: white;
		border-color: #8B1818;
	}
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200 flex items-center justify-between">
			<div>
				<h3 class="text-xl font-semibold text-gray-800">Your Notifications</h3>
				<p class="text-sm text-gray-600 mt-1">Stay updated with your latest system notifications</p>
			</div>
		</div>
		<div>
			@forelse($notifications as $n)
				<div class="p-4 border-b border-gray-100 notification-item {{ $n->read_at ? '' : 'bg-gray-50' }}">
					<div class="flex items-start justify-between">
						<div class="flex-1">
							<div class="flex items-start gap-3">
								<div class="w-2 h-2 rounded-full mt-2 flex-shrink-0 {{ $n->read_at ? 'bg-gray-300' : 'bg-blue-500' }}"></div>
								<div class="flex-1 min-w-0">
									<div class="text-sm font-medium text-gray-800">{{ $n->title }}</div>
									@if($n->body)
									<div class="text-xs text-gray-600 mt-1">{{ $n->body }}</div>
									@endif
									<div class="text-xs text-gray-500 mt-2 flex items-center gap-2">
										<i class="fas fa-clock"></i>
										<span>{{ $n->created_at->diffForHumans() }}</span>
									</div>
								</div>
							</div>
						</div>
						<div class="ml-3 flex-shrink-0">
							@if(!$n->read_at)
							<form method="POST" action="{{ route('notifications.read', $n->id) }}" class="inline">
								@csrf
								<button type="submit" class="text-xs text-blue-600 hover:underline mark-read-btn px-2 py-1 rounded hover:bg-blue-50 transition-colors">Mark read</button>
							</form>
							@else
							<span class="text-xs text-gray-400 px-2 py-1">Read</span>
							@endif
						</div>
					</div>
				</div>
			@empty
				<div class="p-12 text-center empty-state">
					<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
						<i class="fas fa-bell text-gray-400 text-2xl"></i>
					</div>
					<h3 class="text-lg font-medium text-gray-700 mb-2">No notifications yet</h3>
					<p class="text-gray-500">You're all caught up! New notifications will appear here.</p>
				</div>
			@endforelse
		</div>
		@if(method_exists($notifications,'links'))
			<div class="p-4 border-t border-gray-100 bg-gray-50">
				{{ $notifications->links() }}
			</div>
		@endif
	</div>
</div>
@endsection

@section('scripts')
<script>
	// Add hover effects and smooth transitions
	document.addEventListener('DOMContentLoaded', function() {
		// Add notification-item class to all notification items
		const notificationItems = document.querySelectorAll('.p-4.border-b.border-gray-100');
		notificationItems.forEach(item => {
			item.classList.add('notification-item');
		});
		
		// Add mark-read-btn class to all mark read buttons
		const markReadBtns = document.querySelectorAll('button[type="submit"]');
		markReadBtns.forEach(btn => {
			if (btn.textContent.trim() === 'Mark read') {
				btn.classList.add('mark-read-btn');
			}
		});
	});
</script>
@endsection 