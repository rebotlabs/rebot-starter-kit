<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Spatie\OneTimePasswords\Models\OneTimePassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(Organization::class);

        Schedule::command('model:prune', [
            '--model' => [OneTimePassword::class],
        ])->daily();
    }
}
