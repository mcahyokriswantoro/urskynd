<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Redirect to onboarding if not completed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->isAdmin() && !$request->user()->onboarding_completed && !$request->routeIs('onboarding.*')) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
