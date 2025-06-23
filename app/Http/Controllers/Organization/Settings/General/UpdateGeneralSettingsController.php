<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Actions\Organization\UpdateOrganizationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class UpdateGeneralSettingsController extends Controller
{
    public function __invoke(OrganizationUpdateRequest $request, Organization $organization, UpdateOrganizationAction $action): RedirectResponse
    {
        $originalSlug = $organization->slug;
        $validatedData = $request->validated();

        $action->execute($organization, $validatedData);

        // Refresh the organization model to get the updated data
        $organization->refresh();

        // If the slug changed, redirect to the new URL
        if (isset($validatedData['slug']) && $validatedData['slug'] !== $originalSlug) {
            return redirect()->route('organization.settings', $organization);
        }

        return back();
    }
}
