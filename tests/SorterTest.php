<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\DataComponents\Sorter;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class SorterTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SorterTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function sorting_initialized_in_constructor_works()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter(['created_at' => 'asc']);

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($sorter);

        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        $this->assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }

    /**
     * @test
     */
    public function sorting_by_adding_fields_works()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter();
        $sorter->addField('created_at', 'asc');

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($sorter);

        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        $this->assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }
}