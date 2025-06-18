<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('PasswordResetLinkController', function () {
    it('displays password reset request page', function () {
        $response = $this->get(route('password.request'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('auth/forgot-password')
            );
    });

    it('sends reset link for valid email', function () {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => $this->user->email,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($this->user, ResetPassword::class);
    });

    it('validates email field', function () {
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates email format', function () {
        $response = $this->post(route('password.email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('does not reveal non-existent email', function () {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status');
    });
});

describe('NewPasswordController', function () {
    it('displays password reset form', function () {
        $response = $this->get(route('password.reset', 'token'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('auth/reset-password')
                ->where('token', 'token')
            );
    });

    it('resets password with valid token', function () {
        $token = Password::createToken($this->user);
        $newPassword = 'new-password';

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('status');

        expect(Hash::check($newPassword, $this->user->fresh()->password))->toBeTrue();
    });

    it('validates required fields', function () {
        $response = $this->post(route('password.store'), []);

        $response->assertSessionHasErrors(['token', 'email', 'password']);
    });

    it('validates email format', function () {
        $response = $this->post(route('password.store'), [
            'token' => 'token',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates password confirmation', function () {
        $response = $this->post(route('password.store'), [
            'token' => 'token',
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);
    });

    it('rejects invalid token', function () {
        $response = $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => $this->user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors(['email']);
    });
});
