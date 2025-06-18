<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

describe('EmailVerificationPromptController', function () {
    it('redirects verified users to dashboard', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertRedirect(route('dashboard'));
    });

    it('shows verification prompt for unverified users', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('auth/verify-email-otp')
            );
    });

    it('shows verification prompt with status from session', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->withSession(['status' => 'verification-code-sent'])
            ->get(route('verification.notice'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('auth/verify-email-otp')
                ->where('status', 'verification-code-sent')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('login'));
    });
});

describe('ConfirmablePasswordController', function () {
    it('shows password confirmation page', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('password.confirm'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('auth/confirm-password')
            );
    });

    it('confirms password with correct password', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('password.confirm'), [
                'password' => 'password',
            ]);

        $response->assertRedirect()
            ->assertSessionMissing('errors');
    });

    it('validates incorrect password', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('password.confirm'), [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors(['password']);
    });

    it('validates password is required', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('password.confirm'), []);

        $response->assertSessionHasErrors(['password']);
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('password.confirm'));

        $response->assertRedirect(route('login'));
    });
});

describe('AuthenticatedSessionController', function () {
    it('shows login page for guests', function () {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('auth/login')
            );
    });

    it('redirects authenticated users away from login page', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    });

    it('authenticates user with valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    });

    it('validates incorrect credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    });

    it('validates required fields', function () {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
    });

    it('validates email format', function () {
        $response = $this->post(route('login'), [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('logs out authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    });

    it('handles remember me functionality', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        // Check that remember token is set
        $user->refresh();
        expect($user->remember_token)->not->toBeNull();
    });
});
