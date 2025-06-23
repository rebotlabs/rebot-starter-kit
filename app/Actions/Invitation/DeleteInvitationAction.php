<?php

declare(strict_types=1);

namespace App\Actions\Invitation;

use App\Models\Invitation;

class DeleteInvitationAction
{
    public function execute(Invitation $invitation): void
    {
        $invitation->delete();
    }
}
