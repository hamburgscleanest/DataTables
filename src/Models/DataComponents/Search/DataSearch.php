<?php

namespace hamburgscleanest\DataTables\Models\DataComponents\Search;

use hamburgscleanest\DataTables\Facades\UrlHelper;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DataSearch
 * @package hamburgscleanest\DataTables\Models\DataComponents\Search
 */
abstract class DataSearch {

    /** @var array */
    protected $_searchableFields;

    /** @var array */
    protected $_searchQueries = [];

    /**
     * DataSearch constructor.
     * @param array $searchableFields
     */
    public function __construct(array $searchableFields = [])
    {
        $this->_searchableFields = $searchableFields;
    }

    /**
     * @param string $field
     * @return DataSearch
     */
    public function addField(string $field) : DataSearch
    {
        $this->_searchableFields[] = $field;

        return $this;
    }

    /**
     * @param string $query
     * @return DataSearch
     */
    public function addQuery(string $query) : DataSearch
    {
        $this->_searchQueries[] = $query;

        return $this;
    }

    /**
     * @param array $queries
     * @return DataSearch
     */
    public function addQueries(array $queries) : DataSearch
    {
        $this->_searchQueries += $queries;

        return $this;
    }

    /**
     * @return int
     */
    public function queryCount() : int
    {
        return \count($this->_searchQueries);
    }

    /**
     * @param Builder $queryBuilder
     * @return Builder
     */
    public function searchData(Builder $queryBuilder) : Builder
    {
        return $queryBuilder->where(function($query) {
            foreach ($this->_searchQueries as $value)
            {
                $this->_searchFields($query, $value);
            }
        });
    }

    /**
     * @param Builder $queryBuilder
     * @param string $value
     * @return void
     */
    abstract protected function _searchFields(Builder $queryBuilder, string $value) : void;

    /**
     * @return string
     */
    public function getSearchUrl() : string
    {
        $parameters = UrlHelper::queryParameters();
        $parameters['search'] = \implode(',', $this->_searchQueries);

        return \request()->url() . '?' . \http_build_query($parameters);
    }
}