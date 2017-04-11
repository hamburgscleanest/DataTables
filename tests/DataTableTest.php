<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Models\ColumnFormatters\DateColumn;
use hamburgscleanest\DataTables\Models\DataComponents\Sorter;
use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use RuntimeException;

/**
 * Class DataTableTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DataTableTest extends TestCase {

    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        SessionHelper::shouldReceive('getState')->andReturn([]);
    }

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
        TestModel::create([
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

    /**
     * @test
     */
    public function handles_not_existing_class()
    {
        $this->expectException(RuntimeException::class);
        DataTable::model('non-existing');
    }

    /**
     * @test
     */
    public function handles_class_which_is_no_active_record()
    {
        $this->expectException(RuntimeException::class);
        DataTable::model(Header::class);
    }

    /**
     * @test
     */
    public function handles_no_model_set()
    {
        $this->expectException(RuntimeException::class);

        $dataTable = DataTable::columns(['id', 'created_at', 'name']);
        $dataTable->render();
    }

    /**
     * @test
     */
    public function remembers_state()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter(['name' => 'desc']);
        $sorter->remember();

        $dataTable = DataTable::model(TestModel::class)->addComponent($sorter);

        SessionHelper::shouldReceive('saveState')->once();

        $dataTable->render();
    }

    /**
     * @test
     */
    public function forgets_state()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter(['name' => 'desc']);
        DataTable::model(TestModel::class)->addComponent($sorter);

        SessionHelper::shouldReceive('removeState')->once();
        $sorter->forget();
    }

    /**
     * @test
     */
    public function column_formatters_are_set()
    {
        $fieldName = 'formatters-test';
        $date = '2017-01-01 00:00:00';
        $dateFormat = 'd.m.Y';

        TestModel::create(['name' => $fieldName, 'created_at' => $date]);

        $dataTable = DataTable::model(TestModel::class, ['created_at' => new DateColumn($dateFormat)]);
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th>created_at</th></tr><tr><td>01.01.2017</td></tr></table>',
            $dataTable->render()
        );
    }
}