<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Skip check for admin users
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // Check if user has active subscription
        if (!$user || !$user->hasActiveSubscription()) {
            // Redirect to subscription page with message
            return redirect()->route('subscription.index')
                ->with('warning', 'You need an active subscription to access this feature.');
        }

        return $next($request);
    }
}
