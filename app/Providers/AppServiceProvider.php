<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Banking\BankIntegrationManager;
use App\Tenancy\TenantContext;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped( TenantContext::class, fn () => new TenantContext());
        $this->app->singleton(BankIntegrationManager::class, function () {
            return new BankIntegrationManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
