<?php

namespace Support\Factories;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseFactory
{
    private Generator $faker;

    abstract static function new();

    public function times(int $times): Collection
    {
        return collect()
            ->times($times)
            ->map(fn() => $this->create());
    }

    abstract public function create();

    public function createWithNullAttributes()
    {
    }

    public function faker(): Generator
    {
        $this->faker ??= Factory::create();
        return $this->faker;
    }
}
