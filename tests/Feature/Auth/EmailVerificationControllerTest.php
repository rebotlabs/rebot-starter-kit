<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    $this->user = User::factory()->unverified()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
});

describe('EmailVerificationPromptController', function () {
    it('displays email verification page for unverified user', function () {
        $response = $this->actingAs($this->user)
            ->get(route('verification.notice'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('auth/verify-email-otp')
            );
    });

    it('redirects verified user to intended page', function () {
        $this->user->markEmailAsVerified();

        $response = $this->actingAs($this->user)
            ->get(route('verification.notice'));

        $response->assertRedirect(route('dashboard'));
    });
});

describe('VerifyEmailController', function () {
    it('verifies email with valid signed URL', function () {
        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        $response = $this->actingAs($this->user)
            ->get($verificationUrl);

        $response->assertRedirect(route('dashboard').'?verified=1');
        expect($this->user->fresh()->hasVerifiedEmail())->toBeTrue();
        Event::assertDispatched(Verified::class);
    });

    it('returns 403 for invalid signature', function () {
        $response = $this->actingAs($this->user)
            ->get(route('verification.verify', [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]));

        $response->assertForbidden();
    });

    it('returns 403 for wrong hash', function () {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->id,
                'hash' => sha1('wrong-email@example.com'),
            ]
        );

        $response = $this->actingAs($this->user)
            ->get($verificationUrl);

        $response->assertForbidden();
    });

    it('redirects already verified user', function () {
        $this->user->markEmailAsVerified();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        $response = $this->actingAs($this->user)
            ->get($verificationUrl);

        $response->assertRedirect(route('dashboard').'?verified=1');
    });
});

describe('EmailVerificationNotificationController', function () {
    it('sends verification email', function () {
        $response = $this->actingAs($this->user)
            ->post(route('verification.send'));

        $response->assertRedirect()
            ->assertSessionHas('status', 'verification-link-sent');
    });

    it('does not send to already verified user', function () {
        $this->user->markEmailAsVerified();

        $response = $this->actingAs($this->user)
            ->post(route('verification.send'));

        $response->assertRedirect(route('dashboard'));
    });
});
