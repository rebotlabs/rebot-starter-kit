<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteUserAccountAction
{
    public function execute(User $user): void
    {
        Auth::logout();

        // Delete all organizations owned by this user
        $user->ownedOrganizations()->delete();

        // The user's memberships will be deleted by cascade
        // The user's current_organization_id will be set to null by cascade

        $user->delete();
    }
}
