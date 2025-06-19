<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Appearance;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class ShowAppearanceController extends Controller
{
    public function __invoke(): Response
    {
        syncLangFiles(['ui', 'settings']);

        return Inertia::render('settings/appearance');
    }
}
