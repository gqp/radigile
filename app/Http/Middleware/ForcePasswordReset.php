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
        if (Auth::check() && Auth::user()->force_password_reset) {
            // Allow access only to the password reset form
            if (!$request->routeIs('password.reset.form') && !$request->routeIs('password.reset.process')) {
                return redirect()->route('password.reset.form')->with('warning', 'You must reset your password before proceeding.');
            }
        }

        return $next($request);
    }
}
