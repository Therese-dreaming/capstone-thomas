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
        // Check if user is authenticated and has 'User' role (case-insensitive)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }
        
        $userRole = strtolower(auth()->user()->role ?? '');
        
        if ($userRole !== 'user') {
            // Log the attempt for debugging
            \Log::warning('User role mismatch in UserRoleMiddleware', [
                'user_id' => auth()->id(),
                'role' => auth()->user()->role,
                'expected' => 'User',
                'route' => $request->path()
            ]);
            
            // If user has admin role, redirect to admin dashboard
            if (in_array($userRole, ['admin', 'administrator'])) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'This page is for regular users only.');
            }
            
            // For any other role, return 403 with clear message
            abort(403, 'Access denied. This page is only accessible to users with the "User" role. Your role: ' . (auth()->user()->role ?? 'none'));
        }

        return $next($request);
    }
}
