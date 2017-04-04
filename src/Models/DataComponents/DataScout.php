<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;

class DataScout extends DataComponent {

    /** @var array */
    private $_searchQueries = [];

    /**
     * @return Builder
     */
    public function shapeData(): Builder
    {
        foreach ($this->_searchQueries as $field => $value)
        {
            $this->_queryBuilder->where($field, 'like', '%' . $value . '%');
        }

        return $this->_queryBuilder;
    }

    /**
     * Add a query programmatically.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function addQuery(string $field, string $value)
    {
        $this->_searchQueries[$field] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        // TODO: Post request and "controller"

        return '<form method="get" action=""><input class="data-scout-input" placeholder="Search.." /><button type="submit" class="btn btn-primary">Search</button></form>';
    }
}