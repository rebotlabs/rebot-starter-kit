<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team\Settings;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function show(Request $request, Team $team)
    {
        return Inertia::render('team/settings/members');
    }

    public function invite(Request $request, Team $team)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:admin,member'],
        ]);
    }
}
