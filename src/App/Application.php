<?php

namespace App;
class Application extends \Illuminate\Foundation\Application
{
    protected $namespace = "App\\";

    public function path($path = "")
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "src\\App";
    }
}
