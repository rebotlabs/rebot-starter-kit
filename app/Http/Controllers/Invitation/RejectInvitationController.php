<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use App\Jobs\Invitation\RejectInvitationJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RejectInvitationController extends Controller
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        RejectInvitationJob::dispatchSync($token);

        return redirect()->route('home')
            ->with('message', __('messages.success.invitation_rejected'));
    }
}
