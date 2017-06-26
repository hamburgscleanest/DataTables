<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Facades\DataTable;
use hamburgscleanest\DataTables\Models\DataComponents\Paginator;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class PaginationTest
 * @package hamburgscleanest\DataTables\Tests
 */
class PaginationTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_first_page()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        static::assertEquals(
            '<ul class="pagination"><li class="active"><a href="' . $this->baseUrl .
            '?page=1">1</a></li><li><a href="' . $this->baseUrl .
            '?page=2">2</a></li><li><a href="' . $this->baseUrl .
            '?page=3">3</a></li><li><a href="' . $this->baseUrl .
            '?page=2">→</a></li><li><a href="' . $this->baseUrl .
            '?page=7">last</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function has_only_one_page_when_per_page_is_zero()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator(0);

        $dataTable = DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        $dataTable->render();

        static::assertEquals(1, $paginator->pageCount());
    }

    /**
     * @est
     */
    public function has_only_one_page_when_data_count_is_less_than_per_page()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator(150);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        static::assertEquals(1, $paginator->pageCount());
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_when_per_page_is_zero()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator(0);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        static::assertEquals(
            '<ul class="pagination"><li class="active">1</li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_last_page()
    {
        $entriesPerPage = 15;

        /** @var Paginator $paginator */
        $paginator = new Paginator($entriesPerPage);

        $pageCount = (int) \ceil(TestModel::count() / $entriesPerPage);

        \request()->request->add(['page' => $pageCount]);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        static::assertEquals(
            '<ul class="pagination"><li><a href="' . $this->baseUrl .
            '?page=1">first</a></li><li><a href="' . $this->baseUrl .
            '?page=6">←</a></li><li><a href="' . $this->baseUrl .
            '?page=5">5</a></li><li><a href="' . $this->baseUrl .
            '?page=6">6</a></li><li class="active"><a href="' . $this->baseUrl .
            '?page=7">7</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_other_pages()
    {
        $entriesPerPage = 5;

        /** @var Paginator $paginator */
        $paginator = new Paginator($entriesPerPage);

        $page = (int) \ceil(TestModel::count() / $entriesPerPage * 0.5);

        \request()->request->add(['page' => $page]);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        static::assertEquals(
            '<ul class="pagination"><li><a href="' . $this->baseUrl .
            '?page=1">first</a></li><li><a href="' . $this->baseUrl .
            '?page=9">←</a></li><li><a href="' . $this->baseUrl .
            '?page=8">8</a></li><li><a href="' . $this->baseUrl .
            '?page=9">9</a></li><li class="active"><a href="' . $this->baseUrl .
            '?page=10">10</a></li><li><a href="' . $this->baseUrl .
            '?page=11">11</a></li><li><a href="' . $this->baseUrl .
            '?page=12">12</a></li><li><a href="' . $this->baseUrl .
            '?page=11">→</a></li><li><a href="' . $this->baseUrl .
            '?page=20">last</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function entries_per_page_is_greater_than_the_data_count()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator(500);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        static::assertEquals(1, $paginator->pageCount());
    }

    /**
     * @test
     */
    public function entries_are_limited()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();
        $paginator->entriesPerPage(1);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        static::assertEquals($paginator->getQueryCount(), $paginator->pageCount());
    }

    /**
     * @test
     */
    public function surrounding_pages_are_rendered_correctly()
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator();
        $paginator->surroundingPages(1);

        \request()->request->add(['page' => 6]);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        static::assertEquals(
            '<ul class="pagination"><li><a href="' . $this->baseUrl .
            '?page=1">first</a></li><li><a href="' . $this->baseUrl .
            '?page=5">←</a></li><li><a href="' . $this->baseUrl .
            '?page=5">5</a></li><li class="active"><a href="' . $this->baseUrl .
            '?page=6">6</a></li><li><a href="' . $this->baseUrl .
            '?page=7">7</a></li><li><a href="' . $this->baseUrl .
            '?page=7">→</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function dataset_is_reduced()
    {
        $dataTable = DataTable::model(TestModel::class, ['id'])->addComponent(new Paginator(1));

        static::assertEquals(
            '<table class="table"><tr><th>id</th></tr><tr><td>1</td></tr></table>',
            $dataTable->render()
        );
    }

    /**
     * @test
     */
    public function can_change_page_symbols()
    {
        $paginator = new Paginator();
        $paginator->surroundingPages(1);

        \request()->request->add(['page' => 3]);

        DataTable::model(TestModel::class, ['id'])->addComponent($paginator);

        $symbols = [
            'first' => 'erste', 'last' => 'letzte', 'next' => '->', 'previous' => '<-'
        ];

        $paginator->pageSymbols($symbols);

        static::assertEquals(
            '<ul class="pagination"><li><a href="http://localhost?page=1">' .
            $symbols['first'] . '</a></li><li><a href="http://localhost?page=2">' .
            $symbols['previous'] . '</a></li><li><a href="http://localhost?page=2">2</a></li><li class="active"><a href="http://localhost?page=3">3</a>' .
            '</li><li><a href="http://localhost?page=4">4</a></li><li><a href="http://localhost?page=4">' .
            $symbols['next'] . '</a></li><li><a href="http://localhost?page=7">' .
            $symbols['last'] . '</a></li></ul>',
            $paginator->render()
        );
    }
}