<?php

namespace App\Http\Controllers\Organization\Settings\Billing;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;
use Inertia\Response;

class ShowBillingController extends Controller
{
    public function __invoke(Organization $organization): Response
    {
        return Inertia::render('organization/settings/billing');
    }
}
