<?php

namespace hamburgscleanest\DataTables\Models\DataComponents\Search;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FulltextSearch
 * @package hamburgscleanest\DataTables\Models\DataComponents\Search
 */
class FulltextSearch extends DataSearch {

    const SUPPORTED_DRIVERS = ['mysql', 'sqlite'];

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
     * @param string $driver
     * @return FulltextSearch
     * @throws \RuntimeException
     */
    public function forceDatabaseDriver(string $driver) : FulltextSearch
    {
        if (!\in_array($driver, self::SUPPORTED_DRIVERS, true))
        {
            throw new \RuntimeException($driver . ' is not supported at the moment');
        }

        $this->_databaseDriver = $driver;

        return $this;
    }

    /**
     * Set the mode for the full text search.
     * Available modes:
     * "IN NATURAL LANGUAGE MODE", "IN BOOLEAN MODE", "WITH QUERY EXPANSION"
     * @see https://dev.mysql.com/doc/refman/5.7/en/fulltext-search.html
     *
     * @param string $mode
     * @return FulltextSearch
     */
    public function setMode(string $mode) : FulltextSearch
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

        return 'MATCH(' . \implode(',', $this->_searchableFields) . ') AGAINST (\'' . $value . '\'' . (!empty($this->_mode) ? (' ' . $this->_mode) : '') . ')';
    }
}