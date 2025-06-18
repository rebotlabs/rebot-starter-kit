<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

describe('RegisteredUserController', function () {
    it('creates user with valid registration data', function () {
        Event::fake();

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'test@example.com')->first();
        expect($user)->not->toBeNull();
        expect($user->name)->toBe('Test User');
        expect(Hash::check('password123', $user->password))->toBeTrue();

        Event::assertDispatched(Registered::class);
        $this->assertAuthenticatedAs($user);
    });

    it('validates required fields', function () {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    });

    it('validates email format', function () {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates unique email', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates password confirmation', function () {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    });
});

describe('PasswordResetLinkController', function () {
    it('sends password reset link for valid email', function () {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status', 'A reset link will be sent if the account exists.');
    });

    it('validates email format', function () {
        $response = $this->post(route('password.email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates email is required', function () {
        $response = $this->post(route('password.email'), []);

        $response->assertSessionHasErrors(['email']);
    });
});

describe('NewPasswordController', function () {
    it('resets password with valid token', function () {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();
        expect(Hash::check('new-password123', $user->password))->toBeTrue();
    });

    it('validates token is required', function () {
        $response = $this->post(route('password.store'), [
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasErrors(['token']);
    });

    it('validates invalid token', function () {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    });
});

describe('EmailVerificationOtpController', function () {
    it('verifies user with correct OTP', function () {
        $user = User::factory()->create(['email_verified_at' => null]);
        $otp = $user->createOneTimePassword(20);

        $response = $this->actingAs($user)
            ->post(route('verification.otp.store'), [
                'otp' => $otp->password,
            ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        expect($user->email_verified_at)->not->toBeNull();
    });

    it('throws exception for invalid OTP', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->post(route('verification.otp.store'), [
                'otp' => 'invalid-otp',
            ]);

        $response->assertSessionHasErrors(['otp']);
    });

    it('validates OTP is required', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->post(route('verification.otp.store'), []);

        $response->assertSessionHasErrors(['otp']);
    });
});

describe('ResendEmailVerificationOtpController', function () {
    it('resends OTP for unverified user', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->post(route('verification.otp.resend'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-code-sent');
    });

    it('throws exception for already verified user', function () {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)
            ->post(route('verification.otp.resend'));

        $response->assertRedirect(route('dashboard'));
    });
});
