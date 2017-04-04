<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Interfaces\HeaderFormatter;
use Illuminate\Http\Request;
use function str_replace;

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

    /** @var array */
    private $_sortableHeaders;

    /** @var array */
    private $_dontSort;

    /** @var string */
    private $_symbolAsc = '∧';

    /** @var string */
    private $_symbolDesc = '∨';

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

    private function _extractSortFields(Request $request)
    {
        $sortFields = $request->get('sort');
        if (empty($sortFields))
        {
            return [];
        }

        $sorting = [];
        foreach (\explode(',', $sortFields) as $field)
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

    private function _buildSortUrl(Request $request, string $column, string $oldDirection = 'asc')
    {
        $parameters = UrlHelper::parameterizeQuery($request->getQueryString());
        $parameters['sort'] = \str_replace(
            $column . self::SORTING_SEPARATOR . $oldDirection,
            $column . self::SORTING_SEPARATOR . ($oldDirection === 'asc' ? 'desc' : 'asc'),
            $parameters['sort']
        );

        return $request->url() . '?' . \http_build_query($parameters);
    }

    /**
     * Format the given header.
     * For example add a link to sort by this header/column.
     *
     * @param string $header
     * @param Request $request
     */
    public function format(string &$header, Request $request)
    {
        $column = $header;

        $direction = 'asc';
        $sortFields = $this->_extractSortFields($request);
        if (isset($sortFields[$column]))
        {
            $direction = $sortFields[$column];
            $header .= ' <span class="sort-symbol">' . ($direction === 'asc' ? $this->_symbolAsc : $this->_symbolDesc) . '</span>';
        }

        if (\count($this->_sortableHeaders) === 0 || \in_array($column, $this->_sortableHeaders, true))
        {
            $header = '<a class="sortable-header" href="' . $this->_buildSortUrl($request, $column, $direction) . '">' . $header . '</a>';
        }
    }
}