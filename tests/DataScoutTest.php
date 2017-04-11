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
        /** @var DataScout $dataScout */
        $dataScout = new DataScout(['name']);

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($dataScout);

        $countBeforeSearch = $dataScout->getQueryCount();

        $queryString = 'test-123';

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

        $dataScout->addQuery($queryString);
        $dataTable->render();

        $this->assertLessThan($countBeforeSearch, $dataScout->getQueryCount());
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
    public function can_make_field_searchable()
    {
        /** @var DataScout $dataScout */
        $dataScout = new DataScout();
        $dataScout->makeSearchable('name');
        $dataTable = DataTable::model(TestModel::class, ['name'])->addComponent($dataScout);

        $queryString = 'test-123';
        $dataScout->addQuery($queryString);

        TestModel::create(['name' => $queryString, 'created_at' => Carbon::now()]);

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

    /**
     * @test
     */
    public function displays_alternative_button_text()
    {
        $buttonText = 'my-test-button';

        $dataScout = new DataScout();
        $dataScout->buttonText($buttonText);

        DataTable::model(TestModel::class, ['name'])->addComponent($dataScout);

        $this->assertEquals(
            '<form method="get" action="' . $this->baseUrl .
            '?search="><div class="row"><div class="col-md-10"><input name="search" class="form-control data-scout-input" placeholder="Search.."/></div><div class="col-md-2"><button type="submit" class="btn btn-primary">' .
            $buttonText . '</button></div></div></form>',
            $dataScout->render()
        );
    }

    /**
     * @test
     */
    public function displays_alternative_placeholder()
    {
        $placeholder = 'my-test-placeholder';

        $dataScout = new DataScout();
        $dataScout->placeholder($placeholder);

        DataTable::model(TestModel::class, ['name'])->addComponent($dataScout);

        $this->assertEquals(
            '<form method="get" action="' . $this->baseUrl .
            '?search="><div class="row"><div class="col-md-10"><input name="search" class="form-control data-scout-input" placeholder="' .
            $placeholder . '"/></div><div class="col-md-2"><button type="submit" class="btn btn-primary">Search</button></div></div></form>',
            $dataScout->render()
        );
    }
}