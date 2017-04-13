<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;


/**
 * Class Header
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Column {

    /** @var string */
    private $_name;

    /** @var Relation */
    private $_relation;

    /** @var ColumnFormatter */
    private $_formatter;

    /**
     * Column constructor.
     * @param string $name
     * @param ColumnFormatter|null $columnFormatter
     */
    public function __construct(string $name, ? ColumnFormatter $columnFormatter = null)
    {
        $this->setName($name);
        $this->_formatter = $columnFormatter;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $posDivider = \mb_strpos($name, '.');
        if ($posDivider === false)
        {
            $this->_name = $name;

            return;
        }

        $this->_relation = new Relation($name);
        $this->_name = \str_replace(')', '', \mb_substr($name, $posDivider + 1));
    }

    /**
     * @return null|Relation
     */
    public function getRelation(): ? Relation
    {
        return $this->_relation;
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