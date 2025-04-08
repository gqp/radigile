<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{

    public function handle(Request $request, Closure $next, string $requiredTier)
    {
        $user = $request->user();

        // Ensure the user is logged in and has an active subscription
        if (!$user || !$user->activeSubscription()) {
            abort(403, 'You do not have access to this resource.');
        }

        // Check the user's plan name against the required tier
        if ($user->activeSubscription()->plan->name !== $requiredTier) {
            abort(403, 'Your subscription does not grant access to this resource.');
        }

        return $next($request);
    }
}
