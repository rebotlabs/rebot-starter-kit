<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorAuthenticationService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * Generate a new secret key for the user.
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code for the user.
     */
    public function generateQrCode(User $user, string $secret): string
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return QrCode::size(200)->generate($qrCodeUrl)->toHtml();
    }

    /**
     * Verify the provided code against the user's secret.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enableTwoFactorAuthentication(User $user, string $secret): array
    {
        $recoveryCodes = $user->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $recoveryCodes;
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disableTwoFactorAuthentication(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Get the decrypted secret for the user.
     */
    public function getDecryptedSecret(User $user): ?string
    {
        if (! $user->two_factor_secret) {
            return null;
        }

        return Crypt::decryptString($user->two_factor_secret);
    }

    /**
     * Get the decrypted recovery codes for the user.
     */
    public function getDecryptedRecoveryCodes(User $user): ?array
    {
        if (! $user->two_factor_recovery_codes) {
            return null;
        }

        return json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true);
    }

    /**
     * Regenerate recovery codes for the user.
     */
    public function regenerateRecoveryCodes(User $user): array
    {
        $recoveryCodes = $user->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
        ])->save();

        return $recoveryCodes;
    }
}
