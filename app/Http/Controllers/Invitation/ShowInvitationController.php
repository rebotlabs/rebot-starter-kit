<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invitation;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ShowInvitationController extends Controller
{
    public function __invoke(Request $request, string $token): Response
    {
        $invitation = Invitation::where('accept_token', $token)
            ->where('status', 'pending')
            ->with('organization')
            ->firstOrFail();

        $existingUser = User::where('email', $invitation->email)->first();

        return Inertia::render('invitation/handle', [
            'invitation' => $invitation,
            'existingUser' => $existingUser ? true : false,
            'isAuthenticated' => Auth::check(),
            'currentUserEmail' => Auth::user()?->email,
        ]);
    }
}
