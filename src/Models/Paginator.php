<?php

namespace hamburgscleanest\DataTables\Models;

use function array_map;
use function explode;
use function http_build_url;
use const HTTP_URL_JOIN_QUERY;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use function parse_url;

class Paginator {

    /** @var Builder */
    private $_queryBuilder;

    /** @var Request */
    private $_request;

    /** @var int */
    private $_perPage = 0;

    /** @var int */
    private $_currentPage = 1;

    public function __construct(Builder $queryBuilder, Request $request)
    {
        $this->_queryBuilder = $queryBuilder;
        $this->_request = $request;

        $this->_currentPage = + $this->_request->get('page', 1);
    }

    /**
     * How many entries per page?
     *
     * @param int $perPage
     * @return $this
     */
    public function paginate($perPage = 15)
    {
        $this->_perPage = $perPage;

        return $this;
    }

    /**
     * @return Builder
     */
    public function doPagination()
    {
        if ($this->_perPage === 0)
        {
            return $this->_queryBuilder;
        }

        return $this->_queryBuilder->limit($this->_perPage)->offset(($this->_currentPage - 1) * $this->_perPage);
    }

    /**
     * @return int
     */
    public function pageCount()
    {
        return (int) \floor($this->_queryBuilder->count() / $this->_perPage);
    }

    private function _getPreviousPageUrl()
    {
        $previousPage = $this->_currentPage - 1;

        if ($previousPage <= 1)
        {
            return null;
        }

        return $this->_buildPageUrl($previousPage);
    }

    private function _getNextPageUrl()
    {
        $nextPage = $this->_currentPage + 1;

        if ($nextPage >= $this->pageCount())
        {
            return null;
        }

        return $this->_buildPageUrl($nextPage);
    }

    private function _buildPageUrl($pageNumber)
    {
        // TODO: Build URI

        return $this->_request->getUri();
    }

    private function _renderListItem($pagenumber, $url)
    {
        if ($url === null)
        {
            return '';
        }

        return '<li><a href="' . $url . '">' . $pagenumber . '</a></li>';
    }

    public function render()
    {
        if ($this->_perPage === 0)
        {
            return '';
        }

        return '<ul>' .
               $this->_renderListItem($this->_currentPage - 1, $this->_getPreviousPageUrl()) .
               $this->_renderListItem($this->_currentPage + 1, $this->_getNextPageUrl()) .
               '</ul>';
    }
}