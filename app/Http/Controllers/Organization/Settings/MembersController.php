<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function show(Request $request, Organization $organization)
    {
        $members = $organization->members()->with(['user.roles'])->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'user' => $member->user,
                'role' => $member->user->roles->first()?->name ?? 'member',
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ];
        });

        return Inertia::render('organization/settings/members', [
            'invitations' => fn () => $organization->invitations()->with(['user'])->get(),
            'members' => $members,
        ]);
    }

    public function invite(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:admin,member'],
        ]);

        $invitation = $organization->invitations()->create([
            'email' => $data['email'],
            'role' => $data['role'],
            'user_id' => User::query()->where('email', $data['email'])->first()?->id,
            'accept_token' => Str::random(32),
            'reject_token' => Str::random(32),
        ]);

        $invitation->notify(new InvitationSent);

        return back()->with('message', 'Invitation sent successfully.');
    }

    public function resend(Request $request, Organization $organization, Invitation $invitation)
    {
        $invitation->notify(new InvitationSent);

        return back()->with('message', 'Invitation resent successfully.');
    }

    public function delete(Request $request, Organization $organization, Invitation $invitation)
    {
        $invitation->delete();

        return back()->with('message', 'Invitation deleted successfully.');
    }
}
