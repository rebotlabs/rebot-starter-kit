<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationOtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationOtpController extends Controller
{
    /**
     * Show the email verification OTP form.
     */
    public function create(): Response
    {

        return Inertia::render('auth/verify-email-otp');
    }

    /**
     * Handle OTP verification.
     */
    public function store(EmailVerificationOtpRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Find and validate the OTP
        $result = $user->consumeOneTimePassword($request->otp);

        if (! $result->isOk()) {
            throw ValidationException::withMessages([
                'otp' => ['The verification code is invalid or has expired.'],
            ]);
        }

        // Mark email as verified and delete the OTP
        $user->markEmailAsVerified();

        return to_route('dashboard')->with('message', __('messages.success.email_verified'));
    }
}
