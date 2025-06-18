<?php

use App\Models\Invitation;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);
    Notification::fake();
});

describe('MembersController', function () {
    describe('When viewing members page', function () {
        it('allows admins to view members page', function () {
            // Given I have an organization with an admin
            $owner = User::factory()->create();
            $admin = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            $admin->assignRole('admin');

            // Set current organization for admin
            $admin->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the admin and visit members page
            $response = $this->actingAs($admin)
                ->get(route('organization.settings.members', $organization));

            // Then I should see the members page
            $response->assertOk();
            $response->assertInertia(fn ($page) => $page->component('organization/settings/members')
                ->has('invitations')
                ->has('members')
            );
        });

        it('allows owners to view members page', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();
            $owner->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the owner and visit members page
            $response = $this->actingAs($owner)
                ->get(route('organization.settings.members', $organization));

            // Then I should see the members page
            $response->assertOk();
            $response->assertInertia(fn ($page) => $page->component('organization/settings/members')
                ->has('invitations')
                ->has('members')
            );
        });

        it('redirects members to leave page when trying to access members page', function () {
            // Given I have an organization with a regular member
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and try to visit members page
            $response = $this->actingAs($member)
                ->get(route('organization.settings.members', $organization));

            // Then I should be redirected to leave page
            $response->assertRedirect(route('organization.settings.leave', $organization));
        });

        it('provides member role information from spatie permissions', function () {
            // Given I have an organization with members having different roles
            $owner = User::factory()->create(['current_organization_id' => null]);
            $admin = User::factory()->create(['current_organization_id' => null]);
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);

            $owner->update(['current_organization_id' => $organization->id]);
            $admin->update(['current_organization_id' => $organization->id]);
            $member->update(['current_organization_id' => $organization->id]);

            $admin->assignRole('admin');
            $member->assignRole('member');

            // When I authenticate as the owner and visit members page
            $response = $this->actingAs($owner)
                ->get(route('organization.settings.members', $organization));

            // Then I should see members with their roles from spatie permissions
            $response->assertInertia(fn ($page) => $page->has('members.0.role')
                ->has('members.1.role')
                ->where('members.0.role', 'admin')
                ->where('members.1.role', 'member')
            );
        });
    });

    describe('When inviting members', function () {
        it('allows admins to invite new members', function () {
            // Given I have an organization with an admin
            $owner = User::factory()->create(['current_organization_id' => null]);
            $admin = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            $owner->update(['current_organization_id' => $organization->id]);
            $admin->update(['current_organization_id' => $organization->id]);
            $admin->assignRole('admin');

            // When I authenticate as the admin and invite a new member
            $response = $this->actingAs($admin)
                ->post(route('organization.settings.members.invite', $organization), [
                    'email' => 'newmember@example.com',
                    'role' => 'member',
                ]);

            // Then an invitation should be created and notification sent
            $response->assertRedirect();
            $this->assertDatabaseHas('invitations', [
                'email' => 'newmember@example.com',
                'role' => 'member',
                'organization_id' => $organization->id,
            ]);

            Notification::assertSentTo(
                Invitation::where('email', 'newmember@example.com')->first(),
                InvitationSentNotification::class
            );
        });

        it('allows owners to invite new members', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();
            $owner->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the owner and invite a new admin
            $response = $this->actingAs($owner)
                ->post(route('organization.settings.members.invite', $organization), [
                    'email' => 'newadmin@example.com',
                    'role' => 'admin',
                ]);

            // Then an invitation should be created
            $response->assertRedirect();
            $this->assertDatabaseHas('invitations', [
                'email' => 'newadmin@example.com',
                'role' => 'admin',
                'organization_id' => $organization->id,
            ]);
        });

        it('validates invitation data', function () {
            // Given I have an organization with an admin
            $owner = User::factory()->create(['current_organization_id' => null]);
            $admin = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            $owner->update(['current_organization_id' => $organization->id]);
            $admin->update(['current_organization_id' => $organization->id]);
            $admin->assignRole('admin');

            // When I try to invite with invalid data
            $response = $this->actingAs($admin)
                ->post(route('organization.settings.members.invite', $organization), [
                    'email' => 'invalid-email',
                    'role' => 'invalid-role',
                ]);

            // Then I should receive validation errors
            $response->assertStatus(302); // Expect redirect due to validation errors
            $response->assertSessionHasErrors(['email', 'role']);
        });

        it('prevents regular members from inviting users', function () {
            // Given I have an organization with a regular member
            $owner = User::factory()->create(['current_organization_id' => null]);
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $owner->update(['current_organization_id' => $organization->id]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and try to invite someone
            $response = $this->actingAs($member)
                ->post(route('organization.settings.members.invite', $organization), [
                    'email' => 'test@example.com',
                    'role' => 'member',
                ]);

            // Then I should be redirected to leave page (by middleware)
            $response->assertRedirect(route('organization.settings.leave', $organization));
        });
    });

    describe('When managing invitations', function () {
        it('allows admins to resend invitations', function () {
            // Given I have an organization with an admin and a pending invitation
            $owner = User::factory()->create(['current_organization_id' => null]);
            $admin = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            $owner->update(['current_organization_id' => $organization->id]);
            $admin->update(['current_organization_id' => $organization->id]);
            $admin->assignRole('admin');

            $invitation = Invitation::factory()->create([
                'organization_id' => $organization->id,
                'email' => 'test@example.com',
                'role' => 'member',
            ]);

            // When I resend the invitation
            $response = $this->actingAs($admin)
                ->post(route('organization.settings.members.invitations.resend', [$organization, $invitation]));

            // Then the invitation notification should be sent again
            $response->assertRedirect();
            Notification::assertSentTo($invitation, InvitationSentNotification::class);
        });

        it('allows admins to delete invitations', function () {
            // Given I have an organization with an admin and a pending invitation
            $owner = User::factory()->create(['current_organization_id' => null]);
            $admin = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $admin->id,
                'organization_id' => $organization->id,
            ]);
            $owner->update(['current_organization_id' => $organization->id]);
            $admin->update(['current_organization_id' => $organization->id]);
            $admin->assignRole('admin');

            $invitation = Invitation::factory()->create([
                'organization_id' => $organization->id,
                'email' => 'test@example.com',
                'role' => 'member',
            ]);

            // When I delete the invitation
            $response = $this->actingAs($admin)
                ->delete(route('organization.settings.members.invitations.delete', [$organization, $invitation]));

            // Then the invitation should be deleted
            $response->assertRedirect();
            $this->assertSoftDeleted('invitations', ['id' => $invitation->id]);
        });
    });
});
