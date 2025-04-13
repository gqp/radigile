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

        if ($user && $user->force_password_reset) {
            \Log::info('User requires password reset.', ['user_id' => $user->id]);

            // Skip force password reset middleware for POST submission to the process route
            if ($request->route()->getName() === 'password.force.reset.process') {
                return $next($request); // Let the form submission process continue
            }

            if ($request->route()->getName() !== 'password.force.reset') {
                return redirect()->route('password.force.reset')
                    ->with('warning', 'You must reset your password before proceeding.');
            }
        }

        return $next($request);
    }
}
