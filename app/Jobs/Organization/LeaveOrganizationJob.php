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
        }

        if ($this->user->currentOrganization?->id === $this->organization->id) {
            $this->user->currentOrganization()->dissociate()->save();
        }

        $userOrganizations = $this->user->organizations()->get();

        return [
            'organizationsCount' => $userOrganizations->count(),
            'nextOrganization' => $userOrganizations->first(),
            'user' => $this->user,
        ];
    }
}
