<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use hamburgscleanest\DataTables\Models\ColumnFormatters\Adapters\Icon\IconAdapter;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IconColumn
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
class IconColumn implements ColumnFormatter {

    /** @var IconAdapter */
    private $_iconAdapter;

    /**
     * IconColumn constructor.
     * @param IconAdapter $iconAdapter
     */
    public function __construct(IconAdapter $iconAdapter)
    {
        $this->_iconAdapter = $iconAdapter;
    }

    /**
     * @param Model $rowModel
     * @param string $column
     * @return string
     */
    public function format(Model $rowModel, string $column) : string
    {
        return $this->_iconAdapter->format($column);
    }
}