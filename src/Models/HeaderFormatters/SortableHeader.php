<?php

namespace hamburgscleanest\DataTables\Models\HeaderFormatters;

use hamburgscleanest\DataTables\Helpers\SessionHelper;
use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;

/**
 * Class SortableHeader
 *
 * Either whitelist the headers you want to make sortable
 * or blacklist the headers you do not want to become sortable.
 *
 * If nothing is specified, all headers are sortable.
 *
 * @package hamburgscleanest\DataTables\Models
 */
class SortableHeader implements HeaderFormatter {

    const SORTING_SEPARATOR = '~';
    const COLUMN_SEPARATOR  = '.';

    /** @var array */
    private $_sortableHeaders;

    /** @var array */
    private $_dontSort;

    /** @var array */
    private $_sortingSymbols = [
        'asc'  => '&#x25B2;',
        'desc' => '&#x25BC;',
        'none' => '⇵'
    ];

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
     * @param Request $request
     * @return array
     */
    private function _extractSortFields(Request $request)
    {
        $sorting = SessionHelper::getState($request, 'sort', []);

        $sortFields = $request->get('sort');
        if (empty($sortFields))
        {
            return $sorting;
        }

        foreach (\explode(self::COLUMN_SEPARATOR, $sortFields) as $field)
        {
            $sortParts = \explode(self::SORTING_SEPARATOR, $field);
            if (\count($sortParts) === 1)
            {
                $sortParts[1] = 'asc';
            }

            $sorting[$sortParts[0]] = \mb_strtolower($sortParts[1]);
        }

        return $sorting;
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
     * Adds a link to sort by this header/column.
     * Also indicates how the columns are sorted (when sorted).
     *
     * @param array $header
     * @param Request $request
     * @throws \RuntimeException
     */
    public function format(array &$header, Request $request)
    {
        $sortFields = $this->_extractSortFields($request);
        $direction = $sortFields[$header['attribute']] ?? 'none';
        $header['name'] .= ' <span class="sort-symbol">' . ($this->_sortingSymbols[$direction] ?? '') . '</span>';

        if (\count($this->_sortableHeaders) === 0 || \in_array($header['attribute'], $this->_sortableHeaders, true))
        {
            $header['name'] = '<a class="sortable-header" href="' . $this->_buildSortUrl($request, $header['attribute'], $direction) . '">' . $header['name'] . '</a>';
        }
    }
}