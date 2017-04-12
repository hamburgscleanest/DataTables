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

    /** @var string */
    private $_relation;

    /** @var string */
    private $_aggregate = 'first';

    /** @var ColumnFormatter */
    private $_formatter;

    /**
     * Column constructor.
     * @param string $name
     * @param ColumnFormatter|null $columnFormatter
     */
    public function __construct(string $name, ? ColumnFormatter $columnFormatter = null)
    {
        $this->setName($this->_extractAggregate($name));
        $this->_formatter = $columnFormatter;
    }

    /**
     * @param string $name
     * @return string
     */
    private function _extractAggregate(string $name): string
    {
        $replaced = 0;
        $name = preg_replace("/\((.*?)\)/", '#$1', $name, 1, $replaced);
        if ($replaced === 1)
        {
            $parts = \explode('#', $name);
            $this->_aggregate = \mb_strtolower($parts[0]);
            $name = $parts[1];
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getAggregate(): string
    {
        return $this->_aggregate;
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

        $this->_name = \mb_substr($name, $posDivider + 1);
        $this->_relation = \mb_substr($name, 0, $posDivider);
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->_relation;
    }

    /**
     * @return bool
     */
    public function isRelation(): bool
    {
        return !empty($this->_relation);
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