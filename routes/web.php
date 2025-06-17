<?php

use App\Http\Controllers\Organization\Settings\GeneralController;
use App\Http\Controllers\Organization\Settings\MembersController;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        return redirect()->route('organization.overview', ['organization' => $request->user()->currentOrganization]);
    })->name('dashboard');

    Route::get('org/{organization}', function (Organization $organization) {
        return Inertia::render('organization/overview');
    })->name('organization.overview');

    Route::get('org/{organization}/settings', [GeneralController::class, 'show'])->name('organization.settings');
    Route::patch('org/{organization}/settings', [GeneralController::class, 'update'])->name('organization.settings.update');
    Route::patch('org/{organization}/settings/ownership', [GeneralController::class, 'changeOwnership'])->name('organization.settings.ownership');

    Route::get('org/{organization}/settings/members', [MembersController::class, 'show'])->name('organization.settings.members');
    Route::post('org/{organization}/settings/members/invite', [MembersController::class, 'invite'])->name('organization.settings.members.invite');
    Route::post('org/{organization}/settings/members/invitations/{invitation}/resend', [MembersController::class, 'resend'])->name('organization.settings.members.invitations.resend');
    Route::delete('org/{organization}/settings/members/invitations/{invitation}', [MembersController::class, 'delete'])->name('organization.settings.members.invitations.delete');

    Route::get('org/{organization}/settings/billing', function (Organization $organization) {
        return Inertia::render('organization/settings/billing');
    })->name('organization.settings.billing');

});

Route::middleware(['signed'])->group(function () {
    Route::get('invitation/accept', function () {
        return '';
    })->name('invitation.accept');

    Route::get('invitation/reject', function () {
        return '';
    })->name('invitation.reject');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
