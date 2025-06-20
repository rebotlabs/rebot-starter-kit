<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrganizationLogoRequest;
use App\Models\Organization;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoController extends Controller
{
    public function __construct(
        private readonly AvatarService $avatarService
    ) {}

    public function store(UpdateOrganizationLogoRequest $request, Organization $organization): RedirectResponse
    {
        // Delete old logo if exists
        if ($organization->logo) {
            $this->avatarService->delete($organization->logo);
        }

        // Store new logo
        $path = $this->avatarService->store($request->file('logo'), 'logos/organizations');

        // Update organization
        $organization->update(['logo' => $path]);

        return redirect()->back()->with('success', __('ui.logo.upload_success'));
    }

    public function destroy(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('manage', $organization);

        if ($organization->logo) {
            $this->avatarService->delete($organization->logo);
            $organization->update(['logo' => null]);
        }

        return redirect()->back()->with('success', __('ui.logo.delete_success'));
    }
}
