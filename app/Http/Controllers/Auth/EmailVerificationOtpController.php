<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\EmailVerificationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = $request->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'otp' => ['You must be logged in to verify your email.'],
            ]);
        }

        // Find and validate the OTP
        $result = $user->consumeOneTimePassword($request->otp);

        if (! $result->isOk()) {
            throw ValidationException::withMessages([
                'otp' => ['The verification code is invalid or has expired.'],
            ]);
        }

        // Mark email as verified and delete the OTP
        $user->markEmailAsVerified();

        return to_route('dashboard')->with('message', 'Email verified successfully!');
    }

    /**
     * Resend the OTP.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return back()->withErrors(['email' => 'You must be logged in to resend verification code.']);
        }

        if ($user->hasVerifiedEmail()) {
            return to_route('dashboard');
        }

        $otp = $user->createOneTimePassword(20);

        // Send OTP via notification
        $user->notify(new EmailVerificationOtp($otp->password));

        return back()->with('status', 'verification-code-sent');
    }
}
