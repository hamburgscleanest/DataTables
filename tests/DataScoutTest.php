<?php

namespace hamburgscleanest\DataTables\Tests;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\DataComponents\DataScout;
use hamburgscleanest\DataTables\Models\DataComponents\Search\FulltextSearch;
use hamburgscleanest\DataTables\Models\DataComponents\Search\SimpleSearch;
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
        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new SimpleSearch(['name']));

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($dataScout);

        $countBeforeSearch = $dataScout->getQueryCount();

        $queryString = 'test-123';

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataScout->addQuery($queryString);
        $dataTable->render();

        static::assertLessThan($countBeforeSearch, $dataScout->getQueryCount());
    }

    /**
     * @test
     */
    public function changing_the_algorithm_works()
    {
        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new FulltextSearch(['name']));
        $dataScout->setSearch(new SimpleSearch(['name']));

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($dataScout);

        $countBeforeSearch = $dataScout->getQueryCount();

        $queryString = 'test-123';

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataScout->addQuery($queryString);
        $dataTable->render();

        static::assertLessThan($countBeforeSearch, $dataScout->getQueryCount());
    }

    /**
     * @test
     */
    public function search_parameters_are_considered()
    {
        $queryString = 'test-123';

        \request()->request->add(['search' => $queryString]);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name'])->addComponent(new DataScout(new SimpleSearch(['name'])));

        static::assertEquals(
            '<table class="table"><tr><th>name</th></tr><tr><td>' . $queryString . '</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function can_make_field_searchable()
    {
        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new SimpleSearch());
        $dataScout->makeSearchable('name');
        $dataTable = DataTable::model(TestModel::class, ['name'])->addComponent($dataScout);

        $queryString = 'test-123';
        $dataScout->addQuery($queryString);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        static::assertEquals(
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

        $dataTable->addComponent(new DataScout(new SimpleSearch(['name'])));

        static::assertEquals($beforeSearch, $dataTable->render());
    }

    /**
     * @test
     */
    public function dont_search_unsearchable_fields()
    {
        $queryString = 'test-123';

        /** @var DataScout $dataScout */
        $dataScout = new DataScout(new SimpleSearch(['created_at']));
        $dataScout->addQuery($queryString);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataTable = DataTable::model(TestModel::class, ['name', 'created_at'])->addComponent($dataScout);
        $dataTable->render();

        static::assertEquals(0, $dataScout->getQueryCount());
    }

    /**
     * @test
     */
    public function displays_alternative_placeholder()
    {
        $placeholder = 'my-test-placeholder';

        $dataScout = new DataScout(new SimpleSearch());
        $dataScout->placeholder($placeholder);

        DataTable::model(TestModel::class, ['name'])->addComponent($dataScout);

        static::assertEquals(
            '<input name="datascout-search" class="form-control datascout-input" placeholder="' . $placeholder . '"/>',
            $dataScout->render()
        );
    }

    /**
     * @test
     */
    public function get_search_action()
    {
        $dataScout = new DataScout(new SimpleSearch(['name']));
        $dataScout->addQuery('hello world');

        self::assertEquals($this->baseUrl . '?search=hello+world', $dataScout->getSearchUrl());
    }
}