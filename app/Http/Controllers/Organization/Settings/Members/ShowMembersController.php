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
        $members = $organization->members()->with(['user.roles'])->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'user' => $member->user,
                'role' => $member->user->roles->first()?->name ?? 'member',
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ];
        });

        return Inertia::render('organization/settings/members', [
            'invitations' => fn () => $organization->invitations()->with(['user'])->get(),
            'members' => $members,
        ]);
    }
}
