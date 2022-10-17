<?php

namespace Tests;

use App\Http\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . "/../bootstrap/app.php";
        //        $app = (new Application(
        //            $_ENV["APP_BASE_PATH"] ?? dirname(__DIR__)
        //        ))->useAppPath("src/App");

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
