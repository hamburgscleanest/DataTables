<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\ColumnFormatters\LinkColumn;

/**
 * Class LinkColumnTest
 * @package hamburgscleanest\DataTables\Tests
 */
class LinkColumnTest extends TestCase {

    /**
     * @test
     */
    public function link_is_rendered_for_column()
    {
        $url = '/link/{name}';
        $fieldName = 'link-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', (new LinkColumn($url))->openInSame());
        $dataTable->query()->where('name', $fieldName);

        static::assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><a href="/link/' . $fieldName . '">' . $fieldName . '</a></td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function external_link_is_rendered_for_column()
    {
        $url = '/link/{name}';
        $fieldName = 'link-test';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', (new LinkColumn($url))->openInNew());
        $dataTable->query()->where('name', $fieldName);

        static::assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><a href="/link/' . $fieldName . '" target="_blank">' . $fieldName . '</a></td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function link_is_rendered_with_classes()
    {
        $url = '/link/{name}';
        $fieldName = 'link-test';
        $className = 'btn';
        TestModel::create(['name' => $fieldName, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', (new LinkColumn($url))->classes($className));
        $dataTable->query()->where('name', $fieldName);

        static::assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><a href="/link/' . $fieldName . '" class="' . $className . '">' . $fieldName . '</a></td></tr></table>',
            $dataTable->render()
        );
    }
}