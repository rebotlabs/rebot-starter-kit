<?php

use App\Http\Controllers\Settings\Appearance\ShowAppearanceController;
use App\Http\Controllers\Settings\Profile\DeleteAccountController;
use App\Http\Controllers\Settings\Profile\ShowProfileController;
use App\Http\Controllers\Settings\Profile\UpdateProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', ShowProfileController::class)->name('settings.profile');
    Route::patch('settings/profile', UpdateProfileController::class)->name('settings.profile.update');
    Route::delete('settings/profile', DeleteAccountController::class)->name('settings.profile.delete');

    Route::get('settings/security', [TwoFactorAuthenticationController::class, 'show'])->name('settings.security');
    Route::put('settings/security/password', [TwoFactorAuthenticationController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('settings/security/two-factor', [TwoFactorAuthenticationController::class, 'store'])->name('settings.security.two-factor.store');
    Route::post('settings/security/two-factor/confirm', [TwoFactorAuthenticationController::class, 'confirm'])->name('settings.security.two-factor.confirm');
    Route::delete('settings/security/two-factor', [TwoFactorAuthenticationController::class, 'destroy'])->name('settings.security.two-factor.destroy');
    Route::post('settings/security/recovery-codes', [TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])->name('settings.security.recovery-codes');

    Route::get('settings/appearance', ShowAppearanceController::class)->name('appearance');
});
