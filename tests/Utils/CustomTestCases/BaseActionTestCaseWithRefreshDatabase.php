<?php

namespace Tests\Utils\CustomTestCases;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseActionTestCaseWithRefreshDatabase extends BaseActionTestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function seeder()
    {
        return $this->getSeederClass() ?? DatabaseSeeder::class;
    }
}
