<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->user->update(['current_organization_id' => $this->organization->id]);
});

describe('ShowOrganizationOverviewController', function () {
    it('displays organization overview for authenticated users', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.overview', $this->organization));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('organization/overview')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('organization.overview', $this->organization));

        $response->assertRedirect(route('login'));
    });

    it('shows correct organization component', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.overview', $this->organization));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('organization/overview')
        );
    });
});
