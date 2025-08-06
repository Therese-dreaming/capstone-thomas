<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MhadelRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        
        if ($user->role !== 'Mhadel' && $user->role !== 'Ms. Mhadel') {
            // Redirect based on user role
            switch ($user->role) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'IOSA':
                    return redirect()->route('iosa.dashboard');
                case 'User':
                    return redirect()->route('user.dashboard');
                default:
                    return redirect('/');
            }
        }

        return $next($request);
    }
} 