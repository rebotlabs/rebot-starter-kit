<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organization = $request->route('organization');

        if (! $organization) {
            abort(404);
        }

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        // Check if user is owner or admin
        if (! $organization->currentUserCanManage()) {
            // Redirect members to their leave organization page
            return redirect()->route('organization.settings.leave', $organization);
        }

        return $next($request);
    }
}
