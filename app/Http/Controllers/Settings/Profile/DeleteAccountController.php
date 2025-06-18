<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DeleteAccountRequest;
use App\Jobs\User\DeleteUserAccountJob;
use Illuminate\Http\RedirectResponse;

class DeleteAccountController extends Controller
{
    public function __invoke(DeleteAccountRequest $request): RedirectResponse
    {
        DeleteUserAccountJob::dispatch($request->user());

        return redirect('/');
    }
}
