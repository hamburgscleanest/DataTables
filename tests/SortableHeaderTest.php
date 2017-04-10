<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Helpers\SessionHelper;
use hamburgscleanest\DataTables\Models\HeaderFormatters\SortableHeader;
use Mockery;

/**
 * Class SortableHeaderTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SortableHeaderTest extends TestCase {

    private $_sessionHelper;

    public function setUp()
    {
        parent::setUp();

        $this->_sessionHelper = Mockery::mock(SessionHelper::class);
        $this->_sessionHelper->shouldReceive('getState')->andReturn([]);
    }

    /**
     * @test
     */
    public function sorting_initialized_in_constructor_works()
    {
        return; // TODO

        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders(new SortableHeader(['name']));
        $dataTable->query()->where('name', $fieldName);

        dd($dataTable->render());

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function sorting_by_adding_fields_works()
    {
        return; // TODO

        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $sortableHeader = new SortableHeader();
        $sortableHeader->makeSortable('name');

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders($sortableHeader);
        $dataTable->query()->where('name', $fieldName);

        dd($dataTable->render());

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function sorting_by_blacklisting_works()
    {
        return; // TODO

        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders(new SortableHeader([], ['name']));
        $dataTable->query()->where('name', $fieldName);

        dd($dataTable->render());

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function removing_field_from_sorting_works()
    {
        return; // TODO

        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $sortableHeader = new SortableHeader();
        $sortableHeader->dontSort('name');

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders($sortableHeader);
        $dataTable->query()->where('name', $fieldName);

        dd($dataTable->render());

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }
}