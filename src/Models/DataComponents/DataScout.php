<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Models\DataComponent;
use hamburgscleanest\DataTables\Models\DataComponents\Search\DataSearch;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DataScout
 * @package hamburgscleanest\DataTables\Models\DataComponents
 */
class DataScout extends DataComponent {

    /** @var string */
    private $_placeholder = 'Search..';

    /** @var DataSearch */
    private $_dataSearch;

    /**
     * DataScout constructor.
     * @param DataSearch $dataSearch
     * @param bool $remember
     */
    public function __construct(DataSearch $dataSearch, $remember = false)
    {
        $this->_dataSearch = $dataSearch;
        $this->_rememberKey = 'data-scout';
        $this->_rememberState = $remember;
    }

    /**
     * @return Builder
     */
    public function _shapeData() : Builder
    {
        if ($this->_dataSearch->queryCount() === 0)
        {
            return $this->_dataTable->query();
        }

        return $this->_dataSearch->searchData($this->_dataTable->query());
    }

    /**
     * How should the dataset be searched?
     * Define the search algorithm, e.g. SimpleSearch or FulltextSearch
     *
     * @param DataSearch $dataSearch
     * @return DataScout
     */
    public function setSearch(DataSearch $dataSearch) : DataScout
    {
        $this->_dataSearch = $dataSearch;

        return $this;
    }

    /**
     * Add a query programmatically.
     *
     * @param string $query
     * @return DataScout
     */
    public function addQuery(string $query) : DataScout
    {
        $this->_dataSearch->addQuery($query);

        return $this;
    }

    /**
     * Set the placeholder for the input.
     *
     * @param string $text
     * @return DataScout
     */
    public function placeholder(string $text) : DataScout
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
    public function makeSearchable(string $field) : DataScout
    {
        $this->_dataSearch->addField($field);

        return $this;
    }

    /**
     * @return string
     */
    public function render() : string
    {
        return '<input name="' . $this->getName() . '-search" class="form-control datascout-input" placeholder="' . $this->_placeholder . '"/>';
    }

    /**
     * @return string
     */
    public function getSearchUrl() : string
    {
        return $this->_dataSearch->getSearchUrl();
    }

    protected function _afterInit() : void
    {
        $search = \request()->get('search');
        if (!empty($search))
        {
            $this->_dataSearch->addQueries(\explode(',', $search));
        }
    }
}