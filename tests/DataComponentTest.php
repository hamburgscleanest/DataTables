<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Exceptions\MultipleComponentAssertionException;
use hamburgscleanest\DataTables\Facades\DataTable;

/**
 * Class DataComponentTest
 * @package hamburgscleanest\DataTables\Tests
 */
class DataComponentTest extends TestCase {

    /**
     * @test
     */
    public function after_init_is_called()
    {
        $testComponent = new TestDataComponent();
        DataTable::model(TestModel::class)->addComponent($testComponent);

        static::assertTrue($testComponent->afterInitCalled);
    }

    /**
     * @test
     */
    public function remembers_state()
    {
        $testComponent = new TestDataComponent();
        $testComponent->remember();
        DataTable::model(TestModel::class)->addComponent($testComponent);

        static::assertTrue($testComponent->afterInitCalled);
        static::assertTrue($testComponent->readFromSessionCalled);
    }

    /**
     * @test
     */
    public function saves_state()
    {
        $testComponent = new TestDataComponent();
        $testComponent->remember();
        $dataTable = DataTable::model(TestModel::class)->addComponent($testComponent);

        static::assertTrue($testComponent->afterInitCalled);
        static::assertTrue($testComponent->readFromSessionCalled);

        $dataTable->render();

        static::assertTrue($testComponent->storeInSessionCalled);
    }

    /**
     * @test
     */
    public function is_rendered()
    {
        $testComponent = new TestDataComponent();
        static::assertEquals('TEST-RENDER', $testComponent->render());
    }

    /**
     * @test
     */
    public function component_can_be_accessed_directly()
    {
        $dataTable = DataTable::model(TestModel::class)->addComponent(new TestDataComponent());
        static::assertInstanceOf(TestDataComponent::class, $dataTable->testdatacomponent);
    }

    /**
     * @test
     */
    public function component_can_be_added_and_accessed_directly_by_name()
    {
        $dataTable = DataTable::model(TestModel::class)->addComponent(new TestDataComponent(), 'myawesomecomponent');
        static::assertInstanceOf(TestDataComponent::class, $dataTable->myawesomecomponent);
    }

    /**
     * @test
     */
    public function component_can_not_be_added_twice_with_same_name()
    {
        $dataTable = DataTable::model(TestModel::class)->addComponent(new TestDataComponent(), 'myawesomecomponent');
        $this->expectException(MultipleComponentAssertionException::class);
        $dataTable->addComponent(new TestDataComponent(), 'myawesomecomponent');
    }

    /**
     * @test
     */
    public function normal_properties_are_returned_correctly()
    {
        $testValue = 'test';
        $dataTable = DataTable::model(TestModel::class);
        $dataTable->test = $testValue;
        static::assertEquals($testValue, $dataTable->test);
        DataTable::shouldReceive('test')->andReturn($testValue);
    }

    /**
     * @test
     */
    public function can_check_whether_component_exists()
    {
        $dataTable = DataTable::model(TestModel::class);
        static::assertFalse($dataTable->componentExists('testdatacomponent'));
        $dataTable->addComponent(new TestDataComponent());
        static::assertTrue($dataTable->componentExists('testdatacomponent'));
    }
}