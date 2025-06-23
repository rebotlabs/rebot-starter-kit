<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Actions\Organization\LeaveOrganizationAction;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class LeaveOrganizationController extends Controller
{
    public function __invoke(Organization $organization, LeaveOrganizationAction $action): RedirectResponse
    {
        $action->execute(user: auth()->user(), organization: $organization);

        return redirect()->route('dashboard');
    }
}
