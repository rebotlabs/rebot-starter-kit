<?php

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);
});

describe('User Model', function () {
    describe('When managing organizations', function () {
        it('can have a current organization', function () {
            // Given I have a user and an organization
            $user = User::factory()->create();
            $organization = Organization::factory()->create();

            // When I set the current organization
            $user->currentOrganization()->associate($organization)->save();

            // Then the user should have the current organization
            expect($user->fresh()->currentOrganization->id)->toBe($organization->id);
        });

        it('can be a member of multiple organizations', function () {
            // Given I have a user and multiple organizations
            $user = User::factory()->create();
            $org1 = Organization::factory()->create();
            $org2 = Organization::factory()->create();

            // When I add the user as a member to both organizations
            Member::factory()->create([
                'user_id' => $user->id,
                'organization_id' => $org1->id,
            ]);
            Member::factory()->create([
                'user_id' => $user->id,
                'organization_id' => $org2->id,
            ]);

            // Then the user should have access to both organizations
            expect($user->organizations()->count())->toBe(2);
            expect($user->organizations->pluck('id'))->toContain($org1->id, $org2->id);
        });
    });

    describe('When managing roles', function () {
        it('can be assigned admin role', function () {
            // Given I have a user
            $user = User::factory()->create();

            // When I assign admin role
            $user->assignRole('admin');

            // Then the user should have admin role
            expect($user->hasRole('admin'))->toBeTrue();
            expect($user->hasRole('member'))->toBeFalse();
        });

        it('can be assigned member role', function () {
            // Given I have a user
            $user = User::factory()->create();

            // When I assign member role
            $user->assignRole('member');

            // Then the user should have member role
            expect($user->hasRole('member'))->toBeTrue();
            expect($user->hasRole('admin'))->toBeFalse();
        });

        it('can have role changed from member to admin', function () {
            // Given I have a user with member role
            $user = User::factory()->create();
            $user->assignRole('member');

            // When I change role to admin
            $user->syncRoles(['admin']);

            // Then the user should have admin role only
            expect($user->hasRole('admin'))->toBeTrue();
            expect($user->hasRole('member'))->toBeFalse();
        });
    });
});
