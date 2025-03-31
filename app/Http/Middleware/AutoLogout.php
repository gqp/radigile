<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AutoLogout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && now()->diffInMinutes(Auth::user()->last_activity) > 30) {
            Auth::logout();
            return redirect()->route('login')->with('message', 'Your session has expired due to inactivity.');
        }

        if (Auth::check()) {
            Auth::user()->update(['last_activity' => now()]);
        }

        return $next($request);
    }
}
