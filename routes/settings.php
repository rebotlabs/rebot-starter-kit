<?php

use App\Http\Controllers\Settings\Password\ShowPasswordController;
use App\Http\Controllers\Settings\Password\UpdatePasswordController;
use App\Http\Controllers\Settings\Profile\DeleteAccountController;
use App\Http\Controllers\Settings\Profile\ShowProfileController;
use App\Http\Controllers\Settings\Profile\UpdateProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', ShowProfileController::class)->name('settings.profile');
    Route::patch('settings/profile', UpdateProfileController::class)->name('settings.profile.update');
    Route::delete('settings/profile', DeleteAccountController::class)->name('settings.profile.delete');

    Route::get('settings/password', ShowPasswordController::class)->name('settings.password');
    Route::put('settings/password', UpdatePasswordController::class)->name('settings.password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');
});
