<?php

namespace Exolnet\Backup\Tests\Integration;

use Exolnet\Backup\BackupServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            BackupServiceProvider::class,
        ];
    }
}
