<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\DataComponents\DataScout;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class DataScoutTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DataScoutTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function searching_reduces_the_dataset()
    {
        /** @var DataScout $scout */
        $scout = new DataScout(['name']);

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($scout);

        $countBeforeSearch = $scout->getQueryCount();

        $queryString = 'test-123';

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $scout->addQuery($queryString);
        $dataTable->render();

        $this->assertLessThan($countBeforeSearch, $scout->getQueryCount());
    }

    /**
     * @test
     */
    public function search_parameters_are_considered()
    {
        $queryString = 'test-123';

        request()->request->add(['search' => $queryString]);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->addComponent(new DataScout(['name']));

        $this->assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $queryString . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function skip_empty_search()
    {
        $dataTable = DataTable::model(TestModel::class, ['name']);
        $beforeSearch = $dataTable->render();

        $dataTable->addComponent(new DataScout(['name']));

        $this->assertEquals($beforeSearch, $dataTable->render());
    }

    /**
     * @test
     */
    public function dont_search_unsearchable_fields()
    {
        $queryString = 'test-123';

        /** @var DataScout $dataScout */
        $dataScout = new DataScout(['created_at']);
        $dataScout->addQuery($queryString);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name', 'created_at'])->addComponent($dataScout);
        $dataTable->render();

        $this->assertEquals(0, $dataScout->getQueryCount());
    }
}