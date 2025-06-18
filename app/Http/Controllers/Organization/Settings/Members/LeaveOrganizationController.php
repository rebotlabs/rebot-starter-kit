<?php

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Jobs\Member\LeaveMembershipJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeaveOrganizationController extends Controller
{
    public function __invoke(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        try {
            LeaveMembershipJob::dispatch($organization, $request->user());
        } catch (\Exception $e) {
            return back()->withErrors([
                'password' => $e->getMessage(),
            ]);
        }

        $userOrganizations = $request->user()->organizations()->get();

        if ($userOrganizations->isEmpty()) {
            return redirect()->route('onboarding.organization')
                ->with('message', 'You have left the organization. Create or join a new organization to continue.');
        }

        if ($userOrganizations->count() === 1) {
            $nextOrganization = $userOrganizations->first();
            $request->user()->currentOrganization()->associate($nextOrganization)->save();

            return redirect()->route('organization.overview', $nextOrganization)
                ->with('message', 'You have successfully left the organization.');
        }

        return redirect()->route('organization.select')
            ->with('message', 'You have successfully left the organization. Please select another organization to continue.');
    }
}
