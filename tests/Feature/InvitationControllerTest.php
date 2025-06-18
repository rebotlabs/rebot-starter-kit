<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
});

describe('ShowInvitationController', function () {
    it('displays invitation page for valid token', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $signedUrl = url()->signedRoute('invitation.handle', ['token' => $invitation->accept_token]);
        $response = $this->get($signedUrl);

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('invitation/handle')
                ->has('invitation')
                ->where('invitation.id', $invitation->id)
                ->where('invitation.organization.name', $this->organization->name)
            );
    });

    it('returns 403 for unsigned URL', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $response = $this->get(route('invitation.handle', $invitation->accept_token));

        $response->assertForbidden();
    });

    it('returns 404 for invalid token with signed URL', function () {
        $signedUrl = url()->signedRoute('invitation.handle', ['token' => 'invalid-token']);
        $response = $this->get($signedUrl);

        $response->assertNotFound();
    });

    it('returns 404 for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        $signedUrl = url()->signedRoute('invitation.handle', ['token' => $invitation->accept_token]);
        $response = $this->get($signedUrl);

        $response->assertNotFound();
    });

    it('returns 404 for rejected invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'rejected',
        ]);

        $signedUrl = url()->signedRoute('invitation.handle', ['token' => $invitation->accept_token]);
        $response = $this->get($signedUrl);

        $response->assertNotFound();
    });
});

describe('AcceptInvitationController', function () {
    it('accepts invitation for existing user when logged in', function () {
        $user = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => $user->email,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->post(route('invitation.accept', $invitation->accept_token));

        $response->assertRedirect(route('organization.overview', $this->organization->slug));

        // Invitation should be deleted after acceptance
        expect(Invitation::find($invitation->id))->toBeNull();
        expect($user->organizations()->where('organizations.id', $this->organization->id)->exists())->toBeTrue();
    });

    it('accepts invitation for new user with valid data', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'newuser@example.com',
            'status' => 'pending',
        ]);

        $response = $this->post(route('invitation.accept', $invitation->accept_token), [
            'name' => 'New User',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('organization.overview', $this->organization->slug));

        // Invitation should be deleted after acceptance
        expect(Invitation::find($invitation->id))->toBeNull();

        $newUser = User::where('email', 'newuser@example.com')->first();
        expect($newUser)->not->toBeNull();
        expect($newUser->organizations()->where('organizations.id', $this->organization->id)->exists())->toBeTrue();
    });

    it('requires login for existing user with different email', function () {
        $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
        $differentUser = User::factory()->create(['email' => 'different@example.com']);
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'invited@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($differentUser)
            ->from('/some-previous-page')
            ->post(route('invitation.accept', $invitation->accept_token));

        $response->assertRedirect('/some-previous-page')
            ->assertSessionHasErrors(['auth']);
    });

    it('validates required fields for new user', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'newuser@example.com',
            'status' => 'pending',
        ]);

        $response = $this->post(route('invitation.accept', $invitation->accept_token), []);

        $response->assertSessionHasErrors(['name', 'password']);
    });

    it('returns 404 for invalid token', function () {
        $response = $this->post(route('invitation.accept', 'invalid-token'));

        $response->assertNotFound();
    });

    it('returns 404 for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        $response = $this->post(route('invitation.accept', $invitation->accept_token));

        $response->assertNotFound();
    });
});

describe('RejectInvitationController', function () {
    it('rejects invitation with valid token', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $response = $this->post(route('invitation.reject', $invitation->reject_token));

        $response->assertRedirect('/');

        // Invitation should be deleted after rejection
        expect(Invitation::find($invitation->id))->toBeNull();
    });

    it('returns 404 for invalid token', function () {
        $response = $this->post(route('invitation.reject', 'invalid-token'));

        $response->assertNotFound();
    });

    it('returns 404 for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        $response = $this->post(route('invitation.reject', $invitation->reject_token));

        $response->assertNotFound();
    });

    it('returns 404 for already rejected invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'rejected',
        ]);

        $response = $this->post(route('invitation.reject', $invitation->reject_token));

        $response->assertNotFound();
    });
});
