<?php

declare(strict_types=1);

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);

    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);

    Member::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization->id,
    ]);
    $this->user->assignRole('admin');

    $this->user->update(['current_organization_id' => $this->organization->id]);
});

describe('ShowBillingController', function () {
    it('displays billing page for organization owner', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.settings.billing', $this->organization));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('organization/settings/billing')
            );
    });

    it('displays billing page for organization admin', function () {
        $admin = User::factory()->create();
        $member = Member::factory()->create([
            'user_id' => $admin->id,
            'organization_id' => $this->organization->id,
        ]);
        $admin->assignRole('admin');
        $admin->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($admin)
            ->get(route('organization.settings.billing', $this->organization));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('organization/settings/billing')
            );
    });

    it('redirects regular members to leave page', function () {
        $member = User::factory()->create();
        $memberModel = Member::factory()->create([
            'user_id' => $member->id,
            'organization_id' => $this->organization->id,
        ]);
        $member->assignRole('member');
        $member->update(['current_organization_id' => $this->organization->id]);

        $response = $this->actingAs($member)
            ->get(route('organization.settings.billing', $this->organization));

        $response->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('organization.settings.billing', $this->organization));

        $response->assertRedirect(route('login'));
    });

    it('redirects users without current organization to onboarding', function () {
        $userWithoutOrg = User::factory()->create(['current_organization_id' => null]);

        $response = $this->actingAs($userWithoutOrg)
            ->get(route('organization.settings.billing', $this->organization));

        $response->assertRedirect(route('onboarding.organization'));
    });
});
