<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowMembersController extends Controller
{
    public function __invoke(Request $request, Organization $organization): Response
    {

        // Get all members
        $members = $organization->members()->with(['user.roles'])->get()->map(function ($member) use ($organization) {
            return [
                'id' => $member->id,
                'user' => $member->user,
                'role' => $member->user->id === $organization->owner_id ? 'owner' : ($member->user->roles->first()?->name ?? 'member'),
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ];
        });

        // Always ensure the owner is included in the members list
        $ownerInMembers = $members->contains(function ($member) use ($organization) {
            return $member['user']->id === $organization->owner_id;
        });

        if (! $ownerInMembers) {
            $owner = $organization->owner;
            $members->prepend([
                'id' => 0, // Fake ID for owner-only record
                'user' => $owner,
                'role' => 'owner',
                'created_at' => $organization->created_at,
                'updated_at' => $organization->updated_at,
            ]);
        }

        return Inertia::render('organization/settings/members', [
            'invitations' => fn () => $organization->invitations()->with(['user'])->get(),
            'members' => $members,
        ]);
    }
}
