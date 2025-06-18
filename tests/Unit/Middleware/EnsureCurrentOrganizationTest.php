<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureCurrentOrganization;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureCurrentOrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_given_user_with_current_organization_when_accessing_protected_route_then_it_allows_access()
    {
        // Given
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->currentOrganization()->associate($organization)->save();

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $middleware = new EnsureCurrentOrganization;

        // When
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        });

        // Then
        $this->assertEquals('Success', $response->getContent());
    }

    public function test_given_user_without_current_organization_when_accessing_protected_route_then_it_redirects_to_onboarding()
    {
        // Given
        $user = User::factory()->create();
        // User has no current organization

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $middleware = new EnsureCurrentOrganization;

        // When
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        });

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('onboarding', $response->headers->get('Location'));
    }

    public function test_given_no_authenticated_user_when_accessing_protected_route_then_it_redirects_to_onboarding()
    {
        // Given
        $request = Request::create('/test');
        $request->setUserResolver(function () {
            return null; // No authenticated user
        });

        $middleware = new EnsureCurrentOrganization;

        // When
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        });

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('onboarding', $response->headers->get('Location'));
    }

    public function test_given_user_with_current_organization_set_to_null_when_accessing_protected_route_then_it_redirects_to_onboarding()
    {
        // Given
        $user = User::factory()->create();
        $user->currentOrganization()->dissociate()->save();

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $middleware = new EnsureCurrentOrganization;

        // When
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        });

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('onboarding', $response->headers->get('Location'));
    }
}
