<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Actions\Member\LeaveMembershipAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\LeaveOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class LeaveOrganizationController extends Controller
{
    public function __invoke(LeaveOrganizationRequest $request, Organization $organization, LeaveMembershipAction $action): RedirectResponse
    {
        try {
            $action->execute(organization: $organization, user: $request->user());
        } catch (\Exception $e) {
            return back()->withErrors([
                'password' => $e->getMessage(),
            ]);
        }

        $userOrganizations = $request->user()->organizations()->get();

        if ($userOrganizations->isEmpty()) {
            return redirect()->route('onboarding.organization')
                ->with('message', __('messages.leave_organization.create_or_join'));
        }

        if ($userOrganizations->count() === 1) {
            $nextOrganization = $userOrganizations->first();
            $request->user()->currentOrganization()->associate($nextOrganization)->save();

            return redirect()->route('organization.overview', $nextOrganization)
                ->with('message', __('messages.leave_organization.success'));
        }

        return redirect()->route('organization.select')
            ->with('message', __('messages.leave_organization.select_another'));
    }
}
