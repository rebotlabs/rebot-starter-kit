<?php

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Jobs\Organization\UpdateOrganizationJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class UpdateGeneralSettingsController extends Controller
{
    public function __invoke(OrganizationUpdateRequest $request, Organization $organization): RedirectResponse
    {
        UpdateOrganizationJob::dispatch(
            $organization,
            $request->validated()
        );

        return back();
    }
}
