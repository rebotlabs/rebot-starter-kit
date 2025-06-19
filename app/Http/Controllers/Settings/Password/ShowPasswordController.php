<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Password;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ShowPasswordController extends Controller
{
    public function __invoke(): Response
    {
        syncLangFiles(['ui', 'settings']);

        return Inertia::render('settings/password');
    }
}
