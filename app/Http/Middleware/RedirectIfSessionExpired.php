<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfSessionExpired
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('POST') && !$request->ajax()) {
            // Check if the session has expired
            if (session()->has('_token') && $request->input('_token') !== session()->token()) {
                return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
            }
        }

        return $next($request);
    }
}
