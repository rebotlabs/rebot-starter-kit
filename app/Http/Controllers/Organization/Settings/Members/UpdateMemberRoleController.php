<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateMemberRoleController extends Controller
{
    public function __invoke(Request $request, Organization $organization, Member $member): RedirectResponse
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'member'])],
        ]);

        $user = $member->user;
        $role = $request->string('role')->value();

        // Remove all existing roles and assign the new role
        $user->syncRoles([$role]);

        return back();
    }
}
