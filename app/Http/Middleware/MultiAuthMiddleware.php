<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MultiAuthMiddleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request); // Allow access if any guard is authenticated
            }
        }

        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
