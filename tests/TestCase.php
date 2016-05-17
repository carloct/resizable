<?php

namespace Keisen\Resizable\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends Orchestra
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/database.sqlite',
            'prefix'   => '',
        ]);
        $app['config']->set('resizable.folder', 'storage');
    }

    protected function setUpDatabase()
    {
        file_put_contents(__DIR__.'/database.sqlite', null);
        $this->app['db']->connection()->getSchemaBuilder()->create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('file');
        });
    }
}