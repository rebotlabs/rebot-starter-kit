<?php

use App\Models\Team;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('team/{team}', function (Team $team) {
        return Inertia::render('dashboard');
    })->name('team.overview');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
