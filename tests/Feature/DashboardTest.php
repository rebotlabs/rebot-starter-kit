<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $this->actingAs($user = User::factory()->create());

        $organization = Organization::factory()->for($user, 'owner')->create();

        $user->currentOrganization()->associate($organization)->save();

        $this->get('/dashboard')->assertRedirectToRoute('organization.overview', ['organization' => $organization->slug]);
    }
}
