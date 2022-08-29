<?php

namespace App\Providers;

use App\Services\StaffEmailService;
use Illuminate\Support\ServiceProvider;

class StaffEmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(StaffEmailService::class, function ($app) {
            return new StaffEmailService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
