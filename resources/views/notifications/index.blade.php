@extends('layouts.user')

@section('title','Notifications')
@section('page-title','Notifications')

@section('content')
<div class="max-w-3xl mx-auto">
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
		<div class="p-6 border-b border-gray-200 flex items-center justify-between">
			<h3 class="text-lg font-semibold text-gray-800">Your Notifications</h3>
			<form method="POST" action="{{ route('notifications.markAllRead') }}">
				@csrf
				<button type="submit" class="px-3 py-1 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Mark all read</button>
			</form>
		</div>
		<div>
			@forelse($notifications as $n)
				<div class="p-4 border-b border-gray-100 {{ $n->read_at ? '' : 'bg-gray-50' }}">
					<div class="flex items-start justify-between">
						<div>
							<div class="text-sm font-medium text-gray-800">{{ $n->title }}</div>
							@if($n->body)
							<div class="text-xs text-gray-600 mt-1">{{ $n->body }}</div>
							@endif
							<div class="text-xs text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
						</div>
						<div class="ml-3">
							@if(!$n->read_at)
							<form method="POST" action="{{ route('notifications.read', $n->id) }}">
								@csrf
								<button type="submit" class="text-xs text-blue-600 hover:underline">Mark read</button>
							</form>
							@endif
						</div>
					</div>
				</div>
			@empty
				<div class="p-6 text-center text-gray-600">No notifications yet</div>
			@endforelse
		</div>
		@if(method_exists($notifications,'links'))
			<div class="p-4">{{ $notifications->links() }}</div>
		@endif
	</div>
</div>
@endsection 