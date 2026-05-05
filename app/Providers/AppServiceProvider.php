<?php

namespace App\Providers;

use App\Services\Courier\CourierServiceInterface;
use App\Services\Courier\DefaultCourierService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the courier service interface to the default implementation.
        // Swap this binding to use a real courier provider (DHL, PostaKosovës, etc.)
        $this->app->bind(CourierServiceInterface::class, DefaultCourierService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
