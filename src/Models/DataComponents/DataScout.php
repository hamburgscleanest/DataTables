<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Facades\UrlHelper;
use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DataScout
 * @package hamburgscleanest\DataTables\Models\DataComponents
 */
class DataScout extends DataComponent {

    /** @var array */
    private $_searchQueries = [];

    /** @var array */
    private $_searchableFields;

    /** @var string */
    private $_buttonText = 'Search';

    /** @var string */
    private $_placeholder = 'Search..';

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

    /**
     * @return Builder
     */
    public function _shapeData(): Builder
    {
        if (\count($this->_searchQueries) === 0)
        {
            return $this->_queryBuilder;
        }

        $this->_queryBuilder->where(function($query)
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
     * @return DataScout
     */
    public function addQuery(string $value): DataScout
    {
        $this->_searchQueries[] = $value;

        return $this;
    }

    /**
     * Set the text for the search button.
     *
     * @param string $text
     * @return DataScout
     */
    public function buttonText(string $text): DataScout
    {
        $this->_buttonText = $text;

        return $this;
    }

    /**
     * Set the placeholder for the input.
     *
     * @param string $text
     * @return DataScout
     */
    public function placeholder(string $text): DataScout
    {
        $this->_placeholder = $text;

        return $this;
    }

    /**
     * Make the field searchable.
     *
     * @param string $field
     * @return DataScout
     */
    public function makeSearchable(string $field): DataScout
    {
        $this->_searchableFields[] = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '<form method="get" action="' . $this->_buildSearchUrl() .
                '"><div class="row"><div class="col-md-10"><input name="search" class="form-control data-scout-input" placeholder="' .
                $this->_placeholder . '"/></div><div class="col-md-2"><button type="submit" class="btn btn-primary">' .
                $this->_buttonText . '</button></div></div></form>';
    }

    /**
     * @return string
     */
    private function _buildSearchUrl()
    {
        $parameters = UrlHelper::queryParameters();
        $parameters['search'] = \implode(',', $this->_searchQueries);

        return $this->_request->url() . '?' . \http_build_query($parameters);
    }

    protected function _afterInit()
    {
        $search = $this->_request->get('search');
        if (!empty($search))
        {
            $this->_searchQueries += \explode(',', $search);
        }
    }
}