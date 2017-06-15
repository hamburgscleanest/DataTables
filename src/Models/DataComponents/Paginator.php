<?php

namespace hamburgscleanest\DataTables\Models\DataComponents;

use hamburgscleanest\DataTables\Facades\UrlHelper;
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
    private $_firstPageSymbol = 'first';

    /** @var string */
    private $_previousPageSymbol = '←';

    /** @var string */
    private $_nextPageSymbol = '→';

    /** @var string */
    private $_lastPageSymbol = 'last';

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

    /**
     * How many entries per page?
     *
     * @param int $perPage
     * @return Paginator
     */
    public function entriesPerPage($perPage = 15) : Paginator
    {
        $this->_perPage = $perPage;

        return $this;
    }

    /**
     * How many surrounding pages should be shown?
     *
     * @param int $count
     * @return Paginator
     */
    public function surroundingPages($count = 2) : Paginator
    {
        $this->_surroundingPages = $count;

        return $this;
    }

    /**
     * Render the page links.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function render() : string
    {
        if ($this->_perPage === 0)
        {
            return '<ul class="pagination"><li class="active">1</li></ul>';
        }

        return '<ul class="pagination">' .
               $this->_renderListItem($this->_currentPage - 1, $this->_getFirstPageUrl(), $this->_firstPageSymbol) .
               $this->_renderListItem($this->_currentPage - 1, $this->_getPreviousPageUrl(), $this->_previousPageSymbol) .
               $this->_renderPageList() .
               $this->_renderListItem($this->_currentPage + 1, $this->_getNextPageUrl(), $this->_nextPageSymbol) .
               $this->_renderListItem($this->_currentPage + 1, $this->_getLastPageUrl(), $this->_lastPageSymbol) .
               '</ul>';
    }

    /**
     * Renders a list item with a page link.
     *
     * @param int $pagenumber
     * @param string $url
     * @param string|null $symbol
     *
     * @return string
     */
    private function _renderListItem(int $pagenumber, ? string $url, ? string $symbol = null) : string
    {
        if ($url === null)
        {
            return '';
        }

        return '<li' . ($pagenumber === $this->_currentPage ? ' class="active"' : '') . '><a href="' . $url . '">' . ($symbol ?? $pagenumber) . '</a></li>';
    }

    /**
     * @return null|string
     * @throws \RuntimeException
     */
    private function _getFirstPageUrl() : ? string
    {
        if ($this->_currentPage <= $this->_surroundingPages + 1)
        {
            return null;
        }

        return $this->_buildPageUrl(1);
    }

    /**
     * Generate URL to jump to {$pageNumber}.
     *
     * @param int $pageNumber
     * @return string
     *
     * @throws \RuntimeException
     */
    private function _buildPageUrl(int $pageNumber) : string
    {
        $parameters = UrlHelper::queryParameters();
        $parameters['page'] = $pageNumber;

        return \request()->url() . '?' . \http_build_query($parameters);
    }

    /**
     * @return null|string
     * @throws \RuntimeException
     */
    private function _getPreviousPageUrl() : ? string
    {
        $previousPage = $this->_currentPage - 1;
        if ($previousPage < 1)
        {
            return null;
        }

        return $this->_buildPageUrl($previousPage);
    }

    /**
     * Renders a list of pages.
     *
     * @return string
     * @throws \RuntimeException
     */
    private function _renderPageList() : string
    {
        $end = $this->_getEndPage();

        $pageList = '';
        for ($i = $this->_getStartPage(); $i <= $end; $i ++)
        {
            $pageList .= $this->_renderListItem($i, $this->_buildPageUrl($i));
        }

        return $pageList;
    }

    /**
     * @return int
     */
    private function _getEndPage() : int
    {
        $end = $this->_currentPage + $this->_surroundingPages;
        $pageCount = $this->pageCount();

        return $end > $pageCount ? $pageCount : $end;
    }

    /**
     * @return int
     */
    public function pageCount() : int
    {
        if (empty($this->_perPage))
        {
            return 1;
        }

        $queryCount = $this->getQueryCount();
        if ($queryCount < $this->_perPage)
        {
            return 1;
        }

        return (int) \ceil($queryCount / $this->_perPage);
    }

    /**
     * @return int
     */
    private function _getStartPage() : int
    {
        $start = $this->_currentPage - $this->_surroundingPages;

        return $start < 1 ? 1 : $start;
    }

    /**
     * @return null|string
     * @throws \RuntimeException
     */
    private function _getNextPageUrl() : ? string
    {
        if ($this->_currentPage >= $this->pageCount())
        {
            return null;
        }

        return $this->_buildPageUrl($this->_currentPage + 1);
    }

    /**
     * @return null|string
     * @throws \RuntimeException
     */
    private function _getLastPageUrl() : ? string
    {
        $lastPage = $this->pageCount();
        if ($this->_currentPage + $this->_surroundingPages >= $lastPage)
        {
            return null;
        }

        return $this->_buildPageUrl($lastPage);
    }

    /**
     * @return Builder
     */
    protected function _shapeData() : Builder
    {
        if (empty($this->_perPage))
        {
            return $this->_dataTable->query();
        }

        return $this->_dataTable->query()->limit($this->_perPage)->offset(($this->_currentPage - 1) * $this->_perPage);
    }

    protected function _afterInit() : void
    {
        $this->_currentPage = + \request()->get('page', 1);
    }
}