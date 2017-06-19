<?php

namespace hamburgscleanest\DataTables\Models\DataComponents\Search;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FulltextSearch
 * @package hamburgscleanest\DataTables\Models\DataComponents\Search
 */
class FulltextSearch extends DataSearch {

    /** @var string */
    private $_mode;

    /** @var string */
    private $_databaseDriver;

    /**
     * FulltextSearch constructor.
     * @param array $searchableFields
     * @param string|null $mode
     */
    public function __construct(array $searchableFields = [], string $mode = null)
    {
        parent::__construct($searchableFields);
        $this->_mode = $mode;
        $this->_databaseDriver = \config('database.connections.' . \config('database.default') . '.driver');
    }

    /**
     * Set the mode for the full text search.
     * Available modes:
     * "IN NATURAL LANGUAGE MODE", "IN BOOLEAN MODE", "WITH QUERY EXPANSION"
     * @see https://dev.mysql.com/doc/refman/5.7/en/fulltext-search.html
     *
     * @param string $mode
     * @return DataSearch
     */
    public function setMode(string $mode) : DataSearch
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * @param Builder $queryBuilder
     * @param string $value
     * @return void
     */
    protected function _searchFields(Builder $queryBuilder, string $value) : void
    {
        $queryBuilder->orWhereRaw($this->_getMatchQuery($value));
    }

    /**
     * This has to be improved.
     * @param string $value
     * @return string
     */
    private function _getMatchQuery(string $value) : string
    {
        if ($this->_databaseDriver === 'sqlite')
        {
            $query = ' MATCH \'' . $value . '\'';
            $matchQuery = '';
            foreach ($this->_searchableFields as $field)
            {
                $matchQuery .= $field . $query;
            }

            return $matchQuery;
        }

        return 'MATCH(' . \implode(',', $this->_searchableFields) . ') AGAINST (\'' . $value . '\'' . (!empty($this->_mode) ? $this->_mode : '') . ')';
    }
}