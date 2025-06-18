<?php

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Jobs\User\DeleteUserAccountJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteAccountController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        DeleteUserAccountJob::dispatch($request->user());

        return redirect('/');
    }
}
