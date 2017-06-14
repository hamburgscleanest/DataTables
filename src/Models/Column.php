<?php

namespace hamburgscleanest\DataTables\Models;

use hamburgscleanest\DataTables\Interfaces\ColumnFormatter;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Header
 * @package hamburgscleanest\hamburgscleanest\DataTables\Models
 */
class Column {

    /** @var string */
    private $_key;

    /** @var string */
    private $_name;

    /** @var bool */
    private $_mutated = false;

    /** @var string */
    private $_table;

    /** @var Relation */
    private $_relation;

    /** @var ColumnFormatter */
    private $_formatter;

    /**
     * Column constructor.
     * @param string $name
     * @param ColumnFormatter|null $columnFormatter
     * @param Model|null $sourceModel
     */
    public function __construct(string $name, ? ColumnFormatter $columnFormatter = null, ? Model $sourceModel = null)
    {
        $this->_setName($name);
        $this->_formatter = $columnFormatter;
        if ($sourceModel !== null)
        {
            $this->_table = $sourceModel->getTable();
            $this->_mutated = \in_array($name, $sourceModel->getMutatedAttributes(), true);
        }
    }

    /**
     * @param string $name
     */
    private function _setName(string $name) : void
    {
        $posDivider = \mb_strpos($name, '.');
        if ($posDivider === false)
        {
            $this->_name = $this->_key = $name;

            return;
        }

        $this->_name = \str_replace(')', '', \mb_substr($name, $posDivider + 1));

        $this->_relation = new Relation($name);
        $aggregate = $this->_relation->aggregate;
        $this->_key = ($aggregate !== 'first' ? ($aggregate . '_') : '') . $this->_relation->name;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * @return bool
     */
    public function isMutated() : bool
    {
        return $this->_mutated;
    }

    /**
     * @return null|Relation
     */
    public function getRelation() : ? Relation
    {
        return $this->_relation;
    }

    /**
     * @param ColumnFormatter $columnFormatter
     * @return Column
     */
    public function setFormatter(ColumnFormatter $columnFormatter) : Column
    {
        $this->_formatter = $columnFormatter;

        return $this;
    }

    /**
     * Get the formatted column value.
     *
     * @param Model $rowModel
     * @return string|null
     */
    public function getFormattedValue(Model $rowModel) : ? string
    {
        $value = $this->getValue($rowModel);

        return $this->_formatter !== null ? $this->_formatter->format($value) : $value;
    }

    /**
     * Get the value of this column for the given row.
     *
     * @param Model $rowModel
     * @return string|null
     */
    public function getValue(Model $rowModel) : ? string
    {
        return $rowModel->{$this->getKey()};
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->_key;
    }

    /**
     * @return string
     */
    public function getIdentifier() : string
    {
        return $this->getAttributeName() . ' AS ' . $this->_key;
    }

    /**
     * @return string
     */
    public function getAttributeName() : string
    {
        if ($this->_relation !== null)
        {
            return $this->_relation->attributeName;
        }

        if ($this->_mutated)
        {
            return $this->_name;
        }

        return $this->_table . '.' . $this->_name;
    }
}
