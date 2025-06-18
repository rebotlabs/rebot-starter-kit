<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);

    Member::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
    ]);

    $this->user->update(['current_organization_id' => $this->organization->id]);

    $this->invitation = Invitation::factory()->create([
        'organization_id' => $this->organization->id,
        'status' => 'pending',
    ]);
});

describe('ShowMembersController', function () {
    it('displays members page for organization owner', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.settings.members', $this->organization));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('organization/settings/members')
                ->has('members')
                ->has('invitations')
            );
    });

    it('allows admin members to view members page', function () {
        $admin = User::factory()->create();
        Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($admin)
            ->get(route('organization.settings.members', $this->organization));

        $response->assertOk();
    });

    it('denies access to regular members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($member)
            ->get(route('organization.settings.members', $this->organization));

        $response->assertRedirect(route('organization.settings.leave', $this->organization));
    });
});

describe('InviteMemberController', function () {
    it('creates invitation for organization owner', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.settings.members.invite', $this->organization), [
                'email' => 'newmember@example.com',
                'role' => 'member',
            ]);

        $response->assertRedirect();

        expect(Invitation::where('email', 'newmember@example.com')
            ->where('organization_id', $this->organization->id)
            ->exists())->toBeTrue();
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.settings.members.invite', $this->organization), []);

        $response->assertSessionHasErrors(['email', 'role']);
    });

    it('validates email format', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.settings.members.invite', $this->organization), [
                'email' => 'invalid-email',
                'role' => 'member',
            ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates role values', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.settings.members.invite', $this->organization), [
                'email' => 'test@example.com',
                'role' => 'invalid-role',
            ]);

        $response->assertSessionHasErrors(['role']);
    });

    it('denies access to non-admin members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($member)
            ->post(route('organization.settings.members.invite', $this->organization), [
                'email' => 'test@example.com',
                'role' => 'member',
            ]);

        $response->assertRedirect(route('organization.settings.leave', $this->organization));
    });
});

describe('DeleteInvitationController', function () {
    it('deletes invitation for organization owner', function () {
        $response = $this->actingAs($this->user)
            ->delete(route('organization.settings.members.invitations.delete', [
                'organization' => $this->organization,
                'invitation' => $this->invitation,
            ]));

        $response->assertRedirect();
        expect(Invitation::find($this->invitation->id))->toBeNull();
    });

    it('denies access to non-admin members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($member)
            ->delete(route('organization.settings.members.invitations.delete', [
                'organization' => $this->organization,
                'invitation' => $this->invitation,
            ]));

        $response->assertRedirect(route('organization.settings.leave', $this->organization));
    });
});

describe('ResendInvitationController', function () {
    it('resends invitation for organization owner', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.settings.members.invitations.resend', [
                'organization' => $this->organization,
                'invitation' => $this->invitation,
            ]));

        $response->assertRedirect();
    });

    it('denies access to non-admin members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($member)
            ->post(route('organization.settings.members.invitations.resend', [
                'organization' => $this->organization,
                'invitation' => $this->invitation,
            ]));

        $response->assertRedirect(route('organization.settings.leave', $this->organization));
    });
});
