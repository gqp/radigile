<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check()) {
            \Log::warning('Unauthorized access attempt to:', ['url' => $request->url()]);
            return redirect('/login')->with('error', 'Please login to access this page.');
        }


        // Retrieve the authenticated user and load roles explicitly
        $user = Auth::user();

        // Reload roles to ensure proper assignment
        $user->loadMissing('roles');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $userRoles = $user->getRoleNames()->toArray();

        \Log::info('Checking roles for user:', [
            'user_id' => $user->id,
            'user_roles' => $userRoles,
            'required_role' => $role,
            'intended_url' => $request->url(),
        ]);

        // Check if the user has the required role
        if (!$user->hasRole($role)) {
            \Log::error('User denied access due to insufficient role:', [
                'user_id' => $user->id,
                'user_roles' => $userRoles,
                'required_role' => $role,
            ]);

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }

}
