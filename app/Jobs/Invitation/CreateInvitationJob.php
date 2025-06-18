<?php

declare(strict_types=1);

namespace App\Jobs\Invitation;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Organization $organization,
        private array $invitationData,
        private User $inviter
    ) {}

    public function handle(): Invitation
    {
        $invitation = $this->organization->invitations()->create([
            'email' => $this->invitationData['email'],
            'role' => $this->invitationData['role'],
            'user_id' => User::query()->where('email', $this->invitationData['email'])->first()?->id,
            'accept_token' => Str::random(32),
            'reject_token' => Str::random(32),
        ]);

        $invitation->notify(new InvitationSentNotification);

        return $invitation;
    }
}
