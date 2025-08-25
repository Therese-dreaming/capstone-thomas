<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
	public function index(Request $request)
	{
		$user = Auth::user();
		$notifications = Notification::where('user_id', $user->id)
			->latest()->paginate(10);
		$unreadCount = Notification::where('user_id', $user->id)->whereNull('read_at')->count();
		return view('notifications.index', compact('notifications','unreadCount'));
	}

	public function markAsRead(string $id)
	{
		$notification = Notification::where('user_id', Auth::id())->findOrFail($id);
		if (!$notification->read_at) {
			$notification->update(['read_at' => now()]);
		}
		return back()->with('success', 'Notification marked as read.');
	}

	public function markAllAsRead()
	{
		Notification::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
		return back()->with('success', 'All notifications marked as read.');
	}
}
