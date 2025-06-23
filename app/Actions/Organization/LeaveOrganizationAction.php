<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;

class LeaveOrganizationAction
{
    public function execute(User $user, Organization $organization): array
    {
        $member = $organization->members()->where('user_id', $user->id)->first();

        if ($member) {
            $member->delete();

            // Remove user roles that were assigned for this organization
            $user->roles()->detach();
        }

        $invitation = $organization->invitations()->where('user_id', $user->id)->first();

        if ($invitation) {
            $invitation->delete();
        }

        // Clear current organization if this is the one the user is leaving
        if ($user->currentOrganization?->id === $organization->id) {
            $user->currentOrganization()->dissociate()->save();
        }

        // Get remaining organizations the user is a member of
        $userOrganizations = $user->organizations()->get();

        // If user has remaining organizations, set the first one as current
        $nextOrganization = null;
        if ($userOrganizations->isNotEmpty()) {
            $nextOrganization = $userOrganizations->first();
            $user->currentOrganization()->associate($nextOrganization)->save();
        }

        return [
            'organizationsCount' => $userOrganizations->count(),
            'nextOrganization' => $nextOrganization,
            'user' => $user,
        ];
    }
}
