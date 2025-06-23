<?php

declare(strict_types=1);

use App\Actions\Invitation\DeleteInvitationAction;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DeleteInvitationAction', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->action = new DeleteInvitationAction;
    });

    it('deletes invitation successfully', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
        ]);

        $invitationId = $invitation->id;

        $this->action->execute(invitation: $invitation);

        expect(Invitation::find($invitationId))->toBeNull();
    });

    it('handles soft deleted invitations', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
        ]);

        $invitationId = $invitation->id;

        $this->action->execute(invitation: $invitation);

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

        $this->action->execute(invitation: $invitation1);
        $this->action->execute(invitation: $invitation2);

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

        $this->action->execute(invitation: $pendingInvitation);
        $this->action->execute(invitation: $acceptedInvitation);

        expect(Invitation::find($pendingInvitation->id))->toBeNull()
            ->and(Invitation::find($acceptedInvitation->id))->toBeNull();
    });
});
