<?php

declare(strict_types=1);

namespace App\Jobs\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class DeleteUserAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private User $user) {}

    public function handle(): void
    {
        Auth::logout();

        // Delete all organizations owned by this user
        $this->user->ownedOrganizations()->delete();

        // The user's memberships will be deleted by cascade
        // The user's current_organization_id will be set to null by cascade

        $this->user->delete();
    }
}
