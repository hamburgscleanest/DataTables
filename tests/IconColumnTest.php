<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\ColumnFormatters\Adapters\Icon\FontAwesomeAdapter;
use hamburgscleanest\DataTables\Models\ColumnFormatters\IconColumn;

/**
 * Class IconColumnTest
 * @package hamburgscleanest\DataTables\Tests
 */
class IconColumnTest extends TestCase {

    /**
     * @test
     */
    public function icon_is_rendered()
    {
        $iconName = 'user';
        TestModel::create(['name' => $iconName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', new IconColumn(new FontAwesomeAdapter()));
        $dataTable->query()->where('name', $iconName);

        static::assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><i class="fa fa-' . $iconName . '"></i></td></tr></table>',
            $dataTable->render()
        );
    }
}