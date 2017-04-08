<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Helpers\SessionHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class DataComponent {

    /** @var Builder */
    protected $_queryBuilder;

    /** @var Request */
    protected $_request;

    /** @var bool */
    protected $_rememberState = false;

    /** @var string */
    protected $_rememberKey = 'global';

    /**
     * Everything that needs to be read when the state is remembered.
     * Is called before _afterInit(), so that session values can be overriden.
     */
    protected function _readFromSession()
    {

    }

    /**
     * Use this function to save your state in the session.
     * This is called just before rendering, so all dynamically added stuff etc. is considered.
     */
    protected function _storeInSession()
    {

    }

    /**
     * Initalize fields after the query builder instance and the request is set.
     *
     * TODO: Refactor..
     */
    protected function _afterInit()
    {

    }

    /**
     * You cannot count the data when ordering.
     * Disables ordering temporary.
     *
     * @return int
     */
    public function getQueryCount(): int
    {
        /** @var \Illuminate\Database\Query\Builder $query */
        $query = $this->_queryBuilder->getQuery();

        $oldOrders = $query->orders;
        $oldLimit = $query->limit;
        $oldOffset = $query->offset;

        $query->orders = null;
        $query->limit = null;
        $query->offset = null;

        $dataCount = $query->count();

        $query->orders = $oldOrders;
        $query->limit = $oldLimit;
        $query->offset = $oldOffset;

        return $dataCount;
    }

    /**
     * @param Request $request
     * @param Builder $queryBuilder
     */
    public function init(Builder $queryBuilder, Request $request)
    {
        $this->_request = $request;
        $this->_queryBuilder = $queryBuilder;

        if ($this->_rememberState)
        {
            $this->_readFromSession();
        }
        $this->_afterInit();
    }

    /**
     * Remember the state of the data component.
     *
     * @return $this
     */
    public function remember()
    {
        $this->_rememberState = true;

        return $this;
    }

    /**
     * Forget the state of the data component.
     *
     * @return $this
     */
    public function forget()
    {
        $this->_rememberState = false;
        if ($this->_request !== null)
        {
            SessionHelper::removeState($this->_request, $this->_rememberKey);
        }

        return $this;
    }

    public function transformData()
    {
        if ($this->_rememberState)
        {
            $this->_storeInSession();
        }
        $this->shapeData();
    }

    /**
     * @return Builder
     */
    public abstract function shapeData(): Builder;

    /**
     * @return string
     */
    public abstract function render(): string;
}