<?php

namespace Exolnet\Backup\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Backup\Tasks\Backup\DbDumperFactory;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database
                            {--connection= : Connection to use instead of the default one}
                            {--path= : Path where to dump the database instead of the current directory}
                            {filename? : Custom filename to use instead of the generated one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup (dump) the database.';

    /**
     * @var \Spatie\DbDumper\DbDumper
     */
    protected $dbDumper;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->makeDbDumper();

        $this->dumpToFile($this->getFilename());
    }

    /**
     * @return void
     */
    protected function makeDbDumper()
    {
        $connection = $this->option('connection') ?: config('database.default');

        $this->dbDumper = DbDumperFactory::createFromConnection($connection);
        $this->dbDumper->enableCompression();
    }

    /**
     * @return string
     */
    protected function getFilename()
    {
        $filename = $this->argument('filename');

        if (! $filename) {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "{$this->getDatabaseName()}-{$timestamp}.sql.gz";
        }

        if ($path = $this->option('path')) {
            $filename = $path . DIRECTORY_SEPARATOR . $filename;
        }

        return $filename;
    }

    /**
     * @return string
     */
    protected function getDatabaseName()
    {
        return $this->dbDumper->getDbName();
    }

    /**
     * @param $filename
     */
    protected function dumpToFile($filename)
    {
        $this->info("Dumping database {$this->getDatabaseName()} to {$filename}...");

        $this->dbDumper->dumpToFile($filename);
    }
}
