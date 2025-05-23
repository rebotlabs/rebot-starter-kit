<?php

use App\Http\Controllers\Team\Settings\GeneralController;
use App\Http\Controllers\Team\Settings\MembersController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        return redirect()->route('team.overview', ['team' => $request->user()->currentTeam]);
    })->name('dashboard');

    Route::get('team/{team}', function (Team $team) {
        return Inertia::render('team/overview');
    })->name('team.overview');

    Route::get('team/{team}/settings', [GeneralController::class, 'show'])->name('team.settings');
    Route::patch('team/{team}/settings', [GeneralController::class, 'update'])->name('team.settings.update');

    Route::get('team/{team}/settings/members', [MembersController::class, 'show'])->name('team.settings.members');
    Route::post('team/{team}/settings/members/invite', [MembersController::class, 'invite'])->name('team.settings.members.invite');

    Route::get('team/{team}/settings/billing', function (Team $team) {
        return Inertia::render('team/settings/billing');
    })->name('team.settings.billing');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
