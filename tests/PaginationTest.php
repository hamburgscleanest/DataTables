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

        $this->assertEquals(
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

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        $this->assertEquals(1, $paginator->pageCount());
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

        $this->assertEquals(1, $paginator->pageCount());
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

        $this->assertEquals(
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

        request()->request->add(['page' => $pageCount]);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        $this->assertEquals(
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

        request()->request->add(['page' => $page]);

        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        $this->assertEquals(
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
}