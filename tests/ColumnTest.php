<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\Column\Column;

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

        static::assertEquals($name, $column->getName());
        static::assertNull($column->getRelation());
    }

    /**
     * @test
     */
    public function relation_is_set()
    {
        $name = 'test.column';
        $column = new Column($name);
        $relation = $column->getRelation();

        static::assertEquals('column', $column->getName());
        static::assertNotNull($relation);
        static::assertEquals('test_column', $relation->name);
    }

    /**
     * @test
     */
    public function aggregate_is_set()
    {
        $name = 'COUNT(test.column)';
        $column = new Column($name);
        $relation = $column->getRelation();

        static::assertEquals('column', $column->getName());
        static::assertNotNull($relation);
        static::assertEquals('test_column', $relation->name);
        static::assertEquals('count', $relation->aggregate);
    }

    /**
     * @test
     */
    public function can_identify_mutated_attributes()
    {
        $column = new Column('custom_column', null, new TestModel());
        static::assertTrue($column->isMutated());
        static::assertEquals('custom_column', $column->getAttributeName());
    }
}