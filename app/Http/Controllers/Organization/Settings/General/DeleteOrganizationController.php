<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Actions\Organization\DeleteOrganizationAction;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class DeleteOrganizationController extends Controller
{
    public function __invoke(Organization $organization, DeleteOrganizationAction $action): RedirectResponse
    {
        $result = $action->execute($organization, auth()->user());

        // Reload the user to get the updated current_organization_id
        $user = auth()->user()->fresh();
        $user->load('currentOrganization');

        // If the user doesn't need to switch organizations (they weren't using the deleted one as current)
        // redirect back to their current organization
        if (! $result['needsOrganizationSwitch'] && $user->currentOrganization) {
            return redirect()->route('organization.overview', $user->currentOrganization->slug)
                ->with('success', __('ui.organization.delete_success'));
        }

        // If no organizations remain, redirect to onboarding
        if ($result['organizationsCount'] === 0) {
            return redirect()->route('onboarding.organization')
                ->with('success', __('ui.organization.delete_success_no_orgs'));
        }

        // If exactly one organization remains, redirect to it
        if ($result['organizationsCount'] === 1 && $result['nextOrganization']) {
            return redirect()->route('organization.overview', $result['nextOrganization']->slug)
                ->with('success', __('ui.organization.delete_success_switched'));
        }

        // If multiple organizations remain, redirect to organization selection
        return redirect()->route('organization.select')
            ->with('success', __('ui.organization.delete_success_select'));
    }
}
