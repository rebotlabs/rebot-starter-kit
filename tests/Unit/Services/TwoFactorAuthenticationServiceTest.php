<?php

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = app(TwoFactorAuthenticationService::class);
});

describe('TwoFactorAuthenticationService', function () {
    test('it generates a secret key', function () {
        $secret = $this->service->generateSecretKey();

        expect($secret)->toBeString();
        expect(strlen($secret))->toBeGreaterThan(10);
    });

    test('it generates a QR code', function () {
        $secret = $this->service->generateSecretKey();
        $qrCode = $this->service->generateQrCode($this->user, $secret);

        expect($qrCode)->toBeString();
        expect($qrCode)->toContain('<svg');
    });

    test('it verifies a valid code', function () {
        $secret = $this->service->generateSecretKey();
        $google2fa = new \PragmaRX\Google2FA\Google2FA;
        $validCode = $google2fa->getCurrentOtp($secret);

        $result = $this->service->verifyCode($secret, $validCode);

        expect($result)->toBeTrue();
    });

    test('it rejects an invalid code', function () {
        $secret = $this->service->generateSecretKey();

        $result = $this->service->verifyCode($secret, '123456');

        expect($result)->toBeFalse();
    });

    test('it enables two-factor authentication', function () {
        $secret = $this->service->generateSecretKey();

        $recoveryCodes = $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $this->user->refresh();
        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeTrue();
        expect($recoveryCodes)->toBeArray();
        expect(count($recoveryCodes))->toBe(8);
        expect($this->user->two_factor_secret)->not->toBeNull();
        expect($this->user->two_factor_recovery_codes)->not->toBeNull();
        expect($this->user->two_factor_confirmed_at)->not->toBeNull();
    });

    test('it disables two-factor authentication', function () {
        // Enable 2FA first
        $secret = $this->service->generateSecretKey();
        $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $this->service->disableTwoFactorAuthentication($this->user);

        $this->user->refresh();
        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeFalse();
        expect($this->user->two_factor_secret)->toBeNull();
        expect($this->user->two_factor_recovery_codes)->toBeNull();
        expect($this->user->two_factor_confirmed_at)->toBeNull();
    });

    test('it gets decrypted secret', function () {
        $secret = $this->service->generateSecretKey();
        $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $decryptedSecret = $this->service->getDecryptedSecret($this->user);

        expect($decryptedSecret)->toBe($secret);
    });

    test('it returns null for decrypted secret when not set', function () {
        $decryptedSecret = $this->service->getDecryptedSecret($this->user);

        expect($decryptedSecret)->toBeNull();
    });

    test('it gets decrypted recovery codes', function () {
        $secret = $this->service->generateSecretKey();
        $originalCodes = $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $decryptedCodes = $this->service->getDecryptedRecoveryCodes($this->user);

        expect($decryptedCodes)->toBe($originalCodes);
    });

    test('it returns null for decrypted recovery codes when not set', function () {
        $decryptedCodes = $this->service->getDecryptedRecoveryCodes($this->user);

        expect($decryptedCodes)->toBeNull();
    });

    test('it regenerates recovery codes', function () {
        $secret = $this->service->generateSecretKey();
        $originalCodes = $this->service->enableTwoFactorAuthentication($this->user, $secret);

        $newCodes = $this->service->regenerateRecoveryCodes($this->user);

        expect($newCodes)->not->toBe($originalCodes);
        expect(count($newCodes))->toBe(8);

        $storedCodes = $this->service->getDecryptedRecoveryCodes($this->user);
        expect($storedCodes)->toBe($newCodes);
    });
});

describe('User model two-factor methods', function () {
    test('it checks if two-factor authentication is enabled', function () {
        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeFalse();

        $this->user->forceFill([
            'two_factor_secret' => Crypt::encryptString('test-secret'),
            'two_factor_confirmed_at' => now(),
        ])->save();

        expect($this->user->hasEnabledTwoFactorAuthentication())->toBeTrue();
    });

    test('it generates recovery codes', function () {
        $codes = $this->user->generateRecoveryCodes();

        expect($codes)->toBeArray();
        expect(count($codes))->toBe(8);

        foreach ($codes as $code) {
            expect($code)->toMatch('/^\d{4}-\d{4}$/');
        }
    });
});
