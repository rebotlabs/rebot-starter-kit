<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->user->update(['current_organization_id' => $this->organization->id]);
});

describe('ShowDashboardController', function () {
    it('displays the dashboard page for authenticated users', function () {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('organization.overview', ['organization' => $this->organization]));
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    });

    it('redirects users without current organization to onboarding', function () {
        $userWithoutOrg = User::factory()->create(['current_organization_id' => null]);

        $response = $this->actingAs($userWithoutOrg)
            ->get(route('dashboard'));

        $response->assertRedirect(route('onboarding.organization'));
    });
});
