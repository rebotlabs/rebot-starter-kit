<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Password;

use App\Actions\User\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

class UpdatePasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request, UpdateUserPasswordAction $action): RedirectResponse
    {
        $action->execute($request->user(), $request->validated()['password']);

        return back();
    }
}
