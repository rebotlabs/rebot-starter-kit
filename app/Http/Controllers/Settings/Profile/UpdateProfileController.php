<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Jobs\User\UpdateUserProfileJob;
use Illuminate\Http\RedirectResponse;

class UpdateProfileController extends Controller
{
    public function __invoke(ProfileUpdateRequest $request): RedirectResponse
    {
        UpdateUserProfileJob::dispatch($request->user(), $request->validated());

        return to_route('settings.profile');
    }
}
