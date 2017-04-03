<?php

namespace hamburgscleanest\DataTables\Models;

use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class Paginator extends DataComponent {

    /** @var int */
    private $_perPage = 15;

    /** @var int */
    private $_currentPage;

    /** @var int */
    private $_totalItemCount;

    /** @var string */
    private $_previousPageSymbol = '←';

    /** @var string */
    private $_nextPageSymbol = '→';


    protected function _afterInit()
    {
        $this->_totalItemCount = $this->_queryBuilder->count();
        $this->_currentPage = + $this->_request->get('page', 1);
    }

    /**
     * How many entries per page?
     *
     * @param int $perPage
     * @return $this
     */
    public function entriesPerPage($perPage = 15)
    {
        $this->_perPage = $perPage;

        return $this;
    }

    /**
     * @return Builder
     */
    public function shapeData(): Builder
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
        if ($this->_perPage === 0)
        {
            return 1;
        }

        return (int) \floor($this->_totalItemCount / $this->_perPage);
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function _getPreviousPageUrl()
    {
        $previousPage = $this->_currentPage - 1;
        if ($previousPage <= 0)
        {
            return null;
        }

        return $this->_buildPageUrl($previousPage);
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function _getNextPageUrl()
    {
        $nextPage = $this->_currentPage + 1;
        if ($nextPage >= $this->pageCount())
        {
            return null;
        }

        return $this->_buildPageUrl($nextPage);
    }

    /**
     * @param string $queryString
     *
     * @return array
     * @throws \RuntimeException
     */
    private function _parameterizeQuery(?string $queryString)
    {
        if (empty($queryString))
        {
            return [];
        }

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

    /**
     * Generate URL to jump to {$pageNumber}.
     *
     * @param int $pageNumber
     * @return string
     *
     * @throws \RuntimeException
     */
    private function _buildPageUrl(int $pageNumber)
    {
        $parameters = $this->_parameterizeQuery($this->_request->getQueryString());
        $parameters['page'] = $pageNumber;

        return $this->_request->url() . '?' . http_build_query($parameters);
    }

    /**
     * Renders a list item with a page link.
     *
     * @param string $pagenumber
     * @param string $url
     * @param string $symbol
     *
     * @return string
     */
    private function _renderListItem(string $pagenumber, ?string $url, ?string $symbol = null)
    {
        if ($url === null)
        {
            return '';
        }

        if ($symbol === null)
        {
            $symbol = $pagenumber;
        }

        return '<li><a href="' . $url . '">' . $symbol . '</a></li>';
    }

    /**
     * Render the page links.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render(): string
    {
        if ($this->_perPage === 0)
        {
            return '';
        }

        return '<ul>' .
               $this->_renderListItem($this->_currentPage - 1, $this->_getPreviousPageUrl(), $this->_previousPageSymbol) .
               $this->_renderListItem($this->_currentPage + 1, $this->_getNextPageUrl(), $this->_nextPageSymbol) .
               '</ul>';
    }
}