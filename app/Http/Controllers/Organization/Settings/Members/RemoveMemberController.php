<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class RemoveMemberController extends Controller
{
    public function __invoke(Organization $organization, Member $member): RedirectResponse
    {
        $user = $member->user;

        // Prevent removing the organization owner
        if ($user->id === $organization->owner_id) {
            return back()->withErrors(['error' => 'Cannot remove the organization owner.']);
        }

        // Remove the member
        $member->delete();

        // If this was the user's current organization, we need to handle organization switching
        if ($user->current_organization_id === $organization->id) {
            // Get the user's remaining organizations
            $remainingOrganizations = $user->organizations()->get();

            if ($remainingOrganizations->count() > 0) {
                // Set the first remaining organization as current
                $user->update(['current_organization_id' => $remainingOrganizations->first()->id]);
            } else {
                // No remaining organizations, clear current organization
                $user->update(['current_organization_id' => null]);
            }
        }

        return back()->with('success', __('messages.success.member_removed'));
    }
}
