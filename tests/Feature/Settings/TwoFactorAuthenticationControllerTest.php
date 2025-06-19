<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = app(TwoFactorAuthenticationService::class);
});

describe('TwoFactorAuthenticationController', function () {
    test('it shows the security page', function () {
        $response = $this->actingAs($this->user)->get(route('settings.security'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('settings/security')
            ->has('twoFactorEnabled')
            ->where('twoFactorEnabled', false)
        );
    });

    test('it generates QR code for two-factor setup', function () {
        $response = $this->actingAs($this->user)
            ->post(route('settings.security.two-factor.store'));

        $response->assertOk();
        $response->assertJsonStructure(['qrCode', 'secret']);
        expect(Session::has('2fa_secret'))->toBeTrue();
    });

    test('it confirms two-factor authentication with valid code', function () {
        $secret = $this->service->generateSecretKey();
        Session::put('2fa_secret', $secret);

        // Generate a valid TOTP code
        $google2fa = new \PragmaRX\Google2FA\Google2FA;
        $validCode = $google2fa->getCurrentOtp($secret);

        $response = $this->actingAs($this->user)
            ->post(route('settings.security.two-factor.confirm'), [
                'code' => $validCode,
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['recoveryCodes']);

        $this->user->refresh();
        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeTrue();
        expect(Session::has('2fa_secret'))->toBeFalse();
    });

    test('it rejects invalid confirmation code', function () {
        $secret = $this->service->generateSecretKey();
        Session::put('2fa_secret', $secret);

        $response = $this->actingAs($this->user)
            ->postJson(route('settings.security.two-factor.confirm'), [
                'code' => '123456',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['code']);
    });

    test('it disables two-factor authentication with correct password', function () {
        // Enable 2FA first
        $secret = $this->service->generateSecretKey();
        $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $response = $this->actingAs($this->user)
            ->delete(route('settings.security.two-factor.destroy'), [
                'password' => 'password',
            ]);

        $response->assertOk();

        $this->user->refresh();
        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeFalse();
    });

    test('it rejects disabling two-factor authentication with incorrect password', function () {
        // Enable 2FA first
        $secret = $this->service->generateSecretKey();
        $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('settings.security.two-factor.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['password']);
    });

    test('it regenerates recovery codes', function () {
        // Enable 2FA first
        $secret = $this->service->generateSecretKey();
        $originalCodes = $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $response = $this->actingAs($this->user)
            ->post(route('settings.security.recovery-codes'));

        $response->assertOk();
        $response->assertJsonStructure(['recoveryCodes']);

        $newCodes = $response->json('recoveryCodes');
        expect($newCodes)->not->toEqual($originalCodes);
    });

    test('it requires authentication for all endpoints', function () {
        $endpoints = [
            ['GET', route('settings.security')],
            ['POST', route('settings.security.two-factor.store')],
            ['POST', route('settings.security.two-factor.confirm')],
            ['DELETE', route('settings.security.two-factor.destroy')],
            ['POST', route('settings.security.recovery-codes')],
        ];

        foreach ($endpoints as [$method, $url]) {
            $response = $this->call($method, $url);
            $response->assertRedirect(route('login'));
        }
    });
});
