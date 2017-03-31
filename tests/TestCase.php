<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\DataTablesServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 * @package hamburgscleanest\DataTables\Tests
 */
class TestCase extends Orchestra {

    public function setUp()
    {
        parent::setUp();

        $this->withFactories(dirname(__DIR__) . '/tests/factories');
        $this->setUpDb();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [DataTablesServiceProvider::class];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'datatables');
        $app['config']->set('database.connections.datatables', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ]);
    }

    protected function setUpDb()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('testmodels', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->dateTime('created_at');
        });
        TestModel::truncate();
        factory(TestModel::class, 100)->create();
    }
}