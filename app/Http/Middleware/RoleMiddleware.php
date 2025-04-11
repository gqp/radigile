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
            \Log::error('User is not authenticated.');
            Auth::logout();   // Force logout if the session is stale
            abort(403, 'Unauthorized'); // Redirect if user is not logged in
        }

        // Log user info and roles
        \Log::info('Authenticated user:', [
            'user_id' => Auth::id(),
            'roles' => Auth::user()->getRoleNames(), // Logs all roles assigned
            'required_role' => $role,
        ]);

        // Check if the user has the required role
        if (!Auth::user()->hasRole($role)) {
            \Log::error('User does not have required role.', [
                'user_id' => Auth::id(),
                'roles' => Auth::user()->getRoleNames(),
                'required_role' => $role,
            ]);
            abort(403, 'Unauthorized');
        }


        return $next($request);

    }
}
