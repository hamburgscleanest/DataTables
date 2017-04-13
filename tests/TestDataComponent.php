<?php

namespace hamburgscleanest\DataTables\Tests;

use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TestDataComponent
 * @package hamburgscleanest\DataTables\tests
 */
class TestDataComponent extends DataComponent {

    /** @var bool */
    public $afterInitCalled = false;

    /** @var bool */
    public $readFromSessionCalled = false;

    /** @var bool */
    public $storeInSessionCalled = false;

    protected function _afterInit()
    {
        $this->afterInitCalled = true;
    }

    protected function _readFromSession()
    {
        $this->readFromSessionCalled = true;
    }

    protected function _storeInSession()
    {
        $this->storeInSessionCalled = true;
    }

    /**
     * @return Builder
     */
    protected function _shapeData() : Builder
    {
        return TestModel::query();
    }

    /**
     * @return string
     */
    public function render() : string
    {
        return 'TEST-RENDER';
    }
}