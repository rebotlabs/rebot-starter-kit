<?php

declare(strict_types=1);

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization1 = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->organization2 = Organization::factory()->create();

    // Make user a member of organization2
    Member::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization2->id,
    ]);

    // Set initial current organization
    $this->user->currentOrganization()->associate($this->organization1)->save();
});

describe('SwitchOrganizationController', function () {
    it('switches to organization user is member of', function () {
        expect($this->user->currentOrganization->id)->toBe($this->organization1->id);

        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', $this->organization2));

        $response->assertRedirect(route('organization.overview', $this->organization2));

        $this->user->refresh();
        expect($this->user->currentOrganization->id)->toBe($this->organization2->id);
    });

    it('switches to organization user owns', function () {
        $this->user->currentOrganization()->associate($this->organization2)->save();

        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', $this->organization1));

        $response->assertRedirect(route('organization.overview', $this->organization1));

        $this->user->refresh();
        expect($this->user->currentOrganization->id)->toBe($this->organization1->id);
    });

    it('returns 403 for organization user is not member of', function () {
        $otherOrganization = Organization::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', $otherOrganization));

        $response->assertForbidden();

        // Current organization should not change
        $this->user->refresh();
        expect($this->user->currentOrganization->id)->toBe($this->organization1->id);
    });

    it('requires authentication', function () {
        $response = $this->post(route('organization.switch', $this->organization2));

        $response->assertRedirect(route('login'));
    });
});
