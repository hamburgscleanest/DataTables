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
    public function can_force_database_driver()
    {
        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name']);

        /** @var FulltextSearch $searcher */
        $searcher = new FulltextSearch(['name']);
        $searcher->forceDatabaseDriver('mysql');
        $searcher->addQuery('test');


        self::assertEquals('SELECT * FROM "TESTMODELS" WHERE (MATCH(NAME) AGAINST (\'TEST\'))', \mb_strtoupper($searcher->searchData($dataTable->query())->toSql()));
    }

    /**
     * @test
     */
    public function search_in_other_mode()
    {
        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name']);

        /** @var FulltextSearch $searcher */
        $searcher = new FulltextSearch(['name']);
        $searcher->forceDatabaseDriver('mysql');
        $searcher->addQuery('test');
        $searcher->setMode('WITH QUERY EXTENSION');


        self::assertEquals('SELECT * FROM "TESTMODELS" WHERE (MATCH(NAME) AGAINST (\'TEST\' WITH QUERY EXTENSION))', \mb_strtoupper($searcher->searchData($dataTable->query())->toSql()));
    }

    protected function setUpDb()
    {
        DB::statement(\file_get_contents(\dirname(__DIR__) . '/tests/extra_sql/fulltext.sql'));
    }
}