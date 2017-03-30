<?php

namespace packages\hamburgscleanest\DataTables\tests;

use hamburgscleanest\DataTables\DataTablesServiceProvider;
use hamburgscleanest\DataTables\Facades\DataTable;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class DataTableTest
 * @package packages\hamburgscleanest\DataTables\tests
 */
class DataTableTest extends Orchestra {

    private $_testData;

    public function setUp()
    {
        parent::setUp();

        $this->_testData = [
            [
                'id'         => 1,
                'created_at' => '2017-01-01 12:00:00',
                'name'       => 'Andre'
            ],
            [
                'id'         => 2,
                'created_at' => '2017-01-01 12:00:00',
                'name'       => 'Timo'
            ]
        ];
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

    }

    /**
     * @test
     */
    public function empty_data_is_properly_displayed()
    {
        $dataTable = DataTable::data([]);

        $this->assertEquals(
            '<div>no data</div>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function renders_custom_no_data()
    {
        $dataTable = DataTable::data([]);

        $view = '<p>Nothing here</p>';

        $this->assertEquals(
            $view,
            $dataTable->render(function () use ($view) { return $view; })
        );
    }

    /**
     * @test
     */
    public function renders_table_correctly()
    {
        $dataTable = DataTable::data($this->_testData);

        $this->assertEquals(
            '<table><tr><th>Id</th><th>Created at</th><th>Name</th></tr><tr><td>1</td><td>2017-01-01 12:00:00</td><td>Andre</td></tr><tr><td>2</td><td>2017-01-01 12:00:00</td><td>Timo</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function renders_headers_correctly()
    {
        $dataTable = DataTable::data($this->_testData)->headers(['Number', 'Date', 'Name']);

        $this->assertEquals(
            '<table><tr><th>Number</th><th>Date</th><th>Name</th></tr><tr><td>1</td><td>2017-01-01 12:00:00</td><td>Andre</td></tr><tr><td>2</td><td>2017-01-01 12:00:00</td><td>Timo</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function closure_is_working()
    {
        $dataTable = DataTable::data($this->_testData, function ($row) { return ['T', 'ES', 'T']; });

        $this->assertEquals(
            '<table><tr><th>Id</th><th>Created at</th><th>Name</th></tr><tr><td>T</td><td>ES</td><td>T</td></tr><tr><td>T</td><td>ES</td><td>T</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function table_has_class()
    {
        $dataTable = DataTable::data($this->_testData)->classes('test-class');

        $this->assertEquals(
            '<table class="test-class"><tr><th>Id</th><th>Created at</th><th>Name</th></tr><tr><td>1</td><td>2017-01-01 12:00:00</td><td>Andre</td></tr><tr><td>2</td><td>2017-01-01 12:00:00</td><td>Timo</td></tr></table>',
            $dataTable->render()
        );
    }
}