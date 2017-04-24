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
        $this->_setName($name);
        $this->_formatter = $columnFormatter;
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
        $this->_key = ($aggregate !== 'first' ? ($aggregate . '_') : '') . $this->_relation->name . '_' . $this->_name;
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
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getAttributeName() : string
    {
        return $this->_relation ? $this->_relation->attributeName : $this->_name;
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
     * @return string
     */
    public function getFormattedValue(Model $rowModel) : string
    {
        $value = $this->getValue($rowModel);

        return $this->_formatter !== null ? $this->_formatter->format($value) : $value;
    }

    /**
     * Get the value of this column for the given row.
     *
     * @param Model $rowModel
     * @return string
     */
    public function getValue(Model $rowModel) : string
    {
        if ($this->_relation !== null)
        {
            return $this->_getValueFromRelation($rowModel);
        }

        return $this->_getValue($rowModel);
    }

    /**
     * Get the value from the column's relation
     *
     * @param Model $rowModel
     * @return string
     */
    private function _getValueFromRelation(Model $rowModel) : string
    {
        $relation = $rowModel->getRelation($this->_relation->name);
        if ($relation instanceof Model)
        {
            return $relation->{$this->_name};
        }

        return $this->_relation->getValue($this->_name, $relation);
    }

    /**
     * @param Model $rowModel
     * @return string
     */
    private function _getValue(Model $rowModel) : string
    {
        return (string) $rowModel->{$this->_name} ?? '';
    }
}
