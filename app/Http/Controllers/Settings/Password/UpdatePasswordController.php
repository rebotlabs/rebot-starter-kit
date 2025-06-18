<?php

namespace App\Http\Controllers\Settings\Password;

use App\Http\Controllers\Controller;
use App\Jobs\User\UpdateUserPasswordJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        UpdateUserPasswordJob::dispatch($request->user(), $validated['password']);

        return back();
    }
}
