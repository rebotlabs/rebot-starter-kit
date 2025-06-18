<?php

namespace Tests\Unit\Middleware;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureOrganizationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_given_an_organization_owner_when_accessing_protected_route_then_it_allows_access()
    {
        // Given
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $user->id]);
        $organization->members()->create(['user_id' => $user->id]);

        // Set current organization for the user
        $user->update(['current_organization_id' => $organization->id]);

        // When
        $response = $this->actingAs($user)
            ->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertStatus(200);
    }

    public function test_given_an_organization_admin_when_accessing_protected_route_then_it_allows_access()
    {
        // Given
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $owner->id]);
        $organization->members()->create(['user_id' => $admin->id]);
        $admin->assignRole('admin');

        // Set current organization for the admin user
        $admin->update(['current_organization_id' => $organization->id]);

        // When
        $response = $this->actingAs($admin)
            ->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertStatus(200);
    }

    public function test_given_an_organization_member_when_accessing_protected_route_then_it_redirects_to_leave_page()
    {
        // Given
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $owner->id]);
        $organization->members()->create(['user_id' => $member->id]);
        $member->assignRole('member');

        // Set current organization for the member user
        $member->update(['current_organization_id' => $organization->id]);

        // When
        $response = $this->actingAs($member)
            ->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertRedirect(route('organization.settings.leave', $organization));
    }

    public function test_given_no_authenticated_user_when_accessing_protected_route_then_it_redirects_to_login()
    {
        // Given
        $organization = Organization::factory()->create();

        // When
        $response = $this->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertRedirect(route('login'));
    }

    public function test_given_user_not_member_of_organization_when_accessing_protected_route_then_it_redirects_to_leave_page()
    {
        // Given
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $owner->id]);
        // nonMember is not a member of the organization

        // Set current organization for the non-member user (but they won't be a member)
        $nonMember->update(['current_organization_id' => $organization->id]);

        // When
        $response = $this->actingAs($nonMember)
            ->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertRedirect(route('organization.settings.leave', $organization));
    }

    public function test_given_user_with_no_roles_when_accessing_protected_route_then_it_redirects_to_leave_page()
    {
        // Given
        $owner = User::factory()->create();
        $userWithoutRole = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $owner->id]);
        $organization->members()->create(['user_id' => $userWithoutRole->id]);
        // User has no role assigned

        // Set current organization for the user
        $userWithoutRole->update(['current_organization_id' => $organization->id]);

        // When
        $response = $this->actingAs($userWithoutRole)
            ->get("/org/{$organization->slug}/settings");

        // Then
        $response->assertRedirect(route('organization.settings.leave', $organization));
    }
}
