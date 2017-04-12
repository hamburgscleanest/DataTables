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
        $this->assertFalse($column->isRelation());
    }

    /**
     * @test
     */
    public function relation_is_set()
    {
        $name = 'test.column';
        $column = new Column($name);

        $this->assertEquals('column', $column->getName());
        $this->assertEquals('test', $column->getRelation());
        $this->assertTrue($column->isRelation());
    }

    /**
     * @test
     */
    public function aggregate_is_set()
    {
        $name = 'COUNT(test.column)';
        $column = new Column($name);

        $this->assertEquals('column', $column->getName());
        $this->assertEquals('test', $column->getRelation());
        $this->assertEquals('count', $column->getAggregate());
        $this->assertTrue($column->isRelation());
    }
}