<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShowDashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->route('organization.overview', ['organization' => $request->user()->currentOrganization]);
    }
}
