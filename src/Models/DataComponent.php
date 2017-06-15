<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DataComponent
 * @package hamburgscleanest\DataTables\Models
 */
abstract class DataComponent {

    /** @var bool */
    protected $_rememberState = false;

    /** @var string */
    protected $_rememberKey = 'global';

    /** @var DataTable */
    protected $_dataTable;

    /**
     * You cannot count the data when ordering.
     * Disables ordering temporary.
     *
     * @return int
     */
    public function getQueryCount() : int
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        $query = $this->_dataTable->query()->getQuery();

        [$oldOrders, $oldLimit, $oldOffset] = [$query->orders, $query->limit, $query->offset];
        $query->orders = $query->limit = $query->offset = null;

        $dataCount = $query->count();

        $query->orders = $oldOrders;
        $query->limit = $oldLimit;
        $query->offset = $oldOffset;

        return $dataCount;
    }

    /**
     * @param DataTable $dataTable
     *
     * @return DataComponent
     */
    public function init(DataTable $dataTable) : DataComponent
    {
        $this->_dataTable = $dataTable;

        if ($this->_rememberState)
        {
            $this->_readFromSession();
        }

        $this->_afterInit();

        return $this;
    }

    /**
     * Everything that needs to be read when the state is remembered.
     * Is called before _afterInit(), so that session values can be overriden.
     */
    protected function _readFromSession() : void
    {

    }

    /**
     * Initalize fields after the query builder instance is set.
     */
    protected function _afterInit() : void
    {

    }

    /**
     * Remember the state of the data component.
     *
     * @return DataComponent
     */
    public function remember() : DataComponent
    {
        $this->_rememberState = true;

        return $this;
    }

    /**
     * Forget the state of the data component.
     *
     * @return DataComponent
     */
    public function forget() : DataComponent
    {
        $this->_rememberState = false;
        SessionHelper::removeState($this->_rememberKey);

        return $this;
    }

    public function transformData() : void
    {
        if ($this->_rememberState)
        {
            $this->_storeInSession();
        }
        $this->_shapeData();
    }

    /**
     * Use this function to save your state in the session.
     * This is called just before rendering, so all dynamically added stuff etc. is considered.
     */
    protected function _storeInSession() : void
    {

    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return \mb_strtolower(\class_basename($this));
    }

    /**
     * @return Builder
     */
    abstract protected function _shapeData() : Builder;

    /**
     * @return string
     */
    abstract public function render() : string;
}