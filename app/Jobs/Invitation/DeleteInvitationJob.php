<?php

namespace App\Jobs\Invitation;

use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteInvitationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Invitation $invitation
    ) {}

    public function handle(): void
    {
        $this->invitation->delete();
    }
}
