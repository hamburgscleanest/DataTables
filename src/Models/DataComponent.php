<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class DataComponent {

    /** @var Builder */
    protected $_queryBuilder;

    /** @var Request */
    protected $_request;

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

        $this->_afterInit();
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