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

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])
            ->addComponent($paginator);

        $this->assertEquals(
            '<ul class="pagination"><li class="active"><a href="' . $this->baseUrl .
            '?page=1">1</a></li><li><a href="' . $this->baseUrl .
            '?page=2">2</a></li><li><a href="' . $this->baseUrl .
            '?page=3">3</a></li><li><a href="' . $this->baseUrl .
            '?page=2">→</a></li><li><a href="' . $this->baseUrl .
            '?page=6">last</a></li></ul>',
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

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
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

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
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

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
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
        // TODO
        return;

        /** @var Paginator $paginator */
        $paginator = new Paginator();

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::model(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        $pageCount = $paginator->pageCount();

        $this->get($this->baseUrl . '?page=' . $pageCount);

        $this->assertEquals(
            '<ul class="pagination"><li class="active"><a href="' . $this->baseUrl .
            '?page=1">1</a></li><li><a href="' . $this->baseUrl .
            '?page=2">2</a></li><li><a href="' . $this->baseUrl .
            '?page=3">3</a></li><li><a href="' . $this->baseUrl .
            '?page=2">→</a></li><li><a href="' . $this->baseUrl .
            '?page=6">last</a></li></ul>',
            $paginator->render()
        );
    }

    /**
     * @test
     */
    public function pagination_is_rendered_correctly_for_other_pages() // TODO
    {
        // TODO
        return;

        /** @var Paginator $paginator */
        $paginator = new Paginator();

        /** @var \hamburgscleanest\DataTables\Models\DataTable $dataTable */
        DataTable::query(TestModel::class, ['id', 'created_at', 'name'])->addComponent($paginator);

        $this->get($this->baseUrl . '?page=2');

        $this->assertEquals(
            '<ul class="pagination"><li class="active"><a href="' . $this->baseUrl .
            '?page=1">1</a></li><li><a href="' . $this->baseUrl .
            '?page=2">2</a></li><li><a href="' . $this->baseUrl .
            '?page=3">3</a></li><li><a href="' . $this->baseUrl .
            '?page=2">→</a></li><li><a href="' . $this->baseUrl .
            '?page=6">last</a></li></ul>',
            $paginator->render()
        );
    }
}