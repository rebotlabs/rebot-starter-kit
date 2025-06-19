<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        protected TwoFactorAuthenticationService $twoFactorService
    ) {}

    /**
     * Show the two-factor authentication challenge form.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('auth/two-factor-challenge');
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = session('2fa_user_id');
        $remember = session('2fa_remember', false);

        if (! $userId) {
            throw ValidationException::withMessages([
                'code' => 'Your session has expired. Please log in again.',
            ]);
        }

        $user = User::find($userId);

        if (! $user || ! $user->hasEnabledTwoFactorAuthentication()) {
            session()->forget(['2fa_user_id', '2fa_remember']);

            throw ValidationException::withMessages([
                'code' => 'Invalid authentication session.',
            ]);
        }

        $secret = $this->twoFactorService->getDecryptedSecret($user);

        if (! $this->twoFactorService->verifyCode($secret, $request->string('code')->toString())) {
            throw ValidationException::withMessages([
                'code' => 'The provided two-factor authentication code was invalid.',
            ]);
        }

        // Clear 2FA session data
        session()->forget(['2fa_user_id', '2fa_remember']);

        // Log the user in
        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
