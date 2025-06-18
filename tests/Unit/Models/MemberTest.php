<?php

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);
});

describe('Member Model', function () {
    describe('When creating member relationships', function () {
        it('belongs to a user', function () {
            // Given I have a member
            $user = User::factory()->create();
            $organization = Organization::factory()->create();
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);

            // Then the member should belong to the user
            expect($member->user->id)->toBe($user->id);
            expect($member->user)->toBeInstanceOf(User::class);
        });

        it('belongs to an organization', function () {
            // Given I have a member
            $user = User::factory()->create();
            $organization = Organization::factory()->create();
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);

            // Then the member should belong to the organization
            expect($member->organization->id)->toBe($organization->id);
            expect($member->organization)->toBeInstanceOf(Organization::class);
        });
    });

    describe('When using factory states', function () {
        it('can create member with admin role using factory', function () {
            // Given I create a member with admin role using factory
            $member = Member::factory()->admin()->create();

            // Then the user should have admin role
            expect($member->user->hasRole('admin'))->toBeTrue();
        });

        it('can create member with member role using factory', function () {
            // Given I create a member with member role using factory
            $member = Member::factory()->member()->create();

            // Then the user should have member role
            expect($member->user->hasRole('member'))->toBeTrue();
        });
    });

    describe('When managing fillable attributes', function () {
        it('allows mass assignment of organization_id and user_id', function () {
            // Given I have user and organization data
            $user = User::factory()->create();
            $organization = Organization::factory()->create();

            // When I create a member with mass assignment
            $member = Member::create([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);

            // Then the member should be created successfully
            expect($member->user_id)->toBe($user->id);
            expect($member->organization_id)->toBe($organization->id);
        });
    });
});
