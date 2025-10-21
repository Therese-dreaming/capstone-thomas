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
                    'username' => 'Only verified accounts can log in. Please check your inbox for the verification email or request a new one.',
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $fullName = trim($request->first_name . ' ' . $request->last_name);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'User',
            'department' => $request->department,
        ]);

        // Send email verification
        $user->sendEmailVerificationNotification();

        // Log the user in so they can view the verify page (auth middleware)
        Auth::login($user);

        return redirect()->route('verification.notice')->with('message', 'Account created! Check your email for a verification link.');
    }

    public function showResendVerification(Request $request)
    {
        $email = $request->query('email');
        return view('auth.resend-verification', compact('email'));
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return back()->with('error', 'This email is already verified. You can log in now.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email has been sent! Please check your inbox.');
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

