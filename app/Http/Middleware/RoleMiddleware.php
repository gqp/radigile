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

        $user = Auth::user();
        $user->loadMissing('roles');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Support pipe-separated roles (e.g. role:User|Admin means either role is accepted)
        $allowedRoles = explode('|', $role);
        $hasRole = collect($allowedRoles)->contains(fn($r) => $user->hasRole(trim($r)));

        if (!$hasRole) {
            \Log::error('User denied access due to insufficient role:', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'required_role' => $role,
            ]);

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }

}
