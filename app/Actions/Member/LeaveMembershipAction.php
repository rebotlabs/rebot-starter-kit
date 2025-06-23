<?php

declare(strict_types=1);

namespace App\Actions\Member;

use App\Models\Organization;
use App\Models\User;

class LeaveMembershipAction
{
    public function execute(Organization $organization, User $user): void
    {
        // Check if user is the owner first
        if ($organization->owner_id === $user->id) {
            throw new \Exception('Organization owners cannot leave their organization. You must transfer ownership first.');
        }

        $member = $organization->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            throw new \Exception('User is not a member of this organization.');
        }

        $member->delete();

        if ($user->currentOrganization?->id === $organization->id) {
            $user->currentOrganization()->dissociate()->save();
        }
    }
}
