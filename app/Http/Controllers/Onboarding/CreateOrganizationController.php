<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;

class CreateOrganizationController extends Controller
{
    public function __invoke(CreateOrganizationRequest $request): RedirectResponse
    {
        $organization = $request->user()->organizations()->create(array_merge(
            $request->validated(),
            [
                'owner_id' => $request->user()->id,
            ]
        ));

        // Create a member record for the owner with admin role
        $organization->members()->create([
            'user_id' => $request->user()->id,
        ]);

        // Assign admin role to the owner
        $request->user()->assignRole('admin');

        $request->user()->currentOrganization()->associate($organization)->save();

        return redirect()->route('dashboard');
    }
}
