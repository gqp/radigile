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

        // Check if force password reset is required
        if ($user && $user->force_password_reset) {
            return redirect()->route('password.reset.form')
                ->with('warning', 'You are required to reset your password before proceeding.');
        }

        return $next($request);
    }
}
