<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Jobs\Organization\LeaveOrganizationJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class LeaveOrganizationController extends Controller
{
    public function __invoke(Organization $organization): RedirectResponse
    {
        LeaveOrganizationJob::dispatch(
            auth()->user(),
            $organization
        );

        return redirect()->route('dashboard');
    }
}
