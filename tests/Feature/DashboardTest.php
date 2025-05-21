<?php

namespace Tests\Feature;

use App\Models\Team;
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

        $team = Team::factory()->for($user, 'owner')->create();

        $user->currentTeam()->associate($team)->save();

        $this->get('/dashboard')->assertRedirectToRoute('team.overview', ['team' => $team->slug]);
    }
}
