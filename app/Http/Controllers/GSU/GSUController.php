<?php

namespace App\Http\Controllers\GSU;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GSUController extends Controller
{
	public function dashboard()
	{
		$today = Carbon::today();
		$startOfMonth = Carbon::now()->startOfMonth();
		$stats = [
			'approved' => Reservation::where('status', 'approved_OTP')->count(),
			'approved_today' => Reservation::where('status', 'approved_OTP')->whereDate('updated_at', $today)->count(),
			'total_month' => Reservation::where('status', 'approved_OTP')->where('created_at', '>=', $startOfMonth)->count(),
		];
		$recent = Reservation::with(['user','venue'])->where('status','approved_OTP')->orderByDesc('created_at')->limit(5)->get();
		return view('gsu.dashboard', compact('stats','recent'));
	}

	/**
	 * Show profile edit page for the authenticated GSU user.
	 */
	public function profile()
	{
		$user = Auth::user();
		
		// Get GSU-specific stats for the profile
		$user->reservations_count = Reservation::where('status', 'completed')->count();
		$user->events_count = \App\Models\Event::where('status', 'completed')->count();
		$user->issues_resolved = \App\Models\Report::where('status', 'resolved')->count();
		$user->pending_tasks = Reservation::where('status', 'approved_OTP')->count();
		
		return view('gsu.profile', compact('user'));
	}

	/**
	 * Update profile information for the authenticated GSU user.
	 */
	public function updateProfile(Request $request)
	{
		$user = Auth::user();
		
		// Validate the request
		$request->validate([
			'first_name' => 'nullable|string|max:255',
			'last_name' => 'nullable|string|max:255',
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
			'department' => 'nullable|string|max:255',
			'current_password' => 'nullable|required_with:password',
			'password' => 'nullable|string|min:8|confirmed',
		]);

		// Verify current password if changing password
		if ($request->filled('password')) {
			if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
				return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
			}
		}

		// Update user information
		$user->first_name = $request->first_name ?? $user->first_name;
		$user->last_name = $request->last_name ?? $user->last_name;
		$user->name = $request->name;
		$user->email = $request->email;
		$user->department = $request->department ?? $user->department;
		
		// Update password if provided
		if ($request->filled('password')) {
			$user->password = Hash::make($request->password);
		}
		
		$user->save();

		return redirect()->route('gsu.profile')->with('success', 'Profile updated successfully.');
	}
} 