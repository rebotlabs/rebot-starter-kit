<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ResendEmailVerificationOtpController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return back()->withErrors(['email' => __('messages.error.login_required')]);
        }

        if ($user->hasVerifiedEmail()) {
            return to_route('dashboard');
        }

        $otp = $user->createOneTimePassword(20);

        $user->notify(new EmailVerificationOtpNotification($otp->password));

        return back()->with('status', __('messages.status.verification_code_sent'));
    }
}
