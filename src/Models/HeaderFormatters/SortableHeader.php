<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Facades\SessionHelper;
use hamburgscleanest\DataTables\Facades\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use hamburgscleanest\DataTables\Models\Header;
use Illuminate\Http\Request;

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

    /** @var string */
    private $_defaultDirection = 'asc';

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
     * @return $this
     */
    public function sortingSymbols(array $sortingSymbols)
    {
        $this->_sortingSymbols = $sortingSymbols;

        return $this;
    }

    /**
     * Add a field to the sortable fields.
     *
     * @param string $header
     * @return $this
     */
    public function makeSortable(string $header)
    {
        $this->_sortableHeaders[] = $header;
        $this->_removeIndex($this->_dontSort, $header);

        return $this;
    }

    /**
     * @param array $array
     * @param string $key
     */
    private function _removeIndex(array $array, string $key)
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
     * @return $this
     */
    public function dontSort(string $header)
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
     * @param Request $request
     * @throws \RuntimeException
     */
    public function format(Header $header, Request $request)
    {
        $headerAttributeName = $header->getOriginalName();

        $sortFields = $this->_extractSortFields($request);
        $direction = $sortFields[$headerAttributeName] ?? 'none';

        if ($this->_showSortLink($headerAttributeName))
        {
            $header->name = '<a class="sortable-header" href="' . $this->_buildSortUrl($request, $headerAttributeName, $direction) . '">' . $header->name .
                            ' <span class="sort-symbol">' . ($this->_sortingSymbols[$direction] ?? '') . '</span></a>';
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function _extractSortFields(Request $request)
    {
        return \array_diff(
            $this->_getSortFields($request) + $this->_getRememberedState($request) + $this->_getDefaultSorting($this->_sortableHeaders),
            $this->_getDefaultSorting($this->_dontSort)
        );
    }

    /**
     * Get the sorted fields from the request.
     *
     * @param Request $request
     * @return array
     */
    private function _getSortFields(Request $request): array
    {
        $sortFields = $request->get('sort');
        if ($sortFields === null)
        {
            return [];
        }

        $sorting = [];
        foreach (\explode(self::COLUMN_SEPARATOR, $sortFields) as $field)
        {
            $sortParts = $this->_getSortParts($field);
            $sorting[$sortParts[0]] = \mb_strtolower($sortParts[1]);
        }

        return $sorting;
    }

    /**
     * Get the name of the field and the sorting direction (default: "asc").
     *
     * @param string $field
     * @return array
     */
    private function _getSortParts(string $field): array
    {
        $sortParts = \explode(self::SORTING_SEPARATOR, $field);
        if (\count($sortParts) === 1)
        {
            $sortParts[1] = $this->_defaultDirection;
        }

        return $sortParts;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function _getRememberedState(Request $request): array
    {
        return SessionHelper::getState($request, 'sort', []);
    }

    /**
     * @param array $sortFields
     * @return array
     */
    private function _getDefaultSorting(array $sortFields): array
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
    private function _showSortLink(string $headerAttributeName): bool
    {
        return \count($this->_sortableHeaders + $this->_dontSort) === 0 ||
               (\in_array($headerAttributeName, $this->_sortableHeaders, true) && !\in_array($headerAttributeName, $this->_dontSort, true));
    }

    /**
     * @param Request $request
     * @param string $column
     * @param string $oldDirection
     * @return string
     * @throws \RuntimeException
     */
    private function _buildSortUrl(Request $request, string $column, string $oldDirection = 'asc')
    {
        $newDirection = $this->_getNewDirection($oldDirection);

        $newSorting = $column . self::SORTING_SEPARATOR . $newDirection;
        $parameters = UrlHelper::parameterizeQuery($request->getQueryString());

        if (!isset($parameters['sort']))
        {
            $parameters['sort'] = '';
        }

        $columnRegex = '/(^|\\' . self::COLUMN_SEPARATOR . ')' . $column . self::SORTING_SEPARATOR . $oldDirection . '/';
        $replacedCount = 0;
        $parameters['sort'] = \preg_replace($columnRegex, self::COLUMN_SEPARATOR . $newSorting, $parameters['sort'], 1, $replacedCount);

        if (!empty($parameters['sort']) && $parameters['sort'][0] === self::COLUMN_SEPARATOR)
        {
            $parameters['sort'] = \mb_substr($parameters['sort'], 1);
        }

        if ($replacedCount === 0)
        {
            $sorting = $newSorting;
            if (!empty($parameters['sort']))
            {
                $sorting = self::COLUMN_SEPARATOR . $sorting;
            }
            $parameters['sort'] .= $sorting;
        }

        return $request->url() . '?' . \http_build_query($parameters);
    }

    /**
     * Get the next sorting direction.
     *
     * @param string $oldDirection
     * @return string
     */
    private function _getNewDirection(string $oldDirection): string
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
}