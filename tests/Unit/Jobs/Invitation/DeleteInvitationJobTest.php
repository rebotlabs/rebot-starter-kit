<?php

declare(strict_types=1);

use App\Jobs\Invitation\DeleteInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DeleteInvitationJob', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    });

    it('deletes invitation successfully', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
        ]);

        $invitationId = $invitation->id;

        $job = new DeleteInvitationJob($invitation);
        $job->handle();

        expect(Invitation::find($invitationId))->toBeNull();
    });

    it('handles soft deleted invitations', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
        ]);

        $invitationId = $invitation->id;

        $job = new DeleteInvitationJob($invitation);
        $job->handle();

        expect(Invitation::find($invitationId))->toBeNull()
            ->and(Invitation::withTrashed()->find($invitationId))->not->toBeNull();
    });

    it('can delete multiple invitations', function () {
        $invitation1 = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test1@example.com',
        ]);

        $invitation2 = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test2@example.com',
        ]);

        $job1 = new DeleteInvitationJob($invitation1);
        $job2 = new DeleteInvitationJob($invitation2);

        $job1->handle();
        $job2->handle();

        expect(Invitation::find($invitation1->id))->toBeNull()
            ->and(Invitation::find($invitation2->id))->toBeNull();
    });

    it('works with different invitation statuses', function () {
        $pendingInvitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $acceptedInvitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        $job1 = new DeleteInvitationJob($pendingInvitation);
        $job2 = new DeleteInvitationJob($acceptedInvitation);

        $job1->handle();
        $job2->handle();

        expect(Invitation::find($pendingInvitation->id))->toBeNull()
            ->and(Invitation::find($acceptedInvitation->id))->toBeNull();
    });
});
