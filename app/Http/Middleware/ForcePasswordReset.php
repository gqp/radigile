<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordReset
{
    /**
     * Handle an incoming request.
     */
public function handle($request, Closure $next)
{
    // If user is authenticated and requires a password reset
    if (Auth::check() && Auth::user()->force_password_reset) {
        // Allow only access to the password reset form/page
        if (!$request->routeIs('password.reset.form') && !$request->routeIs('password.reset.process')) {
            return redirect()->route('password.reset.form');
        }
    }

    return $next($request);
    }
}
