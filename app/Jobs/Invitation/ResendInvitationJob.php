<?php

declare(strict_types=1);

namespace App\Jobs\Invitation;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResendInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Invitation $invitation,
        private User $user
    ) {}

    public function handle(): void
    {
        // Set status to 'sent' temporarily
        $this->invitation->update(['status' => 'sent']);

        // Send the notification
        $this->invitation->notify(new InvitationSentNotification);

        // Revert status back to 'pending' after a brief delay
        $this->invitation->update(['status' => 'pending']);
    }
}
