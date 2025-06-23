<?php

declare(strict_types=1);

namespace App\Actions\Invitation;

use App\Models\Invitation;

class RejectInvitationAction
{
    public function execute(string $token): void
    {
        $invitation = Invitation::where('reject_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        // Delete the invitation after rejection
        $invitation->delete();
    }
}
