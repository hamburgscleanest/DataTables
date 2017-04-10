<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\HeaderFormatters\TranslateHeader;

/**
 * Class TranslateHeaderTest
 * @package hamburgscleanest\DataTables\Tests
 */
class TranslateHeaderTest extends TestCase {

    /**
     * @test
     */
    public function headers_are_translated()
    {
        TestModel::create(['name' => 'trans-test', 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatHeaders(new TranslateHeader(['name' => 'TheName']));
        $dataTable->query()->where('name', 'trans-test');

        $this->assertEquals(
            '<table class="table"><tr><th>TheName</th></tr><tr><td>trans-test</td></tr></table>',
            $dataTable->render()
        );
    }
}