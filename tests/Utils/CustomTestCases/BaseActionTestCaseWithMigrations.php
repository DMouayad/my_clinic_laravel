<?php

namespace Tests\Utils\CustomTestCases;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class BaseActionTestCaseWithMigrations extends BaseActionTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    protected function seeder()
    {
        return $this->getSeederClass() ?? DatabaseSeeder::class;
    }
}
