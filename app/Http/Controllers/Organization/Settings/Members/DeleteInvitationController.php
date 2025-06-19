<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Jobs\Invitation\DeleteInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class DeleteInvitationController extends Controller
{
    public function __invoke(Organization $organization, Invitation $invitation): RedirectResponse
    {
        DeleteInvitationJob::dispatch($invitation);

        return redirect()->route('organization.settings.members', $organization)->with('message', __('messages.success.invitation_deleted'));
    }
}
