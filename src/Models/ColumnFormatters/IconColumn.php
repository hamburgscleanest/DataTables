<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Models\ColumnFormatters\Adapters\Icon\IconAdapter;

/**
 * Class IconColumn
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
class IconColumn implements ColumnFormatter {

    /** @var IconAdapter */
    private $_iconAdapter;

    /**
     * DateColumn constructor.
     * @param IconAdapter $iconAdapter
     * @internal param string $dateFormat
     */
    public function __construct(IconAdapter $iconAdapter)
    {
        $this->_iconAdapter = $iconAdapter;
    }

    /**
     * @param string $column
     * @return string
     */
    public function format(string $column) : string
    {
        return $this->_iconAdapter->format($column);
    }
}