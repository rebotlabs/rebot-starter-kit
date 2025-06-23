<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Profile;

use App\Actions\User\DeleteUserAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DeleteAccountRequest;
use Illuminate\Http\RedirectResponse;

class DeleteAccountController extends Controller
{
    public function __invoke(DeleteAccountRequest $request, DeleteUserAccountAction $action): RedirectResponse
    {
        $action->execute(user: $request->user());

        return redirect('/');
    }
}
