<?php

declare(strict_types=1);

namespace App\Jobs\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LeaveOrganizationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private User $user,
        private Organization $organization
    ) {}

    public function handle(): array
    {
        $member = $this->organization->members()->where('user_id', $this->user->id)->first();

        if ($member) {
            $member->delete();

            // Remove user roles that were assigned for this organization
            $this->user->roles()->detach();
        }

        $invitation = $this->organization->invitations()->where('user_id', $this->user->id)->first();

        if ($invitation) {
            $invitation->delete();
        }

        // Clear current organization if this is the one the user is leaving
        if ($this->user->currentOrganization?->id === $this->organization->id) {
            $this->user->currentOrganization()->dissociate()->save();
        }

        // Get remaining organizations the user is a member of
        $userOrganizations = $this->user->organizations()->get();

        // If user has remaining organizations, set the first one as current
        $nextOrganization = null;
        if ($userOrganizations->isNotEmpty()) {
            $nextOrganization = $userOrganizations->first();
            $this->user->currentOrganization()->associate($nextOrganization)->save();
        }

        return [
            'organizationsCount' => $userOrganizations->count(),
            'nextOrganization' => $nextOrganization,
            'user' => $this->user,
        ];
    }
}
