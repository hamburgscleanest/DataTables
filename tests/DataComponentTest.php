<?php

namespace hamburgscleanest\DataTables\Tests;

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

        $this->assertTrue($testComponent->afterInitCalled);
    }

    /**
     * @test
     */
    public function remembers_state()
    {
        $testComponent = new TestDataComponent();
        $testComponent->remember();
        DataTable::model(TestModel::class)->addComponent($testComponent);

        $this->assertTrue($testComponent->afterInitCalled);
        $this->assertTrue($testComponent->readFromSessionCalled);
    }

    /**
     * @test
     */
    public function saves_state()
    {
        $testComponent = new TestDataComponent();
        $testComponent->remember();
        $dataTable = DataTable::model(TestModel::class)->addComponent($testComponent);

        $this->assertTrue($testComponent->afterInitCalled);
        $this->assertTrue($testComponent->readFromSessionCalled);

        $dataTable->render();

        $this->assertTrue($testComponent->storeInSessionCalled);
    }
}