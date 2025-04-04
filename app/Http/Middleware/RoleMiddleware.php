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

        // Check if the user has the required role using Spatie's `hasRole` method
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized'); // Abort if role does not match
        }

        return $next($request);

    }
}
