<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;

class DataScout extends DataComponent {

    /** @var array */
    private $_searchQueries = [];

    /** @var array */
    private $_searchableFields = [];

    /**
     * DataScout constructor.
     * @param array $searchableFields
     * @param bool $remember
     */
    public function __construct(array $searchableFields = [], $remember = false)
    {
        $this->_searchableFields = $searchableFields;
        $this->_rememberKey = 'data-scout';
        $this->_rememberState = $remember;
    }

    protected function _afterInit()
    {
        $search = $this->_request->get('search');
        if ($search !== null)
        {
            $this->_searchQueries += \explode(',', $search);
        }
    }

    /**
     * @return Builder
     */
    public function shapeData(): Builder
    {
        if (\count($this->_searchQueries) === 0)
        {
            return $this->_queryBuilder;
        }

        $this->_queryBuilder->where(function ($query)
        {
            foreach ($this->_searchQueries as $value)
            {
                foreach ($this->_searchableFields as $field)
                {
                    $query->orWhere($field, 'like', '%' . $value . '%');
                }
            }
        });

        return $this->_queryBuilder;
    }

    /**
     * Add a query programmatically.
     *
     * @param string $value
     * @return $this
     */
    public function addQuery(string $value)
    {
        $this->_searchQueries[] = $value;

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function makeSearchable(string $field)
    {
        $this->_searchableFields[] = $field;

        return $this;
    }

    private function _buildSearchUrl()
    {
        $parameters = UrlHelper::parameterizeQuery($this->_request->getQueryString());
        $parameters['search'] = \implode(',', $this->_searchQueries);

        return $this->_request->url() . '?' . \http_build_query($parameters);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '<form method="get" action="' . $this->_buildSearchUrl() . '"><input name="search" class="data-scout-input" placeholder="Search.." /><button type="submit" class="btn btn-primary">Search</button></form>';
    }
}