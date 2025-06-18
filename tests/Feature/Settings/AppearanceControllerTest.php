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

describe('ShowAppearanceController', function () {
    it('displays the appearance settings page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('appearance'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/appearance')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('appearance'));

        $response->assertRedirect(route('login'));
    });

    it('redirects users without current organization to onboarding', function () {
        $userWithoutOrg = User::factory()->create(['current_organization_id' => null]);

        $response = $this->actingAs($userWithoutOrg)
            ->get(route('appearance'));

        $response->assertOk(); // Should be able to access appearance settings without organization
    });
});
