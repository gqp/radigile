<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized'); // Redirect if user is not logged in
        }

        // Check the user's role
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized'); // Redirect if role does not match
        }

        return $next($request);

    }
}
