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
        $user = Auth::user();

        if ($user && $user->force_password_reset) {
            // Force the user to go to the password reset page unless they are already on it
            if (!$request->is('password/reset')) {
                return redirect()->route('password.reset.form');
            }
        }

        return $next($request);
    }
}
