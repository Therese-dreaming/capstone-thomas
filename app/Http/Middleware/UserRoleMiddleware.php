<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has 'User' role
        if (!auth()->check() || auth()->user()->role !== 'User') {
            // Redirect to login if not authenticated, or to dashboard if wrong role
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            
            // If user has admin role, redirect to admin dashboard
            if (auth()->user()->role === 'Admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // For any other role or no role, redirect to user dashboard
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
