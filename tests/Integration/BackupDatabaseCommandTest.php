<?php

namespace Exolnet\Backup\Tests\Integration;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class BackupDatabaseCommandTest extends TestCase
{
    /** @var \Illuminate\Support\Carbon */
    protected $date;

    /** @var string */
    protected $expectedDumpPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->date = Carbon::create(2018, 8, 15, 12, 30, 59);

        Carbon::setTestNow($this->date);

        $this->expectedDumpPath = $this->getExpectedDumpPath('forge', $this->date);
    }

    public function testCommandNoArguments()
    {
        $resultCode = Artisan::call('backup:database');

        $this->assertEquals(0, $resultCode);
        $this->assertFileExists($this->expectedDumpPath);
    }

    public function testCommandWithFilename()
    {
        $this->expectedDumpPath = 'custom_filename';

        $resultCode = Artisan::call('backup:database', ['filename' => 'custom_filename']);

        $this->assertEquals(0, $resultCode);
        $this->assertFileExists($this->expectedDumpPath);
    }

    public function testCommandWithPath()
    {
        is_dir('some_dir') || mkdir('some_dir');

        $this->expectedDumpPath = 'some_dir' . DIRECTORY_SEPARATOR . $this->expectedDumpPath;

        $resultCode = Artisan::call('backup:database', ['--path' => 'some_dir']);

        $this->assertEquals(0, $resultCode);
        $this->assertFileExists($this->expectedDumpPath);
    }

    public function testCommandWithPathAndFilename()
    {
        is_dir('some_dir') || mkdir('some_dir');

        $this->expectedDumpPath = 'some_dir' . DIRECTORY_SEPARATOR . 'custom_filename';

        $resultCode = Artisan::call('backup:database', ['--path' => 'some_dir', 'filename' => 'custom_filename']);

        $this->assertEquals(0, $resultCode);
        $this->assertFileExists($this->expectedDumpPath);
    }

    public function testCommandWithConnection()
    {
        $connection = $this->app['config']->get('database.connections.mysql');
        $connection['database'] = 'custom_database';

        $this->app['config']->set('database.connections.custom_connection', $connection);

        $resultCode = Artisan::call('backup:database', ['--connection' => 'custom_connection']);

        $this->assertEquals(0, $resultCode);
        $this->assertFileExists($this->getExpectedDumpPath('custom_database', $this->date));
    }

    /**
     * @param string $database
     * @param \Illuminate\Support\Carbon $date
     * @return string
     */
    protected function getExpectedDumpPath($database, $date)
    {
        return "{$database}-{$date->format('Y-m-d_H-i-s')}.sql.gz";
    }
}
