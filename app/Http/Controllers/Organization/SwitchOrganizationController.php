<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\SwitchOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class SwitchOrganizationController extends Controller
{
    public function __invoke(SwitchOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $request->user()->currentOrganization()->associate($organization)->save();

        return redirect()->route('organization.overview', $organization);
    }
}
