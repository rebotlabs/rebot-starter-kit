<?php

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

    Route::get('team/{team}/settings', function (Team $team) {
        return Inertia::render('team/settings/general');
    })->name('team.settings');

    Route::get('team/{team}/settings/members', function (Team $team) {
        return Inertia::render('team/settings/members');
    })->name('team.settings.members');

    Route::get('team/{team}/settings/billing', function (Team $team) {
        return Inertia::render('team/settings/billing');
    })->name('team.settings.billing');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
