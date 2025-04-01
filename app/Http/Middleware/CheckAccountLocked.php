<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountLocked
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user's account is locked
            $lockedUntil = Auth::user()->locked_until;

            if ($lockedUntil && now()->lessThan($lockedUntil)) {
                // Log the user out if their account is locked
                Auth::logout();

                // Invalidate the session to prevent token issues
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is locked. Please try again later.',
                ]);
            }
        }

        return $next($request);
    }
}
