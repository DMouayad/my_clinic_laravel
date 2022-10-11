<?php

namespace App\Providers;

use App\Services\StaffMemberService;
use Illuminate\Support\ServiceProvider;

class StaffMemberServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(StaffMemberService::class, function ($app) {
            return new StaffMemberService();
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
