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
        if ($user && $user->hasActiveSubscription()) {
            return $next($request);
        }

        // Check if user is a team member of someone with an active subscription
        if ($user) {
            $activeTeamOwner = $user->memberOfTeams()
                ->where('status', 'active')
                ->with('owner')
                ->get()
                ->first(function ($teamMember) {
                    return $teamMember->owner->hasActiveSubscription();
                });

            if ($activeTeamOwner) {
                return $next($request);
            }
        }

        // No valid subscription or team membership found
        if (!$user) {
            return redirect()->route('login');
        }

        return redirect()->route('subscription.index')
            ->with('warning', 'You need an active subscription or to be invited to a team with an active subscription to access this feature.');
    }
}
