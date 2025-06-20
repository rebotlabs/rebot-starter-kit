<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Services\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function __construct(
        private readonly AvatarService $avatarService
    ) {}

    public function store(UpdateUserAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            $this->avatarService->delete($user->avatar);
        }

        // Store new avatar
        $path = $this->avatarService->store($request->file('avatar'), 'avatars/users');

        // Update user
        $user->update(['avatar' => $path]);

        return redirect()->back()->with('success', __('ui.avatar.upload_success'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            $this->avatarService->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->back()->with('success', __('ui.avatar.delete_success'));
    }
}
