<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitation\AcceptInvitationRequest;
use App\Jobs\Invitation\AcceptInvitationJob;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcceptInvitationController extends Controller
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->with('organization')
            ->firstOrFail();

        $user = User::where('email', $invitation->email)->first();
        $userData = null;

        if (! $user) {
            $acceptRequest = app(AcceptInvitationRequest::class);
            $userData = $acceptRequest->validated();
        } else {
            if (! Auth::check() || Auth::user()->email !== $invitation->email) {
                return back()->withErrors([
                    'auth' => 'Please log in with the invited email address to accept this invitation.',
                ]);
            }
        }

        $result = AcceptInvitationJob::dispatchSync($token, $userData);

        return redirect()->route('organization.overview', $result['organization'])
            ->with('message', 'Invitation accepted successfully!');
    }
}
