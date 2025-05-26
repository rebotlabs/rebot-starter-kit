<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team\Settings;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function show(Request $request, Team $team)
    {
        return Inertia::render('team/settings/members', [
            'invitations' => fn() => $team->invitations()->with(['user'])->get(),
        ]);
    }

    public function invite(Request $request, Team $team)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:admin,member'],
        ]);

        $team->invitations()->create([
            'email' => $data['email'],
            'role' => $data['role'],
            'user_id' => User::query()->where('email', $data['email'])->first()?->id,
            'accept_token' => Str::random(32),
            'reject_token' => Str::random(32),
        ]);
    }
}
