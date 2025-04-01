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
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        // Check if the authenticated user's role matches the required role
        if ($role && auth()->user()->role !== $role) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        return $next($request);
    }
}
