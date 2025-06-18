<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    /**
     * Show member settings page (leave organization).
     */
    public function show(Request $request, Organization $organization)
    {
        // Ensure user is a member but not the owner
        if ($organization->owner_id === $request->user()->id) {
            return redirect()->route('organization.settings', $organization);
        }

        $member = $organization->members()->where('user_id', $request->user()->id)->first();
        if (! $member) {
            abort(403, 'You are not a member of this organization.');
        }

        // Get user's role from spatie permissions
        $userRole = $request->user()->roles->first()?->name ?? 'member';

        return Inertia::render('organization/settings/leave', [
            'organization' => $organization,
            'member' => [
                'id' => $member->id,
                'user' => $member->user,
                'role' => $userRole,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ],
        ]);
    }

    /**
     * Leave the organization.
     */
    public function leave(Request $request, Organization $organization)
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        // Ensure user is a member but not the owner
        if ($organization->owner_id === $request->user()->id) {
            return back()->withErrors([
                'password' => 'Organization owners cannot leave their organization. You must transfer ownership first.',
            ]);
        }

        $member = $organization->members()->where('user_id', $request->user()->id)->first();
        if (! $member) {
            abort(403, 'You are not a member of this organization.');
        }

        // Remove membership
        $member->delete();

        // Clear current organization if this was the user's current organization
        if ($request->user()->currentOrganization?->id === $organization->id) {
            $request->user()->currentOrganization()->dissociate()->save();
        }

        // Redirect to organization selection or onboarding
        $userOrganizations = $request->user()->organizations()->get();

        if ($userOrganizations->isEmpty()) {
            return redirect()->route('onboarding.organization')
                ->with('message', 'You have left the organization. Create or join a new organization to continue.');
        }

        if ($userOrganizations->count() === 1) {
            // Set the only available organization as current
            $nextOrganization = $userOrganizations->first();
            $request->user()->currentOrganization()->associate($nextOrganization)->save();

            return redirect()->route('organization.overview', $nextOrganization)
                ->with('message', 'You have successfully left the organization.');
        }

        // Multiple organizations available, let user choose
        return redirect()->route('organization.select')
            ->with('message', 'You have successfully left the organization. Please select another organization to continue.');
    }
}
