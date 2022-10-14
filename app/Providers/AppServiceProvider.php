<?php

namespace App\Providers;

use App\Models\ModifiedPersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Use the customized personal access token model
        Sanctum::usePersonalAccessTokenModel(
            ModifiedPersonalAccessToken::class
        );
    }
}
