<?php

namespace App\Http\Controllers\Settings\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Jobs\User\UpdateUserPasswordJob;
use Illuminate\Http\RedirectResponse;

class UpdatePasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request): RedirectResponse
    {
        UpdateUserPasswordJob::dispatch($request->user(), $request->validated()['password']);

        return back();
    }
}
