<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\DataComponents\DataScout;
use hamburgscleanest\DataTables\Models\DataComponents\Search\FulltextSearch;
use Illuminate\Support\Facades\DB;

/**
 * Class FulltextSearchTest
 * @package hamburgscleanest\DataTables\Tests
 */
class FulltextSearchTest extends TestCase {

    /**
     * @test
     */
    public function searching_reduces_the_dataset()
    {
        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new FulltextSearch(['name']));

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($dataScout);

        $queryString = 'test-123';

        TestModel::create(['id' => 1337, 'name' => $queryString, 'created_at' => Carbon::now()]);

        static::assertEquals(1, $dataScout->getQueryCount());

        $dataScout->addQuery($queryString);
        $dataTable->render();

        static::assertEquals(0, $dataScout->getQueryCount());
    }

    /**
     * @test
     */
    public function search_in_other_mode()
    {
        /** @var FulltextSearch $searcher */
        $searcher = new FulltextSearch(['name']);
        $searcher->setMode('WITH QUERY EXPANSION');

        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new FulltextSearch(['name']));

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($dataScout);

        $queryString = 'test-123';

        TestModel::create(['id' => 1337, 'name' => $queryString, 'created_at' => Carbon::now()]);

        static::assertEquals(1, $dataScout->getQueryCount());

        $dataScout->addQuery($queryString);
        $dataTable->render();

        static::assertEquals(0, $dataScout->getQueryCount());
    }

    protected function setUpDb()
    {
        DB::statement(\file_get_contents(\dirname(__DIR__) . '/tests/extra_sql/fulltext.sql'));
    }
}