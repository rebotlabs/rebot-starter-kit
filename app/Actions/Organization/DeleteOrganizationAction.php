<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Models\User;

class DeleteOrganizationAction
{
    public function execute(Organization $organization, User $user): array
    {
        $organizationId = $organization->id;

        // Check if the user's current organization is the one being deleted
        $needsOrganizationSwitch = $user->current_organization_id === $organizationId;

        // Delete the organization (this will cascade delete members, invitations, etc.)
        $organization->delete();

        // Get remaining organizations the user is a member of
        $userOrganizations = $user->fresh()->organizations()->get();

        $nextOrganization = null;
        if ($needsOrganizationSwitch) {
            if ($userOrganizations->isNotEmpty()) {
                // Set the first remaining organization as current
                $nextOrganization = $userOrganizations->first();
                $user->update(['current_organization_id' => $nextOrganization->id]);
            } else {
                // No remaining organizations, clear current organization
                $user->update(['current_organization_id' => null]);
            }
        }

        return [
            'organizationsCount' => $userOrganizations->count(),
            'nextOrganization' => $nextOrganization,
            'needsOrganizationSwitch' => $needsOrganizationSwitch,
        ];
    }
}
