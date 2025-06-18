<?php

declare(strict_types=1);

use App\Jobs\Invitation\RejectInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('RejectInvitationJob', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    });

    it('rejects invitation with valid token', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'test@example.com',
            'status' => 'pending',
        ]);

        $job = new RejectInvitationJob((string) $invitation->accept_token);
        $job->handle();

        $invitation->refresh();
        expect($invitation->status)->toBe('rejected');
    });

    it('throws exception for invalid token', function () {
        expect(fn () => (new RejectInvitationJob('invalid-token'))->handle())
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        expect(fn () => (new RejectInvitationJob((string) $invitation->accept_token))->handle())
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception for already rejected invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'rejected',
        ]);

        expect(fn () => (new RejectInvitationJob((string) $invitation->accept_token))->handle())
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

        $job1 = new RejectInvitationJob((string) $invitation1->accept_token);
        $job2 = new RejectInvitationJob((string) $invitation2->accept_token);

        $job1->handle();
        $job2->handle();

        $invitation1->refresh();
        $invitation2->refresh();

        expect($invitation1->status)->toBe('rejected')
            ->and($invitation2->status)->toBe('rejected');
    });
});
