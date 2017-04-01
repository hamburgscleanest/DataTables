<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use RuntimeException;

class Paginator {

    /** @var Builder */
    private $_queryBuilder;

    /** @var Request */
    private $_request;

    /** @var int */
    private $_perPage = 0;

    /** @var int */
    private $_currentPage = 1;

    /** @var int */
    private $_totalPageCount = 0;

    public function __construct(Builder $queryBuilder, Request $request)
    {
        $this->_queryBuilder = $queryBuilder;
        $this->_request = $request;

        $this->_totalPageCount = $this->_queryBuilder->count();
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
    private function _doPagination()
    {
        if ($this->_perPage === 0)
        {
            return $this->_queryBuilder;
        }

        return $this->_queryBuilder->limit($this->_perPage)->offset(($this->_currentPage - 1) * $this->_perPage);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getData()
    {
        return $this->_doPagination()->get();
    }

    /**
     * @return int
     */
    public function pageCount()
    {
        return (int) \floor($this->_totalPageCount / $this->_perPage);
    }

    private function _getPreviousPageUrl()
    {
        $previousPage = $this->_currentPage - 1;
        if ($previousPage <= 0)
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

    private function _parameterizeQuery(string $queryString)
    {
        $parameters = [];
        foreach (\explode('&', $queryString) as $query)
        {
            $queryParts = \explode('=', $query);
            if (\count($queryParts) !== 2)
            {
                throw new RuntimeException('Malformed query parameters.');
            }

            $parameters[$queryParts[0]] = $queryParts[1];
        }

        return $parameters;
    }

    private function _buildPageUrl(int $pageNumber)
    {
        $parameters = $this->_parameterizeQuery($this->_request->getQueryString());
        $parameters['page'] = $pageNumber;

        return $this->_request->url() . '?' . http_build_query($parameters);
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