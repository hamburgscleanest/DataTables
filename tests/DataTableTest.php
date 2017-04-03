<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\Paginator;
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
        $dataTable = DataTable::query(TestModel::where('created_at', '1991-08-03'));

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
        $dataTable = DataTable::query(TestModel::where('created_at', '1991-08-03'));

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

        $dataTable = DataTable::query(TestModel::where('name', 'test')->select(['id', 'created_at', 'name']));

        $this->assertEquals(
            '<table><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>' .
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

        $dataTable = DataTable::query(
            TestModel::where('name', 'test')->select(['id', 'created_at', 'name']),
            function ($row)
            {
                $row->id = 1337;
                $row->created_at = '2017-01-01 13:37:00';
                $row->name = 'Test';

                return $row;
            }
        );

        $this->assertEquals(
            '<table><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>1337</td><td>2017-01-01 13:37:00</td><td>Test</td></tr></table>',
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

        $dataTable = DataTable::query(TestModel::where('name', 'test')->select(['id', 'created_at', 'name']))->classes('test-class');

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
    public function pagination_is_rendered_correctly_for_first_page()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::query(TestModel::select(['id', 'created_at', 'name']))->addComponent($paginator);

        $this->assertEquals(
            '<ul><li><a href="' . $this->baseUrl . '?page=2">→</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_last_page()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::query(TestModel::select(['id', 'created_at', 'name']))->addComponent($paginator);

        $pageCount = $paginator->pageCount();

        $this->get($this->baseUrl . '?page=' . $pageCount);

        $this->assertEquals(
            '<ul><li><a href="' . $this->baseUrl . '?page="' . ($pageCount - 1) . '>←</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_other_pages()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::query(TestModel::select(['id', 'created_at', 'name']))->addComponent($paginator);

        $this->get($this->baseUrl . '?page=2');

        $this->assertEquals(
            '<ul><li><a href="' . $this->baseUrl . '?page=1">←</a></li><li><a href="' . $this->baseUrl . '?page=3">→</a></li></ul>',
            $paginator->render()
        );
    }
}