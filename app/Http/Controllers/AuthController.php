<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->username,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Please verify your email address before logging in.',
                ])->withInput(['username' => $request->username]);
            }

            $request->session()->regenerate();
            session([
                'user_role' => $user->role,
                'user_name' => $user->name,
                'logged_in' => true
            ]);

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput(['username' => $request->username]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'You have been logged out successfully.');
    }

    public function showSignup()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'User', // Only allow User role for signup
            'email_verified_at' => now() // Auto-verify for now, can be changed later
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! You can now log in.');
    }

    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'IOSA':
                return redirect()->route('iosa.dashboard');
            case 'Mhadel':
            case 'Ms. Mhadel':
                return redirect()->route('mhadel.dashboard');
                    case 'OTP':
                return redirect()->route('drjavier.dashboard');
            case 'GSU':
                return redirect()->route('gsu.dashboard');
            case 'User':
            default:
                return redirect()->route('user.dashboard');
        }
    }
}

