<?php

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);
});

describe('MemberController', function () {
    describe('When showing leave organization page', function () {
        it('allows members to view the leave organization page', function () {
            // Given I have an organization with a member
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and visit the leave page
            $response = $this->actingAs($member)
                ->get(route('organization.settings.leave', $organization));

            // Then I should see the leave organization page
            $response->assertOk();
            $response->assertInertia(fn ($page) => $page->component('organization/settings/leave')
                ->has('organization')
                ->has('member')
            );
        });

        it('redirects owners to general settings when trying to access leave page', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();
            $owner->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the owner and try to visit the leave page
            $response = $this->actingAs($owner)
                ->get(route('organization.settings.leave', $organization));

            // Then I should be redirected to general settings
            $response->assertRedirect(route('organization.settings', $organization));
        });

        it('returns 403 for non-members trying to access leave page', function () {
            // Given I have an organization and a non-member user
            $owner = User::factory()->create();
            $nonMember = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();
            $nonMember->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the non-member and try to visit the leave page
            $response = $this->actingAs($nonMember)
                ->get(route('organization.settings.leave', $organization));

            // Then I should receive a 403 error
            $response->assertForbidden();
        });
    });

    describe('When leaving organization', function () {
        it('allows members to leave organization with correct password', function () {
            // Given I have an organization with a member
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            $membership = Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and leave with correct password
            $response = $this->actingAs($member)
                ->post(route('organization.settings.member.leave', $organization), [
                    'password' => 'password', // Default factory password
                ]);

            // Then I should be redirected and membership should be deleted
            $response->assertRedirect();
            $this->assertDatabaseMissing('members', ['id' => $membership->id]);
            expect($member->fresh()->current_organization_id)->toBeNull();
        });

        it('prevents leaving with incorrect password', function () {
            // Given I have an organization with a member
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and try to leave with wrong password
            $response = $this->actingAs($member)
                ->post(route('organization.settings.member.leave', $organization), [
                    'password' => 'wrong-password',
                ]);

            // Then I should receive validation errors
            $response->assertSessionHasErrors('password');
        });

        it('prevents owners from leaving their organization', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();
            $owner->update(['current_organization_id' => $organization->id]);

            // When I authenticate as the owner and try to leave
            $response = $this->actingAs($owner)
                ->post(route('organization.settings.member.leave', $organization), [
                    'password' => 'password',
                ]);

            // Then I should receive an error
            $response->assertSessionHasErrors('password');
            $response->assertSessionHasErrorsIn('default', [
                'password' => 'Organization owners cannot leave their organization. You must transfer ownership first.',
            ]);
        });

        it('redirects to onboarding when user has no remaining organizations', function () {
            // Given I have an organization with a member who has no other organizations
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization->id,
            ]);
            $member->update(['current_organization_id' => $organization->id]);
            $member->assignRole('member');

            // When I authenticate as the member and leave
            $response = $this->actingAs($member)
                ->post(route('organization.settings.member.leave', $organization), [
                    'password' => 'password',
                ]);

            // Then I should be redirected to onboarding
            $response->assertRedirect(route('onboarding.organization'));
            $response->assertSessionHas('message', 'You have left the organization. Create or join a new organization to continue.');
        });

        it('redirects to organization selection when user has multiple organizations', function () {
            // Given I have a member with three organizations
            $owner1 = User::factory()->create();
            $owner2 = User::factory()->create();
            $owner3 = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization1 = Organization::factory()->for($owner1, 'owner')->create();
            $organization2 = Organization::factory()->for($owner2, 'owner')->create();
            $organization3 = Organization::factory()->for($owner3, 'owner')->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization1->id,
            ]);
            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization2->id,
            ]);
            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization3->id,
            ]);
            $member->update(['current_organization_id' => $organization1->id]);
            $member->assignRole('member');

            // Verify the user has 3 organizations before leaving
            expect($member->organizations()->count())->toBe(3);

            // When I leave one organization
            $response = $this->actingAs($member)
                ->post(route('organization.settings.member.leave', $organization1), [
                    'password' => 'password',
                ]);

            // Verify the user has 2 organizations after leaving
            $member->refresh();
            expect($member->organizations()->count())->toBe(2);

            // Then I should be redirected to organization selection
            $response->assertRedirect(route('organization.select'));
            $response->assertSessionHas('message', 'You have successfully left the organization. Please select another organization to continue.');
        });

        it('sets only remaining organization as current when user has exactly one left', function () {
            // Given I have a member with two organizations
            $owner = User::factory()->create();
            $member = User::factory()->create(['current_organization_id' => null]);
            $organization1 = Organization::factory()->for($owner, 'owner')->create();
            $organization2 = Organization::factory()->create();

            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization1->id,
            ]);
            Member::factory()->create([
                'user_id' => $member->id,
                'organization_id' => $organization2->id,
            ]);
            $member->update(['current_organization_id' => $organization1->id]);
            $member->assignRole('member');

            // When I leave one organization
            $response = $this->actingAs($member)
                ->post(route('organization.settings.member.leave', $organization1), [
                    'password' => 'password',
                ]);

            // Then the remaining organization should be set as current
            $response->assertRedirect(route('organization.overview', $organization2));
            expect($member->fresh()->current_organization_id)->toBe($organization2->id);
        });
    });
});
