<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;


/**
 * Class Header
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Column {

    /** @var string */
    public $name;

    /** @var ColumnFormatter */
    private $_formatter;

    /**
     * Column constructor.
     * @param string $name
     * @param ColumnFormatter|null $columnFormatter
     */
    public function __construct(string $name, ? ColumnFormatter $columnFormatter = null)
    {
        $this->name = $name;
        $this->_formatter = $columnFormatter;
    }

    /**
     * @param ColumnFormatter $columnFormatter
     * @return Column
     */
    public function setFormatter(ColumnFormatter $columnFormatter): Column
    {
        $this->_formatter = $columnFormatter;

        return $this;
    }

    /**
     * Formats the column data.
     *
     * @param string $data
     * @return string
     */
    public function format(string $data): string
    {
        return $this->_formatter !== null ? $this->_formatter->format($data) : $data;
    }
}