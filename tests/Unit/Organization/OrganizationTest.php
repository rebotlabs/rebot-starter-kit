<?php

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);
});

describe('Organization Model', function () {
    describe('When getting current user role', function () {
        it('returns owner when current user is the owner', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            // When I authenticate as the owner
            $this->actingAs($owner);

            // Then the current user role should be owner
            expect($organization->getCurrentUserRole())->toBe('owner');
        });

        it('returns admin when current user is an admin member', function () {
            // Given I have an organization with an admin member
            $owner = User::factory()->create();
            $admin = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'organization_id' => $organization->id,
                'user_id' => $admin->id,
            ]);
            $admin->assignRole('admin');

            // When I authenticate as the admin
            $this->actingAs($admin);

            // Then the current user role should be admin
            expect($organization->getCurrentUserRole())->toBe('admin');
        });

        it('returns member when current user is a regular member', function () {
            // Given I have an organization with a regular member
            $owner = User::factory()->create();
            $member = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'organization_id' => $organization->id,
                'user_id' => $member->id,
            ]);
            $member->assignRole('member');

            // When I authenticate as the member
            $this->actingAs($member);

            // Then the current user role should be member
            expect($organization->getCurrentUserRole())->toBe('member');
        });

        it('returns null when user is not authenticated', function () {
            // Given I have an organization
            $organization = Organization::factory()->create();

            // When no user is authenticated
            // Then the current user role should be null
            expect($organization->getCurrentUserRole())->toBeNull();
        });

        it('returns null when user is not a member', function () {
            // Given I have an organization and a user who is not a member
            $owner = User::factory()->create();
            $nonMember = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            // When I authenticate as the non-member
            $this->actingAs($nonMember);

            // Then the current user role should be null
            expect($organization->getCurrentUserRole())->toBeNull();
        });
    });

    describe('When checking if current user can manage', function () {
        it('returns true when current user is the owner', function () {
            // Given I have an organization with an owner
            $owner = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            // When I authenticate as the owner
            $this->actingAs($owner);

            // Then the user should be able to manage
            expect($organization->currentUserCanManage())->toBeTrue();
        });

        it('returns true when current user is an admin member', function () {
            // Given I have an organization with an admin member
            $owner = User::factory()->create();
            $admin = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'organization_id' => $organization->id,
                'user_id' => $admin->id,
            ]);
            $admin->assignRole('admin');

            // When I authenticate as the admin
            $this->actingAs($admin);

            // Then the user should be able to manage
            expect($organization->currentUserCanManage())->toBeTrue();
        });

        it('returns false when current user is a regular member', function () {
            // Given I have an organization with a regular member
            $owner = User::factory()->create();
            $member = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            Member::factory()->create([
                'organization_id' => $organization->id,
                'user_id' => $member->id,
            ]);
            $member->assignRole('member');

            // When I authenticate as the member
            $this->actingAs($member);

            // Then the user should not be able to manage
            expect($organization->currentUserCanManage())->toBeFalse();
        });

        it('returns false when user is not authenticated', function () {
            // Given I have an organization
            $organization = Organization::factory()->create();

            // When no user is authenticated
            // Then the user should not be able to manage
            expect($organization->currentUserCanManage())->toBeFalse();
        });

        it('returns false when user is not a member', function () {
            // Given I have an organization and a user who is not a member
            $owner = User::factory()->create();
            $nonMember = User::factory()->create();
            $organization = Organization::factory()->for($owner, 'owner')->create();

            // When I authenticate as the non-member
            $this->actingAs($nonMember);

            // Then the user should not be able to manage
            expect($organization->currentUserCanManage())->toBeFalse();
        });
    });
});
