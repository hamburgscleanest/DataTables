<?php

namespace hamburgscleanest\DataTables\Models\DataComponents\Search;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SimpleSearch
 * @package hamburgscleanest\DataTables\Models\DataComponents\Search
 */
class SimpleSearch extends DataSearch {

    /**
     * @param Builder $queryBuilder
     * @param string $value
     * @return void
     */
    protected function _searchFields(Builder $queryBuilder, string $value) : void
    {
        foreach ($this->_searchableFields as $field)
        {
            $this->_searchField($queryBuilder, $field, $value);
        }
    }

    /**
     * @param Builder $queryBuilder
     * @param string $fieldName
     * @param string $value
     * @return Builder
     */
    private function _searchField(Builder $queryBuilder, string $fieldName, string $value) : Builder
    {
        return $queryBuilder->orWhere($fieldName, 'like', '%' . $value . '%');
    }
}