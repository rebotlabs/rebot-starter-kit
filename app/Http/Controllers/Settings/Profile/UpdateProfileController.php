<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Profile;

use App\Actions\User\UpdateUserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;

class UpdateProfileController extends Controller
{
    public function __invoke(ProfileUpdateRequest $request, UpdateUserProfileAction $action): RedirectResponse
    {
        $action->execute(user: $request->user(), data: $request->validated());

        return to_route('settings.profile');
    }
}
