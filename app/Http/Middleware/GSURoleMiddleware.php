<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GSURoleMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		if (!auth()->check()) {
			return redirect('/login');
		}
		if (auth()->user()->role !== 'GSU') {
			return redirect('/')->with('error', 'Access denied. You do not have permission to access this area.');
		}
		return $next($request);
	}
} 