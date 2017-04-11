<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\ColumnFormatters\DateColumn;

/**
 * Class DateColumnTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DateColumnTest extends TestCase {

    /**
     * @test
     */
    public function date_is_formatted_correctly()
    {
        $fieldName = 'datecolumn-test';
        $date = '1991-03-08 12:00:00';
        $dateFormat = 'd.m.Y';
        TestModel::create(['name' => $fieldName, 'created_at' => $date]);

        $dataTable = DataTable::model(TestModel::class, ['created_at'])->formatColumn('created_at', new DateColumn($dateFormat));
        $dataTable->query()->where('name', $fieldName);

        $this->assertEquals(
            '<table class="table"><tr><th>created_at</th></tr><tr><td>' . Carbon::parse($date)->format($dateFormat) . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function date_is_formatted_when_using_function()
    {
        $fieldName = 'datecolumn-test';
        $date = '1991-03-08 12:00:00';
        $dateFormat = 'd.m.Y';
        TestModel::create(['name' => $fieldName, 'created_at' => $date]);

        /** @var DateColumn $dateColumn */
        $dateColumn = new DateColumn();

        $dataTable = DataTable::model(TestModel::class, ['created_at'])->formatColumn('created_at', $dateColumn);
        $dataTable->query()->where('name', $fieldName);

        $dateColumn->dateFormat($dateFormat);

        $this->assertEquals(
            '<table class="table"><tr><th>created_at</th></tr><tr><td>' . Carbon::parse($date)->format($dateFormat) . '</td></tr></table>',
            $dataTable->render()
        );
    }
}