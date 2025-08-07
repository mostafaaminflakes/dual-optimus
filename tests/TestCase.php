<?php

namespace MostafaAminFlakes\DualOptimus\Tests;

use MostafaAminFlakes\DualOptimus\DualOptimusServiceProvider;
use MostafaAminFlakes\DualOptimus\Facades\DualOptimus;
use MostafaAminFlakes\DualOptimus\Tests\Helpers\KeyGenerator;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            DualOptimusServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'DualOptimus' => DualOptimus::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('dual-optimus.default', 'main');
        $app['config']->set('dual-optimus.connections.main', KeyGenerator::generate(64));
        $app['config']->set('dual-optimus.connections.legacy', KeyGenerator::generate(32));
    }
}
