<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class Sorter
 * @package hamburgscleanest\DataTables\Models
 */
class Sorter {

    /** @var Builder */
    private $_queryBuilder;

    /** @var Request */
    private $_request;

    /** @var array */
    private $_sortFields;

    /**
     * Sorter constructor.
     * @param Builder $queryBuilder
     * @param Request $request
     */
    public function __construct(Builder $queryBuilder, Request $request)
    {
        $this->_queryBuilder = $queryBuilder;
        $this->_request = $request;

        $this->_sortFields = \array_map(
            function ($sorting)
            {
                if (mb_strpos($sorting, ':') === false)
                {
                    $sorting .= ':asc';
                }

                return $sorting;
            },
            \explode(',', $this->_request->get('sort', []))
        );
    }

    /**
     * @return Builder
     */
    public function doSorting()
    {
        if (\count($this->_sortFields) > 0)
        {
            foreach ($this->_sortFields as $sortField)
            {
                $sorting = \explode(':', $sortField);

                $this->_queryBuilder->orderBy($sorting[0], $sorting[1]);
            }
        }

        return $this->_queryBuilder;
    }

    /**
     * Sort by this column.
     *
     * @param string $field
     * @param string $direction
     */
    public function addField(string $field, string $direction = 'asc')
    {
        $this->_sortFields[] = $field . ':' . $direction;
    }
}