<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\User\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorAuthenticationController extends Controller
{
    public function __construct(
        protected TwoFactorAuthenticationService $twoFactorService
    ) {}

    /**
     * Show the two-factor authentication setup page.
     */
    public function show(): Response
    {

        $user = Auth::user();

        return Inertia::render('settings/security', [
            'twoFactorEnabled' => $user->hasEnabledTwoFactorAuthentication(),
            'recoveryCodes' => $user->hasEnabledTwoFactorAuthentication()
                ? $this->twoFactorService->getDecryptedRecoveryCodes($user)
                : null,
        ]);
    }

    /**
     * Generate a new two-factor authentication secret and QR code.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $secret = $this->twoFactorService->generateSecretKey();
        $qrCode = $this->twoFactorService->generateQrCode($user, $secret);

        // Store secret temporarily in session
        session(['2fa_secret' => $secret]);

        return response()->json([
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);
    }

    /**
     * Confirm and enable two-factor authentication.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $secret = session('2fa_secret');

        if (! $secret) {
            throw ValidationException::withMessages([
                'code' => __('Two-factor authentication setup session has expired. Please try again.'),
            ]);
        }

        if (! $this->twoFactorService->verifyCode($secret, $request->code)) {
            throw ValidationException::withMessages([
                'code' => __('The provided two-factor authentication code is invalid.'),
            ]);
        }

        $recoveryCodes = $this->twoFactorService->enableTwoFactorAuthentication($user, $secret);

        // Clear the temporary secret from session
        session()->forget('2fa_secret');

        return response()->json([
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Disable two-factor authentication.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('The provided password is incorrect.'),
            ]);
        }

        $this->twoFactorService->disableTwoFactorAuthentication($user);

        return response()->json([
            'message' => __('Two-factor authentication has been disabled.'),
        ]);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = Auth::user();

        if (! $user->hasEnabledTwoFactorAuthentication()) {
            return response()->json(['message' => 'Two-factor authentication is not enabled.'], 400);
        }

        $recoveryCodes = $this->twoFactorService->regenerateRecoveryCodes($user);

        return response()->json([
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request, UpdateUserPasswordAction $action): RedirectResponse
    {
        $action->execute(user: $request->user(), password: $request->validated()['password']);

        return back();
    }
}
