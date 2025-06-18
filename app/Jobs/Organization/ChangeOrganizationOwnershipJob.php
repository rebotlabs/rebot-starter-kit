<?php

declare(strict_types=1);

namespace App\Jobs\Organization;

use App\Models\Member;
use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeOrganizationOwnershipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Organization $organization,
        private int $memberId
    ) {}

    public function handle(): Organization
    {
        $member = Member::findOrFail($this->memberId);
        $this->organization->update(['owner_id' => $member->user_id]);

        return $this->organization;
    }
}
