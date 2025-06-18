<?php

declare(strict_types=1);

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('Laravel')
    ->preset()
    ->laravel();

arch('controllers')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toExtend('App\Http\Controllers\Controller')
    ->toHaveSuffix('Controller')
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'Illuminate\Support\Facades\Route',
    ])
    ->ignoring('App\Http\Controllers\Controller');

arch('requests')
    ->expect('App\Http\Requests')
    ->classes()
    ->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->toHaveSuffix('Request')
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'App\Http\Requests',
    ]);

arch('models')
    ->expect('App\Models')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'App\Http\Requests',
        'App\Models',
        'App\Notifications',
        'App\Providers',
        'App\Jobs',
        'Database\Factories',
        'Database\Seeders',
    ]);

arch('notifications')
    ->expect('App\Notifications')
    ->classes()
    ->toExtend('Illuminate\Notifications\Notification')
    ->toHaveSuffix('Notification');

arch('middleware naming')
    ->expect(['App\Http\Middleware\EnsureCurrentOrganization', 'App\Http\Middleware\EnsureOrganizationAdmin'])
    ->toBeClasses()
    ->and(['App\Http\Middleware\HandleInertiaRequests', 'App\Http\Middleware\HandleAppearance'])
    ->toBeClasses();

arch('providers')
    ->expect('App\Providers')
    ->classes()
    ->toExtend('Illuminate\Support\ServiceProvider')
    ->toHaveSuffix('ServiceProvider');

arch('factories')
    ->expect('Database\Factories')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Factories\Factory')
    ->toHaveSuffix('Factory');

arch('seeders')
    ->expect('Database\Seeders')
    ->classes()
    ->toExtend('Illuminate\Database\Seeder')
    ->toHaveSuffix('Seeder');

// arch('concrete controllers should be invokable or have standard actions')
//     ->expect('App\Http\Controllers')
//     ->classes()
//     ->not->toBeAbstract()
//     ->toBeInvokable()
//     ->or()
//     ->toHaveMethod('create')
//     ->or()
//     ->toHaveMethod('store')
//     ->or()
//     ->toHaveMethod('show')
//     ->or()
//     ->toHaveMethod('edit')
//     ->or()
//     ->toHaveMethod('update')
//     ->or()
//     ->toHaveMethod('destroy')
//     ->or()
//     ->toHaveMethod('index');

arch('no debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('no globals')
    ->expect('App')
    ->not->toUse(['extract', 'compact', 'global']);

arch('secure')
    ->expect(['App\Http\Controllers', 'App\Http\Requests'])
    ->not->toUse(['eval', 'exec', 'shell_exec', 'system', 'passthru']);
