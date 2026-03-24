<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected function addCookieToResponse($request, $response)
    {
        if ($response->getStatusCode() === 419) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        return parent::addCookieToResponse($request, $response);
    }
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
