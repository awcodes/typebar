<?php

declare(strict_types=1);

namespace Awcodes\Typebar\Tests;

use Awcodes\Typebar\TypebarServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TypebarServiceProvider::class,
        ];
    }
}
