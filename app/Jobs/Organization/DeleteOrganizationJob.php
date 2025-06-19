<?php

declare(strict_types=1);

namespace App\Jobs\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteOrganizationJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Organization $organization,
        private User $user
    ) {}

    public function handle(): array
    {
        $organizationId = $this->organization->id;

        // Check if the user's current organization is the one being deleted
        $needsOrganizationSwitch = $this->user->current_organization_id === $organizationId;

        // Delete the organization (this will cascade delete members, invitations, etc.)
        $this->organization->delete();

        // Get remaining organizations the user is a member of
        $userOrganizations = $this->user->fresh()->organizations()->get();

        $nextOrganization = null;
        if ($needsOrganizationSwitch) {
            if ($userOrganizations->isNotEmpty()) {
                // Set the first remaining organization as current
                $nextOrganization = $userOrganizations->first();
                $this->user->update(['current_organization_id' => $nextOrganization->id]);
            } else {
                // No remaining organizations, clear current organization
                $this->user->update(['current_organization_id' => null]);
            }
        }

        return [
            'organizationsCount' => $userOrganizations->count(),
            'nextOrganization' => $nextOrganization,
            'needsOrganizationSwitch' => $needsOrganizationSwitch,
        ];
    }
}
