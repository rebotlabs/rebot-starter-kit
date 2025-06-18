<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization\Settings\General;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;
use Inertia\Response;

class ShowGeneralSettingsController extends Controller
{
    public function __invoke(Organization $organization): Response
    {
        $members = $organization->members()->with(['user'])->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'user' => $member->user,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
            ];
        });

        return Inertia::render('organization/settings/general', [
            'organization' => $organization,
            'members' => $members,
        ]);
    }
}
