<?php

declare(strict_types=1);

use App\Jobs\Invitation\ResendInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

describe('ResendInvitationJob', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
        ]);

        Notification::fake();
    });

    it('resends invitation notification', function () {
        $job = new ResendInvitationJob($this->invitation, $this->user);
        $job->handle();

        Notification::assertSentTo($this->invitation, InvitationSentNotification::class);
        Notification::assertCount(1);
    });

    it('handles multiple resend requests', function () {
        $job1 = new ResendInvitationJob($this->invitation, $this->user);
        $job2 = new ResendInvitationJob($this->invitation, $this->user);

        $job1->handle();
        $job2->handle();

        Notification::assertSentTo($this->invitation, InvitationSentNotification::class);
        Notification::assertCount(2);
    });

    it('works with different users resending same invitation', function () {
        $anotherUser = User::factory()->create();

        $job1 = new ResendInvitationJob($this->invitation, $this->user);
        $job2 = new ResendInvitationJob($this->invitation, $anotherUser);

        $job1->handle();
        $job2->handle();

        Notification::assertSentTo($this->invitation, InvitationSentNotification::class);
        Notification::assertCount(2);
    });

    it('temporarily sets status to sent then reverts to pending', function () {
        expect($this->invitation->status)->toBe('pending');

        $job = new ResendInvitationJob($this->invitation, $this->user);
        $job->handle();

        $this->invitation->refresh();
        expect($this->invitation->status)->toBe('pending');

        Notification::assertSentTo($this->invitation, InvitationSentNotification::class);
    });
});
