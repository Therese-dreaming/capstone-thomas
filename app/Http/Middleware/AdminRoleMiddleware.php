<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has 'Admin' role
        if (!auth()->check() || auth()->user()->role !== 'Admin') {
            // Redirect to login if not authenticated, or to appropriate dashboard if wrong role
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            
            // If user has IOSA role, redirect to IOSA dashboard
            if (auth()->user()->role === 'IOSA') {
                return redirect()->route('iosa.dashboard');
            }
            
            // If user has user role, redirect to user dashboard
            if (auth()->user()->role === 'User') {
                return redirect()->route('user.dashboard');
            }
            
            // For any other role, redirect to user dashboard
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
} 