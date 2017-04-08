<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class DataTableTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DataTableTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function empty_data_is_properly_displayed()
    {
        $dataTable = DataTable::model(TestModel::class);
        $dataTable->query()->where('created_at', '1991-08-03');

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
        $dataTable = DataTable::model(TestModel::class);
        $dataTable->query()->where('created_at', '1991-08-03');

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
        /** @var TestModel $testmodel */
        $testmodel = TestModel::create([
            'name'       => 'test',
            'created_at' => '2017-01-01 12:00:00'
        ]);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name']);
        $dataTable->query()->where('name', 'test');

        $this->assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>' .
            $testmodel->id . '</td><td>' .
            $testmodel->created_at->format('Y-m-d H:i:s') . '</td><td>' .
            $testmodel->name . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function closure_is_working()
    {
        /** @var TestModel $testmodel */
        TestModel::create([
            'name'       => 'test',
            'created_at' => '2017-01-01 12:00:00'
        ]);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->renderRow(
                function ($row)
                {
                    $row->id = 1337;
                    $row->created_at = '2017-01-01 13:37:00';
                    $row->name = 'Test';

                    return $row;
                }
            );
        $dataTable->query()->where('name', 'test');

        $this->assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>1337</td><td>2017-01-01 13:37:00</td><td>Test</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function table_has_class()
    {
        /** @var TestModel $testmodel */
        $testmodel = TestModel::create([
            'name'       => 'test',
            'created_at' => '2017-01-01 12:00:00'
        ]);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->classes('test-class');
        $dataTable->query()->where('name', 'test');

        $this->assertEquals(
            '<table class="test-class"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>' .
            $testmodel->id . '</td><td>' .
            $testmodel->created_at->format('Y-m-d H:i:s') . '</td><td>' .
            $testmodel->name . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function table_renders_mutated_attributes()
    {
        /** @var TestModel $testmodel */
        $testmodel = TestModel::create([
            'name'       => 'test',
            'created_at' => '2017-01-01 12:00:00'
        ]);

        $dataTable = DataTable::model(TestModel::class, ['custom_column']);
        $dataTable->query()->where('name', 'test');

        $this->assertEquals(
            '<table class="table"><tr><th>custom_column</th></tr><tr><td>custom-column</td></tr></table>',
            $dataTable->render()
        );
    }
}