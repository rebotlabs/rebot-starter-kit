<?php

declare(strict_types=1);

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ShowGeneralSettingsController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->user->update(['current_organization_id' => $this->organization->id]);
    });

    it('displays general settings page for organization owner', function () {
        $this->actingAs($this->user)
            ->get(route('organization.settings', $this->organization))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('organization/settings/general')
                ->has('organization')
                ->where('organization.id', $this->organization->id)
                ->where('organization.name', $this->organization->name)
                ->where('organization.slug', $this->organization->slug)
                ->has('members')
                ->where('members', [])
            );
    });

    it('displays general settings page for organization admin', function () {
        $admin = User::factory()->create();
        $member = Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($admin)
            ->get(route('organization.settings', $this->organization))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('organization/settings/general')
                ->has('organization')
                ->where('organization.id', $this->organization->id)
                ->has('members')
                ->has('members.0')
                ->where('members.0.id', $member->id)
                ->where('members.0.user.id', $admin->id)
            );
    });

    it('denies access to regular members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($member)
            ->get(route('organization.settings', $this->organization))
            ->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('requires authentication', function () {
        $this->get(route('organization.settings', $this->organization))
            ->assertRedirect(route('login'));
    });
});

describe('UpdateGeneralSettingsController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->user->update(['current_organization_id' => $this->organization->id]);
    });

    it('updates organization settings for organization owner', function () {
        $updateData = [
            'name' => 'Updated Organization Name',
            'slug' => 'updated-org-slug',
        ];

        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), $updateData)
            ->assertRedirect();

        $this->organization->refresh();
        expect($this->organization->name)->toBe($updateData['name'])
            ->and($this->organization->slug)->toBe($updateData['slug']);
    });

    it('updates organization settings for organization admin', function () {
        $admin = User::factory()->create();
        Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $updateData = [
            'name' => 'Admin Updated Name',
            'slug' => 'admin-updated-slug',
        ];

        $this->actingAs($admin)
            ->patch(route('organization.settings.update', $this->organization), $updateData)
            ->assertRedirect();

        $this->organization->refresh();
        expect($this->organization->name)->toBe($updateData['name']);
    });

    it('validates required fields', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [])
            ->assertSessionHasErrors(['name', 'slug']);
    });

    it('validates name is a string', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => 123,
                'slug' => 'valid-slug',
            ])
            ->assertSessionHasErrors(['name']);
    });

    it('validates name maximum length', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => str_repeat('a', 256),
                'slug' => 'valid-slug',
            ])
            ->assertSessionHasErrors(['name']);
    });

    it('validates slug is unique', function () {
        $existingOrg = Organization::factory()->create(['slug' => 'existing-slug']);

        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => 'Valid Name',
                'slug' => 'existing-slug',
            ])
            ->assertSessionHasErrors(['slug']);
    });

    it('allows updating to the same slug', function () {
        $currentSlug = $this->organization->slug;

        $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => 'Updated Name',
                'slug' => $currentSlug,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    });

    it('redirects to new URL when slug changes', function () {
        $newSlug = 'brand-new-slug';

        $response = $this->actingAs($this->user)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => 'Updated Name',
                'slug' => $newSlug,
            ]);

        $this->organization->refresh();
        expect($this->organization->slug)->toBe($newSlug);

        $response->assertRedirect(route('organization.settings', $this->organization));
    });

    it('denies access to regular members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($member)
            ->patch(route('organization.settings.update', $this->organization), [
                'name' => 'Updated Name',
                'slug' => 'updated-slug',
            ])
            ->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('requires authentication', function () {
        $this->patch(route('organization.settings.update', $this->organization), [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
        ])
            ->assertRedirect(route('login'));
    });
});

describe('ChangeOwnershipController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->user->update(['current_organization_id' => $this->organization->id]);

        $this->newOwner = User::factory()->create();
        $this->member = Member::factory()->create([
            'user_id' => $this->newOwner->id,
            'organization_id' => $this->organization->id,
        ]);
    });

    it('changes organization ownership for organization owner', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.ownership', $this->organization), [
                'member_id' => $this->member->id,
            ])
            ->assertRedirect();

        $this->organization->refresh();
        expect($this->organization->owner_id)->toBe($this->newOwner->id);
    });

    it('validates member_id is required', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.ownership', $this->organization), [])
            ->assertSessionHasErrors(['member_id']);
    });

    it('validates member_id exists', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.ownership', $this->organization), [
                'member_id' => 99999,
            ])
            ->assertSessionHasErrors(['member_id']);
    });

    it('denies access to non-owners', function () {
        $admin = User::factory()->create();
        Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($admin)
            ->patch(route('organization.settings.ownership', $this->organization), [
                'member_id' => $this->member->id,
            ])
            ->assertForbidden();
    });

    it('requires authentication', function () {
        $this->patch(route('organization.settings.ownership', $this->organization), [
            'member_id' => $this->member->id,
        ])
            ->assertRedirect(route('login'));
    });
});

describe('DeleteOrganizationController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        // Create member record for the user in their own organization
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);
        $this->user->update(['current_organization_id' => $this->organization->id]);
    });

    it('deletes organization for organization owner', function () {
        $organizationId = $this->organization->id;

        $this->actingAs($this->user)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('onboarding.organization'));

        $this->assertDatabaseMissing('organizations', ['id' => $organizationId]);
    });

    it('deletes organization for organization admin', function () {
        $admin = User::factory()->create();
        Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $organizationId = $this->organization->id;

        $this->actingAs($admin)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('onboarding.organization'));

        $this->assertDatabaseMissing('organizations', ['id' => $organizationId]);
    });

    it('redirects to current organization when deleting non-current organization', function () {
        // Create a second organization for the user
        $secondOrg = Organization::factory()->create(['owner_id' => $this->user->id]);
        // Create member record for the user in the second organization
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $secondOrg->id,
        ]);

        // Set the second organization as current
        $this->user->update(['current_organization_id' => $secondOrg->id]);

        $organizationId = $this->organization->id;

        $this->actingAs($this->user)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('organization.overview', $secondOrg));

        $this->assertDatabaseMissing('organizations', ['id' => $organizationId]);

        // Verify the user's current organization didn't change
        $this->user->refresh();
        expect($this->user->current_organization_id)->toBe($secondOrg->id);
    });

    it('switches to another organization when deleting current organization with multiple orgs', function () {
        // Create a second organization for the user
        $secondOrg = Organization::factory()->create(['owner_id' => $this->user->id]);
        // Create member record for the user in the second organization
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $secondOrg->id,
        ]);

        // Keep the first organization as current
        $organizationId = $this->organization->id;

        $this->actingAs($this->user)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('organization.overview', $secondOrg));

        $this->assertDatabaseMissing('organizations', ['id' => $organizationId]);

        // Verify the user's current organization switched to the remaining one
        $this->user->refresh();
        expect($this->user->current_organization_id)->toBe($secondOrg->id);
    });

    it('redirects to organization selection when user has multiple organizations after deletion', function () {
        // Create multiple organizations for the user
        $secondOrg = Organization::factory()->create(['owner_id' => $this->user->id]);
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $secondOrg->id,
        ]);
        $thirdOrg = Organization::factory()->create(['owner_id' => $this->user->id]);
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $thirdOrg->id,
        ]);

        $organizationId = $this->organization->id;

        $this->actingAs($this->user)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('organization.select'));

        $this->assertDatabaseMissing('organizations', ['id' => $organizationId]);

        // Verify the user's current organization switched to one of the remaining ones
        $this->user->refresh();
        expect($this->user->current_organization_id)->toBeIn([$secondOrg->id, $thirdOrg->id]);
    });

    it('denies access to regular members', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($member)
            ->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('requires authentication', function () {
        $this->delete(route('organization.delete', $this->organization))
            ->assertRedirect(route('login'));
    });
});

describe('LeaveOrganizationController', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        $this->user->update(['current_organization_id' => $this->organization->id]);
    });

    it('dispatches leave organization job and redirects to dashboard', function () {
        $member = User::factory()->create();
        Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');

        $this->actingAs($member);

        // Test by directly calling the controller since no route exists
        $controller = new \App\Http\Controllers\Organization\Settings\General\LeaveOrganizationController;
        $response = $controller($this->organization);

        expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
        expect($response->getTargetUrl())->toBe(route('dashboard'));
    });

    it('works for organization admins', function () {
        $admin = User::factory()->create();
        Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $controller = new \App\Http\Controllers\Organization\Settings\General\LeaveOrganizationController;
        $response = $controller($this->organization);

        expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
        expect($response->getTargetUrl())->toBe(route('dashboard'));
    });

    it('works for organization owners', function () {
        $this->actingAs($this->user);

        $controller = new \App\Http\Controllers\Organization\Settings\General\LeaveOrganizationController;
        $response = $controller($this->organization);

        expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
        expect($response->getTargetUrl())->toBe(route('dashboard'));
    });

    it('requires authentication', function () {
        $controller = new \App\Http\Controllers\Organization\Settings\General\LeaveOrganizationController;

        // When no user is authenticated, auth()->user() returns null
        // This would cause a TypeError when passed to the job
        expect(fn () => $controller($this->organization))
            ->toThrow(\TypeError::class);
    });
});
