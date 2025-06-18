<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Jobs\Invitation\ResendInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class ResendInvitationController extends Controller
{
    public function __invoke(Organization $organization, Invitation $invitation): RedirectResponse
    {
        ResendInvitationJob::dispatch($invitation, auth()->user());

        return redirect()->route('organization.settings.members', $organization);
    }
}
