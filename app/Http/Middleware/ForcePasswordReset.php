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
        // Check if the user is logged in and their force_password_reset flag is active
        if (Auth::check() && Auth::user()->force_password_reset) {
            // Redirect to a dedicated password reset page
            return redirect()->route('password.force_reset');
        }

        return $next($request);
    }
}
