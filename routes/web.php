<?php

use App\Http\Controllers\Organization\Settings\General\ChangeOwnershipController;
use App\Http\Controllers\Organization\Settings\General\DeleteOrganizationController;
use App\Http\Controllers\Organization\Settings\General\ShowGeneralSettingsController;
use App\Http\Controllers\Organization\Settings\General\UpdateGeneralSettingsController;
use App\Http\Controllers\Organization\Settings\Members\DeleteInvitationController;
use App\Http\Controllers\Organization\Settings\Members\InviteMemberController;
use App\Http\Controllers\Organization\Settings\Members\LeaveOrganizationController;
use App\Http\Controllers\Organization\Settings\Members\ResendInvitationController;
use App\Http\Controllers\Organization\Settings\Members\ShowLeaveOrganizationController;
use App\Http\Controllers\Organization\Settings\Members\ShowMembersController;
use App\Http\Middleware\EnsureCurrentOrganization;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified', EnsureCurrentOrganization::class])->group(function () {
    Route::get('dashboard', function (Request $request) {
        return redirect()->route('organization.overview', ['organization' => $request->user()->currentOrganization]);
    })->name('dashboard');

    Route::get('org/{organization}', function (Organization $organization) {
        return Inertia::render('organization/overview');
    })->name('organization.overview');

    // Organization settings routes - admin/owner only
    Route::middleware(['organization.admin'])->group(function () {
        Route::get('org/{organization}/settings', ShowGeneralSettingsController::class)->name('organization.settings');
        Route::patch('org/{organization}/settings', UpdateGeneralSettingsController::class)->name('organization.settings.update');
        Route::patch('org/{organization}/settings/ownership', ChangeOwnershipController::class)->name('organization.settings.ownership');
        Route::delete('org/{organization}', DeleteOrganizationController::class)->name('organization.delete');

        Route::get('org/{organization}/settings/members', ShowMembersController::class)->name('organization.settings.members');
        Route::post('org/{organization}/settings/members/invite', InviteMemberController::class)->name('organization.settings.members.invite');
        Route::post('org/{organization}/settings/members/invitations/{invitation}/resend', ResendInvitationController::class)->name('organization.settings.members.invitations.resend');
        Route::delete('org/{organization}/settings/members/invitations/{invitation}', DeleteInvitationController::class)->name('organization.settings.members.invitations.delete');

        Route::get('org/{organization}/settings/billing', function (Organization $organization) {
            return Inertia::render('organization/settings/billing');
        })->name('organization.settings.billing');
    });

    // Member settings routes - for regular members
    Route::get('org/{organization}/settings/leave', ShowLeaveOrganizationController::class)->name('organization.settings.leave');
    Route::post('org/{organization}/settings/member/leave', LeaveOrganizationController::class)->name('organization.settings.member.leave');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('organization/select', function (Request $request) {
        $organizations = $request->user()->organizations()->get();

        return Inertia::render('organization/select', [
            'organizations' => $organizations,
        ]);
    })->name('organization.select');

    Route::post('organization/{organization}/switch', function (Request $request, Organization $organization) {
        // Ensure user is a member of this organization
        $member = $organization->members()->where('user_id', $request->user()->id)->first();
        if (! $member && $organization->owner_id !== $request->user()->id) {
            abort(403, 'You are not a member of this organization.');
        }

        $request->user()->currentOrganization()->associate($organization)->save();

        return redirect()->route('organization.overview', $organization);
    })->name('organization.switch');

    Route::get('onboarding/organization', function (Request $request) {
        return Inertia::render('onboarding/create-organization');
    })->name('onboarding.organization');
    Route::post('onboarding/organization', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
        ]);

        $organization = $request->user()->organizations()->create(array_merge(
            $data,
            [
                'owner_id' => $request->user()->id,
            ]
        ));
        $request->user()->currentOrganization()->associate($organization)->save();

        return redirect()->route('dashboard');
    })->name('onboarding.organization.store');
});

Route::middleware(['signed'])->group(function () {
    Route::get('invitation/{token}', [App\Http\Controllers\Invitation\ShowInvitationController::class, '__invoke'])->name('invitation.handle');
});

Route::post('invitation/{token}/accept', [App\Http\Controllers\Invitation\AcceptInvitationController::class, '__invoke'])->name('invitation.accept');
Route::post('invitation/{token}/reject', [App\Http\Controllers\Invitation\RejectInvitationController::class, '__invoke'])->name('invitation.reject');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
