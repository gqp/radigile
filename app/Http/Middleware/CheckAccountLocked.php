<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckAccountLocked
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->locked_until && now()->lessThan(Auth::user()->locked_until)) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is locked. Please try again later.'
            ]);
        }

        return $next($request);
    }
}
