<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\ColumnFormatters\ImageColumn;

/**
 * Class ImageColumnTest
 * @package hamburgscleanest\DataTables\Tests
 */
class ImageColumnTest extends TestCase {

    /**
     * @test
     */
    public function image_is_rendered()
    {
        $imagePath = __DIR__ . '/images/test.jpg';
        TestModel::create(['name' => $imagePath, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', new ImageColumn());
        $dataTable->query()->where('name', $imagePath);

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><img src="' . $imagePath . '"/></td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function is_rendered_with_custom_classes()
    {
        $imagePath = __DIR__ . '/images/test.jpg';
        TestModel::create(['name' => $imagePath, 'created_at' => Carbon::now()]);

        $class = 'test-class';
        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', new ImageColumn(null, $class));
        $dataTable->query()->where('name', $imagePath);

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td><img src="' . $imagePath . '" class="' . $class . '"/></td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function empty_string_is_rendered_when_the_image_is_not_found()
    {
        $imagePath = 'no_image';
        TestModel::create(['name' => $imagePath, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', new ImageColumn());
        $dataTable->query()->where('name', $imagePath);

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td></td></tr></table>',
            $dataTable->render()
        );
    }


    /**
     * @test
     */
    public function fallback_is_rendered_when_the_image_is_not_found()
    {
        $imagePath = 'no_image';
        TestModel::create(['name' => $imagePath, 'created_at' => Carbon::now()]);

        $fallback = 'test-fallback';
        $dataTable = DataTable::model(TestModel::class, ['name'])->formatColumn('name', new ImageColumn($fallback));
        $dataTable->query()->where('name', $imagePath);

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $fallback . '</td></tr></table>',
            $dataTable->render()
        );
    }
}