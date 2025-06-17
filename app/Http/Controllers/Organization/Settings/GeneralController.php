<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneralController extends Controller
{
    public function show(Request $request, Organization $organization)
    {
        return Inertia::render('organization/settings/general', [
            'organization' => $organization,
            'members' => fn () => $organization->members()->with(['user'])->get(),
        ]);
    }

    public function changeOwnership(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'current_password'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);

        $organization->update(['owner_id' => $data['owner_id']]);

        return redirect()->route('organization.settings', ['organization' => $organization]);
    }

    public function update(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:organizations,slug,'.$organization->id],
        ]);

        $organization->update($data);

        return redirect()->route('organization.settings', ['organization' => $organization]);
    }
}
