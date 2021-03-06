<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Facades\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use hamburgscleanest\DataTables\Models\Header;

/**
 * Class SortableHeader
 *
 * Either whitelist the headers you want to make sortable
 * or blacklist the headers you do not want to become sortable.
 *
 * If nothing is specified, all headers are sortable.
 *
 * @package hamburgscleanest\DataTables\Models\HeaderFormatters
 */
class SortableHeader implements HeaderFormatter {

    const SORTING_SEPARATOR = '~';
    const COLUMN_SEPARATOR  = '.';

    /** @var array */
    private $_sortableHeaders;

    /** @var array */
    private $_dontSort;

    /** @var array */
    private $_sortingSymbols = ['asc' => '&#x25B2;', 'desc' => '&#x25BC;', 'none' => '⇵'];

    /**
     * SortableHeader constructor.
     *
     * @param array $sortableHeaders
     * @param array $dontSort
     */
    public function __construct(array $sortableHeaders = [], array $dontSort = [])
    {
        $this->_sortableHeaders = $sortableHeaders;
        $this->_dontSort = $dontSort;

    }

    /**
     * @param array $sortingSymbols
     * @return SortableHeader
     */
    public function sortingSymbols(array $sortingSymbols) : SortableHeader
    {
        $this->_sortingSymbols = \array_merge($this->_sortingSymbols, $sortingSymbols);

        return $this;
    }

    /**
     * Add a field to the sortable fields.
     *
     * @param string $header
     * @return SortableHeader
     */
    public function makeSortable(string $header) : SortableHeader
    {
        $this->_sortableHeaders[] = $header;
        $this->_removeIndex($this->_dontSort, $header);

        return $this;
    }

    /**
     * @param array $array
     * @param string $key
     */
    private function _removeIndex(array $array, string $key) : void
    {
        $index = \array_search($key, $array, true);
        if ($index !== false)
        {
            unset($array[$index]);
        }
    }

    /**
     * Remove the ability to sort by this column/header.
     *
     * @param string $header
     * @return SortableHeader
     */
    public function dontSort(string $header) : SortableHeader
    {
        $this->_dontSort[] = $header;
        $this->_removeIndex($this->_sortableHeaders, $header);

        return $this;
    }

    /**
     * Adds a link to sort by this header/column.
     * Also indicates how the columns are sorted (when sorted).
     *
     * @param Header $header
     * @throws \RuntimeException
     */
    public function format(Header $header) : void
    {
        $headerAttributeName = $header->getAttributeName();
        $sortFields = $this->_extractSortFields();
        $direction = $sortFields[$headerAttributeName] ?? 'none';

        if ($this->_showSortLink($headerAttributeName))
        {
            $header->key = '<a class="sortable-header" href="' . (\request()->url() . '?' . $this->_buildSortQuery($headerAttributeName, $direction)) . '">' .
                           $header->key . ' <span class="sort-symbol">' . $this->_sortingSymbols[$direction] . '</span></a>';
        }
    }

    /**
     * @return array
     */
    private function _extractSortFields() : array
    {
        return \array_diff(
            $this->_getRememberedState() + $this->_getDefaultSorting($this->_sortableHeaders),
            $this->_getDefaultSorting($this->_dontSort)
        );
    }

    /**
     * @return array
     */
    private function _getRememberedState() : array
    {
        return (array) SessionHelper::getState('sort', []);
    }

    /**
     * @param array $sortFields
     * @return array
     */
    private function _getDefaultSorting(array $sortFields) : array
    {
        $sorting = [];
        foreach ($sortFields as $field)
        {
            $sorting[$field] = 'none';
        }

        return $sorting;
    }

    /**
     * @param $headerAttributeName
     * @return bool
     */
    private function _showSortLink(string $headerAttributeName) : bool
    {
        return \count($this->_sortableHeaders + $this->_dontSort) === 0 ||
               (\in_array($headerAttributeName, $this->_sortableHeaders, true) && !\in_array($headerAttributeName, $this->_dontSort, true));
    }

    /**
     * @param string $columnName
     * @param string $oldDirection
     * @return string
     * @throws \RuntimeException
     */
    private function _buildSortQuery(string $columnName, string & $oldDirection) : string
    {
        $parameters = UrlHelper::queryParameters();
        if (!isset($parameters['sort']))
        {
            $parameters['sort'] = '';
        }

        $queryDirection = $this->_getDirectionFromQuery($columnName, $parameters['sort']);
        if ($queryDirection !== null)
        {
            /** @var string $queryDirection */
            $oldDirection = $queryDirection;
        }

        return \http_build_query($this->_getQueryParameters($parameters, $columnName, $oldDirection));
    }

    /**
     * @param string $columnName
     * @param string $queryString
     * @return null|string
     */
    private function _getDirectionFromQuery(string $columnName, string $queryString) : ? string
    {
        $column = $columnName . self::SORTING_SEPARATOR;
        $columnPos = \mb_strpos($queryString, $column);

        if ($columnPos === false)
        {
            return null;
        }

        $sortValue = \mb_substr($queryString, $columnPos + \mb_strlen($column));
        $dividerPos = \mb_strpos($sortValue, self::COLUMN_SEPARATOR);
        if ($dividerPos !== false)
        {
            $sortValue = \mb_substr($sortValue, 0, $dividerPos);
        }

        return $sortValue;
    }

    /**
     * @param array $parameters
     * @param string $columnName
     * @param string $oldDirection
     * @return array
     */
    private function _getQueryParameters(array $parameters, string $columnName, string $oldDirection) : array
    {
        $newSorting = $columnName . self::SORTING_SEPARATOR . $this->_getNewDirection($oldDirection);
        if (!$this->_replaceOldSort($columnName, $parameters, $oldDirection, $newSorting))
        {
            $this->_addSortParameter($parameters, $newSorting);
        }

        return $parameters;
    }

    /**
     * Get the next sorting direction.
     *
     * @param string $oldDirection
     * @return string
     */
    private function _getNewDirection(string $oldDirection) : string
    {
        switch ($oldDirection)
        {
            case 'asc':
                $newDirection = 'desc';
                break;
            case 'desc':
                $newDirection = 'none';
                break;
            default:
                $newDirection = 'asc';
        }

        return $newDirection;
    }

    /**
     * @param string $columnName
     * @param array $parameters
     * @param string $oldDirection
     * @param string $newSorting
     * @return bool
     */
    private function _replaceOldSort(string $columnName, array &$parameters, string $oldDirection, string $newSorting) : bool
    {
        $replacedCount = 0;
        $columnRegex = '/(^|\\' . self::COLUMN_SEPARATOR . ')' . $columnName . '(' . self::SORTING_SEPARATOR . $oldDirection . '|)/';
        $parameters['sort'] = \preg_replace($columnRegex, self::COLUMN_SEPARATOR . $newSorting, $parameters['sort'], 1, $replacedCount);
        if (!empty($parameters['sort']) && $parameters['sort'][0] === self::COLUMN_SEPARATOR)
        {
            $parameters['sort'] = \mb_substr($parameters['sort'], 1);
        }

        return $replacedCount > 0;
    }

    /**
     * @param array $parameters
     * @param string $newSorting
     */
    private function _addSortParameter(array &$parameters, string $newSorting) : void
    {
        if (!empty($parameters['sort']))
        {
            $newSorting = self::COLUMN_SEPARATOR . $newSorting;
        }
        $parameters['sort'] .= $newSorting;
    }
}