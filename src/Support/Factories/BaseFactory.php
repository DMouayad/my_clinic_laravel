<?php

namespace Support\Factories;

use Illuminate\Database\Eloquent\Collection;

abstract class BaseFactory
{
    public function times(int $times): Collection
    {
        return collect()
            ->times($times)
            ->map(fn() => $this->create());
    }

    abstract public function create();
}
