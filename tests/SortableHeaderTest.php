<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Models\DataComponents\Sorter;
use hamburgscleanest\DataTables\Models\HeaderFormatters\SortableHeader;

/**
 * Class SortableHeaderTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SortableHeaderTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        SessionHelper::shouldReceive('getState')->andReturn([]);
    }

    /**
     * @test
     */
    public function sorting_initialized_in_constructor_works()
    {
        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders(new SortableHeader(['name']));
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th><a class="sortable-header" href="' . $this->baseUrl . '?sort=name%7Easc">name <span class="sort-symbol">⇵</span></a></th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function sorting_by_adding_fields_works()
    {
        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $sortableHeader = new SortableHeader();
        $sortableHeader->makeSortable('name');

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders($sortableHeader);
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th><a class="sortable-header" href="' . $this->baseUrl . '?sort=name%7Easc">name <span class="sort-symbol">⇵</span></a></th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function sorting_by_blacklisting_works()
    {
        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders(new SortableHeader([], ['name']));
        $dataTable->query()->where('name', $fieldName);

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
        $date = '2017-01-01 00:00:00';
        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => $date]);

        $sortableHeader = new SortableHeader(['name', 'created_at']);
        $sortableHeader->dontSort('name');

        $dataTable = DataTable::model(TestModel::class, ['name', 'created_at'])->formatHeaders($sortableHeader);
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th>name</th><th><a class="sortable-header" href="' . $this->baseUrl . '?sort=created_at%7Easc">created_at <span class="sort-symbol">⇵</span></a></th></tr><tr><td>' .
            $fieldName . '</td><td>' . $date . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function sorting_symbols_are_changed()
    {
        $fieldName = 'sort-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $symbols = [
            'asc'  => 'X',
            'desc' => 'Y',
            'none' => 'Z'
        ];

        $sorter = new Sorter();

        $sortableHeader = new SortableHeader(['name']);
        $sortableHeader->sortingSymbols($symbols);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders($sortableHeader)->addComponent($sorter);
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th><a class="sortable-header" href="' . $this->baseUrl .
            '?sort=name%7Easc">name <span class="sort-symbol">' . $symbols['none'] . '</span></a></th></tr><tr><td>' . $fieldName . '</td></tr></table>',
            $dataTable->render()
        );
    }
}