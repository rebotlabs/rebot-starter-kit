<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team\Settings;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneralController extends Controller
{
    public function show(Request $request, Team $team)
    {
        return Inertia::render("team/settings/general", [
            'team' => $team,
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:teams,slug,' . $team->id],
        ]);

        $team->update($data);

        return redirect()->route('team.settings', ['team' => $team]);
    }
}
