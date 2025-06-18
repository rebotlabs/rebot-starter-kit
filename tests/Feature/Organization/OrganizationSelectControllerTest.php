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
    $this->organization1 = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->organization2 = Organization::factory()->create();

    // Make user a member of both organizations
    Member::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization1->id,
    ]);
    Member::factory()->create([
        'user_id' => $this->user->id,
        'organization_id' => $this->organization2->id,
    ]);
    $this->user->assignRole('member');
    $this->user->update(['current_organization_id' => $this->organization1->id]);
});

describe('ShowOrganizationSelectController', function () {
    it('displays organization selection page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.select'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('organization/select')
                ->has('organizations')
            );
    });

    it('shows all user organizations', function () {
        $response = $this->actingAs($this->user)
            ->get(route('organization.select'));

        $response->assertInertia(fn (Assert $page) => $page
            ->has('organizations', 2)
        );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('organization.select'));

        $response->assertRedirect(route('login'));
    });
});

describe('SwitchOrganizationController', function () {
    it('switches to a valid organization', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', $this->organization2));

        $response->assertRedirect(route('organization.overview', $this->organization2));

        $this->user->refresh();
        expect($this->user->current_organization_id)->toBe($this->organization2->id);
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->post(route('organization.switch', $this->organization1));

        $response->assertRedirect(route('login'));
    });

    it('throws exception for non-member organization', function () {
        $nonMemberOrg = Organization::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', $nonMemberOrg));

        $response->assertForbidden();
    });

    it('throws exception for non-existent organization', function () {
        $response = $this->actingAs($this->user)
            ->post(route('organization.switch', 99999));

        $response->assertNotFound();
    });
});
