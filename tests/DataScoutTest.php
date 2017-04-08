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
}