<?php

namespace hamburgscleanest\DataTables\Models\ColumnFormatters;

use Carbon\Carbon;
use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DateColumn
 * @package hamburgscleanest\DataTables\Models\ColumnFormatters
 */
class DateColumn implements ColumnFormatter {

    private $_dateFormat;

    /**
     * DateColumn constructor.
     * @param string $dateFormat
     */
    public function __construct(string $dateFormat = 'Y-m-d H:i:s')
    {
        $this->_dateFormat = $dateFormat;
    }

    /**
     * Set the format of the date column, e.g. "Y-m-d H:i:s".
     *
     * @param string $dateFormat
     * @return DateColumn
     */
    public function dateFormat(string $dateFormat) : DateColumn
    {
        $this->_dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @param Model $rowModel
     * @param string $column
     * @return string
     */
    public function format(Model $rowModel, string $column) : string
    {
        return Carbon::parse($column)->format($this->_dateFormat);
    }
}