<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // If the request targets teacher/admin/cashier areas, send to the
            // corresponding guard login route — this avoids bouncing between
            // the generic login and guarded routes which can cause redirect loops.
            if ($request->is('teacher') || $request->is('teacher/*')) {
                Log::info('Authenticate::redirectTo -> teacher login redirect', ['path' => $request->path()]);
                return route('teacher.login');
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                Log::info('Authenticate::redirectTo -> admin login redirect', ['path' => $request->path()]);
                return route('admin.login');
            }

            if ($request->is('cashier') || $request->is('cashier/*')) {
                Log::info('Authenticate::redirectTo -> cashier login redirect', ['path' => $request->path()]);
                return route('cashier.login');
            }

            Log::info('Authenticate::redirectTo -> default login redirect', ['path' => $request->path()]);
            return route('login');
        }
    }
}
