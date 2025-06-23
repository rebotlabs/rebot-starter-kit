<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Organization;

class UpdateOrganizationAction
{
    public function execute(Organization $organization, array $data): Organization
    {
        $organization->update($data);

        return $organization;
    }
}
