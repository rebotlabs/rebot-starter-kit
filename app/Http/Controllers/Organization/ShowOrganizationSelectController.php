<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowOrganizationSelectController extends Controller
{
    public function __invoke(Request $request): Response
    {
        syncLangFiles(['ui', 'organizations']);

        $organizations = $request->user()->organizations()->get();

        return Inertia::render('organization/select', [
            'organizations' => $organizations,
        ]);
    }
}
