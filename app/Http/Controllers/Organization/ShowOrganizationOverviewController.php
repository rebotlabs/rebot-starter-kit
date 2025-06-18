<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;
use Inertia\Response;

class ShowOrganizationOverviewController extends Controller
{
    public function __invoke(Organization $organization): Response
    {
        return Inertia::render('organization/overview');
    }
}
