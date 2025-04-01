<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $roles Comma-separated list of roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        // Convert roles into an array and check user role
        $rolesArray = explode(',', $roles);
        if (!in_array(auth()->user()->role, $rolesArray)) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        return $next($request);
    }
}
