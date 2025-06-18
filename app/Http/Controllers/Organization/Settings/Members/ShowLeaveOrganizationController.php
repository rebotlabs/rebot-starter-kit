<?php

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class ShowLeaveOrganizationController extends Controller
{
    public function __invoke(Request $request, Organization $organization)
    {
        if ($organization->owner_id === $request->user()->id) {
            return redirect()->route('organization.settings', $organization);
        }

        $member = $organization->members()->where('user_id', $request->user()->id)->first();
        if (! $member) {
            abort(403, 'You are not a member of this organization.');
        }

        $userRole = $request->user()->roles->first()?->name ?? 'member';

        return inertia('organization/settings/leave', [
            'organization' => $organization,
            'member' => [
                'id' => $member->id,
                'user' => $member->user,
                'role' => $userRole,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ],
        ]);
    }
}
