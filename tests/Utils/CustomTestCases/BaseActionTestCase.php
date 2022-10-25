<?php

namespace Tests\Utils\CustomTestCases;

use Tests\TestCase;

abstract class BaseActionTestCase extends TestCase
{
    abstract public function getSeederClass(): string;

    abstract public function action();

    abstract public function test_execution_with_valid_data_is_success(): void;

    abstract public function test_execution_with_invalid_data_is_failure(): void;
}
