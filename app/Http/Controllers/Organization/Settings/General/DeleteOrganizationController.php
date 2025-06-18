<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Jobs\Organization\DeleteOrganizationJob;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class DeleteOrganizationController extends Controller
{
    public function __invoke(Organization $organization): RedirectResponse
    {
        DeleteOrganizationJob::dispatch(
            $organization,
            auth()->user()
        );

        return redirect()->route('dashboard');
    }
}
