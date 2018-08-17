<?php

namespace Exolnet\Backup;

use Exolnet\Backup\Commands\BackupDatabaseCommand;
use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('command.backup:database', BackupDatabaseCommand::class);

        $this->commands([
            'command.backup:database',
        ]);
    }
}
