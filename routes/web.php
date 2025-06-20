<?php

use App\Http\Controllers\Onboarding\CreateOrganizationController;
use App\Http\Controllers\Onboarding\ShowCreateOrganizationController;
use App\Http\Controllers\Organization\Settings\Billing\ShowBillingController;
use App\Http\Controllers\Organization\Settings\General\ChangeOwnershipController;
use App\Http\Controllers\Organization\Settings\General\DeleteOrganizationController;
use App\Http\Controllers\Organization\Settings\General\ShowGeneralSettingsController;
use App\Http\Controllers\Organization\Settings\General\UpdateGeneralSettingsController;
use App\Http\Controllers\Organization\Settings\Members\DeleteInvitationController;
use App\Http\Controllers\Organization\Settings\Members\InviteMemberController;
use App\Http\Controllers\Organization\Settings\Members\LeaveOrganizationController;
use App\Http\Controllers\Organization\Settings\Members\RemoveMemberController;
use App\Http\Controllers\Organization\Settings\Members\ResendInvitationController;
use App\Http\Controllers\Organization\Settings\Members\ShowLeaveOrganizationController;
use App\Http\Controllers\Organization\Settings\Members\ShowMembersController;
use App\Http\Controllers\Organization\Settings\Members\UpdateMemberRoleController;
use App\Http\Controllers\Organization\ShowOrganizationOverviewController;
use App\Http\Controllers\Organization\ShowOrganizationSelectController;
use App\Http\Controllers\Organization\SwitchOrganizationController;
use App\Http\Controllers\ShowDashboardController;
use App\Http\Middleware\EnsureCurrentOrganization;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('welcome');
})->name('home');

Route::middleware(['auth', 'verified', EnsureCurrentOrganization::class])->group(function () {
    Route::get('dashboard', ShowDashboardController::class)->name('dashboard');

    Route::get('org/{organization}', ShowOrganizationOverviewController::class)->name('organization.overview');

    Route::middleware(['organization.admin'])->group(function () {
        Route::get('org/{organization}/settings', ShowGeneralSettingsController::class)->name('organization.settings');
        Route::patch('org/{organization}/settings', UpdateGeneralSettingsController::class)->name('organization.settings.update');
        Route::patch('org/{organization}/settings/ownership', ChangeOwnershipController::class)->name('organization.settings.ownership');
        Route::delete('org/{organization}', DeleteOrganizationController::class)->name('organization.delete');

        Route::get('org/{organization}/settings/members', ShowMembersController::class)->name('organization.settings.members');
        Route::post('org/{organization}/settings/members/invite', InviteMemberController::class)->name('organization.settings.members.invite');
        Route::patch('org/{organization}/settings/members/{member}/role', UpdateMemberRoleController::class)->name('organization.settings.members.update-role');
        Route::delete('org/{organization}/settings/members/{member}', RemoveMemberController::class)->name('organization.settings.members.remove');
        Route::post('org/{organization}/settings/members/invitations/{invitation}/resend', ResendInvitationController::class)->name('organization.settings.members.invitations.resend');
        Route::delete('org/{organization}/settings/members/invitations/{invitation}', DeleteInvitationController::class)->name('organization.settings.members.invitations.delete');

        Route::get('org/{organization}/settings/billing', ShowBillingController::class)->name('organization.settings.billing');
    });

    Route::get('org/{organization}/settings/leave', ShowLeaveOrganizationController::class)->name('organization.settings.leave');
    Route::post('org/{organization}/settings/member/leave', LeaveOrganizationController::class)->name('organization.settings.member.leave');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('organization/select', ShowOrganizationSelectController::class)->name('organization.select');

    Route::post('organization/{organization}/switch', SwitchOrganizationController::class)->name('organization.switch');

    Route::get('onboarding/organization', ShowCreateOrganizationController::class)->name('onboarding.organization');
    Route::post('onboarding/organization', CreateOrganizationController::class)->name('onboarding.organization.store');
});

Route::middleware(['signed'])->group(function () {
    Route::get('invitation/{token}', [App\Http\Controllers\Invitation\ShowInvitationController::class, '__invoke'])->name('invitation.handle');
});

// Notifications API
Route::middleware(['auth'])->prefix('api/notifications')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::patch('{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::patch('read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
    Route::delete('{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
});

Route::post('invitation/{token}/accept', [App\Http\Controllers\Invitation\AcceptInvitationController::class, '__invoke'])->name('invitation.accept');
Route::post('invitation/{token}/reject', [App\Http\Controllers\Invitation\RejectInvitationController::class, '__invoke'])->name('invitation.reject');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
