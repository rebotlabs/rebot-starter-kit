<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invitation;

use App\Actions\Invitation\RejectInvitationAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RejectInvitationController extends Controller
{
    public function __invoke(Request $request, string $token, RejectInvitationAction $action): RedirectResponse
    {
        $action->execute($token);

        return redirect()->route('home')
            ->with('message', __('messages.success.invitation_rejected'));
    }
}
