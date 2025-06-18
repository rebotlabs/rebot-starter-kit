<?php

declare(strict_types=1);

namespace App\Jobs\Member;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LeaveMembershipJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Organization $organization,
        private User $user
    ) {}

    public function handle(): void
    {
        // Check if user is the owner first
        if ($this->organization->owner_id === $this->user->id) {
            throw new \Exception('Organization owners cannot leave their organization. You must transfer ownership first.');
        }

        $member = $this->organization->members()
            ->where('user_id', $this->user->id)
            ->first();

        if (! $member) {
            throw new \Exception('User is not a member of this organization.');
        }

        $member->delete();

        if ($this->user->currentOrganization?->id === $this->organization->id) {
            $this->user->currentOrganization()->dissociate()->save();
        }
    }
}
