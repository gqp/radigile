<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is authenticated and force a password reset
        if ($user && $user->force_password_reset) {
            // Exclude the force-reset route to avoid redirection loops
            if ($request->route()->getName() !== 'password.force.reset') {
                return redirect()->route('password.force.reset')
                    ->with('warning', 'You are required to reset your password before proceeding.');
            }
        }

        return $next($request);
    }
}
