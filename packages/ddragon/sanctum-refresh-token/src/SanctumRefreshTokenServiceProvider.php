<?php

namespace DDragon\SanctumRefreshToken;

use Illuminate\Support\ServiceProvider;

class SanctumRefreshTokenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . "/../config/sanctumRefreshToken.php",
                "sanctum-refresh-token"
            );
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . "/../database/migrations" => database_path(
                    "migrations"
                ),
            ],
            "sanctum-refresh-token-migrations"
        );

        $this->publishes(
            [
                __DIR__ . "/../config/sanctumRefreshToken.php" => config_path(
                    "sanctumRefreshToken.php"
                ),
            ],
            "sanctum-refresh-token-config"
        );

        $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
    }
}
