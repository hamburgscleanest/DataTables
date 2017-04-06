<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\DataComponents\Sorter;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class SorterTest
 * @package hamburgscleanest\DataTables\Tests
 */
class SorterTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function sorting_initialized_in_constructor_works()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter(['name' => 'desc']);

        // TODO
    }

    /**
     * @test
     */
    public function sorting_by_adding_fields_works()
    {
        /** @var Sorter $sorter */
        $sorter = new Sorter();
        $sorter->addField('name', 'desc');

        // TODO
    }
}