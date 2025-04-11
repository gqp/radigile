<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Log unauthenticated users and redirect
        if (!Auth::check()) {
            \Log::warning('Unauthorized access attempt.', ['url' => $request->url()]);
            return redirect('/login')->with('error', 'Please login to continue.');
        }

        // Retrieve the authenticated user and load roles explicitly
        $user = Auth::user();

        $user->load('roles');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = $user->getRoleNames();

        \Log::info('Role check initiated:', [
            'user_id' => $user->id,
            'roles' => $roles,
            'required_role' => $role,
        ]);

        // Check if the user has the required role
        if (!$user->hasRole($role)) {
            \Log::error('Role validation failed.', [
                'user_id' => $user->id,
                'roles' => $roles->toArray(),
                'required_role' => $role,
            ]);

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }

}
