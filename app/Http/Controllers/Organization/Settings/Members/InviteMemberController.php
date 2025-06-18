<?php

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationRequest;
use App\Jobs\Invitation\CreateInvitationJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class InviteMemberController extends Controller
{
    public function __invoke(InvitationRequest $request, Organization $organization): RedirectResponse
    {
        CreateInvitationJob::dispatch(
            $organization,
            $request->validated(),
            auth()->user()
        );

        return back();
    }
}
