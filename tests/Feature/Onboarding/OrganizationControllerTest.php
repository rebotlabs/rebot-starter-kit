<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);

    $this->user = User::factory()->create(['current_organization_id' => null]);
});

describe('ShowCreateOrganizationController', function () {
    it('displays the create organization page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('onboarding.organization'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('onboarding/create-organization')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('onboarding.organization'));

        $response->assertRedirect(route('login'));
    });
});

describe('CreateOrganizationController', function () {
    it('creates organization with valid data', function () {
        $organizationData = [
            'name' => 'Test Organization',
            'slug' => 'test-organization',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('onboarding.organization.store'), $organizationData);

        $response->assertRedirect();

        $organization = Organization::where('slug', 'test-organization')->first();
        expect($organization)->not->toBeNull();
        expect($organization->name)->toBe('Test Organization');
        expect($organization->owner_id)->toBe($this->user->id);

        $this->user->refresh();
        expect($this->user->current_organization_id)->toBe($organization->id);
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->post(route('onboarding.organization.store'), []);

        $response->assertSessionHasErrors(['name']);
    });

    it('validates name is a string', function () {
        $response = $this->actingAs($this->user)
            ->post(route('onboarding.organization.store'), [
                'name' => 123,
            ]);

        $response->assertSessionHasErrors(['name']);
    });

    it('validates name is not too long', function () {
        $response = $this->actingAs($this->user)
            ->post(route('onboarding.organization.store'), [
                'name' => str_repeat('a', 256),
            ]);

        $response->assertSessionHasErrors(['name']);
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->post(route('onboarding.organization.store'), [
            'name' => 'Test Organization',
        ]);

        $response->assertRedirect(route('login'));
    });
});
