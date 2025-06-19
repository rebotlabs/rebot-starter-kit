<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowCreateOrganizationController extends Controller
{
    public function __invoke(Request $request): Response
    {
        syncLangFiles(['ui', 'organizations']);

        return Inertia::render('onboarding/create-organization');
    }
}
