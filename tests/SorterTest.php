<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Models\DataComponents\Sorter;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class SorterTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SorterTest extends TestCase {

    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        SessionHelper::shouldReceive('removeState');
    }

    /**
     * @test
     */
    public function sorting_initialized_in_constructor_works()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter(['created_at' => 'asc']);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($sorter);

        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        static::assertEquals(
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
        $sorter->addField('created_at');

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($sorter);

        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        static::assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }

    /**
     * @test
     */
    public function sorting_defaults_to_asc()
    {
        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent(new Sorter(['created_at']));

        static::assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }

    /**
     * @test
     */
    public function sorting_parameters_are_considered()
    {
        \request()->request->add(['sort' => 'created_at']);

        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent(new Sorter());

        static::assertEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }

    /**
     * @test
     */
    public function none_removes_the_column_from_sorting()
    {
        $date = '1991-08-03 00:00:00';
        TestModel::create(['name' => 'test', 'created_at' => $date]);

        \request()->request->add(['sort' => 'created_at~none']);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent(new Sorter(['created_at']));

        static::assertNotEquals(
            '<table class="table"><tr><th>id</th><th>created_at</th><th>name</th></tr><tr><td>101</td><td>' . $date . '</td><td>test</td></tr><tr>',
            \mb_substr($dataTable->render(), 0, 139)
        );
    }

    /**
     * @test
     */
    public function renders_correctly()
    {
        $fieldName = 'test';

        /** @var Sorter $sorter */
        $sorter = new Sorter([$fieldName]);

        static::assertEquals($fieldName, $sorter->render());
    }

    /**
     * @test
     */
    public function can_access_relation()
    {
        $fieldName = 'test';
        $parent = TestModel::create(['name' => $fieldName, 'created_at' => '2017-01-01 00:00:00']);
        TestModel::create(['name' => $fieldName, 'created_at' => '2017-02-02 00:00:00', 'test_model_id' => $parent->id]);
        TestModel::create(['name' => $fieldName, 'created_at' => '2017-03-03 00:00:00', 'test_model_id' => $parent->id]);

        $fieldName = 'test-2';
        $parent2 = TestModel::create(['name' => $fieldName, 'created_at' => '2017-01-01 00:00:00']);
        TestModel::create(['name' => $fieldName, 'created_at' => '2017-02-02 00:00:00', 'test_model_id' => $parent2->id]);

       /* $sorter = new Sorter(['count_testers_id']);
        $dataTable = DataTable::model(TestModel::class)->with(['testers'])->columns(['COUNT(testers.id)'])->addComponent($sorter);

        $this->assertEquals(
            '<table class="table"><tr><th>count_testers_id</th></tr><tr><td>1</td></tr><tr><td>2</td></tr></table>',
            $dataTable->render()
        );*/

        $sorter = new Sorter(['count_testers_id' => 'desc']);
        $dataTable = DataTable::model(TestModel::class)->with(['testers'])->columns(['COUNT(testers.id)'])->addComponent($sorter);

        static::assertEquals(
            '<table class="table"><tr><th>count_testers_id</th></tr><tr><td>2</td></tr><tr><td>1</td></tr></table>',
            $dataTable->render()
        );
    }
}