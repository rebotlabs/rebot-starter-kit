<?php

declare(strict_types=1);

use App\Jobs\Invitation\CreateInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

describe('CreateInvitationJob', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);

        Notification::fake();
    });

    it('creates invitation with valid data', function () {
        $invitationData = [
            'email' => 'test@example.com',
            'role' => 'member',
        ];

        $job = new CreateInvitationJob($this->organization, $invitationData, $this->user);
        $invitation = $job->handle();

        expect($invitation)->toBeInstanceOf(Invitation::class)
            ->and($invitation->email)->toBe('test@example.com')
            ->and($invitation->role)->toBe('member')
            ->and($invitation->organization_id)->toBe($this->organization->id)
            ->and($invitation->accept_token)->not->toBeNull()
            ->and($invitation->reject_token)->not->toBeNull();

        Notification::assertSentTo($invitation, InvitationSentNotification::class);
    });

    it('creates invitation with existing user', function () {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $invitationData = [
            'email' => 'existing@example.com',
            'role' => 'admin',
        ];

        $job = new CreateInvitationJob($this->organization, $invitationData, $this->user);
        $invitation = $job->handle();

        expect($invitation->user_id)->toBe($existingUser->id)
            ->and($invitation->email)->toBe('existing@example.com')
            ->and($invitation->role)->toBe('admin');
    });

    it('creates invitation without user for new email', function () {
        $invitationData = [
            'email' => 'newuser@example.com',
            'role' => 'member',
        ];

        $job = new CreateInvitationJob($this->organization, $invitationData, $this->user);
        $invitation = $job->handle();

        expect($invitation->user_id)->toBeNull()
            ->and($invitation->email)->toBe('newuser@example.com');
    });

    it('sends notification after creating invitation', function () {
        $invitationData = [
            'email' => 'notify@example.com',
            'role' => 'member',
        ];

        $job = new CreateInvitationJob($this->organization, $invitationData, $this->user);
        $invitation = $job->handle();

        Notification::assertSentTo($invitation, InvitationSentNotification::class);
        Notification::assertCount(1);
    });

    it('generates unique tokens for each invitation', function () {
        $invitationData1 = ['email' => 'test1@example.com', 'role' => 'member'];
        $invitationData2 = ['email' => 'test2@example.com', 'role' => 'member'];

        $job1 = new CreateInvitationJob($this->organization, $invitationData1, $this->user);
        $job2 = new CreateInvitationJob($this->organization, $invitationData2, $this->user);

        $invitation1 = $job1->handle();
        $invitation2 = $job2->handle();

        expect($invitation1->accept_token)->not->toBe($invitation2->accept_token)
            ->and($invitation1->reject_token)->not->toBe($invitation2->reject_token);
    });
});
