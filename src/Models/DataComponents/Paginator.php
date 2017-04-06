<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Helpers\UrlHelper;
use hamburgscleanest\DataTables\Models\DataComponent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Paginator
 * @package hamburgscleanest\DataTables\Models\DataComponents
 */
class Paginator extends DataComponent {

    /** @var int */
    private $_perPage;

    /** @var int */
    private $_currentPage;

    /** @var string */
    private $_previousPageSymbol = '←';

    /** @var string */
    private $_nextPageSymbol = '→';

    /** @var int */
    private $_surroundingPages = 2;


    /**
     * Paginator constructor.
     * @param int $perPage
     */
    public function __construct(int $perPage = 15)
    {
        $this->_perPage = $perPage;
    }

    protected function _afterInit()
    {
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
     * How many surrounding pages should be shown?
     *
     * @param int $count
     * @return $this
     */
    public function surroundingPages($count = 2)
    {
        $this->_surroundingPages = $count;

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
    public function pageCount(): int
    {
        if ($this->_perPage === 0)
        {
            return 1;
        }

        $queryCount = $this->getQueryCount();
        if ($queryCount < $this->_perPage)
        {
            return 1;
        }

        return (int) \floor($queryCount / $this->_perPage);
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function _getPreviousPageUrl()
    {
        $previousPage = $this->_currentPage - 1;
        if ($previousPage < 1)
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
        if ($nextPage > $this->pageCount())
        {
            return null;
        }

        return $this->_buildPageUrl($nextPage);
    }

    /**
     * Generate URL to jump to {$pageNumber}.
     *
     * @param int $pageNumber
     * @return string
     *
     * @throws \RuntimeException
     */
    private function _buildPageUrl(int $pageNumber): string
    {
        $parameters = UrlHelper::parameterizeQuery($this->_request->getQueryString());
        $parameters['page'] = $pageNumber;

        return $this->_request->url() . '?' . \http_build_query($parameters);
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
    private function _renderListItem(string $pagenumber, ?string $url, ?string $symbol = null): string
    {
        if ($url === null)
        {
            return '';
        }

        if ($symbol === null)
        {
            $symbol = $pagenumber;
        }

        $class = '';
        if (+ $pagenumber === $this->_currentPage)
        {
            $class = ' class="active"';
        }

        return '<li' . $class . '><a href="' . $url . '">' . $symbol . '</a></li>';
    }

    /**
     * Renders a list of pages.
     *
     * @return string
     * @throws \RuntimeException
     */
    private function _renderPageList(): string
    {
        $start = $this->_currentPage - $this->_surroundingPages;
        if ($start < 1)
        {
            $start = 1;
        }

        $pageCount = $this->pageCount();
        $end = $this->_currentPage + $this->_surroundingPages;
        if ($end > $pageCount)
        {
            $end = $pageCount;
        }

        $pageList = '';
        for ($i = $start; $i <= $end; $i ++)
        {
            $pageList .= $this->_renderListItem($i, $this->_buildPageUrl($i));
        }

        return $pageList;
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

        return '<ul class="pagination">' .
               $this->_renderListItem($this->_currentPage - 1, $this->_getPreviousPageUrl(), $this->_previousPageSymbol) .
               $this->_renderPageList() .
               $this->_renderListItem($this->_currentPage + 1, $this->_getNextPageUrl(), $this->_nextPageSymbol) .
               '</ul>';
    }
}