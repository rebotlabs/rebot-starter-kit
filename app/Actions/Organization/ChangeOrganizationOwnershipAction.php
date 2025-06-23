<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Member;
use App\Models\Organization;

class ChangeOrganizationOwnershipAction
{
    public function execute(Organization $organization, int $memberId): Organization
    {
        $member = Member::findOrFail($memberId);
        $organization->update(['owner_id' => $member->user_id]);

        return $organization;
    }
}
