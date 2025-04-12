<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveStatus
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Just check if user is inactive
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact an administrator.',
            ]);
        }

        return $next($request);
    }
}
