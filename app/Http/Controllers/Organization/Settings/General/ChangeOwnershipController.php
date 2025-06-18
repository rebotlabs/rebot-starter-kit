<?php

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Jobs\Organization\ChangeOrganizationOwnershipJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChangeOwnershipController extends Controller
{
    public function __invoke(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        ChangeOrganizationOwnershipJob::dispatch(
            $organization,
            $request->input('member_id')
        );

        return back();
    }
}
