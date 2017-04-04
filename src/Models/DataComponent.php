<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class DataComponent {

    /** @var Builder */
    protected $_queryBuilder;

    /** @var Request */
    protected $_request;

    /** @var bool */
    protected $_rememberState = false;

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
     */
    public function remember()
    {
        $this->_rememberState = true;
    }

    public function transformData()
    {
        $this->_storeInSession();
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