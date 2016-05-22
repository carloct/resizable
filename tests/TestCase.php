<?php

namespace Keisen\Resizable\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Belt\Folder;

abstract class TestCase extends Orchestra
{

    protected $resizableFile;

    /**
     * Set up the database and copy the test image from fixtures
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        copy('tests/fixtures/test.jpg', 'tests/uploads/test.jpg');

        $this->resizableFile = new UploadedFile(
            'tests/uploads/test.jpg',
            'test.jpg',
            null,
            null,
            null,
            true
        );
    }

    /**
     * Attach the service provider to the test suite
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Keisen\Resizable\ResizableServiceProvider',
        ];
    }



    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app 
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => __DIR__.'/database.sqlite',
                'prefix'   => '',
            ]
        );
    }

    /**
     * Set up the testing database
     *
     * @return void
     */
    protected function setUpDatabase()
    {
        file_put_contents(__DIR__.'/database.sqlite', null);
        $this->app['db']->connection()->getSchemaBuilder()->create(
            'images',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('file')->nullable();
            }
        );
    }

    /**
     * Remove the image created
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Folder::empty('tests/storage');
    }
}