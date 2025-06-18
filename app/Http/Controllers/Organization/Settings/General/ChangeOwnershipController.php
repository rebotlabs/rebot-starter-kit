<?php

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\ChangeOwnershipRequest;
use App\Jobs\Organization\ChangeOrganizationOwnershipJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class ChangeOwnershipController extends Controller
{
    public function __invoke(ChangeOwnershipRequest $request, Organization $organization): RedirectResponse
    {
        ChangeOrganizationOwnershipJob::dispatch(
            $organization,
            $request->input('member_id')
        );

        return back();
    }
}
