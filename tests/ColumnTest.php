<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\Column;

/**
 * Class ColumnTest
 * @package hamburgscleanest\DataTables\Tests
 */
class ColumnTest extends TestCase {

    /**
     * @test
     */
    public function name_is_set()
    {
        $name = 'testcolumn';
        $column = new Column($name);

        $this->assertEquals($name, $column->getName());
        $this->assertNull($column->getRelation());
    }

    /**
     * @test
     */
    public function relation_is_set()
    {
        $name = 'test.column';
        $column = new Column($name);
        $relation = $column->getRelation();

        $this->assertEquals('column', $column->getName());
        $this->assertNotNull($relation);
        $this->assertEquals('test', $relation->name);
    }

    /**
     * @test
     */
    public function aggregate_is_set()
    {
        $name = 'COUNT(test.column)';
        $column = new Column($name);
        $relation = $column->getRelation();

        $this->assertEquals('column', $column->getName());
        $this->assertNotNull($relation);
        $this->assertEquals('test', $relation->name);
        $this->assertEquals('count', $relation->aggregate);
    }
}