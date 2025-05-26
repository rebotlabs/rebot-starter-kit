<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team\Settings;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Notifications\InvitationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function show(Request $request, Team $team)
    {
        return Inertia::render('team/settings/members', [
            'invitations' => fn() => $team->invitations()->with(['user'])->get(),
            'members' => fn() => $team->members()->with(['user'])->get(),
        ]);
    }

    public function invite(Request $request, Team $team)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:admin,member'],
        ]);

        $invitation = $team->invitations()->create([
            'email' => $data['email'],
            'role' => $data['role'],
            'user_id' => User::query()->where('email', $data['email'])->first()?->id,
            'accept_token' => Str::random(32),
            'reject_token' => Str::random(32),
        ]);

        $invitation->notify(new InvitationSent());
    }

    public function resend(Request $request, Team $team, Invitation $invitation)
    {
        $invitation->notify(new InvitationSent());
    }

    public function delete(Request $request, Team $team, Invitation $invitation)
    {
        $invitation->delete();
    }
}
