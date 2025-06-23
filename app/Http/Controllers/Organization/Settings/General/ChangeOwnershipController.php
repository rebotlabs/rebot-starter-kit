<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Actions\Organization\ChangeOrganizationOwnershipAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\ChangeOwnershipRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class ChangeOwnershipController extends Controller
{
    public function __invoke(ChangeOwnershipRequest $request, Organization $organization, ChangeOrganizationOwnershipAction $action): RedirectResponse
    {
        $action->execute(organization: $organization, memberId: $request->input('member_id'));

        return back();
    }
}
