<?php

declare(strict_types=1);

use App\Actions\Invitation\RejectInvitationAction;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('RejectInvitationAction', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->action = new RejectInvitationAction;
    });

    it('rejects invitation with valid token', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
            'status' => 'pending',
        ]);

        $this->action->execute(token: (string) $invitation->reject_token);

        // Invitation should be deleted after rejection
        expect(Invitation::find($invitation->id))->toBeNull();
    });

    it('throws exception for invalid token', function () {
        expect(fn () => $this->action->execute(token: 'invalid-token'))
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        expect(fn () => $this->action->execute(token: (string) $invitation->reject_token))
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception for already rejected invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'rejected',
        ]);

        expect(fn () => $this->action->execute(token: (string) $invitation->reject_token))
            ->toThrow(ModelNotFoundException::class);
    });

    it('can reject multiple different invitations', function () {
        $invitation1 = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test1@example.com',
        ]);

        $invitation2 = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test2@example.com',
        ]);

        $this->action->execute(token: (string) $invitation1->reject_token);
        $this->action->execute(token: (string) $invitation2->reject_token);

        // Both invitations should be deleted after rejection
        expect(Invitation::find($invitation1->id))->toBeNull()
            ->and(Invitation::find($invitation2->id))->toBeNull();
    });
});
