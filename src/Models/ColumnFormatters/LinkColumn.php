<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Models\DataLink;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LinkColumn
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
class LinkColumn implements ColumnFormatter {

    /** @var DataLink */
    private $_dataLink;

    /** @var bool */
    private $_openNew;

    /** @var string */
    private $_classes;

    /**
     * LinkColumn constructor.
     * @param string $url
     * @param null|string $classes
     * @param bool $openInNewWindow
     */
    public function __construct(string $url, ? string $classes = null, bool $openInNewWindow = false)
    {
        $this->_dataLink = new DataLink($url);
        $this->_classes = $classes;
        $this->_openNew = $openInNewWindow;
    }

    /**
     * @param string $classes
     * @return LinkColumn
     */
    public function classes(string $classes) : LinkColumn
    {
        $this->_classes = $classes;

        return $this;
    }

    /**
     * Open the link in a new window.
     *
     * @return LinkColumn
     */
    public function openInNew() : LinkColumn
    {
        $this->_openNew = true;

        return $this;
    }

    /**
     * Open the link in the same window (redirect).
     *
     * @return LinkColumn
     */
    public function openInSame() : LinkColumn
    {
        $this->_openNew = false;

        return $this;
    }

    /**
     * @param string $name
     * @param string $url
     * @return string
     */
    private function _renderLink(string $name, string $url) : string
    {
        return '<a href="' . $url . '"' .
               ($this->_openNew ? ' target="_blank"' : '') .
               (!empty($this->_classes) ? (' class="' . $this->_classes . '"') : '') .
               '>' . $name . '</a>';
    }

    /**
     * @param Model $rowModel
     * @param string $column
     * @return string
     */
    public function format(Model $rowModel, string $column) : string
    {
        return $this->_renderLink($column, $this->_dataLink->generate($rowModel));
    }
}