<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
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

    protected function _afterInit()
    {
        $search = $this->_request->get('search');
        if (!empty($search))
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
     * Set the text for the search button.
     *
     * @param string $text
     * @return $this
     */
    public function buttonText(string $text)
    {
        $this->_buttonText = $text;

        return $this;
    }

    /**
     * Set the placeholder for the input.
     *
     * @param string $text
     * @return $this
     */
    public function placeholder(string $text)
    {
        $this->_placeholder = $text;

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

    /**
     * @return string
     */
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
        return '<form method="get" action="' . $this->_buildSearchUrl() .
               '"><div class="row"><div class="col-md-10"><input name="search" class="form-control data-scout-input" placeholder="' .
               $this->_placeholder . '"/></div><div class="col-md-2"><button type="submit" class="btn btn-primary">' .
               $this->_buttonText . '</button></div></div></form>';
    }
}